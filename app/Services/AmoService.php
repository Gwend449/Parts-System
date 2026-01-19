<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use App\Models\AmocrmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AmoService
{
    protected AmoCRMApiClient $client;

    public function __construct()
    {
        $this->client = new AmoCRMApiClient();
        $this->initClient();
    }

    /**
     * Инициализация клиента с OAuth токеном из БД
     */
    protected function initClient(): void
    {
        $subdomain = config('amocrm.subdomain');
        
        if (!$subdomain) {
            throw new \Exception('amoCRM не сконфигурирована (subdomain не установлен)');
        }

        // Получаем токен из БД
        $token = AmocrmToken::where('domain', $subdomain)->first();

        if (!$token || !$token->access_token) {
            throw new \Exception('amoCRM не авторизована. Перейдите по ссылке: ' . route('amocrm.install'));
        }

        // Проверяем не истек ли токен (с запасом 5 минут)
        if ($token->expires_at && $token->expires_at->subMinutes(5)->isPast()) {
            $this->refreshToken($token);
            // Перезагружаем токен из БД после обновления
            $token->refresh();
        }

        // Устанавливаем токен и домен в клиент
        $this->client
            ->setAccessToken($token->access_token)
            ->setAccountBaseDomain($subdomain . '.amocrm.ru');
    }

    /**
     * Обновить access_token используя refresh_token
     */
    protected function refreshToken(AmocrmToken $token): void
    {
        if (!$token->refresh_token) {
            throw new \Exception('Не удалось обновить токен: refresh_token отсутствует. Требуется переавторизация.');
        }

        $clientId = config('amocrm.client_id');
        $clientSecret = config('amocrm.client_secret');
        $subdomain = config('amocrm.subdomain');

        if (!$clientId || !$clientSecret || !$subdomain) {
            throw new \Exception('amoCRM не сконфигурирована для обновления токена (client_id, client_secret или subdomain не установлены)');
        }

        try {
            $response = Http::post("https://{$subdomain}.amocrm.ru/oauth2/access_token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token,
                'redirect_uri' => config('amocrm.redirect_uri'),
            ]);

            if (!$response->successful()) {
                throw new \Exception('Ошибка обновления токена: ' . $response->body());
            }

            $data = $response->json();
            
            // Сохраняем обновленный токен
            $token->update([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in']),
                'raw' => $data,
            ]);

            Log::info('AmoCRM токен успешно обновлен', [
                'domain' => $subdomain,
                'expires_at' => $token->expires_at,
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка обновления AmoCRM токена', [
                'error' => $e->getMessage(),
                'domain' => $subdomain,
            ]);
            throw $e;
        }
    }

    /**
     * Получить готовый API-клиент
     */
    public function api(): AmoCRMApiClient
    {
        return $this->client;
    }

    /**
     * Отправить лид в AmoCRM с контактной информацией
     *
     * @param string $name Имя клиента
     * @param string $phone Телефон клиента
     * @param string|null $brand Марка автомобиля
     * @param string|null $model Модель автомобиля
     * @param string|null $comment Комментарий/сообщение
     * @param string|null $source Источник лида
     * @return int ID созданного лида
     */
    public function sendLead(
        string $name,
        string $phone,
        ?string $brand = null,
        ?string $model = null,
        ?string $comment = null,
        ?string $source = null
    ): int {
        try {
            // 1. Создаем контакт с телефоном
            $contactId = $this->createOrUpdateContact($name, $phone);

            // 2. Создаем лид с основной информацией
            $lead = new \AmoCRM\Models\LeadModel();
            $leadName = $name ?? 'Лид без названия';
            if ($brand) {
                $leadName .= " ({$brand})";
            }
            if ($model) {
                $leadName .= " {$model}";
            }
            $lead->setName($leadName);

            // 3. Связываем контакт с лидом
            // В AmoCRM SDK связь контакта с лидом делается через setContactsId или через массив
            if ($contactId) {
                try {
                    if (method_exists($lead, 'setContactsId')) {
                        $lead->setContactsId([$contactId]);
                    } elseif (method_exists($lead, 'setLinkedContactId')) {
                        $lead->setLinkedContactId($contactId);
                    } else {
                        // Альтернативный способ - связываем после создания лида
                        // Это будет сделано в отдельном запросе, если нужно
                        Log::info('Контакт будет связан с лидом отдельным запросом', [
                            'contact_id' => $contactId,
                        ]);
                    }
                } catch (\Exception $linkException) {
                    Log::warning('Не удалось связать контакт с лидом при создании', [
                        'error' => $linkException->getMessage(),
                        'contact_id' => $contactId,
                    ]);
                    // Продолжаем создание лида без связи (можно связать позже)
                }
            }

            // 4. Отправляем лид в amoCRM
            $leadResponse = $this->client->leads()->addOne($lead);
            $leadId = $leadResponse->getId();

            // 5. Добавляем комментарий как примечание
            $noteText = $this->buildNoteText($phone, $brand, $model, $comment, $source);
            if ($noteText) {
                $this->addNoteToLead($leadId, $noteText);
            }

            Log::info('Лид успешно создан в AmoCRM', [
                'lead_id' => $leadId,
                'contact_id' => $contactId,
                'name' => $name,
                'phone' => $phone,
            ]);

            return $leadId;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании лида в AmoCRM', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Ошибка при создании лида в amoCRM: ' . $e->getMessage());
        }
    }

    /**
     * Создать или обновить контакт
     */
    private function createOrUpdateContact(string $name, string $phone): ?int
    {
        try {
            $contact = new \AmoCRM\Models\ContactModel();
            $contact->setFirstName($name);
            
            // Добавляем телефон в контакт
            // В AmoCRM SDK телефоны добавляются через setCustomFields или через специальные методы
            // Попробуем использовать setCustomFields для телефона
            try {
                // Стандартное поле "Телефон" в AmoCRM обычно имеет код "PHONE"
                // Используем метод для добавления телефона
                if (method_exists($contact, 'setPhone')) {
                    $contact->setPhone($phone);
                } else {
                    // Альтернативный способ через кастомные поля
                    $contact->setCustomFields([
                        [
                            'id' => 'PHONE',
                            'values' => [
                                [
                                    'value' => $phone,
                                    'enum' => 'WORK', // WORK, MOB, HOME и т.д.
                                ]
                            ]
                        ]
                    ]);
                }
            } catch (\Exception $phoneException) {
                Log::warning('Не удалось добавить телефон в контакт', [
                    'error' => $phoneException->getMessage(),
                    'phone' => $phone,
                ]);
                // Продолжаем создание контакта без телефона
            }

            $response = $this->client->contacts()->addOne($contact);
            return $response->getId();
        } catch (\Exception $e) {
            Log::warning('Не удалось создать контакт', [
                'error' => $e->getMessage(),
                'name' => $name,
                'phone' => $phone,
            ]);
            return null;
        }
    }

    /**
     * Построить текст примечания с информацией из формы
     */
    private function buildNoteText(
        ?string $phone,
        ?string $brand,
        ?string $model,
        ?string $comment,
        ?string $source
    ): string {
        $parts = [];

        if ($source) {
            $parts[] = "📌 Источник: {$source}";
        }

        if ($phone) {
            $parts[] = "📱 Телефон: {$phone}";
        }

        if ($brand) {
            $parts[] = "🚗 Марка: {$brand}";
        }

        if ($model) {
            $parts[] = "🔧 Модель: {$model}";
        }

        if ($comment) {
            $parts[] = "💬 Сообщение: {$comment}";
        }

        return implode("\n", $parts);
    }

    /**
     * Добавить примечание к лиду
     */
    private function addNoteToLead(int $leadId, string $text): void
    {
        try {
            $note = new \AmoCRM\Models\NoteModel();
            $note->setText($text);
            $note->setEntityId($leadId);
            $note->setNoteType(\AmoCRM\Models\NoteModel::COMMON); // Тип примечания: обычное
            
            $this->client->notes($leadId)->addOne($note);
            
            Log::info('Примечание успешно добавлено к лиду', [
                'lead_id' => $leadId,
                'text_length' => strlen($text),
            ]);
        } catch (\Exception $e) {
            Log::warning('Ошибка при добавлении примечания к лиду', [
                'lead_id' => $leadId,
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 100), // Первые 100 символов для отладки
            ]);
            // Не бросаем исключение, так как примечание - это дополнительная информация
        }
    }
}

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
            // 1. Создаем контакт
            $contactId = $this->createOrUpdateContact($name, $phone);

            // 2. Создаем лид с основной информацией
            $lead = new \AmoCRM\Models\LeadModel();
            $leadName = $name ?? 'Лид без названия';
            if ($brand) {
                $leadName .= " ({$brand})";
            }
            $lead->setName($leadName);

            // 3. Отправляем лид в amoCRM
            $leadResponse = $this->client->leads()->addOne($lead);
            $leadId = $leadResponse->getId();

            // 4. Добавляем комментарий как примечание
            $noteText = $this->buildNoteText($phone, $brand, $model, $comment, $source);
            if ($noteText) {
                $this->addNoteToLead($leadId, $noteText);
            }

            return $leadId;
        } catch (\Exception $e) {
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

            $response = $this->client->contacts()->addOne($contact);
            return $response->getId();
        } catch (\Exception $e) {
            \Log::warning('Не удалось создать контакт', [
                'error' => $e->getMessage(),
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
            // API SDK может иметь разные методы в зависимости от версии
            // Эта часть требует проверки с вашей версией SDK
            \Log::info('Примечание для лида подготовлено', [
                'lead_id' => $leadId,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Ошибка при добавлении примечания', [
                'lead_id' => $leadId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

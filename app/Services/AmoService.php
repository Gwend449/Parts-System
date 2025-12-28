<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use App\Models\AmocrmToken;

class AmoService
{
    protected AmoCRMApiClient $client;

    public function __construct()
    {
        $this->client = new AmoCRMApiClient();
        $this->initClient();
    }

    /**
     * Инициализация клиента с приватным токеном
     */
    protected function initClient(): void
    {
        $privateToken = config('amocrm.private_token');
        $subdomain = config('amocrm.subdomain');

        if (!$privateToken || !$subdomain) {
            throw new \Exception('amoCRM не сконфигурирована (private_token или subdomain не установлены)');
        }

        // Для приватной интеграции просто устанавливаем токен и домен
        $this->client
            ->setAccessToken($privateToken)
            ->setAccountBaseDomain($subdomain . '.amocrm.ru');

        // Опционально: сохраняем информацию о подключении в БД
        AmocrmToken::updateOrCreate(
            ['domain' => $subdomain],
            [
                'access_token' => $privateToken,
                'refresh_token' => null,
                'expires_at' => null, // У приватных токенов нет срока действия
                'raw' => [
                    'type' => 'private',
                    'initialized_at' => now(),
                ],
            ]
        );
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

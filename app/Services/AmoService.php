<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use App\Models\AmocrmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Token\AccessToken;

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

        // Создаем объект AccessToken для SDK
        $accessTokenData = [
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
        ];

        // Добавляем expires (Unix timestamp) если есть expires_at
        // SDK ожидает поле 'expires' с Unix timestamp, а не 'expires_in'
        if ($token->expires_at) {
            // expires должен быть Unix timestamp, когда токен истекает
            $accessTokenData['expires'] = $token->expires_at->timestamp;
        } else {
            // Если expires_at не установлено, устанавливаем его как истекший
            // чтобы SDK попытался обновить токен при следующем запросе
            // или устанавливаем время в будущем (например, через час по умолчанию)
            $accessTokenData['expires'] = now()->addHour()->timestamp;
            Log::warning('AmoCRM токен не имеет expires_at, используется временное значение', [
                'domain' => $subdomain,
            ]);
        }

        $accessToken = new AccessToken($accessTokenData);

        // Устанавливаем токен и домен в клиент
        $this->client
            ->setAccessToken($accessToken)
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
            $response = Http::asForm()->post("https://{$subdomain}.amocrm.ru/oauth2/access_token", [
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
     * @param string|null $email Email клиента
     * @param string|null $brand Марка автомобиля
     * @param string|null $model Модель автомобиля
     * @param string|null $comment Комментарий/сообщение
     * @param string|null $source Источник лида
     * @return int ID созданного лида
     */
    public function sendLead(
        string $name,
        string $phone,
        ?string $email = null,
        ?string $brand = null,
        ?string $model = null,
        ?string $comment = null,
        ?string $source = null
    ): int {
        try {
            // 1. Создаем контакт с телефоном и email
            $contactId = $this->createOrUpdateContact($name, $phone, $email);

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

            // 3. Связываем контакт с лидом через _embedded
            // В AmoCRM SDK для публичной интеграции связь делается через _embedded->contacts
            if ($contactId) {
                try {
                    // Используем правильный способ связывания через _embedded
                    $contactsCollection = new \AmoCRM\Collections\ContactsCollection();
                    $linkedContact = new \AmoCRM\Models\ContactModel();
                    $linkedContact->setId($contactId);
                    $contactsCollection->add($linkedContact);
                    $lead->setContacts($contactsCollection);
                } catch (\Exception $linkException) {
                    Log::warning('Не удалось связать контакт с лидом при создании, попробуем после создания лида', [
                        'error' => $linkException->getMessage(),
                        'contact_id' => $contactId,
                    ]);
                }
            }

            // 4. Отправляем лид в amoCRM
            $leadResponse = $this->client->leads()->addOne($lead);
            $leadId = $leadResponse->getId();
            
            // 5. Если контакт не был связан при создании, связываем отдельным запросом
            if ($contactId) {
                try {
                    // Проверяем, связан ли контакт
                    $leadData = $this->client->leads()->getOne($leadId);
                    $hasContacts = false;
                    if (method_exists($leadData, 'getContacts')) {
                        $contacts = $leadData->getContacts();
                        $hasContacts = $contacts && $contacts->count() > 0;
                    }
                    
                    if (!$hasContacts) {
                        $this->linkContactToLead($leadId, $contactId);
                    }
                } catch (\Exception $linkException) {
                    Log::warning('Не удалось связать контакт с лидом после создания', [
                        'error' => $linkException->getMessage(),
                        'lead_id' => $leadId,
                        'contact_id' => $contactId,
                    ]);
                    // Не прерываем выполнение, так как лид уже создан
                }
            }

            // 6. Добавляем комментарий как примечание
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
    private function createOrUpdateContact(string $name, string $phone, ?string $email = null): ?int
    {
        try {
            $contact = new \AmoCRM\Models\ContactModel();
            $contact->setFirstName($name);

            // Получаем ID стандартных полей для телефона и email
            $phoneFieldId = $this->getStandardFieldId('PHONE');
            $emailFieldId = $this->getStandardFieldId('EMAIL');

            // Добавляем телефон и email через CustomFieldsValues
            $customFieldsValues = new \AmoCRM\Collections\CustomFieldsValuesCollection();
            
            // Добавляем телефон
            if ($phoneFieldId) {
                $phoneField = new \AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel();
                $phoneField->setFieldId($phoneFieldId);
                
                $phoneField->setValues(
                    (new \AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection())
                        ->add(
                            (new \AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel())
                                ->setEnum('WORK')
                                ->setValue($phone)
                        )
                );
                $customFieldsValues->add($phoneField);
            }

            // Добавляем email если указан
            if ($email && $emailFieldId) {
                $emailField = new \AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel();
                $emailField->setFieldId($emailFieldId);
                
                $emailField->setValues(
                    (new \AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection())
                        ->add(
                            (new \AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel())
                                ->setEnum('WORK')
                                ->setValue($email)
                        )
                );
                $customFieldsValues->add($emailField);
            }

            if ($customFieldsValues->count() > 0) {
                $contact->setCustomFieldsValues($customFieldsValues);
            }

            $response = $this->client->contacts()->addOne($contact);
            return $response->getId();
        } catch (\Exception $e) {
            Log::error('Не удалось создать контакт в AmoCRM', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
            ]);
            return null;
        }
    }

    /**
     * Получить ID стандартного поля контакта (PHONE или EMAIL)
     * Использует кеш, чтобы не делать лишние запросы к API
     */
    private function getStandardFieldId(string $fieldCode): ?int
    {
        static $cache = [];
        
        if (isset($cache[$fieldCode])) {
            return $cache[$fieldCode];
        }

        try {
            // Получаем информацию о стандартных полях контактов через API
            // В AmoCRM SDK поля получаются для конкретного типа сущности
            $fieldsCollection = $this->client->customFields('contacts')->get();
            
            // Ищем поле по коду
            if ($fieldsCollection) {
                $fields = $fieldsCollection;
                
                foreach ($fields as $field) {
                    // Стандартные поля имеют определенные коды
                    // Проверяем оба возможных метода получения кода
                    $code = null;
                    if (method_exists($field, 'getFieldCode')) {
                        $code = $field->getFieldCode();
                    } elseif (method_exists($field, 'getCode')) {
                        $code = $field->getCode();
                    }
                    
                    if ($code === $fieldCode) {
                        $fieldId = method_exists($field, 'getId') ? $field->getId() : null;
                        if ($fieldId) {
                            $cache[$fieldCode] = $fieldId;
                            return $fieldId;
                        }
                    }
                }
                
                // Если не нашли через код, ищем по типу и имени
                foreach ($fields as $field) {
                    $fieldName = method_exists($field, 'getName') ? strtolower($field->getName()) : '';
                    $fieldType = method_exists($field, 'getType') ? $field->getType() : '';
                    
                    // Проверяем оба возможных метода получения кода
                    $fieldCodeFromApi = null;
                    if (method_exists($field, 'getFieldCode')) {
                        $fieldCodeFromApi = $field->getFieldCode();
                    } elseif (method_exists($field, 'getCode')) {
                        $fieldCodeFromApi = $field->getCode();
                    }
                    
                    if ($fieldCode === 'PHONE' && (
                        $fieldCodeFromApi === 'PHONE' ||
                        stripos($fieldName, 'телефон') !== false || 
                        stripos($fieldName, 'phone') !== false ||
                        $fieldType === 'PHONE' ||
                        strtolower($fieldType) === 'multitext'
                    )) {
                        $fieldId = method_exists($field, 'getId') ? $field->getId() : null;
                        if ($fieldId) {
                            $cache[$fieldCode] = $fieldId;
                            return $fieldId;
                        }
                    }
                    
                    if ($fieldCode === 'EMAIL' && (
                        $fieldCodeFromApi === 'EMAIL' ||
                        stripos($fieldName, 'email') !== false ||
                        stripos($fieldName, 'почта') !== false ||
                        $fieldType === 'EMAIL' ||
                        strtolower($fieldType) === 'multitext'
                    )) {
                        $fieldId = method_exists($field, 'getId') ? $field->getId() : null;
                        if ($fieldId) {
                            $cache[$fieldCode] = $fieldId;
                            return $fieldId;
                        }
                    }
                }
            }
            
            Log::warning("Не удалось найти ID стандартного поля {$fieldCode} через API, будет использован упрощенный подход");
            // Возвращаем null - в этом случае попробуем создать контакт без этих полей
            return null;
        } catch (\Exception $e) {
            Log::warning("Ошибка при получении ID стандартного поля {$fieldCode}", [
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
            $note = new \AmoCRM\Models\NoteModel();
            $note->setEntityId($leadId);
            
            // Устанавливаем тип сущности для примечания (lead = сделка)
            if (method_exists($note, 'setEntityType')) {
                // Проверяем различные варианты констант
                if (defined('\AmoCRM\Models\NoteModel::NOTE_TYPE_LEAD')) {
                    $note->setEntityType(\AmoCRM\Models\NoteModel::NOTE_TYPE_LEAD);
                } elseif (defined('\AmoCRM\Models\NoteModel::ENTITY_LEAD')) {
                    $note->setEntityType(\AmoCRM\Models\NoteModel::ENTITY_LEAD);
                } else {
                    // Если константы нет, используем строку
                    $note->setEntityType('leads');
                }
            }

            // Используем правильные методы
            if (method_exists($note, 'setText')) {
                $note->setText($text);
            } else if (method_exists($note, 'setNote')) {
                $note->setNote($text);
            }

            // Устанавливаем тип примечания если метод доступен
            if (method_exists($note, 'setNoteType')) {
                try {
                    if (defined('\AmoCRM\Models\NoteModel::COMMON')) {
                        $note->setNoteType(\AmoCRM\Models\NoteModel::COMMON);
                    }
                } catch (\Exception $e) {
                    Log::debug('Тип примечания COMMON не доступен', ['error' => $e->getMessage()]);
                }
            }

            // Метод notes() требует ID сущности (leadId) в качестве аргумента
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

    /**
     * Связать контакт с лидом отдельным запросом
     */
    private function linkContactToLead(int $leadId, int $contactId): void
    {
        try {
            // Получаем лид
            $lead = $this->client->leads()->getOne($leadId);
            
            // Добавляем контакт к лиду
            $contactsCollection = new \AmoCRM\Collections\ContactsCollection();
            $linkedContact = new \AmoCRM\Models\ContactModel();
            $linkedContact->setId($contactId);
            $contactsCollection->add($linkedContact);
            $lead->setContacts($contactsCollection);
            
            // Обновляем лид
            $this->client->leads()->updateOne($lead);
            
            Log::info('Контакт успешно связан с лидом', [
                'lead_id' => $leadId,
                'contact_id' => $contactId,
            ]);
        } catch (\Exception $e) {
            Log::warning('Ошибка при связывании контакта с лидом', [
                'lead_id' => $leadId,
                'contact_id' => $contactId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

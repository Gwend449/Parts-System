<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use App\Models\AmocrmToken;
use League\OAuth2\Client\Token\AccessToken;

class AmoService
{
    protected AmoCRMApiClient $client;

    public function __construct()
    {
        $this->client = new AmoCRMApiClient(
            config('amocrm.client_id'),
            config('amocrm.client_secret'),
            config('amocrm.redirect_uri')
        );

        $this->initClient();
    }

    /**
     * Инициализация клиента из БД
     */
    protected function initClient(): void
    {
        $token = AmocrmToken::first();

        if (!$token) {
            throw new \Exception('amoCRM не подключена');
        }

        $accessToken = new AccessToken([
            'access_token'  => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires'       => $token->expires_at->timestamp,
        ]);

        $this->client
            ->setAccessToken($accessToken)
            ->setAccountBaseDomain($token->domain);
    }

    /**
     * Получить готовый API-клиент
     */
    public function api(): AmoCRMApiClient
    {
        return $this->client;
    }
}

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

        // Проверить, не истек ли токен
        if ($token->expires_at->isPast()) {
            $this->refreshToken($token);
            $token->refresh();
        }

        $accessToken = new AccessToken([
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires' => $token->expires_at->timestamp,
        ]);

        $this->client
            ->setAccessToken($accessToken)
            ->setAccountBaseDomain($token->domain);
    }

    /**
     * Обновить токен доступа (refresh token)
     */
    protected function refreshToken(AmocrmToken $token): void
    {
        try {
            $accessToken = $this->client
                ->getOAuthClient()
                ->getAccessTokenByRefreshToken($token->refresh_token);

            $token->update([
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires_at' => now()->addSeconds($accessToken->getExpires()),
                'raw' => $accessToken->jsonSerialize(),
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Не удалось обновить токен: ' . $e->getMessage());
        }
    }

    /**
     * Получить готовый API-клиент
     */
    public function api(): AmoCRMApiClient
    {
        return $this->client;
    }
}

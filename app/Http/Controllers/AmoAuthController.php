<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;


class AmoAuthController extends Controller
{
    public function install()
    {
        $url = 'https://' . config('amocrm.subdomain') . '.amocrm.ru/oauth?' . http_build_query([
            'client_id' => config('amocrm.client_id'),
            'mode' => 'post',
            'redirect_uri' => config('amocrm.redirect_uri'),
        ]);

        return redirect($url);
    }

    public function callback(Request $request)
    {
        try {
            $code = $request->get('code');
            $state = $request->get('state');
            $error = $request->get('error');

            // Проверка на ошибки авторизации от AmoCRM
            if ($error) {
                return redirect('/')->with('error', 'Ошибка амоCRM: ' . $error);
            }

            if (!$code) {
                return redirect('/')->with('error', 'Нет кода авторизации');
            }

            // Создаем клиент для получения токена
            $client = new AmoCRMApiClient(
                config('amocrm.client_id'),
                config('amocrm.client_secret'),
                config('amocrm.redirect_uri')
            );

            // Получаем токен по коду
            $accessToken = $client
                ->getOAuthClient()
                ->getAccessTokenByCode($code);

            // Извлекаем данные из токена
            $tokenData = $accessToken->getValues();
            $baseDomain = $tokenData['baseDomain'];

            // Сохраняем токен в БД
            \App\Models\AmocrmToken::updateOrCreate(
                ['domain' => $baseDomain],
                [
                    'access_token' => $accessToken->getToken(),
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'expires_at' => now()->addSeconds($accessToken->getExpires()),
                    'raw' => $accessToken->jsonSerialize(),
                ]
            );

            return redirect('/')->with('success', 'amoCRM успешно подключена!');
        } catch (\Exception $e) {
            \Log::error('AmoCRM auth error: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Ошибка при подключении amoCRM: ' . $e->getMessage());
        }
    }
}

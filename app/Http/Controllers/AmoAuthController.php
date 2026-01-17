<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AmocrmToken;

class AmoAuthController extends Controller
{
    /**
     * Инициировать авторизацию OAuth - перенаправляет на страницу авторизации AmoCRM
     */
    public function install()
    {
        $clientId = config('amocrm.client_id');
        $redirectUri = config('amocrm.redirect_uri');
        $subdomain = config('amocrm.subdomain');

        if (!$clientId || !$redirectUri || !$subdomain) {
            return redirect('/')
                ->with('error', 'amoCRM не сконфигурирована. Проверьте настройки в .env файле (AMO_CLIENT_ID, AMO_CLIENT_SECRET, AMO_REDIRECT_URI, AMOCRM_SUBDOMAIN)');
        }

        $state = bin2hex(random_bytes(16));
        session(['amocrm_oauth_state' => $state]);

        // Формируем URL для авторизации
        $authUrl = "https://{$subdomain}.amocrm.ru/oauth?client_id={$clientId}&state={$state}&mode=post_message";

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        try {
            // Проверяем state для защиты от CSRF
            $sessionState = session('amocrm_oauth_state');
            $requestState = $request->input('state');

            if (!$sessionState || $sessionState !== $requestState) {
                Log::warning('AmoCRM OAuth: неверный state параметр', [
                    'session_state' => $sessionState,
                    'request_state' => $requestState,
                ]);
                return redirect('/')
                    ->with('error', 'Ошибка безопасности при авторизации. Попробуйте еще раз.');
            }

            // Удаляем state из сессии
            session()->forget('amocrm_oauth_state');

            $code = $request->input('code');
            if (!$code) {
                return redirect('/')
                    ->with('error', 'Код авторизации не получен. Попробуйте еще раз.');
            }

            $clientId = config('amocrm.client_id');
            $clientSecret = config('amocrm.client_secret');
            $redirectUri = config('amocrm.redirect_uri');
            $subdomain = config('amocrm.subdomain');

            if (!$clientId || !$clientSecret || !$redirectUri || !$subdomain) {
                return redirect('/')
                    ->with('error', 'amoCRM не сконфигурирована. Проверьте настройки в .env файле.');
            }

            // Обмениваем код на токен
            $response = Http::post("https://{$subdomain}.amocrm.ru/oauth2/access_token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                Log::error('AmoCRM OAuth: ошибка обмена кода на токен', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                ]);
                return redirect('/')
                    ->with('error', 'Ошибка при получении токена: ' . $errorBody);
            }

            $data = $response->json();

            // Сохраняем токен в БД
            AmocrmToken::updateOrCreate(
                ['domain' => $subdomain],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
                    'raw' => $data,
                ]
            );

            Log::info('AmoCRM успешно авторизована', [
                'domain' => $subdomain,
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]);

            return redirect('/')
                ->with('success', 'amoCRM успешно подключена!');

        } catch (\Exception $e) {
            Log::error('AmoCRM OAuth callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')
                ->with('error', 'Ошибка подключения amoCRM: ' . $e->getMessage());
        }
    }
}

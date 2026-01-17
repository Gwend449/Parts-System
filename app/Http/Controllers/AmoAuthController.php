<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
                ->with('error', 'amoCRM не сконфигурирована. Проверьте настройки в .env файле (AMOCRM_CLIENT_ID, AMOCRM_CLIENT_SECRET, AMOCRM_REDIRECT_URI, AMOCRM_SUBDOMAIN)');
        }

        $state = bin2hex(random_bytes(16));
        
        // Сохраняем state в сессию
        session(['amocrm_oauth_state' => $state]);
        
        // Также сохраняем в кеш как резервный вариант (TTL 10 минут)
        // Это поможет, если cookies не передаются между доменами
        Cache::put("amocrm_oauth_state_{$state}", $state, now()->addMinutes(10));
        
        // Явно сохраняем сессию перед редиректом, чтобы state был доступен после возврата
        session()->save();

        // Формируем URL для авторизации
        // redirect_uri должен быть закодирован в URL и точно совпадать с настройками в AmoCRM
        // Используем http_build_query для правильного кодирования параметров
        $params = [
            'client_id' => $clientId,
            'state' => $state,
            'redirect_uri' => $redirectUri, // http_build_query автоматически закодирует
        ];
        
        $queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $authUrl = "https://{$subdomain}.amocrm.ru/oauth?{$queryString}";

        Log::info('AmoCRM OAuth: перенаправление на авторизацию', [
            'auth_url' => $authUrl,
            'redirect_uri' => $redirectUri,
            'redirect_uri_encoded' => rawurlencode($redirectUri),
            'subdomain' => $subdomain,
            'client_id' => $clientId,
            'state' => $state,
            'session_id' => session()->getId(),
            'params' => $params,
        ]);

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        try {
            // Проверяем state для защиты от CSRF
            $requestState = $request->input('state');
            $sessionState = session('amocrm_oauth_state');
            
            // Если state нет в сессии, пытаемся получить из кеша (резервный вариант)
            $cachedState = null;
            if ($requestState) {
                $cachedState = Cache::get("amocrm_oauth_state_{$requestState}");
            }

            Log::info('AmoCRM OAuth: callback получен', [
                'session_state' => $sessionState,
                'request_state' => $requestState,
                'cached_state' => $cachedState,
                'session_id' => session()->getId(),
                'has_state_in_session' => session()->has('amocrm_oauth_state'),
            ]);

            // Проверяем state из сессии или кеша
            $validState = ($sessionState && $sessionState === $requestState) || 
                         ($cachedState && $cachedState === $requestState);

            if (!$validState || !$requestState) {
                Log::warning('AmoCRM OAuth: неверный state параметр', [
                    'session_state' => $sessionState,
                    'request_state' => $requestState,
                    'cached_state' => $cachedState,
                    'session_id' => session()->getId(),
                    'session_data' => session()->all(),
                ]);
                return redirect('/')
                    ->with('error', 'Ошибка безопасности при авторизации. Попробуйте еще раз.');
            }

            // Удаляем state из сессии и кеша
            session()->forget('amocrm_oauth_state');
            if ($requestState) {
                Cache::forget("amocrm_oauth_state_{$requestState}");
            }

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

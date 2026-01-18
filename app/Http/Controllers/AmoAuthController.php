<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AmocrmToken;
use App\Models\AmocrmOauthState;

class AmoAuthController extends Controller
{
    /**
     * Инициировать авторизацию OAuth - перенаправляет на страницу авторизации AmoCRM
     * 
     * Согласно официальной документации AmoCRM:
     * https://github.com/amocrm/amocrm-oauth-client
     */
    public function install()
    {
        try {
            $clientId = config('amocrm.client_id');
            $redirectUri = config('amocrm.redirect_uri');
            $subdomain = config('amocrm.subdomain');

            Log::info('AmoCRM OAuth: запрос на авторизацию', [
                'has_client_id' => !!$clientId,
                'has_redirect_uri' => !!$redirectUri,
                'has_subdomain' => !!$subdomain,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            if (!$clientId || !$redirectUri || !$subdomain) {
                Log::warning('AmoCRM OAuth: неполная конфигурация', [
                    'client_id' => !!$clientId,
                    'redirect_uri' => !!$redirectUri,
                    'subdomain' => !!$subdomain,
                ]);

                return redirect('/')
                    ->with('error', 'amoCRM не сконфигурирована. Проверьте настройки в .env файле (AMOCRM_CLIENT_ID, AMOCRM_CLIENT_SECRET, AMOCRM_REDIRECT_URI, AMOCRM_SUBDOMAIN)');
            }

            // Генерируем и сохраняем state в БД для защиты от CSRF
            // Используем БД вместо сессии, так как это надежнее при HTTPS и редиректах между доменами
            $state = AmocrmOauthState::generateState($subdomain);

            Log::debug('AmoCRM OAuth: state сгенерирован', [
                'state_length' => strlen($state),
                'subdomain' => $subdomain,
            ]);

            // Формируем URL для авторизации согласно документации AmoCRM
            // URL должен быть: https://{subdomain}.amocrm.ru/oauth?client_id={client_id}&state={state}&redirect_uri={redirect_uri}
            $params = [
                'client_id' => $clientId,
                'state' => $state,
                'redirect_uri' => $redirectUri,
            ];

            // Используем http_build_query с правильной кодировкой
            $queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            $authUrl = "https://{$subdomain}.amocrm.ru/oauth?{$queryString}";

            Log::info('AmoCRM OAuth: перенаправление на авторизацию', [
                'auth_url' => $authUrl,
                'redirect_uri' => $redirectUri,
                'subdomain' => $subdomain,
                'client_id' => $clientId,
                'state' => $state,
            ]);

            return redirect($authUrl);

        } catch (\Exception $e) {
            Log::error('AmoCRM OAuth install error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')
                ->with('error', 'Ошибка при инициировании авторизации AmoCRM: ' . $e->getMessage());
        }
    }

    /**
     * Callback для обработки ответа от AmoCRM OAuth
     * 
     * Согласно официальной документации, важно:
     * 1. Обработать параметр 'referer' (subdomain)
     * 2. Использовать setBaseDomain() если используется официальный пакет
     * 3. Обменять код на токен
     */
    public function callback(Request $request)
    {
        try {
            Log::info('AmoCRM OAuth: callback получен', [
                'all_params' => $request->all(),
                'referer' => $request->input('referer'),
                'code' => $request->has('code'),
                'state' => $request->input('state'),
                'error' => $request->input('error'),
            ]);

            // Проверяем наличие ошибки от AmoCRM
            if ($request->has('error')) {
                $error = $request->input('error');
                $errorDescription = $request->input('error_description', 'Неизвестная ошибка');

                Log::error('AmoCRM OAuth: ошибка от AmoCRM', [
                    'error' => $error,
                    'error_description' => $errorDescription,
                ]);

                return redirect('/')
                    ->with('error', "Ошибка авторизации AmoCRM: {$errorDescription}");
            }

            // Проверяем state для защиты от CSRF
            // State хранится в БД, а не в сессии (более надежно)
            $requestState = $request->input('state');

            if (!$requestState) {
                Log::error('AmoCRM OAuth: state параметр не получен');
                return redirect('/')
                    ->with('error', 'Ошибка: state параметр не получен.');
            }

            // Проверяем state в БД и удаляем его
            $stateRecord = AmocrmOauthState::verifyAndDelete($requestState);

            if (!$stateRecord) {
                Log::warning('AmoCRM OAuth: неверный или устаревший state параметр', [
                    'request_state' => $requestState,
                ]);
                return redirect('/')
                    ->with('error', 'Ошибка безопасности при авторизации. State параметр истек или неверен. Попробуйте еще раз.');
            }

            // Получаем код авторизации
            $code = $request->input('code');
            if (!$code) {
                Log::error('AmoCRM OAuth: код авторизации не получен');
                return redirect('/')
                    ->with('error', 'Код авторизации не получен. Попробуйте еще раз.');
            }

            // Получаем referer (subdomain) из параметров или используем сохраненный в stateRecord
            // Согласно документации, AmoCRM может передать referer в параметрах
            $referer = $request->input('referer');
            $subdomain = $referer ?: ($stateRecord->subdomain ?? config('amocrm.subdomain'));

            if (!$subdomain) {
                Log::error('AmoCRM OAuth: subdomain не определен');
                return redirect('/')
                    ->with('error', 'Ошибка: не удалось определить поддомен AmoCRM.');
            }

            $clientId = config('amocrm.client_id');
            $clientSecret = config('amocrm.client_secret');
            $redirectUri = config('amocrm.redirect_uri');

            if (!$clientId || !$clientSecret || !$redirectUri) {
                Log::error('AmoCRM OAuth: не все параметры конфигурации установлены');
                return redirect('/')
                    ->with('error', 'amoCRM не сконфигурирована. Проверьте настройки в .env файле.');
            }

            Log::info('AmoCRM OAuth: обмен кода на токен', [
                'subdomain' => $subdomain,
                'redirect_uri' => $redirectUri,
            ]);

            // Обмениваем код на токен
            // Согласно документации: POST https://{subdomain}.amocrm.ru/oauth2/access_token
            $response = Http::asForm()->post("https://{$subdomain}.amocrm.ru/oauth2/access_token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

            if (!$response->successful()) {
                $status = $response->status();
                $errorBody = $response->body();

                Log::error('AmoCRM OAuth: ошибка обмена кода на токен', [
                    'status' => $status,
                    'body' => $errorBody,
                    'headers' => $response->headers(),
                ]);

                return redirect('/')
                    ->with('error', "Ошибка при получении токена (HTTP {$status}): {$errorBody}");
            }

            $data = $response->json();

            // Проверяем наличие необходимых полей в ответе
            if (!isset($data['access_token']) || !isset($data['refresh_token'])) {
                Log::error('AmoCRM OAuth: неполный ответ от сервера', [
                    'response' => $data,
                ]);
                return redirect('/')
                    ->with('error', 'Неполный ответ от сервера AmoCRM. Проверьте логи.');
            }

            // Определяем baseDomain (subdomain без .amocrm.ru)
            $baseDomain = $subdomain;

            // Сохраняем токен в БД
            AmocrmToken::updateOrCreate(
                ['domain' => $baseDomain],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at' => now()->addSeconds($data['expires_in'] ?? 86400),
                    'raw' => $data,
                ]
            );

            Log::info('AmoCRM успешно авторизована', [
                'domain' => $baseDomain,
                'expires_at' => now()->addSeconds($data['expires_in'] ?? 86400),
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

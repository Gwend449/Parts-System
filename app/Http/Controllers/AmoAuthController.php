<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;


class AmoAuthController extends Controller
{
    public function install()
    {
        try {
            $amo = new AmoService();
            $client = $amo->api();

            // Делаем простой запрос к аккаунту, чтобы проверить токен
            $account = $client->account()->getCurrent();

            return redirect('/')
                ->with('success', 'amoCRM подключена (private integration)! Account: ' . ($account['name'] ?? 'unknown'));
        } catch (\Exception $e) {
            \Log::error('AmoCRM private integration error: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')
                ->with('error', 'Ошибка подключения amoCRM: ' . $e->getMessage());
        }
    }
}

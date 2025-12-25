<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;
use App\Models\AmoToken;
use Illuminate\Support\Str;


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



    // public function callback(Request $request)
    // {
    //     $code = $request->get('code');

    //     if (!$code) {
    //         return redirect('/')->with('error', 'Нет кода авторизации');
    //     }

    //     $client = new AmoCRMApiClient(
    //         config('amocrm.client_id'),
    //         config('amocrm.client_secret'),
    //         config('amocrm.redirect_uri')
    //     );

    //     $accessToken = $client
    //         ->getOAuthClient()
    //         ->getAccessTokenByCode($code);

    //     $baseDomain = $accessToken->getValues()['baseDomain'];

    //     \App\Models\AmocrmToken::updateOrCreate(
    //         ['domain' => $baseDomain],
    //         [
    //             'access_token' => $accessToken->getToken(),
    //             'refresh_token' => $accessToken->getRefreshToken(),
    //             'expires_at' => now()->addSeconds($accessToken->getExpires()),
    //             'raw' => $accessToken->jsonSerialize(),
    //         ]
    //     );

    //     return redirect('/')->with('success', 'amoCRM подключена');
    // }

    public function callback(Request $request)
    {
        dd([
            'all' => $request->all(),
            'code' => $request->get('code'),
            'state' => $request->get('state'),
            'url' => $request->fullUrl(),
        ]);
    }
}

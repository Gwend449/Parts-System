<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmocrmToken extends Model
{
    protected $fillable = ['domain', 'access_token', 'refresh_token', 'expires_at', 'raw'];
    protected $casts = ['raw' => 'array', 'expires_at' => 'datetime'];

    function saveToken(array $data): void
    {
        AmocrmToken::updateOrCreate(
            ['domain' => $data['baseDomain']],
            [
                'access_token' => $data['accessToken'],
                'refresh_token' => $data['refreshToken'],
                'expires_at' => now()->addSeconds($data['expires']),
                'raw' => $data,
            ]
        );
    }
}

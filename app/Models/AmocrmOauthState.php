<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmocrmOauthState extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'state';
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['state', 'subdomain', 'created_at', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime', 'created_at' => 'datetime'];

    /**
     * Генерировать и сохранить новый state
     */
    public static function generateState(?string $subdomain = null): string
    {
        $state = bin2hex(random_bytes(32));
        
        self::create([
            'state' => $state,
            'subdomain' => $subdomain,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(15), // State действителен 15 минут
        ]);
        
        return $state;
    }

    /**
     * Проверить и удалить state
     */
    public static function verifyAndDelete(string $state): ?self
    {
        $record = self::where('state', $state)
            ->where('expires_at', '>', now())
            ->first();
        
        if ($record) {
            $record->delete();
        }
        
        return $record;
    }

    /**
     * Очистить устаревшие states
     */
    public static function cleanup(): int
    {
        return self::where('expires_at', '<', now())->delete();
    }
}

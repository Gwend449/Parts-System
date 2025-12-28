<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AmocrmToken;

class CheckAmoStatus extends Command
{
   protected $signature = 'amo:status';

   protected $description = 'Проверить статус подключения AmoCRM';

   public function handle()
   {
      $token = AmocrmToken::first();

      if (!$token) {
         $this->error('❌ AmoCRM не подключена!');
         $this->info('Перейди по ссылке для авторизации: ' . route('amocrm.install'));
         return 1;
      }

      $this->info('✅ AmoCRM подключена!');
      $this->line('');
      $this->table(
         ['Параметр', 'Значение'],
         [
            ['Domain', $token->domain],
            ['Access Token', str($token->access_token)->limit(30) . '...'],
            ['Expires At', $token->expires_at->format('Y-m-d H:i:s')],
            ['Is Expired', $token->expires_at->isPast() ? 'ДА ⚠️' : 'НЕТ ✅'],
            ['Created At', $token->created_at->format('Y-m-d H:i:s')],
            ['Updated At', $token->updated_at->format('Y-m-d H:i:s')],
         ]
      );

      if ($token->expires_at->isPast()) {
         $this->warn('⚠️ Токен истек! Требуется переавторизация.');
         return 1;
      }

      return 0;
   }
}

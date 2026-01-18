<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AmocrmOauthState;

class CleanupAmocrm extends Command
{
   protected $signature = 'amo:cleanup';
   protected $description = 'Очистить устаревшие AmoCRM OAuth states';

   public function handle()
   {
      $deleted = AmocrmOauthState::cleanup();

      $this->info("✅ Удалено устаревших OAuth states: {$deleted}");

      return 0;
   }
}

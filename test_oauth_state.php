<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AmocrmOauthState;

// 1. Проверить что table существует
echo "=== ПРОВЕРКА ТАБЛИЦЫ ===\n";
try {
   $count = \DB::table('amocrm_oauth_states')->count();
   echo "✅ Таблица amocrm_oauth_states существует\n";
   echo "   Records: $count\n";
} catch (\Exception $e) {
   echo "❌ Ошибка таблицы: " . $e->getMessage() . "\n";
}

// 2. Проверить Model
echo "\n=== ПРОВЕРКА MODEL ===\n";
try {
   $state = AmocrmOauthState::generateState('fastis02');
   echo "✅ State сгенерирован: $state\n";

   // Проверить что сохранилось в БД
   $record = AmocrmOauthState::where('state', $state)->first();
   if ($record) {
      echo "✅ Record сохранен в БД\n";
      echo "   state: {$record->state}\n";
      echo "   subdomain: {$record->subdomain}\n";
      echo "   created_at: {$record->created_at}\n";
      echo "   expires_at: {$record->expires_at}\n";
   } else {
      echo "❌ Record НЕ сохранен в БД!\n";
   }
} catch (\Exception $e) {
   echo "❌ Ошибка Model: " . $e->getMessage() . "\n";
   echo $e->getTraceAsString() . "\n";
}

// 3. Проверить verifyAndDelete
echo "\n=== ПРОВЕРКА VERIFY AND DELETE ===\n";
try {
   $testState = 'test_state_' . time();
   $record = AmocrmOauthState::create([
      'state' => $testState,
      'subdomain' => 'test',
      'created_at' => now(),
      'expires_at' => now()->addMinutes(15)
   ]);
   echo "✅ Test state создан\n";

   $verified = AmocrmOauthState::verifyAndDelete($testState);
   if ($verified) {
      echo "✅ State проверен и удален\n";
   } else {
      echo "❌ State НЕ проверен\n";
   }

   // Проверить что удален
   $stillExists = AmocrmOauthState::where('state', $testState)->exists();
   if (!$stillExists) {
      echo "✅ State успешно удален из БД\n";
   } else {
      echo "❌ State ВСЕ ЕЩЕ в БД!\n";
   }
} catch (\Exception $e) {
   echo "❌ Ошибка verify: " . $e->getMessage() . "\n";
}

// 4. Посмотреть все states
echo "\n=== ВСЕ STATES В БД ===\n";
$states = AmocrmOauthState::all();
echo "Всего: " . count($states) . "\n";
foreach ($states as $s) {
   echo "- {$s->state} (expires: {$s->expires_at})\n";
}

echo "\n✅ ТЕСТИРОВАНИЕ ЗАВЕРШЕНО\n";

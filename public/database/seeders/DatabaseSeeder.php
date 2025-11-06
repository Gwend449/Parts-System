<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\{Brand, CarModel, Engine};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $brand = Brand::create(['name' => 'BMW', 'slug' => 'bmw']);
        $model = $brand->models()->create(['name' => 'X5', 'slug' => 'x5']);
        $model->engines()->create([
            'name' => '3.0 Diesel',
            'code' => 'B57D30',
            'volume' => '3.0',
            'power' => '286hp',
            'price' => 12000,
        ]);
    }
}

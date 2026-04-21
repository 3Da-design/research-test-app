<?php

namespace Database\Seeders;

use App\Models\Greeting;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ResearchSampleSeeder extends Seeder
{
    public function run(): void
    {
        if (! Greeting::query()->exists()) {
            Greeting::query()->create([
                'message' => 'BROKEN_INTENTIONALLY_FOR_CI_DEMO',
            ]);
        }

        if (Item::query()->exists()) {
            return;
        }

        $now = now();
        Item::query()->insert([
            ['name' => 'Laravel', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'React', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Comparison Sample', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}

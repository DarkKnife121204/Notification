<?php

namespace Database\Seeders;

use App\Models\Recipient;
use Illuminate\Database\Seeder;

class RecipientSeeder extends Seeder
{
    public function run(): void
    {
        Recipient::factory()->count(10)->create();
    }
}

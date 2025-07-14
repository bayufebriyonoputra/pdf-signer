<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\InvoiceCounter;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(UserTableSeeder::class);
        // $this->call(MasterStepSeeder::class);

        Setting::create([
            'name' => 'invoice_counter',
            'value' => '0'
        ]);

    }
}

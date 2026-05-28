<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::first();

        if (! $store) {
            $this->command->warn('No store found. Seed a Store first before running StaffSeeder.');
            return;
        }

        Staff::firstOrCreate(
            ['email' => 'dapur@gmail.com'],
            [
                'name' => 'Dapur',
                'password' => bcrypt('123456'),
                'store_id' => $store->id,
            ]
        );

        Staff::firstOrCreate(
            ['email' => 'bar@gmail.com'],
            [
                'name' => 'Bar',
                'password' => bcrypt('123456'),
                'store_id' => $store->id,
            ]
        );
    }
}

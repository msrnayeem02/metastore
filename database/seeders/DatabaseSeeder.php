<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PlanSeeder::class);
        $this->call(DefaultUsersSeeder::class);
        $this->call(DeliveryChargeSeeder::class);
        $this->call(CategoryAndSubCategorySeeder::class);
        $this->call(VariantAndVariantValueSeeder::class);
    }
}

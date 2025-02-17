<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ColorSeeder::class,
            CountrySeeder::class,
            GovernoratesSeeder::class,
            CitiesSeeder::class,
            CurrencySeeder::class,
            SizeSeeder::class,
            RoleSeeder::class,
            UsersWithRolesSeeder::class,
            CategoriesSeeder::class,
            ProductsSeeder::class,
        ]);
    }
}

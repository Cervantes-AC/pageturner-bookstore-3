<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        $categories = [
            ['name' => 'Fiction', 'description' => 'Fiction books'],
            ['name' => 'Mystery', 'description' => 'Mystery and thriller books'],
            ['name' => 'Science Fiction', 'description' => 'Science fiction books'],
            ['name' => 'Fantasy', 'description' => 'Fantasy books'],
            ['name' => 'Romance', 'description' => 'Romance books'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Created users and ' . count($categories) . ' categories.');
    }
}

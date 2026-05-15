<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding mock users, orders, and reviews...');

        $bookCount = Book::count();
        if ($bookCount < 50) {
            $this->command->warn('Only ' . $bookCount . ' books found. Run MassBookSeeder first for richer data.');
        }

        $this->seedUsers();
        $this->seedOrders();
        $this->seedReviews();

        $this->command->info('Mock data seeding complete!');
    }

    protected function seedUsers(): void
    {
        $names = [
            'Maria Santos', 'Juan Dela Cruz', 'Ana Gonzales', 'Pedro Reyes', 'Luisa Mercado',
            'Carlos Hernandez', 'Sofia Villanueva', 'Miguel Ramos', 'Teresa Lopez', 'Antonio Castillo',
            'Isabel Fernandez', 'Jose Garcia', 'Carmen Martinez', 'Ramon Torres', 'Elena Rivera',
            'Francisco Cruz', 'Rosa Aquino', 'Manuel Ortega', 'Clara Mendoza', 'Diego Alvarez',
            'Gabriela Silva', 'Andres Guerrero', 'Patricia Navarro', 'Ricardo Dominguez', 'Angela Morales',
            'Fernando Ortiz', 'Monica Rios', 'Alberto Castro', 'Leticia Reyes', 'Enrique Vargas',
            'Jessica Tan', 'Kevin Lim', 'Michelle Go', 'Patrick Sy', 'Christine Uy',
            'Brian Co', 'Angela Dy', 'Dennis Chua', 'Catherine Sia', 'Edward Ang',
        ];

        $created = 0;
        foreach ($names as $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@gmail.com';

            try {
                User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => bcrypt('password'),
                        'role' => fake()->randomElement(['customer', 'customer', 'customer', 'premium']),
                        'email_verified_at' => fake()->dateTimeBetween('-6 months', 'now'),
                        'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
                        'updated_at' => now(),
                    ]
                );
                $created++;
            } catch (\Exception $e) {
                continue;
            }
        }

        $this->command->info("Created {$created} mock users.");
    }

    protected function seedOrders(): void
    {
        $userIds = User::whereIn('role', ['customer', 'premium'])->pluck('id')->toArray();
        $bookIds = Book::where('is_active', true)->pluck('id')->toArray();
        $prices = Book::where('is_active', true)->pluck('price', 'id')->toArray();

        if (empty($userIds) || empty($bookIds)) {
            $this->command->warn('Not enough users or books for orders. Skipping.');
            return;
        }

        $statuses = ['pending', 'processing', 'completed', 'completed', 'completed', 'cancelled'];
        $totalOrders = 300;
        $bar = $this->command->getOutput()->createProgressBar($totalOrders);
        $bar->start();

        $inserted = 0;

        for ($i = 0; $i < $totalOrders; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $status = $statuses[array_rand($statuses)];
            $createdAt = fake()->dateTimeBetween('-6 months', 'now');
            $itemCount = rand(1, 5);
            $totalAmount = 0;

            $items = [];
            for ($j = 0; $j < $itemCount; $j++) {
                $bookId = $bookIds[array_rand($bookIds)];
                $qty = rand(1, 3);
                $price = $prices[$bookId] ?? fake()->randomFloat(2, 10, 50);
                $totalAmount += $price * $qty;
                $items[] = [
                    'book_id' => $bookId,
                    'quantity' => $qty,
                    'unit_price' => $price,
                ];
            }

            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => round($totalAmount, 2),
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create([
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            $inserted++;
            if ($inserted % 50 === 0) {
                $bar->advance(50);
            }
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Created {$inserted} orders with order items.");
    }

    protected function seedReviews(): void
    {
        $userIds = User::whereIn('role', ['customer', 'premium'])->pluck('id')->toArray();
        $bookIds = Book::where('is_active', true)->pluck('id')->toArray();

        if (empty($userIds) || empty($bookIds)) {
            $this->command->warn('Not enough users or books for reviews. Skipping.');
            return;
        }

        $reviewTexts = [
            1 => ['Terrible book, waste of money.', 'Very disappointing read.', 'Poorly written and boring.', 'Would not recommend to anyone.', 'One of the worst books I have read.'],
            2 => ['Not my cup of tea.', 'Below average, had potential.', 'Could have been better.', 'Some parts were okay.', 'Expected more from this author.'],
            3 => ['It was an okay read.', 'Average book, nothing special.', 'Some good parts, some bad.', 'Decent but not memorable.', 'Middle of the road read.'],
            4 => ['Really enjoyed this book!', 'Great read, highly recommend.', 'Well written and engaging.', 'One of the better books this year.', 'Thoroughly enjoyed it.'],
            5 => ['Absolutely amazing!', 'Masterpiece! A must-read.', 'Best book I have read in years.', 'Outstanding in every way.', 'Life-changing read. Highly recommended!'],
        ];

        $totalReviews = 150;
        $bar = $this->command->getOutput()->createProgressBar($totalReviews);
        $bar->start();

        $inserted = 0;
        $pairs = [];

        for ($i = 0; $i < $totalReviews; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $bookId = $bookIds[array_rand($bookIds)];
            $key = $userId . '-' . $bookId;

            if (isset($pairs[$key])) {
                continue;
            }
            $pairs[$key] = true;

            $rating = fake()->randomElement([5, 5, 4, 4, 4, 3, 3, 2, 1]);
            $comment = $reviewTexts[$rating][array_rand($reviewTexts[$rating])];
            $createdAt = fake()->dateTimeBetween('-6 months', 'now');

            DB::table('reviews')->insert([
                'user_id' => $userId,
                'book_id' => $bookId,
                'rating' => $rating,
                'comment' => $comment,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $inserted++;
        }

        $bar->advance($inserted);
        $bar->finish();
        $this->command->newLine();
        $this->command->info("Created {$inserted} reviews.");
    }
}

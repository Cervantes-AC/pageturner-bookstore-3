<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@pageturner.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'john@example.com'],
            ['name' => 'John Doe', 'password' => bcrypt('password'), 'role' => 'customer']
        );

        $categories = ['Fiction', 'Non-Fiction', 'Science', 'Technology', 'History', 'Fantasy', 'Biography', 'Self-Help'];
        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['name' => $name],
                ['description' => "Books in the {$name} category"]
            );
        }

        if (Book::count() === 0) {
            $sampleBooks = [
                ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'price' => 12.99, 'stock' => 25],
                ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'price' => 14.99, 'stock' => 30],
                ['title' => '1984', 'author' => 'George Orwell', 'price' => 11.99, 'stock' => 20],
                ['title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'price' => 9.99, 'stock' => 15],
                ['title' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'price' => 13.99, 'stock' => 18],
                ['title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'price' => 16.99, 'stock' => 22],
                ['title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'price' => 18.99, 'stock' => 35],
                ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'price' => 29.99, 'stock' => 10],
            ];

            foreach ($sampleBooks as $i => $book) {
                Book::create([
                    'category_id' => ($i % 8) + 1,
                    'title' => $book['title'],
                    'author' => $book['author'],
                    'isbn' => $this->generateISBN(),
                    'price' => $book['price'],
                    'stock_quantity' => $book['stock'],
                    'description' => "A great book titled '{$book['title']}' by {$book['author']}.",
                    'publication_year' => rand(1950, 2024),
                    'published_at' => now()->subYears(rand(1, 50))->format('Y-m-d'),
                    'format' => 'Paperback',
                    'cover_image' => 'https://picsum.photos/seed/' . Str::slug($book['title']) . '/400/600',
                ]);
            }
        }

        if (AuditLog::count() === 0) {
            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $admin->id,
                'event' => 'created',
                'auditable_type' => 'App\\Models\\Book',
                'auditable_id' => 1,
                'new_values' => ['title' => 'The Great Gatsby', 'price' => 12.99],
                'checksum' => hash('sha256', Str::uuid()->toString()),
                'ip_address' => '127.0.0.1',
                'url' => '/admin/books',
                'method' => 'POST',
            ]);
        }

        $this->call([
            CategorySeeder::class,
            MassBookSeeder::class,
        ]);
    }

    private function generateISBN()
    {
        return '978-' . rand(0, 9) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . rand(0, 9);
    }
}

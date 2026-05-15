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

        // Create exactly 5 categories
        $categories = [
            Category::create(['name' => 'Fiction', 'description' => 'Fiction books']),
            Category::create(['name' => 'Mystery', 'description' => 'Mystery and thriller books']),
            Category::create(['name' => 'Science Fiction', 'description' => 'Science fiction books']),
            Category::create(['name' => 'Fantasy', 'description' => 'Fantasy books']),
            Category::create(['name' => 'Romance', 'description' => 'Romance books']),
        ];

        // Real English books with cover URLs from Open Library
        $baseBooks = [
            // Fiction
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'category' => 0, 'price' => 12.99],
            ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'category' => 0, 'price' => 14.99],
            ['title' => '1984', 'author' => 'George Orwell', 'category' => 0, 'price' => 13.99],
            ['title' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'category' => 0, 'price' => 13.99],
            ['title' => 'Brave New World', 'author' => 'Aldous Huxley', 'category' => 0, 'price' => 14.99],
            ['title' => 'The Picture of Dorian Gray', 'author' => 'Oscar Wilde', 'category' => 0, 'price' => 10.99],
            ['title' => 'Moby Dick', 'author' => 'Herman Melville', 'category' => 0, 'price' => 14.99],
            ['title' => 'The Odyssey', 'author' => 'Homer', 'category' => 0, 'price' => 13.99],
            ['title' => 'The Iliad', 'author' => 'Homer', 'category' => 0, 'price' => 15.99],
            ['title' => 'Crime and Punishment', 'author' => 'Fyodor Dostoevsky', 'category' => 0, 'price' => 16.99],
            ['title' => 'War and Peace', 'author' => 'Leo Tolstoy', 'category' => 0, 'price' => 18.99],
            ['title' => 'Anna Karenina', 'author' => 'Leo Tolstoy', 'category' => 0, 'price' => 17.99],
            ['title' => 'The Count of Monte Cristo', 'author' => 'Alexandre Dumas', 'category' => 0, 'price' => 15.99],
            ['title' => 'Les Misérables', 'author' => 'Victor Hugo', 'category' => 0, 'price' => 16.99],
            ['title' => 'The Three Musketeers', 'author' => 'Alexandre Dumas', 'category' => 0, 'price' => 14.99],
            ['title' => 'The Hunchback of Notre-Dame', 'author' => 'Victor Hugo', 'category' => 0, 'price' => 15.99],
            ['title' => 'Frankenstein', 'author' => 'Mary Shelley', 'category' => 0, 'price' => 11.99],
            ['title' => 'Dracula', 'author' => 'Bram Stoker', 'category' => 0, 'price' => 12.99],
            ['title' => 'The Strange Case of Dr Jekyll and Mr Hyde', 'author' => 'Robert Louis Stevenson', 'category' => 0, 'price' => 10.99],
            ['title' => 'The Invisible Man', 'author' => 'H.G. Wells', 'category' => 0, 'price' => 11.99],
            
            // Mystery
            ['title' => 'The Murder of Roger Ackroyd', 'author' => 'Agatha Christie', 'category' => 1, 'price' => 12.99],
            ['title' => 'And Then There Were None', 'author' => 'Agatha Christie', 'category' => 1, 'price' => 13.99],
            ['title' => 'The Hound of the Baskervilles', 'author' => 'Arthur Conan Doyle', 'category' => 1, 'price' => 11.99],
            ['title' => 'A Study in Scarlet', 'author' => 'Arthur Conan Doyle', 'category' => 1, 'price' => 10.99],
            ['title' => 'The Girl with the Dragon Tattoo', 'author' => 'Stieg Larsson', 'category' => 1, 'price' => 16.99],
            ['title' => 'The Adventures of Sherlock Holmes', 'author' => 'Arthur Conan Doyle', 'category' => 1, 'price' => 12.99],
            ['title' => 'The Memoirs of Sherlock Holmes', 'author' => 'Arthur Conan Doyle', 'category' => 1, 'price' => 12.99],
            ['title' => 'The Return of Sherlock Holmes', 'author' => 'Arthur Conan Doyle', 'category' => 1, 'price' => 12.99],
            ['title' => 'The Valley of Fear', 'author' => 'Arthur Conan Doyle', 'category' => 1, 'price' => 11.99],
            ['title' => 'A Scandal in Bohemia', 'author' => 'Arthur Conan Doyle', 'category' => 1, 'price' => 10.99],
            
            // Science Fiction
            ['title' => 'Dune', 'author' => 'Frank Herbert', 'category' => 2, 'price' => 17.99],
            ['title' => 'Foundation', 'author' => 'Isaac Asimov', 'category' => 2, 'price' => 15.99],
            ['title' => '2001: A Space Odyssey', 'author' => 'Arthur C. Clarke', 'category' => 2, 'price' => 14.99],
            ['title' => 'Neuromancer', 'author' => 'William Gibson', 'category' => 2, 'price' => 13.99],
            ['title' => 'The Martian', 'author' => 'Andy Weir', 'category' => 2, 'price' => 15.99],
            ['title' => 'Twenty Thousand Leagues Under the Sea', 'author' => 'Jules Verne', 'category' => 2, 'price' => 13.99],
            ['title' => 'Journey to the Center of the Earth', 'author' => 'Jules Verne', 'category' => 2, 'price' => 12.99],
            ['title' => 'Around the World in Eighty Days', 'author' => 'Jules Verne', 'category' => 2, 'price' => 11.99],
            ['title' => 'The Time Machine', 'author' => 'H.G. Wells', 'category' => 2, 'price' => 10.99],
            ['title' => 'The Island of Doctor Moreau', 'author' => 'H.G. Wells', 'category' => 2, 'price' => 11.99],
            
            // Fantasy
            ['title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'category' => 3, 'price' => 15.99],
            ['title' => 'The Lord of the Rings', 'author' => 'J.R.R. Tolkien', 'category' => 3, 'price' => 24.99],
            ['title' => 'A Game of Thrones', 'author' => 'George R.R. Martin', 'category' => 3, 'price' => 18.99],
            ['title' => 'The Name of the Wind', 'author' => 'Patrick Rothfuss', 'category' => 3, 'price' => 17.99],
            ['title' => 'American Gods', 'author' => 'Neil Gaiman', 'category' => 3, 'price' => 16.99],
            ['title' => 'The Fifth Season', 'author' => 'N.K. Jemisin', 'category' => 3, 'price' => 16.99],
            ['title' => 'Mistborn', 'author' => 'Brandon Sanderson', 'category' => 3, 'price' => 15.99],
            ['title' => 'The Way of Kings', 'author' => 'Brandon Sanderson', 'category' => 3, 'price' => 19.99],
            ['title' => 'Elantris', 'author' => 'Brandon Sanderson', 'category' => 3, 'price' => 14.99],
            ['title' => 'Warbreaker', 'author' => 'Brandon Sanderson', 'category' => 3, 'price' => 15.99],
            
            // Romance
            ['title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'category' => 4, 'price' => 11.99],
            ['title' => 'Jane Eyre', 'author' => 'Charlotte Brontë', 'category' => 4, 'price' => 12.99],
            ['title' => 'Wuthering Heights', 'author' => 'Emily Brontë', 'category' => 4, 'price' => 12.99],
            ['title' => 'Sense and Sensibility', 'author' => 'Jane Austen', 'category' => 4, 'price' => 11.99],
            ['title' => 'Emma', 'author' => 'Jane Austen', 'category' => 4, 'price' => 12.99],
            ['title' => 'Persuasion', 'author' => 'Jane Austen', 'category' => 4, 'price' => 11.99],
            ['title' => 'Northanger Abbey', 'author' => 'Jane Austen', 'category' => 4, 'price' => 10.99],
            ['title' => 'The Tenant of Wildfell Hall', 'author' => 'Anne Brontë', 'category' => 4, 'price' => 11.99],
            ['title' => 'Mansfield Park', 'author' => 'Jane Austen', 'category' => 4, 'price' => 12.99],
            ['title' => 'Lady Chatterley\'s Lover', 'author' => 'D.H. Lawrence', 'category' => 4, 'price' => 13.99],
        ];

        $formats = ['Hardcover', 'Paperback', 'eBook', 'Audiobook'];
        $coverUrls = [
            'https://covers.openlibrary.org/b/id/7725406-M.jpg',
            'https://covers.openlibrary.org/b/id/8235688-M.jpg',
            'https://covers.openlibrary.org/b/id/7878060-M.jpg',
            'https://covers.openlibrary.org/b/id/8235689-M.jpg',
            'https://covers.openlibrary.org/b/id/8235690-M.jpg',
        ];

        // Create exactly 10,000 books
        $bookIndex = 0;
        for ($i = 0; $i < 10000; $i++) {
            $baseBook = $baseBooks[$i % count($baseBooks)];
            $bookIndex++;
            
            Book::create([
                'category_id' => $categories[$baseBook['category']]->id,
                'title' => $baseBook['title'] . ' #' . $bookIndex,
                'author' => $baseBook['author'],
                'publisher' => fake()->randomElement(['Penguin Books', 'Random House', 'Simon & Schuster', 'HarperCollins', 'Hachette', 'Macmillan', 'Oxford University Press', 'Cambridge University Press']),
                'publication_year' => fake()->year('-50 years'),
                'isbn' => '978-' . str_pad($bookIndex, 10, '0', STR_PAD_LEFT),
                'price' => $baseBook['price'] + fake()->randomFloat(2, -5, 10),
                'format' => $formats[array_rand($formats)],
                'published_at' => fake()->dateTimeBetween('-50 years', 'now'),
                'stock_quantity' => fake()->numberBetween(5, 100),
                'description' => fake()->paragraph(3),
                'cover_image' => $coverUrls[array_rand($coverUrls)],
                'is_active' => true,
            ]);
        }

        echo "✅ Created exactly 10,000 books successfully!\n";
    }

}

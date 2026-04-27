<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        User::query()->delete();
        Category::query()->delete();
        Book::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'Admin User',
            'email' => 'aaronclydeccervantes@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sample Customer
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'AC Cervantes',
            'email' => 'customer@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        $categories = [
            ['name' => 'Fiction', 'description' => 'Fictional stories and novels'],
            ['name' => 'Non-Fiction', 'description' => 'Real-world topics and biographies'],
            ['name' => 'Science Fiction', 'description' => 'Futuristic and speculative fiction'],
            ['name' => 'Fantasy', 'description' => 'Magical and mythical adventures'],
            ['name' => 'Mystery', 'description' => 'Detective and thriller stories'],
            ['name' => 'Romance', 'description' => 'Love stories and relationships'],
            ['name' => 'Business', 'description' => 'Business and entrepreneurship'],
            ['name' => 'Self-Help', 'description' => 'Personal development and motivation'],
            ['name' => 'Technology', 'description' => 'Tech and programming books'],
            ['name' => 'History', 'description' => 'Historical events and biographies'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        /*
        |--------------------------------------------------------------------------
        | 100 Books with Realistic Data
        |--------------------------------------------------------------------------
        */
        $books = [
            // Fiction (10 books)
            ['category_id' => 1, 'title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'price' => 299, 'stock' => 45, 'year' => 1960],
            ['category_id' => 1, 'title' => '1984', 'author' => 'George Orwell', 'price' => 349, 'stock' => 38, 'year' => 1949],
            ['category_id' => 1, 'title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'price' => 279, 'stock' => 52, 'year' => 1813],
            ['category_id' => 1, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'price' => 289, 'stock' => 41, 'year' => 1925],
            ['category_id' => 1, 'title' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'price' => 319, 'stock' => 33, 'year' => 1951],
            ['category_id' => 1, 'title' => 'Brave New World', 'author' => 'Aldous Huxley', 'price' => 329, 'stock' => 29, 'year' => 1932],
            ['category_id' => 1, 'title' => 'The Lord of the Flies', 'author' => 'William Golding', 'price' => 299, 'stock' => 36, 'year' => 1954],
            ['category_id' => 1, 'title' => 'Animal Farm', 'author' => 'George Orwell', 'price' => 259, 'stock' => 48, 'year' => 1945],
            ['category_id' => 1, 'title' => 'The Grapes of Wrath', 'author' => 'John Steinbeck', 'price' => 359, 'stock' => 27, 'year' => 1939],
            ['category_id' => 1, 'title' => 'Wuthering Heights', 'author' => 'Emily Brontë', 'price' => 309, 'stock' => 31, 'year' => 1847],

            // Non-Fiction (10 books)
            ['category_id' => 2, 'title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'price' => 599, 'stock' => 55, 'year' => 2011],
            ['category_id' => 2, 'title' => 'Educated', 'author' => 'Tara Westover', 'price' => 549, 'stock' => 42, 'year' => 2018],
            ['category_id' => 2, 'title' => 'Becoming', 'author' => 'Michelle Obama', 'price' => 649, 'stock' => 38, 'year' => 2018],
            ['category_id' => 2, 'title' => 'The Immortal Life of Henrietta Lacks', 'author' => 'Rebecca Skloot', 'price' => 499, 'stock' => 33, 'year' => 2010],
            ['category_id' => 2, 'title' => 'Thinking, Fast and Slow', 'author' => 'Daniel Kahneman', 'price' => 579, 'stock' => 29, 'year' => 2011],
            ['category_id' => 2, 'title' => 'The Wright Brothers', 'author' => 'David McCullough', 'price' => 529, 'stock' => 25, 'year' => 2015],
            ['category_id' => 2, 'title' => 'Born a Crime', 'author' => 'Trevor Noah', 'price' => 489, 'stock' => 44, 'year' => 2016],
            ['category_id' => 2, 'title' => 'The Glass Castle', 'author' => 'Jeannette Walls', 'price' => 459, 'stock' => 37, 'year' => 2005],
            ['category_id' => 2, 'title' => 'Into the Wild', 'author' => 'Jon Krakauer', 'price' => 469, 'stock' => 31, 'year' => 1996],
            ['category_id' => 2, 'title' => 'Unbroken', 'author' => 'Laura Hillenbrand', 'price' => 539, 'stock' => 28, 'year' => 2010],

            // Science Fiction (10 books)
            ['category_id' => 3, 'title' => 'Dune', 'author' => 'Frank Herbert', 'price' => 449, 'stock' => 50, 'year' => 1965],
            ['category_id' => 3, 'title' => 'Foundation', 'author' => 'Isaac Asimov', 'price' => 399, 'stock' => 43, 'year' => 1951],
            ['category_id' => 3, 'title' => 'Ender\'s Game', 'author' => 'Orson Scott Card', 'price' => 429, 'stock' => 39, 'year' => 1985],
            ['category_id' => 3, 'title' => 'The Hitchhiker\'s Guide to the Galaxy', 'author' => 'Douglas Adams', 'price' => 379, 'stock' => 47, 'year' => 1979],
            ['category_id' => 3, 'title' => 'Neuromancer', 'author' => 'William Gibson', 'price' => 419, 'stock' => 34, 'year' => 1984],
            ['category_id' => 3, 'title' => 'Snow Crash', 'author' => 'Neal Stephenson', 'price' => 439, 'stock' => 30, 'year' => 1992],
            ['category_id' => 3, 'title' => 'The Left Hand of Darkness', 'author' => 'Ursula K. Le Guin', 'price' => 389, 'stock' => 26, 'year' => 1969],
            ['category_id' => 3, 'title' => 'Hyperion', 'author' => 'Dan Simmons', 'price' => 459, 'stock' => 32, 'year' => 1989],
            ['category_id' => 3, 'title' => 'The Martian', 'author' => 'Andy Weir', 'price' => 469, 'stock' => 51, 'year' => 2011],
            ['category_id' => 3, 'title' => 'Ready Player One', 'author' => 'Ernest Cline', 'price' => 449, 'stock' => 45, 'year' => 2011],

            // Fantasy (10 books)
            ['category_id' => 4, 'title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'price' => 399, 'stock' => 60, 'year' => 1937],
            ['category_id' => 4, 'title' => 'Harry Potter and the Sorcerer\'s Stone', 'author' => 'J.K. Rowling', 'price' => 429, 'stock' => 75, 'year' => 1997],
            ['category_id' => 4, 'title' => 'The Name of the Wind', 'author' => 'Patrick Rothfuss', 'price' => 479, 'stock' => 42, 'year' => 2007],
            ['category_id' => 4, 'title' => 'A Game of Thrones', 'author' => 'George R.R. Martin', 'price' => 499, 'stock' => 53, 'year' => 1996],
            ['category_id' => 4, 'title' => 'The Way of Kings', 'author' => 'Brandon Sanderson', 'price' => 549, 'stock' => 38, 'year' => 2010],
            ['category_id' => 4, 'title' => 'The Chronicles of Narnia', 'author' => 'C.S. Lewis', 'price' => 459, 'stock' => 47, 'year' => 1950],
            ['category_id' => 4, 'title' => 'Mistborn', 'author' => 'Brandon Sanderson', 'price' => 489, 'stock' => 41, 'year' => 2006],
            ['category_id' => 4, 'title' => 'The Lies of Locke Lamora', 'author' => 'Scott Lynch', 'price' => 469, 'stock' => 35, 'year' => 2006],
            ['category_id' => 4, 'title' => 'American Gods', 'author' => 'Neil Gaiman', 'price' => 509, 'stock' => 39, 'year' => 2001],
            ['category_id' => 4, 'title' => 'The Wheel of Time', 'author' => 'Robert Jordan', 'price' => 529, 'stock' => 33, 'year' => 1990],

            // Mystery (10 books)
            ['category_id' => 5, 'title' => 'The Girl with the Dragon Tattoo', 'author' => 'Stieg Larsson', 'price' => 449, 'stock' => 48, 'year' => 2005],
            ['category_id' => 5, 'title' => 'Gone Girl', 'author' => 'Gillian Flynn', 'price' => 429, 'stock' => 52, 'year' => 2012],
            ['category_id' => 5, 'title' => 'The Da Vinci Code', 'author' => 'Dan Brown', 'price' => 439, 'stock' => 44, 'year' => 2003],
            ['category_id' => 5, 'title' => 'Big Little Lies', 'author' => 'Liane Moriarty', 'price' => 409, 'stock' => 37, 'year' => 2014],
            ['category_id' => 5, 'title' => 'The Silent Patient', 'author' => 'Alex Michaelides', 'price' => 459, 'stock' => 41, 'year' => 2019],
            ['category_id' => 5, 'title' => 'And Then There Were None', 'author' => 'Agatha Christie', 'price' => 349, 'stock' => 55, 'year' => 1939],
            ['category_id' => 5, 'title' => 'The Woman in the Window', 'author' => 'A.J. Finn', 'price' => 419, 'stock' => 33, 'year' => 2018],
            ['category_id' => 5, 'title' => 'Sharp Objects', 'author' => 'Gillian Flynn', 'price' => 399, 'stock' => 29, 'year' => 2006],
            ['category_id' => 5, 'title' => 'The Cuckoo\'s Calling', 'author' => 'Robert Galbraith', 'price' => 429, 'stock' => 36, 'year' => 2013],
            ['category_id' => 5, 'title' => 'In the Woods', 'author' => 'Tana French', 'price' => 439, 'stock' => 31, 'year' => 2007],

            // Romance (10 books)
            ['category_id' => 6, 'title' => 'The Notebook', 'author' => 'Nicholas Sparks', 'price' => 359, 'stock' => 58, 'year' => 1996],
            ['category_id' => 6, 'title' => 'Me Before You', 'author' => 'Jojo Moyes', 'price' => 389, 'stock' => 49, 'year' => 2012],
            ['category_id' => 6, 'title' => 'The Fault in Our Stars', 'author' => 'John Green', 'price' => 369, 'stock' => 62, 'year' => 2012],
            ['category_id' => 6, 'title' => 'Outlander', 'author' => 'Diana Gabaldon', 'price' => 449, 'stock' => 43, 'year' => 1991],
            ['category_id' => 6, 'title' => 'The Time Traveler\'s Wife', 'author' => 'Audrey Niffenegger', 'price' => 399, 'stock' => 38, 'year' => 2003],
            ['category_id' => 6, 'title' => 'Eleanor & Park', 'author' => 'Rainbow Rowell', 'price' => 349, 'stock' => 45, 'year' => 2013],
            ['category_id' => 6, 'title' => 'The Rosie Project', 'author' => 'Graeme Simsion', 'price' => 379, 'stock' => 34, 'year' => 2013],
            ['category_id' => 6, 'title' => 'Red, White & Royal Blue', 'author' => 'Casey McQuiston', 'price' => 419, 'stock' => 51, 'year' => 2019],
            ['category_id' => 6, 'title' => 'Beach Read', 'author' => 'Emily Henry', 'price' => 399, 'stock' => 47, 'year' => 2020],
            ['category_id' => 6, 'title' => 'People We Meet on Vacation', 'author' => 'Emily Henry', 'price' => 409, 'stock' => 42, 'year' => 2021],

            // Business (10 books)
            ['category_id' => 7, 'title' => 'Think and Grow Rich', 'author' => 'Napoleon Hill', 'price' => 449, 'stock' => 65, 'year' => 1937],
            ['category_id' => 7, 'title' => 'The Lean Startup', 'author' => 'Eric Ries', 'price' => 529, 'stock' => 48, 'year' => 2011],
            ['category_id' => 7, 'title' => 'Good to Great', 'author' => 'Jim Collins', 'price' => 559, 'stock' => 42, 'year' => 2001],
            ['category_id' => 7, 'title' => 'Zero to One', 'author' => 'Peter Thiel', 'price' => 499, 'stock' => 51, 'year' => 2014],
            ['category_id' => 7, 'title' => 'The 4-Hour Workweek', 'author' => 'Tim Ferriss', 'price' => 479, 'stock' => 44, 'year' => 2007],
            ['category_id' => 7, 'title' => 'Rich Dad Poor Dad', 'author' => 'Robert Kiyosaki', 'price' => 429, 'stock' => 72, 'year' => 1997],
            ['category_id' => 7, 'title' => 'The E-Myth Revisited', 'author' => 'Michael E. Gerber', 'price' => 469, 'stock' => 37, 'year' => 1995],
            ['category_id' => 7, 'title' => 'Start with Why', 'author' => 'Simon Sinek', 'price' => 509, 'stock' => 53, 'year' => 2009],
            ['category_id' => 7, 'title' => 'The Hard Thing About Hard Things', 'author' => 'Ben Horowitz', 'price' => 549, 'stock' => 39, 'year' => 2014],
            ['category_id' => 7, 'title' => 'Shoe Dog', 'author' => 'Phil Knight', 'price' => 519, 'stock' => 46, 'year' => 2016],

            // Self-Help (10 books)
            ['category_id' => 8, 'title' => 'Atomic Habits', 'author' => 'James Clear', 'price' => 549, 'stock' => 88, 'year' => 2018],
            ['category_id' => 8, 'title' => 'The 7 Habits of Highly Effective People', 'author' => 'Stephen Covey', 'price' => 499, 'stock' => 67, 'year' => 1989],
            ['category_id' => 8, 'title' => 'How to Win Friends and Influence People', 'author' => 'Dale Carnegie', 'price' => 429, 'stock' => 73, 'year' => 1936],
            ['category_id' => 8, 'title' => 'The Power of Now', 'author' => 'Eckhart Tolle', 'price' => 469, 'stock' => 54, 'year' => 1997],
            ['category_id' => 8, 'title' => 'You Are a Badass', 'author' => 'Jen Sincero', 'price' => 439, 'stock' => 61, 'year' => 2013],
            ['category_id' => 8, 'title' => 'The Subtle Art of Not Giving a F*ck', 'author' => 'Mark Manson', 'price' => 459, 'stock' => 79, 'year' => 2016],
            ['category_id' => 8, 'title' => 'Mindset', 'author' => 'Carol S. Dweck', 'price' => 489, 'stock' => 52, 'year' => 2006],
            ['category_id' => 8, 'title' => 'Daring Greatly', 'author' => 'Brené Brown', 'price' => 479, 'stock' => 48, 'year' => 2012],
            ['category_id' => 8, 'title' => 'The Alchemist', 'author' => 'Paulo Coelho', 'price' => 399, 'stock' => 85, 'year' => 1988],
            ['category_id' => 8, 'title' => 'Man\'s Search for Meaning', 'author' => 'Viktor E. Frankl', 'price' => 419, 'stock' => 58, 'year' => 1946],

            // Technology (10 books)
            ['category_id' => 9, 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'price' => 649, 'stock' => 45, 'year' => 2008],
            ['category_id' => 9, 'title' => 'The Pragmatic Programmer', 'author' => 'Andrew Hunt', 'price' => 679, 'stock' => 38, 'year' => 1999],
            ['category_id' => 9, 'title' => 'Design Patterns', 'author' => 'Gang of Four', 'price' => 699, 'stock' => 32, 'year' => 1994],
            ['category_id' => 9, 'title' => 'You Don\'t Know JS', 'author' => 'Kyle Simpson', 'price' => 529, 'stock' => 51, 'year' => 2014],
            ['category_id' => 9, 'title' => 'Eloquent JavaScript', 'author' => 'Marijn Haverbeke', 'price' => 549, 'stock' => 43, 'year' => 2011],
            ['category_id' => 9, 'title' => 'The Phoenix Project', 'author' => 'Gene Kim', 'price' => 589, 'stock' => 47, 'year' => 2013],
            ['category_id' => 9, 'title' => 'Cracking the Coding Interview', 'author' => 'Gayle Laakmann McDowell', 'price' => 729, 'stock' => 56, 'year' => 2015],
            ['category_id' => 9, 'title' => 'Introduction to Algorithms', 'author' => 'Thomas H. Cormen', 'price' => 899, 'stock' => 28, 'year' => 1990],
            ['category_id' => 9, 'title' => 'Code Complete', 'author' => 'Steve McConnell', 'price' => 749, 'stock' => 34, 'year' => 1993],
            ['category_id' => 9, 'title' => 'The Mythical Man-Month', 'author' => 'Frederick P. Brooks Jr.', 'price' => 569, 'stock' => 29, 'year' => 1975],

            // History (10 books)
            ['category_id' => 10, 'title' => 'A People\'s History of the United States', 'author' => 'Howard Zinn', 'price' => 579, 'stock' => 41, 'year' => 1980],
            ['category_id' => 10, 'title' => 'Guns, Germs, and Steel', 'author' => 'Jared Diamond', 'price' => 599, 'stock' => 38, 'year' => 1997],
            ['category_id' => 10, 'title' => 'The Diary of a Young Girl', 'author' => 'Anne Frank', 'price' => 349, 'stock' => 67, 'year' => 1947],
            ['category_id' => 10, 'title' => 'Team of Rivals', 'author' => 'Doris Kearns Goodwin', 'price' => 629, 'stock' => 33, 'year' => 2005],
            ['category_id' => 10, 'title' => 'The Silk Roads', 'author' => 'Peter Frankopan', 'price' => 649, 'stock' => 36, 'year' => 2015],
            ['category_id' => 10, 'title' => '1776', 'author' => 'David McCullough', 'price' => 559, 'stock' => 42, 'year' => 2005],
            ['category_id' => 10, 'title' => 'The Rise and Fall of the Third Reich', 'author' => 'William L. Shirer', 'price' => 799, 'stock' => 24, 'year' => 1960],
            ['category_id' => 10, 'title' => 'SPQR', 'author' => 'Mary Beard', 'price' => 619, 'stock' => 31, 'year' => 2015],
            ['category_id' => 10, 'title' => 'The Warmth of Other Suns', 'author' => 'Isabel Wilkerson', 'price' => 589, 'stock' => 28, 'year' => 2010],
            ['category_id' => 10, 'title' => 'The Guns of August', 'author' => 'Barbara W. Tuchman', 'price' => 569, 'stock' => 35, 'year' => 1962],
        ];

        foreach ($books as $book) {
            Book::create([
                'category_id' => $book['category_id'],
                'title' => $book['title'],
                'author' => $book['author'],
                'isbn' => $this->generateISBN(),
                'price' => $book['price'],
                'stock_quantity' => $book['stock'],
                'publication_year' => $book['year'],
                'description' => 'A compelling read that has captivated readers worldwide. ' . $book['title'] . ' by ' . $book['author'] . ' is a must-have for any book collection.',
                'is_featured' => false, // Will set featured books later
            ]);
        }

        // Mark some popular books as featured (12 books across different categories)
        $featuredTitles = [
            'Atomic Habits',
            'Sapiens',
            'Harry Potter and the Sorcerer\'s Stone',
            'The Hobbit',
            'Dune',
            '1984',
            'To Kill a Mockingbird',
            'The Great Gatsby',
            'Gone Girl',
            'The Notebook',
            'Clean Code',
            'Think and Grow Rich',
        ];

        Book::whereIn('title', $featuredTitles)->update(['is_featured' => true]);

        $this->command->info('✓ Seeded 100 books across 10 categories');

        /*
        |--------------------------------------------------------------------------
        | Additional Customers for Reviews
        |--------------------------------------------------------------------------
        */
        $customers = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah.j@example.com'],
            ['name' => 'Michael Chen', 'email' => 'michael.c@example.com'],
            ['name' => 'Emma Williams', 'email' => 'emma.w@example.com'],
            ['name' => 'David Martinez', 'email' => 'david.m@example.com'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa.a@example.com'],
            ['name' => 'James Taylor', 'email' => 'james.t@example.com'],
            ['name' => 'Maria Garcia', 'email' => 'maria.g@example.com'],
            ['name' => 'Robert Brown', 'email' => 'robert.b@example.com'],
            ['name' => 'Jennifer Lee', 'email' => 'jennifer.l@example.com'],
            ['name' => 'Christopher Davis', 'email' => 'chris.d@example.com'],
            ['name' => 'Amanda Wilson', 'email' => 'amanda.w@example.com'],
            ['name' => 'Daniel Rodriguez', 'email' => 'daniel.r@example.com'],
            ['name' => 'Jessica Moore', 'email' => 'jessica.m@example.com'],
            ['name' => 'Matthew Jackson', 'email' => 'matthew.j@example.com'],
            ['name' => 'Ashley White', 'email' => 'ashley.w@example.com'],
            ['name' => 'Joshua Harris', 'email' => 'joshua.h@example.com'],
            ['name' => 'Stephanie Martin', 'email' => 'stephanie.m@example.com'],
            ['name' => 'Andrew Thompson', 'email' => 'andrew.t@example.com'],
            ['name' => 'Nicole Clark', 'email' => 'nicole.c@example.com'],
            ['name' => 'Ryan Lewis', 'email' => 'ryan.l@example.com'],
        ];

        foreach ($customers as $customer) {
            User::create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Realistic Reviews
        |--------------------------------------------------------------------------
        */
        $reviewTemplates = [
            // 5-star reviews
            [
                'rating' => 5,
                'comments' => [
                    'Absolutely brilliant! One of the best books I\'ve ever read. The writing is captivating and the story stays with you long after you finish.',
                    'A masterpiece! I couldn\'t put it down. The characters are so well-developed and the plot is engaging from start to finish.',
                    'Outstanding work! This book exceeded all my expectations. Highly recommend to anyone looking for a great read.',
                    'Perfect in every way. The author\'s storytelling ability is exceptional. This is a must-read!',
                    'Five stars aren\'t enough! This book changed my perspective and I\'ve already recommended it to all my friends.',
                    'Phenomenal read! The depth of the narrative and character development is truly remarkable. I was completely immersed.',
                    'Incredible! This book has everything - great pacing, memorable characters, and a story that resonates deeply.',
                    'Best book I\'ve read this year! The author\'s prose is beautiful and the themes are thought-provoking.',
                    'Absolutely loved it! From the first page to the last, I was hooked. Can\'t wait to read more from this author.',
                    'A true gem! This book deserves all the praise it gets. Beautifully written and deeply moving.',
                    'Stunning work! The storytelling is masterful and the emotional impact is profound. Highly recommended!',
                    'Exceptional! Every chapter was a delight. The author has created something truly special here.',
                    'Brilliant from start to finish! The plot twists kept me guessing and the ending was perfect.',
                    'One of those rare books that stays with you forever. Absolutely magnificent!',
                    'Flawless execution! The writing, characters, and story all come together beautifully.',
                ]
            ],
            // 4-star reviews
            [
                'rating' => 4,
                'comments' => [
                    'Really enjoyed this book! Well-written with interesting characters. A few slow parts but overall a great read.',
                    'Solid read with compelling themes. The pacing was good and I found myself thinking about it days later.',
                    'Very good book! The author has a unique voice and the story is engaging. Would definitely recommend.',
                    'Great storytelling! Some parts could have been tighter, but the overall experience was very satisfying.',
                    'Impressive work. The narrative kept me hooked and the ending was particularly strong.',
                    'Thoroughly enjoyed this! The characters felt real and the plot was well-constructed. Minor pacing issues but still excellent.',
                    'Strong book with memorable moments. The writing style is engaging and the themes are relevant.',
                    'Very well done! A few predictable moments but the execution was solid throughout.',
                    'Great read! The author clearly knows their craft. Looking forward to more from them.',
                    'Highly entertaining! The story flows well and the characters are likeable. Would read again.',
                    'Excellent book! Lost half a star for some slow sections, but otherwise fantastic.',
                    'Really good! The plot kept me engaged and the writing was polished. Definitely worth your time.',
                    'Compelling story with strong character arcs. A few minor flaws but nothing that detracted from my enjoyment.',
                    'Well-crafted and engaging. The author has a talent for creating vivid scenes and authentic dialogue.',
                    'Impressive debut/work! Shows great promise and delivers on most fronts. Recommended!',
                ]
            ],
            // 3-star reviews
            [
                'rating' => 3,
                'comments' => [
                    'Decent read. It had its moments but didn\'t quite live up to the hype. Still worth reading if you\'re interested in the genre.',
                    'Good but not great. The concept was interesting but the execution could have been better. Average overall.',
                    'It was okay. Some parts were engaging while others dragged. Might appeal more to certain readers.',
                    'Mixed feelings about this one. Good writing but the story didn\'t fully capture my attention.',
                    'Not bad, but not exceptional either. Has some interesting ideas but falls short in places.',
                    'Middle of the road. The premise was promising but the delivery was inconsistent.',
                    'Readable but forgettable. Nothing particularly wrong with it, but nothing that stands out either.',
                    'Fair read. Some good moments balanced by some weaker sections. Your mileage may vary.',
                    'Okay for what it is. If you\'re a fan of the genre you might enjoy it more than I did.',
                    'Moderately entertaining. The story has potential but needed more development in key areas.',
                    'Average book. Not terrible but not memorable either. Fine for a casual read.',
                    'Somewhat enjoyable. The beginning was strong but it lost momentum halfway through.',
                    'Hit or miss. Some chapters were great, others felt like filler. Overall just okay.',
                    'Decent effort but could have been better. The ideas are there but the execution is lacking.',
                ]
            ],
            // 2-star reviews
            [
                'rating' => 2,
                'comments' => [
                    'Disappointing. Expected more based on the reviews. The plot was predictable and characters felt flat.',
                    'Struggled to finish this one. The pacing was off and I couldn\'t connect with the story.',
                    'Not for me. While the writing was decent, the story just didn\'t engage me at all.',
                    'Below average. Had potential but didn\'t deliver. Wouldn\'t recommend unless you\'re a die-hard fan.',
                    'Underwhelming. The concept sounded interesting but the execution fell flat.',
                    'Hard to get through. The characters were one-dimensional and the plot was predictable.',
                    'Not great. Too many clichés and the pacing dragged throughout.',
                    'Disappointing read. Expected much more based on the premise and reviews.',
                    'Struggled with this one. The writing style didn\'t work for me and the story felt forced.',
                    'Below expectations. Some good ideas but poorly executed overall.',
                ]
            ],
            // 1-star reviews (rare)
            [
                'rating' => 1,
                'comments' => [
                    'Really didn\'t enjoy this. The story was confusing and poorly structured. Had to force myself to finish.',
                    'Not what I expected at all. Couldn\'t get into it despite multiple attempts.',
                    'Very disappointing. The plot made no sense and the characters were completely unbelievable.',
                    'Couldn\'t finish it. The writing was poor and the story went nowhere.',
                    'Not recommended. This book has serious issues with pacing, character development, and plot coherence.',
                ]
            ],
        ];

        $allUsers = User::where('role', 'customer')->get();
        $allBooks = Book::all();

        // Add reviews to books (weighted towards higher ratings)
        foreach ($allBooks as $book) {
            // Random number of reviews per book (3-15 for more reviews)
            $reviewCount = rand(3, 15);
            
            $usedUsers = [];
            
            for ($i = 0; $i < $reviewCount; $i++) {
                // Weighted random rating (more 4-5 stars, fewer 1-2 stars)
                $rand = rand(1, 100);
                if ($rand <= 40) {
                    $ratingIndex = 0; // 5 stars (40%)
                } elseif ($rand <= 75) {
                    $ratingIndex = 1; // 4 stars (35%)
                } elseif ($rand <= 90) {
                    $ratingIndex = 2; // 3 stars (15%)
                } elseif ($rand <= 97) {
                    $ratingIndex = 3; // 2 stars (7%)
                } else {
                    $ratingIndex = 4; // 1 star (3%)
                }

                $template = $reviewTemplates[$ratingIndex];
                $comment = $template['comments'][array_rand($template['comments'])];

                // Get a user that hasn't reviewed this book yet
                $availableUsers = $allUsers->whereNotIn('id', $usedUsers);
                if ($availableUsers->isEmpty()) {
                    break; // No more unique users available
                }
                
                $user = $availableUsers->random();
                $usedUsers[] = $user->id;

                Review::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'rating' => $template['rating'],
                    'comment' => $comment,
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);
            }
        }

        $totalReviews = Review::count();
        $this->command->info('✓ Added ' . $totalReviews . ' realistic reviews to books');

        /*
        |--------------------------------------------------------------------------
        | Sample Orders
        |--------------------------------------------------------------------------
        */
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $addresses = [
            '123 Main Street, Manila, Metro Manila 1000, Philippines',
            '456 Rizal Avenue, Quezon City, Metro Manila 1100, Philippines',
            '789 EDSA Boulevard, Makati City, Metro Manila 1200, Philippines',
            '321 Bonifacio Drive, Taguig City, Metro Manila 1630, Philippines',
            '654 Roxas Boulevard, Pasay City, Metro Manila 1300, Philippines',
            '987 Commonwealth Avenue, Quezon City, Metro Manila 1121, Philippines',
            '147 Ortigas Avenue, Pasig City, Metro Manila 1600, Philippines',
            '258 Shaw Boulevard, Mandaluyong City, Metro Manila 1550, Philippines',
            '369 Katipunan Avenue, Quezon City, Metro Manila 1108, Philippines',
            '741 Marcos Highway, Marikina City, Metro Manila 1800, Philippines',
        ];

        $phoneNumbers = [
            '+63 917 123 4567',
            '+63 918 234 5678',
            '+63 919 345 6789',
            '+63 920 456 7890',
            '+63 921 567 8901',
            '+63 922 678 9012',
            '+63 923 789 0123',
            '+63 924 890 1234',
            '+63 925 901 2345',
            '+63 926 012 3456',
        ];

        // Create 30-50 orders
        $orderCount = rand(30, 50);
        
        for ($i = 0; $i < $orderCount; $i++) {
            $user = $allUsers->random();
            $orderDate = now()->subDays(rand(1, 120));
            
            // Determine status based on order age (older orders more likely to be completed)
            $daysAgo = $orderDate->diffInDays(now());
            if ($daysAgo > 60) {
                // Old orders: mostly completed or cancelled
                $status = rand(1, 100) <= 85 ? 'completed' : 'cancelled';
            } elseif ($daysAgo > 30) {
                // Medium age: mix of completed, processing, and some cancelled
                $rand = rand(1, 100);
                if ($rand <= 70) {
                    $status = 'completed';
                } elseif ($rand <= 85) {
                    $status = 'processing';
                } else {
                    $status = 'cancelled';
                }
            } elseif ($daysAgo > 7) {
                // Recent: mostly processing or completed
                $status = rand(1, 100) <= 60 ? 'completed' : 'processing';
            } else {
                // Very recent: pending or processing
                $status = rand(1, 100) <= 50 ? 'pending' : 'processing';
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0, // Will calculate after adding items
                'status' => $status,
                'shipping_name' => $user->name,
                'shipping_phone' => $phoneNumbers[array_rand($phoneNumbers)],
                'shipping_address' => $addresses[array_rand($addresses)],
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add 1-5 items to the order
            $itemCount = rand(1, 5);
            $totalAmount = 0;
            $orderedBooks = [];

            for ($j = 0; $j < $itemCount; $j++) {
                // Get a book that hasn't been added to this order yet
                $book = $allBooks->whereNotIn('id', $orderedBooks)->random();
                $orderedBooks[] = $book->id;

                $quantity = rand(1, 3);
                $unitPrice = $book->price;
                $subtotal = $quantity * $unitPrice;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);
        }

        $totalOrders = Order::count();
        $this->command->info('✓ Created ' . $totalOrders . ' sample orders with items');
        $this->command->info('✓ Admin: aaronclydeccervantes@gmail.com / password');
        $this->command->info('✓ Customer: customer@gmail.com / password');

        // Lab 6 data
        $this->call(Lab6Seeder::class);
    }

    private function generateISBN()
    {
        return '978-' . rand(0, 9) . '-' . 
               str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . 
               str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . 
               rand(0, 9);
    }
}

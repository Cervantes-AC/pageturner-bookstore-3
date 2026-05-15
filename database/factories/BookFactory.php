<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    private static ?array $cachedCategoryIds = null;
    private static int $isbnCounter = 0;

    private static array $publishers = [
        'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
        'Hachette Book Group', 'Macmillan Publishers', 'Oxford University Press',
        'Cambridge University Press', 'Bloomsbury Publishing', 'Scholastic',
        'Wiley & Sons', 'Pearson Education', 'McGraw-Hill',
        'Springer Nature', 'Taylor & Francis', 'Elsevier',
    ];

    private static array $formats = ['Paperback', 'Hardcover', 'eBook', 'Audiobook'];

    private static array $titlePrefixes = [
        'The', 'A', 'An', 'The Secret', 'The Last', 'The Lost', 'The Dark',
        'The Hidden', 'The Complete', 'The Essential', 'The Art of', 'The Power of',
    ];

    private static array $titleAdjectives = [
        'Silent', 'Eternal', 'Forgotten', 'Invisible', 'Broken', 'Golden',
        'Shattered', 'Ancient', 'Modern', 'Simple', 'Complex', 'Restless',
        'Wicked', 'Bright', 'Fading', 'Rising', 'Fallen', 'Distant',
    ];

    private static array $titleNouns = [
        'World', 'Kingdom', 'Shadow', 'Light', 'Storm', 'River', 'Mountain',
        'Ocean', 'Forest', 'Garden', 'Castle', 'Bridge', 'Path', 'Door',
        'Clock', 'Mirror', 'Crown', 'Sword', 'Heart', 'Soul', 'Dream',
        'Memory', 'Horizon', 'Journey', 'Legend', 'Empire', 'Secret',
    ];

    private static array $titleThemes = [
        'of Time', 'of War', 'of Peace', 'of Love', 'of Hope', 'of Darkness',
        'of Light', 'of Kings', 'of Thieves', 'of Lies', 'of Truth',
        'of the World', 'of the Heart', 'of the Mind', 'of the Dead',
        'in the Dark', 'in the Sky', 'in the Shadows', 'at Midnight',
        'at Dawn', 'from the Ashes', 'from the Past',
    ];

    private static array $authorFirstNames = [
        'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard',
        'Thomas', 'Mark', 'Steven', 'Paul', 'Andrew', 'George', 'Stephen',
        'Edward', 'Christopher', 'Sarah', 'Emily', 'Laura', 'Rachel',
        'Catherine', 'Elizabeth', 'Margaret', 'Alice', 'Helen', 'Rebecca',
        'Anna', 'Claire', 'Louise', 'Grace', 'Emma', 'Olivia', 'Sophia',
    ];

    private static array $authorLastNames = [
        'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis',
        'Wilson', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson',
        'Martin', 'Lee', 'Thompson', 'White', 'Harris', 'Clark', 'Lewis',
        'Walker', 'Hall', 'Allen', 'Young', 'King', 'Wright', 'Scott',
        'Adams', 'Baker', 'Hill', 'Green', 'Carter', 'Mitchell', 'Roberts',
    ];

    private function generateEnglishTitle(): string
    {
        $pattern = rand(0, 7);

        return match ($pattern) {
            0 => ($this->faker->randomElement(self::$titlePrefixes) . ' '
                . $this->faker->randomElement(self::$titleAdjectives) . ' '
                . $this->faker->randomElement(self::$titleNouns)),
            1 => ($this->faker->randomElement(self::$titleAdjectives) . ' '
                . $this->faker->randomElement(self::$titleNouns) . ' '
                . $this->faker->randomElement(self::$titleThemes)),
            2 => ($this->faker->randomElement(self::$titlePrefixes) . ' '
                . $this->faker->randomElement(self::$titleNouns) . ' '
                . $this->faker->randomElement(self::$titleThemes)),
            3 => ($this->faker->randomElement(self::$titlePrefixes) . ' '
                . $this->faker->randomElement(self::$titleNouns)),
            4 => ($this->faker->randomElement(self::$titleAdjectives) . ' '
                . $this->faker->randomElement(self::$titleNouns)),
            5 => ($this->faker->randomElement(self::$titleNouns) . ' '
                . $this->faker->randomElement(self::$titleThemes)),
            6 => ($this->faker->randomElement(self::$titlePrefixes) . ' '
                . $this->faker->randomElement(self::$titleAdjectives) . ' '
                . $this->faker->randomElement(self::$titleNouns) . ' '
                . $this->faker->randomElement(self::$titleThemes)),
            7 => ($this->faker->randomElement(self::$titleAdjectives) . ' '
                . $this->faker->randomElement(self::$titleNouns)),
        };
    }

    private function generateEnglishAuthor(): string
    {
        return $this->faker->randomElement(self::$authorFirstNames)
            . ' ' . $this->faker->randomElement(self::$authorLastNames);
    }

    public function definition(): array
    {
        if (self::$cachedCategoryIds === null) {
            self::$cachedCategoryIds = Category::pluck('id')->toArray();
        }

        $format = $this->faker->randomElement(self::$formats);

        $basePrice = match ($format) {
            'Paperback' => $this->faker->randomFloat(2, 7.99, 24.99),
            'Hardcover' => $this->faker->randomFloat(2, 16.99, 45.00),
            'eBook' => $this->faker->randomFloat(2, 3.99, 14.99),
            'Audiobook' => $this->faker->randomFloat(2, 12.99, 39.99),
        };

        return [
            'isbn' => $this->generateValidIsbn13(),
            'title' => $this->generateEnglishTitle(),
            'author' => $this->generateEnglishAuthor(),
            'publisher' => $this->faker->randomElement(self::$publishers),
            'price' => $basePrice,
            'format' => $format,
            'published_at' => $this->faker->dateTimeBetween('-100 years', 'now')->format('Y-m-d'),
            'publication_year' => $this->faker->numberBetween(1925, 2026),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'category_id' => $this->faker->randomElement(self::$cachedCategoryIds),
            'description' => $this->faker->sentence(rand(8, 15)),
            'cover_image' => 'https://picsum.photos/seed/' . Str::random(8) . '/400/600',
            'is_active' => $this->faker->boolean(85),
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function bestseller(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(500, 5000),
            'is_active' => true,
            'price' => $this->faker->randomFloat(2, 19.99, 49.99),
        ]);
    }

    private function generateValidIsbn13(): string
    {
        $num = str_pad((string)(self::$isbnCounter++), 12, '0', STR_PAD_LEFT);
        $num = substr($num, -12);
        $digits = substr($num, 0, 12);

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0 ? 1 : 3) * (int)$digits[$i];
        }

        $check = (10 - ($sum % 10)) % 10;

        return substr($digits, 0, 3) . '-' . $digits[3] . '-'
            . substr($digits, 4, 4) . '-' . substr($digits, 8, 4) . '-' . $check;
    }
}

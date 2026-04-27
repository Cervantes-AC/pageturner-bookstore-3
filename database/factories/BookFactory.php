<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Lab Activity 7 — Step 2
 * High-performance BookFactory designed for 1M+ record generation.
 *
 * Key design decisions:
 *  - Category IDs are loaded ONCE into a static property (avoids 1M DB queries)
 *  - Publisher pool is a static array (no DB lookup)
 *  - Format-based pricing uses a match expression for realistic distributions
 *  - Valid ISBN-13 generation with proper modulo-10 checksum
 *  - 85% of books are active (realistic catalog distribution)
 *  - published_at spans 1950–2025 for partition pruning demonstrations
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /** @var int[]|null Cached category IDs — loaded once per seeding run */
    private static ?array $categoryIds = null;

    /** Pre-defined publisher pool — avoids faker overhead for 1M records */
    private static array $publishers = [
        'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
        'Hachette Book Group', 'Macmillan Publishers', 'Scholastic',
        'Oxford University Press', 'Cambridge University Press',
        'Wiley', 'Springer', 'Elsevier', 'MIT Press',
        'O\'Reilly Media', 'Packt Publishing', 'Manning Publications',
    ];

    private static array $formats = ['ebook', 'paperback', 'hardcover', 'audiobook'];

    // ── Factory Definition ────────────────────────────────────────────────────

    public function definition(): array
    {
        // Load category IDs once — critical for performance at 1M records
        if (self::$categoryIds === null) {
            self::$categoryIds = Category::pluck('id')->toArray();
            if (empty(self::$categoryIds)) {
                self::$categoryIds = [1]; // fallback
            }
        }

        $format    = $this->faker->randomElement(self::$formats);
        $basePrice = $this->formatPrice($format);

        return [
            'isbn'             => $this->generateValidIsbn13(),
            'title'            => $this->faker->sentence(rand(2, 6)),
            'author'           => $this->faker->name(),
            'publisher'        => $this->faker->randomElement(self::$publishers),
            'format'           => $format,
            'price'            => $basePrice,
            'stock_quantity'   => $this->faker->numberBetween(0, 1000),
            'category_id'      => $this->faker->randomElement(self::$categoryIds),
            'description'      => $this->faker->paragraph(3),
            'published_at'     => $this->faker->dateTimeBetween('1950-01-01', '2025-12-31')->format('Y-m-d'),
            'publication_year' => null, // use published_at instead
            'is_active'        => $this->faker->boolean(85), // 85% active
            'is_featured'      => $this->faker->boolean(5),  // 5% featured
            'cover_image'      => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }

    // ── Factory States ────────────────────────────────────────────────────────

    /**
     * Bestseller state: always active, high stock, recent publication.
     */
    public function bestseller(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active'      => true,
            'is_featured'    => true,
            'stock_quantity' => $this->faker->numberBetween(500, 1000),
            'published_at'   => $this->faker->dateTimeBetween('2020-01-01', '2025-12-31')->format('Y-m-d'),
        ]);
    }

    /**
     * Out-of-stock state for testing inventory filters.
     */
    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock_quantity' => 0,
            'is_active'      => false,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Generate a valid ISBN-13 with proper 978 prefix and modulo-10 checksum.
     */
    private function generateValidIsbn13(): string
    {
        // 978 + 9 random digits
        $digits = [9, 7, 8];
        for ($i = 0; $i < 9; $i++) {
            $digits[] = rand(0, 9);
        }

        // Compute check digit (alternating weights 1 and 3)
        $sum = 0;
        foreach ($digits as $i => $d) {
            $sum += ($i % 2 === 0) ? $d : $d * 3;
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        $digits[]   = $checkDigit;

        // Format as 978-X-XXXX-XXXX-X
        return sprintf(
            '%d%d%d-%d-%d%d%d%d-%d%d%d%d-%d',
            ...$digits
        );
    }

    /**
     * Realistic price ranges per format.
     */
    private function formatPrice(string $format): float
    {
        return match ($format) {
            'ebook'     => round($this->faker->randomFloat(2, 2.99,  19.99), 2),
            'paperback' => round($this->faker->randomFloat(2, 9.99,  39.99), 2),
            'hardcover' => round($this->faker->randomFloat(2, 19.99, 79.99), 2),
            'audiobook' => round($this->faker->randomFloat(2, 14.99, 44.99), 2),
            default     => round($this->faker->randomFloat(2, 9.99,  29.99), 2),
        };
    }
}

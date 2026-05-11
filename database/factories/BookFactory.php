<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    private const COVER_BASE_URL = 'https://picsum.photos/seed';

    private static array $publishers = [
        'Penguin Random House',
        'HarperCollins',
        'Simon & Schuster',
        'Hachette Book Group',
        'Macmillan Publishers',
        'Oxford University Press',
        'Cambridge University Press',
        'Wiley & Sons',
        'Pearson Education',
        'Bloomsbury Publishing',
        'Scholastic',
        'Springer Nature',
        'Taylor & Francis',
        'McGraw-Hill',
        'Cengage Learning',
    ];

    private static ?array $cachedCategoryIds = null;
    private static int $isbnCounter = 0;

    private static function getCategoryIds(): array
    {
        if (self::$cachedCategoryIds === null) {
            self::$cachedCategoryIds = Category::pluck('id')->toArray();
        }
        return self::$cachedCategoryIds;
    }

    public function definition(): array
    {
        $format = $this->faker->randomElement(['hardcover', 'paperback', 'ebook', 'audiobook']);
        $basePrice = match ($format) {
            'hardcover' => $this->faker->randomFloat(2, 15.00, 49.99),
            'paperback' => $this->faker->randomFloat(2, 8.99, 29.99),
            'ebook' => $this->faker->randomFloat(2, 3.99, 19.99),
            'audiobook' => $this->faker->randomFloat(2, 12.99, 39.99),
            default => $this->faker->randomFloat(2, 8.99, 29.99),
        };

        $title = $this->faker->unique()->sentence(rand(2, 6));
        $seed = md5($title);

        return [
            'isbn' => $this->generateValidIsbn13(),
            'title' => $title,
            'author' => $this->faker->name(),
            'publisher' => $this->faker->randomElement(self::$publishers),
            'price' => $basePrice,
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'category_id' => $this->faker->randomElement(self::getCategoryIds()),
            'format' => $format,
            'is_active' => $this->faker->boolean(85),
            'description' => $this->faker->paragraphs(rand(2, 5), true),
            'published_at' => $this->faker->dateTimeBetween('-30 years', 'now')->format('Y-m-d'),
            'cover_image_url' => sprintf('%s/%s/400/600', self::COVER_BASE_URL, $seed),
            'is_featured' => $this->faker->boolean(5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function bestseller(): static
    {
        return $this->state(fn(array $attrs) => [
            'stock_quantity' => $this->faker->numberBetween(500, 2000),
            'is_active' => true,
            'price' => $this->faker->randomFloat(2, 25.00, 59.99),
        ]);
    }

    private function generateValidIsbn13(): string
    {
        self::$isbnCounter++;
        $counter = self::$isbnCounter % 1000000000;
        $prefix = '978';
        $body = str_pad((string) $counter, 9, '0', STR_PAD_LEFT);
        $digits = $prefix . $body;
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $check = (10 - ($sum % 10)) % 10;
        return sprintf('%s-%s-%s-%s-%d',
            $prefix,
            substr($body, 0, 1),
            substr($body, 1, 4),
            substr($body, 5, 4),
            $check
        );
    }
}

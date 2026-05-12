<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    private static ?array $cachedCategoryIds = null;

    private static array $publishers = [
        'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
        'Hachette Book Group', 'Macmillan Publishers', 'Oxford University Press',
        'Cambridge University Press', 'Bloomsbury Publishing', 'Scholastic',
        'Wiley & Sons', 'Pearson Education', 'McGraw-Hill',
        'Springer Nature', 'Taylor & Francis', 'Elsevier',
    ];

    private static array $formats = ['Paperback', 'Hardcover', 'eBook', 'Audiobook'];

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

        $title = $this->faker->unique()->sentence(rand(2, 6));

        return [
            'isbn' => $this->generateValidIsbn13(),
            'title' => $title,
            'author' => $this->faker->name(),
            'publisher' => $this->faker->randomElement(self::$publishers),
            'price' => $basePrice,
            'format' => $format,
            'published_at' => $this->faker->dateTimeBetween('-100 years', 'now')->format('Y-m-d'),
            'publication_year' => $this->faker->numberBetween(1925, 2026),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'category_id' => $this->faker->randomElement(self::$cachedCategoryIds),
            'description' => $this->faker->paragraphs(rand(2, 5), true),
            'cover_image' => 'https://picsum.photos/seed/' . Str::random(8) . '/400/600',
            'is_active' => $this->faker->boolean(85),
            'created_at' => now(),
            'updated_at' => now(),
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
        $prefix = '978';
        $group = str_pad((string)$this->faker->numberBetween(0, 9), 1, '0', STR_PAD_LEFT);
        $publisher = str_pad((string)$this->faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT);
        $title = str_pad((string)$this->faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT);

        $digits = $prefix . $group . $publisher . $title;

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0 ? 1 : 3) * (int)$digits[$i];
        }

        $check = (10 - ($sum % 10)) % 10;

        return "{$prefix}-{$group}-{$publisher}-{$title}-{$check}";
    }
}

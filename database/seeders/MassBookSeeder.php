<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MassBookSeeder extends Seeder
{
    private const CHUNK_SIZE = 4000;
    private const TOTAL_RECORDS = 1000000;

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

    private static ?array $categoryIds = null;

    public function run(): void
    {
        self::$categoryIds = Category::pluck('id')->toArray();

        if (empty(self::$categoryIds)) {
            $this->command->error('No categories found. Run CategorySeeder first.');
            return;
        }

        $this->command->info('Starting mass book seeding...');
        $this->command->info('Target: ' . number_format(self::TOTAL_RECORDS) . ' records');
        $this->command->info('Chunk size: ' . number_format(self::CHUNK_SIZE));
        $this->command->warn('Memory limit: ' . ini_get('memory_limit'));

        $inserted = 0;
        $startTime = microtime(true);
        $isbnCounter = 0;

        $bar = $this->command->getOutput()->createProgressBar(self::TOTAL_RECORDS);
        $bar->start();

        while ($inserted < self::TOTAL_RECORDS) {
            $batchSize = min(self::CHUNK_SIZE, self::TOTAL_RECORDS - $inserted);
            $books = [];

            for ($i = 0; $i < $batchSize; $i++) {
                $books[] = $this->generateBookRow($isbnCounter);
                $isbnCounter++;
            }

            DB::table('books')->insert($books);

            $inserted += $batchSize;
            $bar->advance($batchSize);

            if ($inserted % (self::CHUNK_SIZE * 10) === 0) {
                unset($books);
                gc_collect_cycles();
            }
        }

        $bar->finish();
        $this->command->newLine(2);

        $elapsed = round(microtime(true) - $startTime, 2);
        $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        $this->command->info("Seeded " . number_format($inserted) . " books in {$elapsed}s");
        $this->command->info("Peak memory usage: {$memory} MB");
    }

    private function generateBookRow(int &$isbnCounter): array
    {
        $format = self::$formats[array_rand(self::$formats)];

        $basePrice = match ($format) {
            'Paperback' => round(mt_rand(799, 2499) / 100, 2),
            'Hardcover' => round(mt_rand(1699, 4500) / 100, 2),
            'eBook' => round(mt_rand(399, 1499) / 100, 2),
            'Audiobook' => round(mt_rand(1299, 3999) / 100, 2),
        };

        $year = mt_rand(1925, 2026);
        $publishedAt = "{$year}-" . str_pad((string)mt_rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad((string)mt_rand(1, 28), 2, '0', STR_PAD_LEFT);

        return [
            'isbn' => $this->generateValidIsbn13($isbnCounter),
            'title' => $this->generateEnglishTitle(),
            'author' => $this->generateEnglishAuthor(),
            'publisher' => self::$publishers[array_rand(self::$publishers)],
            'price' => $basePrice,
            'format' => $format,
            'published_at' => $publishedAt,
            'publication_year' => $year,
            'stock_quantity' => mt_rand(0, 1000),
            'category_id' => self::$categoryIds[array_rand(self::$categoryIds)],
            'description' => '',
            'cover_image' => null,
            'is_active' => mt_rand(1, 100) <= 85 ? 1 : 0,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function generateValidIsbn13(int $counter): string
    {
        $num = str_pad((string)($counter % 1000000000000), 12, '0', STR_PAD_LEFT);
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

    private function generateEnglishTitle(): string
    {
        $pattern = mt_rand(0, 7);

        return match ($pattern) {
            0 => (self::$titlePrefixes[array_rand(self::$titlePrefixes)] . ' '
                . self::$titleAdjectives[array_rand(self::$titleAdjectives)] . ' '
                . self::$titleNouns[array_rand(self::$titleNouns)]),
            1 => (self::$titleAdjectives[array_rand(self::$titleAdjectives)] . ' '
                . self::$titleNouns[array_rand(self::$titleNouns)] . ' '
                . self::$titleThemes[array_rand(self::$titleThemes)]),
            2 => (self::$titlePrefixes[array_rand(self::$titlePrefixes)] . ' '
                . self::$titleNouns[array_rand(self::$titleNouns)] . ' '
                . self::$titleThemes[array_rand(self::$titleThemes)]),
            3 => (self::$titlePrefixes[array_rand(self::$titlePrefixes)] . ' '
                . self::$titleNouns[array_rand(self::$titleNouns)]),
            4 => (self::$titleAdjectives[array_rand(self::$titleAdjectives)] . ' '
                . self::$titleNouns[array_rand(self::$titleNouns)]),
            5 => (self::$titleNouns[array_rand(self::$titleNouns)] . ' '
                . self::$titleThemes[array_rand(self::$titleThemes)]),
            6 => (self::$titlePrefixes[array_rand(self::$titlePrefixes)] . ' '
                . self::$titleAdjectives[array_rand(self::$titleAdjectives)] . ' '
                . self::$titleNouns[array_rand(self::$titleNouns)] . ' '
                . self::$titleThemes[array_rand(self::$titleThemes)]),
            7 => (self::$titleAdjectives[array_rand(self::$titleAdjectives)] . ' '
                . self::$titleNouns[array_rand(self::$titleNouns)]),
        };
    }

    private function generateEnglishAuthor(): string
    {
        return self::$authorFirstNames[array_rand(self::$authorFirstNames)]
            . ' ' . self::$authorLastNames[array_rand(self::$authorLastNames)];
    }
}

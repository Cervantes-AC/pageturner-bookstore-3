<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();
        Category::truncate();
        Book::truncate();

        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('root123'),
            'role' => 'admin'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        Category::create(['name' => 'Action']);
        Category::create(['name' => 'Fantasy']);
        Category::create(['name' => 'Adventure']);

        /*
        |--------------------------------------------------------------------------
        | Bleach Series
        |--------------------------------------------------------------------------
        */
        Book::create([
            'category_id' => 1,
            'title' => 'Bleach Volume 1: Strawberry and the Soul Reapers',
            'author' => 'Tite Kubo',
            'isbn' => '9781591164418',
            'price' => 9.99,
            'stock_quantity' => 20,
            'publication_year' => 2004,
            'description' => 'Ichigo Kurosaki has always been able to see ghosts, but this ability doesn\'t change his life nearly as much as his close encounter with Rukia Kuchiki, a Soul Reaper.'
        ]);

        Book::create([
            'category_id' => 1,
            'title' => 'Bleach Volume 2: Goodbye Parakeet, Goodnite My Sista',
            'author' => 'Tite Kubo',
            'isbn' => '9781591164425',
            'price' => 9.99,
            'stock_quantity' => 18,
            'publication_year' => 2004,
            'description' => 'Ichigo continues his Soul Reaper duties while dealing with a dangerous Hollow that targets his family.'
        ]);

        Book::create([
            'category_id' => 1,
            'title' => 'Bleach Volume 3: Memories in the Rain',
            'author' => 'Tite Kubo',
            'isbn' => '9781591164906',
            'price' => 9.99,
            'stock_quantity' => 15,
            'publication_year' => 2004,
            'description' => 'Ichigo faces the Grand Fisher, the Hollow responsible for his mother\'s death.'
        ]);

        Book::create([
            'category_id' => 1,
            'title' => 'Bleach Volume 74: The Death and the Strawberry',
            'author' => 'Tite Kubo',
            'isbn' => '9781421590530',
            'price' => 9.99,
            'stock_quantity' => 12,
            'publication_year' => 2018,
            'description' => 'The final volume of Bleach! The epic conclusion to Ichigo\'s journey as a Soul Reaper.'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Naruto Series
        |--------------------------------------------------------------------------
        */
        Book::create([
            'category_id' => 1,
            'title' => 'Naruto Volume 1: Uzumaki Naruto',
            'author' => 'Masashi Kishimoto',
            'isbn' => '9781569319000',
            'price' => 9.99,
            'stock_quantity' => 25,
            'publication_year' => 2003,
            'description' => 'Naruto Uzumaki wants to be the best ninja in the land. He\'s done well so far, but with the looming danger posed by the mysterious Orochimaru, Naruto knows he must train harder than ever.'
        ]);

        Book::create([
            'category_id' => 1,
            'title' => 'Naruto Volume 2: The Worst Client',
            'author' => 'Masashi Kishimoto',
            'isbn' => '9781569319017',
            'price' => 9.99,
            'stock_quantity' => 22,
            'publication_year' => 2003,
            'description' => 'Team 7 takes on their first real mission: protecting a bridge builder from deadly ninja assassins.'
        ]);

        Book::create([
            'category_id' => 1,
            'title' => 'Naruto Volume 27: Departure',
            'author' => 'Masashi Kishimoto',
            'isbn' => '9781421518558',
            'price' => 9.99,
            'stock_quantity' => 10,
            'publication_year' => 2007,
            'description' => 'Naruto leaves the village to train with Jiraiya. The beginning of a new journey!'
        ]);

        Book::create([
            'category_id' => 1,
            'title' => 'Naruto Volume 72: Uzumaki Naruto!!',
            'author' => 'Masashi Kishimoto',
            'isbn' => '9781421584409',
            'price' => 9.99,
            'stock_quantity' => 8,
            'publication_year' => 2015,
            'description' => 'The final volume! The epic conclusion to Naruto\'s journey to become Hokage.'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Dragon Ball Series
        |--------------------------------------------------------------------------
        */
        Book::create([
            'category_id' => 2,
            'title' => 'Dragon Ball Volume 1',
            'author' => 'Akira Toriyama',
            'isbn' => '9781569319208',
            'price' => 9.99,
            'stock_quantity' => 30,
            'publication_year' => 2003,
            'description' => 'Before there was Dragon Ball Z, there was Akira Toriyama\'s action epic Dragon Ball! Meet a naive young monkey-tailed boy named Goku.'
        ]);

        Book::create([
            'category_id' => 2,
            'title' => 'Dragon Ball Volume 2: Wish Upon a Dragon',
            'author' => 'Akira Toriyama',
            'isbn' => '9781569319215',
            'price' => 9.99,
            'stock_quantity' => 28,
            'publication_year' => 2003,
            'description' => 'Goku and Bulma continue their quest for the Dragon Balls, facing the evil Emperor Pilaf.'
        ]);

        Book::create([
            'category_id' => 2,
            'title' => 'Dragon Ball Volume 16: Goku vs. Piccolo',
            'author' => 'Akira Toriyama',
            'isbn' => '9781569319901',
            'price' => 9.99,
            'stock_quantity' => 20,
            'publication_year' => 2004,
            'description' => 'The epic battle between Goku and Piccolo at the World Martial Arts Tournament!'
        ]);

        Book::create([
            'category_id' => 3,
            'title' => 'Dragon Ball Z Volume 1',
            'author' => 'Akira Toriyama',
            'isbn' => '9781569319307',
            'price' => 9.99,
            'stock_quantity' => 35,
            'publication_year' => 2003,
            'description' => 'Five years have passed since Goku and his friends defeated Piccolo. Raditz arrives on Earth!'
        ]);

        Book::create([
            'category_id' => 3,
            'title' => 'Dragon Ball Z Volume 26: Goodbye, Dragon World!',
            'author' => 'Akira Toriyama',
            'isbn' => '9781421506319',
            'price' => 9.99,
            'stock_quantity' => 15,
            'publication_year' => 2006,
            'description' => 'The final volume of Dragon Ball Z! The conclusion of the greatest martial arts manga of all time.'
        ]);

        Book::create([
            'category_id' => 2,
            'title' => 'Dragon Ball Super Volume 1',
            'author' => 'Akira Toriyama',
            'isbn' => '9781421592541',
            'price' => 9.99,
            'stock_quantity' => 40,
            'publication_year' => 2017,
            'description' => 'Ever since Goku became Earth\'s greatest hero, his life on Earth has grown a little dull. But new threats loom overhead!'
        ]);
    }
}

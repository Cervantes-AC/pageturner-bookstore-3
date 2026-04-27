<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class BooksRowCounter implements ToArray
{
    public function array(array $array): void {}
}

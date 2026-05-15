<?php

namespace App\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedBooksImport extends BooksImport implements ShouldQueue
{
}

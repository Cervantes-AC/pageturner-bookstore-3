<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedBooksExport extends BooksExport implements ShouldQueue
{
}

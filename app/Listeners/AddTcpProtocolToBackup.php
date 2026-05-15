<?php

namespace App\Listeners;

use Spatie\Backup\Events\DumpingDatabase;
use Spatie\DbDumper\Databases\MySql;

class AddTcpProtocolToBackup
{
    /**
     * Handle the event.
     */
    public function handle(DumpingDatabase $event): void
    {
        // Add TCP protocol option for Windows compatibility
        if ($event->dbDumper instanceof MySql) {
            // Ensure socket is empty to force TCP/IP connection
            $event->dbDumper->setSocket('');
            // Add TCP protocol option
            $event->dbDumper->addExtraOption('--protocol=TCP');
        }
    }
}

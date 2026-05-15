# Testing and Validation Evidence

Use these commands from the project root.

## Fast Validation Suite

```bash
php artisan test --filter=OperationalValidationTest
```

Expected result: 9 passing tests and 2 skipped large-volume tests.

This verifies:

- Import validation failures are reported for malformed files.
- Import chunk size and batch size are both 1,000 rows.
- Queue-capable import and export jobs complete successfully.
- Export uses a query-backed, chunked export.
- Manual backup creates a successful `backup_monitoring` entry and downloadable file path.
- Scheduled `backup:run` and `backup:clean` tasks are registered.
- Backup retention policy is configured.
- Audit entries are created, passwords are redacted, filtering works, and checksum tampering is detected.
- Tiered role limits map to `standard`, `premium`, and `admin`.
- Burst protection returns `429` with `X-RateLimit-Limit`, `X-RateLimit-Remaining`, and `Retry-After`.

## Large Import/Export Evidence

```bash
RUN_LARGE_VALIDATION_TESTS=1 php artisan test --filter="large_book"
```

On Windows PowerShell:

```powershell
$env:RUN_LARGE_VALIDATION_TESTS='1'; php artisan test --filter="large_book"
```

Latest local run:

- Imported 10,025 book records with validation in 39.84s.
- Exported 50,001 book records in 20.50s.
- Both tests passed.

## Backup and Scheduling Demo

Manual backup from browser:

```bash
php artisan backup:run --only-db
```

Scheduled task simulation:

```bash
php artisan schedule:run
php artisan schedule:list
```

Retention cleanup:

```bash
php artisan backup:clean
```

Restoration procedure:

1. Download the latest successful backup from Admin > Backup.
2. Extract the ZIP file.
3. Restore the SQL dump into the target database.
4. Run `php artisan migrate --force` if the restored database is older than the current code.
5. Confirm the app loads and `backup_monitoring.status` contains the successful backup record.

Failure notifications are configured in `config/backup.php` through Spatie backup mail notifications. Set `BACKUP_NOTIFICATION_EMAIL` in `.env` before demoing delivery.

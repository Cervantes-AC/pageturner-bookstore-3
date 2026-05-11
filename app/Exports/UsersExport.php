<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize
{
    protected bool $redactPii;

    public function __construct(bool $redactPii = false)
    {
        $this->redactPii = $redactPii;
    }

    public function query()
    {
        return User::withCount('orders');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Role', 'Email Verified', '2FA Enabled', 'Orders', 'Joined'];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $this->redactPii ? '[REDACTED]' : $user->name,
            $this->redactPii ? '[REDACTED]' : $user->email,
            ucfirst($user->role),
            $user->hasVerifiedEmail() ? 'Yes' : 'No',
            $user->two_factor_enabled ? 'Yes' : 'No',
            $user->orders_count,
            $user->created_at?->format('Y-m-d'),
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}

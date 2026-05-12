<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithChunkReading
{
    protected $redactPii;

    public function __construct(bool $redactPii = false)
    {
        $this->redactPii = $redactPii;
    }

    public function query()
    {
        return User::query()->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Email Verified At',
            'Created At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $this->redactPii ? '[REDACTED]' : $user->name,
            $this->redactPii ? '[REDACTED]' : $user->email,
            $user->role,
            $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'N/A',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}

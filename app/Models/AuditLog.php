<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'checksum',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    private const REDACTED = '[REDACTED]';

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function sanitizeValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $sensitive = ['password', 'password_confirmation', 'current_password', 'token', 'remember_token'];

        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $values[$key] = self::REDACTED;
                continue;
            }

            if (is_array($value)) {
                $values[$key] = self::sanitizeValues($value);
            }
        }

        return $values;
    }

    public static function checksumFor(
        string $id,
        ?int $userId,
        string $event,
        string $auditableType,
        int|string|null $auditableId,
        ?array $oldValues,
        ?array $newValues
    ): string {
        return hash('sha256', json_encode([
            'id' => $id,
            'user_id' => $userId,
            'event' => $event,
            'auditable_type' => $auditableType,
            'auditable_id' => (string) $auditableId,
            'old_values' => self::sanitizeValues($oldValues),
            'new_values' => self::sanitizeValues($newValues),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function hasValidChecksum(): bool
    {
        return hash_equals($this->checksum ?? '', self::checksumFor(
            $this->id,
            $this->user_id,
            $this->event,
            $this->auditable_type,
            $this->auditable_id,
            $this->old_values,
            $this->new_values
        ));
    }
}

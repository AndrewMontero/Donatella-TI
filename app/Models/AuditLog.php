<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $actor_id
 * @property string $action
 * @property string $subject_type
 * @property string $subject_id
 * @property array<string, mixed>|null $changes
 * @property string|null $ip_address
 */
class AuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'changes',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    public static function record(
        ?User $actor,
        string $action,
        Model $subject,
        array $changes = [],
        ?string $ipAddress = null,
    ): self {
        return self::create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => (string) $subject->getKey(),
            'changes' => $changes === [] ? null : $changes,
            'ip_address' => $ipAddress,
        ]);
    }
}

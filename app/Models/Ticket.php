<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $subject
 * @property string $category
 * @property string $description
 * @property TicketStatus $status
 * @property bool $requires_anydesk
 * @property string|null $anydesk_address
 */
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'subject',
        'category',
        'description',
        'status',
        'requires_anydesk',
        'anydesk_address',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'requires_anydesk' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<TicketComment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    /** @return HasMany<TicketAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }
}

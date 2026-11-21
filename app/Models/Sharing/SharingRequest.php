<?php

namespace App\Models\Sharing;

use App\Enums\SharingRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SharingRequest extends Model
{
    protected $table = 'circlo_sharing_requests';

    protected $fillable = [
        'item_id', 'borrower_id', 'owner_id', 'starts_on', 'ends_on', 'message',
        'status', 'daily_price', 'total_price', 'rejection_reason', 'decided_at', 'demo_key',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'immutable_date', 'ends_on' => 'immutable_date',
            'decided_at' => 'immutable_datetime', 'status' => SharingRequestStatus::class,
            'daily_price' => 'integer', 'total_price' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SharingItem::class, 'item_id');
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(DemoActor::class, 'borrower_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(DemoActor::class, 'owner_id');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(SharingTransaction::class, 'request_id');
    }

    public function days(): int
    {
        return (int) $this->starts_on->diffInDays($this->ends_on) + 1;
    }
}

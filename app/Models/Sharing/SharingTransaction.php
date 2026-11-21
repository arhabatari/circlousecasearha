<?php

namespace App\Models\Sharing;

use App\Enums\SharingTransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharingTransaction extends Model
{
    protected $table = 'circlo_sharing_transactions';

    protected $fillable = ['request_id', 'status', 'completed_at'];

    protected function casts(): array
    {
        return [
            'status' => SharingTransactionStatus::class,
            'completed_at' => 'immutable_datetime',
        ];
    }

    public function sharingRequest(): BelongsTo
    {
        return $this->belongsTo(SharingRequest::class, 'request_id');
    }
}

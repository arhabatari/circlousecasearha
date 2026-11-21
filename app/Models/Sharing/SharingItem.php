<?php

namespace App\Models\Sharing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SharingItem extends Model
{
    protected $table = 'circlo_sharing_items';

    protected $fillable = ['demo_item_id', 'owner_id', 'pickup_location', 'description'];

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'demo_item_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(DemoActor::class, 'owner_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(SharingRequest::class, 'item_id');
    }
}

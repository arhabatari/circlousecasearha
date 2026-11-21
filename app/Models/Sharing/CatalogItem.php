<?php

namespace App\Models\Sharing;

use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    protected $table = 'circlo_demo_items';

    protected $fillable = ['title', 'category', 'owner', 'daily_price', 'status'];

    protected function casts(): array
    {
        return ['daily_price' => 'integer'];
    }
}

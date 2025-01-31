<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Output extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'item' => 'array',
        'orig_item' => 'array',
        'crossref_item' => 'array',
        'warnings' => 'array',
        'notices' => 'array',
        'details' => 'array',
    ];

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(Conversion::class);
    }

    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class);
    }

    public function origItemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'orig_item_type_id', 'id');
    }
}

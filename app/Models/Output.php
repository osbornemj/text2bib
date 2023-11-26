<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Output extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'item' => 'array'
    ];

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(Conversion:: class);
    }

    public function rawOutput(): HasOne
    {
        return $this->hasOne(RawOutput:: class);
    }

    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType:: class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(OutputField:: class)->orderBy('item_field_id');
    }
}

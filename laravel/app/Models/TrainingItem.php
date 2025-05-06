<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingItem extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'item' => 'array',
    ];

    public function output(): BelongsTo
    {
        return $this->belongsTo(Output::class);
    }
}

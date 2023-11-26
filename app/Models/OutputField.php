<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutputField extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function itemField(): BelongsTo
    {
        return $this->belongsTo(ItemField:: class);
    }

}

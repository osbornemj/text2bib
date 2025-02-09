<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorReportField extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function itemField(): BelongsTo
    {
        return $this->belongsTo(ItemField::class);
    }
}

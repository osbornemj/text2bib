<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorReportComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function errorReport(): BelongsTo
    {
        return $this->belongsTo(ErrorReport:: class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User:: class);
    }

    public function requiredResponse(): HasOne
    {
        return $this->hasOne(RequiredResponse::class);
    }
}

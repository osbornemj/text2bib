<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorReportComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function errorReport(): HasOne
    {
        return $this->hasOne(ErrorReport:: class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User:: class);
    }

}

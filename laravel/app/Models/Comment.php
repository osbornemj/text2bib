<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread:: class);
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

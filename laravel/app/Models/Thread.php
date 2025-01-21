<?php

namespace App\Models;

use App\Enums\FeedbackThreadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['status' => FeedbackThreadStatus::class];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment:: class);
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

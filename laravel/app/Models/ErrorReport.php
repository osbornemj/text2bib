<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErrorReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['status' => ReportStatus::class];

    public function output(): BelongsTo
    {
        return $this->belongsTo(Output::class);
    }

    public function errorReportComments(): HasMany
    {
        return $this->hasMany(ErrorReportComment::class);
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status == ReportStatus::Open;
    }

    public function getIsWaitingAttribute(): bool
    {
        return $this->status == ReportStatus::Waiting;
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status == ReportStatus::Closed;
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

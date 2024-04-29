<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequiredResponse extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'comment_id', 'error_report_comment_id'];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function errorReportComment(): BelongsTo
    {
        return $this->belongsTo(ErrorReportComment::class);
    }
}

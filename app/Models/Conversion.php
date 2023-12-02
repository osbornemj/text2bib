<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversion extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'item_separator' => 'line',
        'first_component' => 'authors',
        'label_style' => 'short',
        'override_labels' => false,
        'char_encoding' => 'utf8',
        'percent_comment' => true,
        'include_source' => true,
        'report_type' => 'standard',
    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userFile(): belongsTo
    {
        return $this->belongsTo(UserFile::class);
    }
}

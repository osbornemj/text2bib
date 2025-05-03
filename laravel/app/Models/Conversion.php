<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Conversion extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'use' => '',
        'other_use' => '',
        'item_separator' => 'line',
        'language' => 'en',
        'label_style' => 'short',
        'override_labels' => false,
        'line_endings' => 'w',
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

    public function bst(): belongsTo
    {
        return $this->belongsTo(Bst::class);
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(Output::class);
    }

    public function firstOutput(): Output|null
    {
        return $this->hasMany(Output::class)->first();
    }

    public function firstLowercaseOutput(): Output|null
    {
        return $this->hasMany(Output::class)->whereRaw('BINARY source REGEXP "^[a-z]"')->first();
    }

    public function correctnessCounts(): Collection
    {
        return $this->hasMany(Output::class)->pluck('correctness')->countBy()->sortKeys();
    }

    public function adminCorrectnessCounts(): Collection
    {
        return $this->hasMany(Output::class)->pluck('admin_correctness')->countBy()->sortKeys();
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function (Conversion $conversion) {
            Storage::disk('public')->delete('files/'.$conversion->user_id.'-'.$conversion->user_file_id.'-source.txt');
        });
    }
}

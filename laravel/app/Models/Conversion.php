<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    // public function firstOutput(): Output|null
    // {
    //     return $this->hasMany(Output::class)->first();
    // }

    public function firstOutput(): HasOne
    {
        return $this->hasOne(Output::class)->orderBy('id');
    }

    public function firstLowercaseOutput(): HasOne
    {
        $vonNames = VonName::all()->toArray();

        $excludedPrefixes = array_merge(
            array_map(fn($vn) => $vn['name'] . ' ', $vonNames),
            ["d'"]
        );
        
        // Build regex that matches excluded prefixes
        $excludedRegex = implode('|', array_map(
            fn($prefix) => '^' . preg_quote($prefix, '/'),
            $excludedPrefixes
        ));

        return $this->hasOne(Output::class)
            ->whereRaw('CAST(source AS BINARY) REGEXP "^[a-z]"')
            ->whereRaw('CAST(source AS BINARY) NOT REGEXP ?', [$excludedRegex]);
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

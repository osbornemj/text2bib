<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrossrefBibtex extends Model
{
    use HasFactory;

    protected $table = 'crossref_bibtexs';

    protected $casts = [
        'item' => 'array',
    ];

    protected $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalWordAbbreviation extends Model
{
    use HasFactory;

    protected $fillable = ['word', 'output_id', 'checked', 'distinctive'];

    public function output()
    {
        return $this->belongsTo(Output::class);
    }
}

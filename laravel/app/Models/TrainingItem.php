<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingItem extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'item' => 'array',
    ];

}

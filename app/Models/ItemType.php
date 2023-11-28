<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'fields' => 'array'
    ];
}

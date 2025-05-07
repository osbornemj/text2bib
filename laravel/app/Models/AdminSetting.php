<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'training_items_conversion_started_at' => 'datetime',
            'training_items_conversion_ended_at' => 'datetime',
        ];
    }    
}

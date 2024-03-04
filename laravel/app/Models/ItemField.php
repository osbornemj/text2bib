<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemField extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name'];

    public function itemTypes()
    {
        return ItemType::whereJsonContains('fields', $this->name)->get();
    }
}

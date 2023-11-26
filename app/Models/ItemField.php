<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItemField extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name'];

    public function itemTypes(): BelongsToMany
    {
        return $this->belongsToMany(ItemType::class)->orderBy('name');
    }
}

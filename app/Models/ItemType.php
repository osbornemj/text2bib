<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItemType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name'];

    public function itemFields(): BelongsToMany
    {
        return $this->belongsToMany(ItemField::class)->orderBy('id');
    }
}

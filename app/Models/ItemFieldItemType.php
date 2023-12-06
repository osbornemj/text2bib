<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemFieldItemType extends Model
{
    public $timestamps = false;

    protected $table = 'item_field_item_type';

    protected $fillable = ['item_field_id', 'item_type_id'];
}

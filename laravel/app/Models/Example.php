<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

class Example extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function fields(): HasMany
    {
        return $this->hasMany(ExampleField:: class);
    }

    public function bibtexFields(): stdClass
    {
        $output = new \stdClass();
        foreach ($this->fields as $field) {
            $output->{$field->name} = $field->content; 
        }

        return $output;
    }
}

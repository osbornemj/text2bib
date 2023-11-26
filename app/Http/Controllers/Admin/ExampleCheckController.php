<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Example;

class ExampleCheckController extends Controller
{
    public function run()
    {
        $examples = Example::all();


    }
}

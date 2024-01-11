<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\UserSetting;

class IndexController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function convert()
    {
        $user = Auth::user();

        if ($user) {
            $settings = UserSetting::where('user_id', $user->id)->first();
            return view('convert')
                ->with('settings', $settings);
        } else {
            return view('welcome');
        }
    }

    public function versions()
    {
        return view('versions');
    }
}

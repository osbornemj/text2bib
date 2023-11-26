<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\UserSetting;

class IndexController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user) {
            $settings = UserSetting::where('user_id', $user->id)->first();
            return view('dashboard')
                ->with('settings', $settings);
        }

        return view('welcome');
    }
}

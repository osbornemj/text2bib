<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\UserSetting;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('welcome', compact('user'));
    }

    public function convertFile(): View
    {
        $user = Auth::user();

        if ($user) {
            $settings = UserSetting::where('user_id', $user->id)->first();
            return view('convert', compact('settings'));
        }

        return view('welcome');
    }

    public function about(): View
    {
        return view('about');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::withCount('conversions')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(50);

        return view('admin.users.index')->with('users', $users);
    }
}

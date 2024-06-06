<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::withCount('conversions');

        $searchString = request()->search_string;

        if ($searchString) {
            $users = $users->whereAny(['first_name',  'middle_name', 'last_name'], 'like', '%' . $searchString . '%');            
        }

        $users = $users
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(50);

        return view('admin.users.index', compact('users', 'searchString'));
    }

    public function destroy(int $id)
    {
        $user = User::find($id);
        $user->delete();

        return back();
    }
}

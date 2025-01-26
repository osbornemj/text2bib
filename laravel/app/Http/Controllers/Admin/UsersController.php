<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(string $sortBy = 'name'): View
    {
        $users = User::withCount('conversions');

        $searchString = request()->search_string;

        if ($searchString) {
            $users = $users->whereAny(['first_name',  'middle_name', 'last_name'], 'like', '%' . $searchString . '%');            
        }

        if ($sortBy == 'name') {
            $users = $users
                ->orderBy('last_name')
                ->orderBy('first_name');
        } elseif ($sortBy == 'conversionCount') {
            $users = $users->orderByDesc('conversions_count');
        } elseif ($sortBy == 'lastLogin') {
            $users = $users->orderByDesc('date_last_login');
        } elseif ($sortBy == 'source') {
            $users = $users->orderByDesc('source');
        } elseif ($sortBy == 'registered') {
            $users = $users->orderByDesc('created_at');
        }

        $users = $users->paginate(50);

        return view('admin.users.index', compact('users', 'searchString', 'sortBy'));
    }

    public function destroy($id): RedirectResponse
    {
        $user = User::find($id);
        
        if($user) {
            $user->delete();
        }

        return back();
    }
}

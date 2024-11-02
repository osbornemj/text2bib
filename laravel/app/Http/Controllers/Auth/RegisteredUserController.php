<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $sourceOptions = [
            'webSearch' => 'A web search for a term like "convert references to BibTeX"',
            'youtube' => 'A search on YouTube',
            'friend' => 'A friend/colleague told you about it',
            'chatGPT' => 'ChatGPT or another AI system suggested it',
            'otherSite' => 'Link on another website (enter URL in text box)',
            'other' => 'Other (enter in text box)',
        ];

        return view('auth.register', compact('sourceOptions'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (isset($request->empty)) {
            abort(404);
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => [],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'source' => ['required', 'string', 'in:webSearch,youtube,friend,otherSite,other'],
            'source_other_site' => ['required_if:source,otherSite', 'url', 'nullable'],
            'source_other' => ['required_if:source,other'],
        ], [
            'source' => 'Please indicate how you found this website',
            'source_other_site.required_if' => 'Please enter the URL of the site where you found a link to this site',
            'source_other_site.url' => 'Please enter a valid URL (starting with https://)',
            'source_other' => 'Please indicate how you discovered this site',
        ]);

        // No need to explicitly hash the password here, because the User model casts password as hashed
        $user = User::create($data);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

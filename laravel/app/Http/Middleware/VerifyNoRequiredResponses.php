<?php

namespace App\Http\Middleware;

use Closure;
//use Session;

use App\Models\User;

class VerifyNoRequiredResponses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user->requiredResponses->count()) {
            return redirect('/requiredResponses');
        }

        return $next($request);
    }
}

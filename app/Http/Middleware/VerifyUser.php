<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class VerifyUser
{
    //verify authenticated user
    public function handle($request, Closure $next)
    {
        $user   =   Session::get('user_id');
        if (is_null($user)) {
            return redirect('/user-authenticate');
        }

        return $next($request);
    }
}

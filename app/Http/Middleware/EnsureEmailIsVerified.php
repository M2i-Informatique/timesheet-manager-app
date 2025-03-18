<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (
            !$request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
                !$request->user()->hasVerifiedEmail())
        ) {
            if ($request->expectsJson()) {
                return abort(403, 'Votre adresse email n\'est pas vérifiée.');
            }

            return Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'))
                ->with('error', 'Veuillez vérifier votre adresse email avant d\'accéder à cette page.');
        }

        return $next($request);
    }
}

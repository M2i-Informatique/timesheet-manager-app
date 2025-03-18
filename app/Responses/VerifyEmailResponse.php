<?php

namespace App\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function toResponse($request)
    {
        return redirect()->route('home')->with('success', 'Votre adresse email a été vérifiée avec succès!');
    }
}
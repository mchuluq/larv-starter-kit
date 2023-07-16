<?php

namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;
use function response;

class WebAuthnRegisterController
{
    /**
     * Returns a challenge to be verified by the user device.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AttestationRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function options(AttestationRequest $request): Responsable
    {
        return $request
            ->fastRegistration()
            //->userless()
            //->allowDuplicates()
            ->toCreate();
    }

    /**
     * Registers a device for further WebAuthn authentication.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AttestedRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(AttestedRequest $request): Response{
        $request->save(function ($credentials) use ($request) {
            $credentials->alias = $request->input('alias');
            $credentials->user_agent = $request->server('HTTP_USER_AGENT');
        });
        $user = $request->user();
        Cookie::queue(config('auth.webauthn_remember_cookie'),encrypt($user->id),config('auth.webauthn_remember_expire'));
        return response()->noContent();
    }
}

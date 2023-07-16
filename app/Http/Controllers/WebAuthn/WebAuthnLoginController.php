<?php

namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use function response;

class WebAuthnLoginController
{
    /**
     * Returns the challenge to assertion.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AssertionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function options(AssertionRequest $request): Responsable
    {
        $credential = [];
        try {
            $credential['id'] = Crypt::decryptString($request->cookie(config('auth.webauthn_remember_cookie')));
        } catch (DecryptException $e) {
            $credential['name'] = $request->input('name');
        };
        Log::info(json_encode($credential));
        return $request->toVerify($credential);
    }

    /**
     * Log the user in.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AssertedRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(AssertedRequest $request): Response
    {
        $login = $request->login();
        $cookie = $request->cookie(config('auth.webauthn_remember_cookie'));
        if(!$login){
            Cookie::queue(Cookie::forget(config('auth.webauthn_remember_cookie')));
        }else{
            Cookie::queue(config('auth.webauthn_remember_cookie'),$cookie,config('auth.webauthn_remember_expire'));
        }        
        return response()->noContent($login ? 204 : 422);
    }
}

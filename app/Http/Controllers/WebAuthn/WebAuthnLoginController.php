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
    public function login(AssertedRequest $request){
        $login = $request->login();
        $req_token = $request->boolean('token');
        $cookie = $request->cookie(config('auth.webauthn_remember_cookie'));
        $data = [];
        if(!$login){
            Cookie::queue(Cookie::forget(config('auth.webauthn_remember_cookie')));
        }else{
            Cookie::queue(config('auth.webauthn_remember_cookie'),$cookie,config('auth.webauthn_remember_expire'));
            if($req_token == 1){
                $user = $request->user();
                $token = $user->createToken($request->server('HTTP_USER_AGENT'),['identity','user-setting','route-permission']);
                
                $data['token'] = $token->accessToken;
                $data['user'] = $user;
            }
        }
        return response()->json($data,$login ? 200 : 422);
    }
}

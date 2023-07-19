<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use PragmaRX\Google2FALaravel\Support\Authenticator;

class Otp{

    public function handle($request, Closure $next){
        if(!$request->user()->token()){
            $authenticator = app(Authenticator::class)->boot($request);
            if ($authenticator->isAuthenticated()) {
                return $next($request);
            }
            return $authenticator->makeRequestOneTimePasswordResponse();
        }else{
            $user = $request->user();
            $lifetime = config('google2fa.lifetime',3600);
            if(!$user){
                return response()->json(['message' => 'LOGIN_REQUIRED'], 403);
            }
            if($lifetime > 0 && $user->otp_status == true){
                $token = $user->token();
                $diff = now()->timestamp - $token->otp_checked_at;
                if($diff > $lifetime){
                    return response()->json(['message' => 'OTP_REQUIRED'], 403);
                }
            }
            return $next($request);
        }
    }
    
}

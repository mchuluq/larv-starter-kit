<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Concerns\ThrottleAttempts;
use App\Models\User;
use App\Rules\Otp;

class AuthController extends \App\Http\Controllers\Controller{

    use ThrottleAttempts;

    protected $maxAttempts = 10;
    protected $decayMinutes = 10;
    protected $throttleKeyName = 'name';

    public function login(Request $req){
        try {
            // check is too much attempt
            if($this->hasTooManyAttempts($req)){
                $this->sendLockoutResponse($req);
            }
            // validate input
            $req->validate([
                'name' => 'required|max:255',
                'otp' => 'nullable|numeric|digits:6',
                'password' => [Rule::requiredIf( fn() => !$req->has('otp')),'max:255']
            ]);
            if(!$req->has('otp')){
                if(Auth::attempt(['name' => $req->input('name'),'password' => $req->input('password')])){
                    $user = Auth::user();
                    $this->clearAttempts($req);
                }else{
                    throw new \Exception(__('auth.failed'));
                }
            }else{
                $google2fa = app('pragmarx.google2fa');
                $user = User::where('name','=',$req->input('name'))->firstOrFail();
                if($google2fa->verifyGoogle2FA($user->otp_secret,$req->input('otp'))){
                    Auth::login($user);
                    $this->clearAttempts($req);
                }else{
                    throw new \Exception(__('auth.failed'));
                };
            }
            $token = $user->createToken($req->server('HTTP_USER_AGENT'),['identity','user-setting','route-permission']);
            return response()->json([
                'message' => __('auth.success'),
                'data' => ['token' => $token->accessToken,'user' => $user]
            ]);
        }catch(\Illuminate\Validation\ValidationException $e){
            $this->incrementAttempts($req);
            return response()->json(['message' => $e->getMessage(),'errors' => $e->errors()],422);            
        }catch(\Exception $e){
            $this->incrementAttempts($req);
            return response()->json(['message' => $e->getMessage()],401);
        };
    }

    public function logout(Request $req){
        try {
            $user = $req->user();
            if(!$user){
                throw new \Exception(__('auth.something_wrong'));
            }
            $token = $user->token();
            if(!$token){
                throw new \Exception(__('auth.something_wrong'));
            }
            $token->delete();
            return response()->json(['message' => __('auth.logged_out')],200);            
        } catch(\Exception $e){
            $this->incrementAttempts($req);
            return response()->json(['message' => $e->getMessage()],401);  
        };
    }

    public function otp(Request $req){
        try {
            $user = $req->user();
            $req->validate(array(
                'otp' => ['required','numeric','digits:6', new Otp],
            ));

            $token = $user->token();
            $token->otp_checked_at = now()->timestamp;
            $token->save();
            
            return response()->json(['message'=>__('auth.otp_checked')]);
        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json(['message' => $e->getMessage(),'errors' => $e->errors()],422);            
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],401);
        };
        
    }
}

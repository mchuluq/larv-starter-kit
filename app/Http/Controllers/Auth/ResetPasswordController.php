<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected function credentials(Request $request){
        return $request->only('email','name', 'password', 'password_confirmation', 'token');
    }

    public function reset(Request $request){
        $user = \App\Models\User::whereBlind('email','email_index',$request->input('email'))->first();
        if(!$user){
            return back()->withInput($request->only('email'))->withErrors(['email' => __(Password::INVALID_USER)]);
        }
        
        $request->validate([
            'token' => 'required',
            'email' => ['required','email',function(string $attribute, mixed $value, \Closure $fail) use ($user){
                if(\App\Models\User::whereBlind('email','email_index',$value)->where('id','<>',$user->id)->count() > 0){
                    $fail("email tidak dapat digunakan");
                };
            }],
            'name' =>  ['required','string','max:255','regex:/^\S*$/u',Rule::unique(\App\Models\User::class)->ignore($user->id)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], $this->validationErrorMessages());
        
        $response = $this->broker()->reset([
            'id' => $user->id,
            'name' => $user->name,
            'token' => $request->input('token'),
            'password' => $request->input('password'),
        ], function ($user, $password) use ($request){
                $user->name = $request->input('name');
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET ? $this->sendResetResponse($request, $response) : $this->sendResetFailedResponse($request, $response);
    }

}

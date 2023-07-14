<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Otp implements ValidationRule{

    protected $user;
    protected $google2fa;

    public function __construct(){
        $this->user = Auth()->user();
        $this->google2fa = app('pragmarx.google2fa');
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void{
        if(!$this->user){
            $fail('authenticated user required');
        }elseif($this->user->otp_status){
            if(!$this->google2fa->verifyGoogle2FA($this->user->otp_secret,$value)){
                $fail('Invalid OTP code');
            }
        };
    }
}
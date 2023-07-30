<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;
use Laragear\WebAuthn\WebAuthnAuthentication;
use Laravel\Passport\HasApiTokens;

use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;

use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

class User extends Authenticatable implements CipherSweetEncrypted, MustVerifyEmail, WebAuthnAuthenticatable {
    
    use HasApiTokens, HasFactory, Notifiable, UsesCipherSweet, WebAuthnAuthentication;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo_url',
        'otp_secret',
        'user_config'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_secret',
        'user_config'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_secret' => 'encrypted',
        'user_config' => 'encrypted:array'
    ];

    protected $appends = ['otp_status'];

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public static function configureCipherSweet(EncryptedRow $encryptedRow): void{
        $encryptedRow->addField('email')->addBlindIndex('email',new BlindIndex('email_index'));
    }

    // automatic hash password string
    public function setPasswordAttribute($string=null){
        if(!empty($string)){
            $info = password_get_info($string);
            if($info['algo'] == 0){
                $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($string);
            }else{
                $this->attributes['password'] = $string;
            }
        }else{
            unset($this->attributes['password']);
        }
    }

    public function getOtpStatusAttribute(){
        return !!$this->otp_secret;
    }

    public function getPhotoUrlAttribute(){
        $attr = $this->attributes['photo_url'];
        if($attr){
            return url('storage/uploads/avatar/'.$attr);
        }
        return $this->get_gravatar($this->email);
    }

    private function get_gravatar($email, $s = 128, $d = 'mm', $r = 'g', $img = false, $atts = array()){
		$url 	= 'http://www.gravatar.com/avatar/';
		$url 	.= md5(strtolower(trim($email)));
		$url 	.= "?s=$s&d=$d&r=$r";
		if($img){
			$url = '<img src="' . $url . '"';
			foreach($atts as $key => $val){
				$url .= ' ' . $key . '="' . $val . '"';
				$url .= ' />';
			}
		}
		return $url;
	}

    public function findForPassport($username){
        return $this->where('name',$username)->first();
    }
}
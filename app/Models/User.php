<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;

use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

class User extends Authenticatable implements CipherSweetEncrypted, MustVerifyEmail{
    
    use HasApiTokens, HasFactory, Notifiable, UsesCipherSweet;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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

    public function getAuthIdentifierName(){
        return 'name';
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
}
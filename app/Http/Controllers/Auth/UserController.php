<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Rules\Otp;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Image;
use stdClass;

class UserController extends Controller{

    public function __construct(){
        $this->middleware(['auth','verified','otp']);
    }

    public function index(Request $req){
        $google2fa = app('pragmarx.google2fa');
        $user = $req->user();

        $otp = new stdClass();
        $otp->status = $user->otp_status;
        $otp->otp_secret = ($user->otp_status) ? null : $google2fa->generateSecretKey();
        $otp->qr_image = ($user->otp_status) ? null : "data:image/svg+xml;base64,".base64_encode($google2fa->getQRCodeInline(
            config('app.name'),$user->email,$otp->otp_secret
        ));

        $data['user'] = $user;
        $data['otp'] = $otp;

        return view('auth.user',$data);
    }

    public function update(Request $req){
        $user = $req->user();
        $validated = $req->validate([
            'email' => ['required','email',function(string $attribute, mixed $value, \Closure $fail) use ($user){
                if(\App\Models\User::whereBlind('email','email_index',$value)->where('id','<>',$user->id)->count() > 0){
                    $fail("email tidak dapat digunakan");
                };
            }],
            'photo_url' => ['nullable','image','mimes:jpg,bmp,png','max:1024'],
        ]);

        $user->email = $validated['email'];
        
        if($req->hasFile('photo_url')){
            $photo = $req->file('photo_url');
            $filename = sha1($user->id).'.'.$photo->getClientOriginalExtension();
            Image::make($photo)->resize(300,300,function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save(storage_path('app/public/uploads/avatar/'.$filename));
            $user->photo_url = $filename;
        }
        
        $user->save();
        return back()->with('update_status', 'user-updated');
    }

    public function password(Request $req){
        $user = $req->user();
        $validated = $req->validate([
            'name' =>  ['required','string','max:255','regex:/^\S*$/u',Rule::unique(\App\Models\User::class)->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Rules\Password::defaults(), 'confirmed'],
        ]);
        $user = $req->user();
        $user->name = $validated['name'];
        $user->password = $validated['password'];
        $user->save();

        return back()->with('password_status', 'password-updated');
    }

    public function otp(Request $req){
        $user = $req->user();
        if($req->isMethod('post')){
            $req->validate(array(
                'otp_secret' => 'required|string',
            ));
            $input = $req->only(['otp_secret']);
            $user->otp_secret = $input['otp_secret'];
        
            $user->save();

            return back()->with('register_otp_status', 'otp-registered');
        }elseif($req->isMethod('delete')){
            $req->validate(array(
                'otp_code' => ['required',new Otp],
                'password' => 'required|current_password',
            ));
            $input = $req->only(['otp_secret']);
            $user->otp_secret = null;
        
            $user->save();

            return back()->with('register_otp_status', 'otp-unregistered');
        }
    }
}

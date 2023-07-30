<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Crypter;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Rules\Otp;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

use Image;
use stdClass;

class UserController extends Controller{

    public function index(){
        return view('auth.user');
    }

    public function update(Request $req){
        $user = $req->user();
        try {
            if($req->isMethod('post')){
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
                return response()->json(['message'=>__('auth.user_updated'),'path'=>$user->photo_url]);
            }else{
                return response()->json(['user'=>$user]);
            }
        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json(['message' => $e->getMessage(),'errors' => $e->errors()],422);            
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],401);
        };
    }

    public function password(Request $req){
        $user = $req->user();
        try {
            if($req->isMethod('post')){
                $validated = $req->validate([
                    'name' =>  ['required','string','max:255','regex:/^\S*$/u',Rule::unique(\App\Models\User::class)->ignore($user->id)],
                    'current_password' => ['required', 'current_password'],
                    'password' => ['required', Rules\Password::defaults(), 'confirmed'],
                ]);
                $user = $req->user();
                $user->name = $validated['name'];
                $user->password = $validated['password'];
                $user->save();
    
                return response()->json(['message'=>__('auth.password_updated')]);
            }else{
                return response()->json(['user'=>$user]);
            }
        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json(['message' => $e->getMessage(),'errors' => $e->errors()],422);            
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],401);
        };
    }

    public function otp(Request $req){
        $google2fa = app('pragmarx.google2fa');
        $user = $req->user();
        try {
            if($req->isMethod('post')){
                $req->validate(array(
                    'otp_secret' => 'required|string',
                    'password' => 'required|current_password'
                ));
                $input = $req->only(['otp_secret']);
                $user->otp_secret = $input['otp_secret'];
                $user->save();

                return response()->json(['message'=>__('auth.otp_registered')]);
            }elseif($req->isMethod('delete')){
                $req->validate(array(
                    'otp_code' => ['required',new Otp],
                    'password' => 'required|current_password',
                ));
                $input = $req->only(['otp_secret']);
                $user->otp_secret = null;
                $user->save();

                return response()->json(['message'=>__('auth.otp_unregistered')]);
            }else{
                $otp = new stdClass();
                $otp->status = $user->otp_status;
                $otp->otp_secret_key = ($otp->status) ? null : $google2fa->generateSecretKey();
                $otp->qr_image = ($otp->status) ? null : "data:image/svg+xml;base64,".base64_encode($google2fa->getQRCodeInline(
                    config('app.name'),$user->email,$otp->otp_secret_key
                ));    
                $data['user'] = $user;
                $data['otp'] = $otp;

                return response()->json($data);
            }
        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json(['message' => $e->getMessage(),'errors' => $e->errors()],422);            
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],401);
        };
    }

    public function webauthn(Request $req,$id=null){
        $user = $req->user();
        if($req->isMethod('delete')){
            $user->webAuthnCredentials()->where('id','=',$id)->delete();
            return response()->json(['message'=>__('auth.credential_deleted')]);
        }else{
            $data['credentials'] = $user->webAuthnCredentials()->get()->map(function($row){
                $r = $row->toArray();
                $r['user_device'] = \App\Helpers\UserAgent::parse($row->user_agent);
                return $r;
            });
            return response()->json($data);
        }
    }

    public function tokens(Request $req,$id=null){
        try {
            if($req->isMethod('delete')){
                $user = $req->user();
                $req->validate([
                    'otp' => ['nullable','numeric','digits:6',new Otp],
                    'password' => [Rule::requiredIf( fn() => !$req->has('otp')),'max:255','current_password']
                ]);
                if($id){
                    $user->tokens()->where('id',$id)->delete();
                }else{
                    throw new \Exception('auth.something_wrong');
                }
                return response()->json(['message'=>__('auth.token_removed')]);
            }else{
                $data['tokens'] = $req->user()->tokens()->with('client')->get()->map(function($row){
                    $r = Arr::only($row->toArray(),['id','name','revoked','scope','client','created_at','updated_at','expires_at','user_device']);
                    $r['client'] = Arr::only($row->client->toArray(),['name','redirect','personal_access_client','password_client']);
                    $r['device'] = \App\Helpers\UserAgent::parse($row->name);
                    return $r;
                });
                return response()->json($data);
            }          
        } catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],401);  
        };
    }

    public function user(Request $req){
        $user = $req->user();
        $user->username = $user->name;
        $user->token = $req->bearerToken();
        return response()->json($user);
    }

}

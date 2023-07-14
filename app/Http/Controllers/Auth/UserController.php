<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Image;

class UserController extends Controller{

    public function __construct(){
        $this->middleware(['auth','verified']);
    }

    public function index(Request $req){
        $data['user'] = $req->user();
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
}

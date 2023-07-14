<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

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
            }]
        ]);

        $user = $req->user();
        $user->email = $validated['email'];
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

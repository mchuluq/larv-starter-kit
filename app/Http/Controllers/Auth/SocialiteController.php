<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Google_Client;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller{

    protected $available = ['google'];
    
    public function redirectToProvider($provider){
        if(!in_array($provider,$this->available)){
            abort(404);
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleProvideCallback($provider){
        try {
            if(!in_array($provider,$this->available)){
                throw new \Exception("not found");
            }
            $oauth_user = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->back()->with('message',$e->getMessage());
        }
        $user = \App\Models\User::whereBlind('email','email_index',$oauth_user->email)->first();
        if($user){
            $user_config = $user->user_config;
            $user_config["oauth_".$provider."_user"] = $oauth_user;
            $user->user_config = $user_config;
            $user->save();

            Auth()->login($user,true);
        
            return redirect()->route('home');
        }else{
            abort(404);
        }
    }

    public function googleOneTapLogin(Request $req){
        if ($_COOKIE['g_csrf_token'] !== $req->input('g_csrf_token')) {
            return back();
        }
        $idToken = $req->input('credential'); 
        $client = new Google_Client([
            'client_id' => env('GOOGLE_DRIVE_CLIENT_ID')
        ]);
        $payload = $client->verifyIdToken($idToken);
        if (!$payload) {
            return back();
        }
        
        $user = \App\Models\User::whereBlind('email','email_index',$payload['email'])->first();
        if($user){
            Auth()->login($user,true);
            return redirect()->route('home');
        }else{
            abort(404);
        }
    }

}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;//for google sign in and more
use App\Models\User;//user
use Illuminate\Support\Facades\Auth;//auth
use Exception;

class ProviderController extends Controller
{
    //redirect
    public function redirect($provider) {
        return Socialite::driver($provider)->redirect();
    }

    //callback
    //get user -> check user and provider -> otherwise create and call auth login and redirect to dashboard
    public function callback($provider) {
        // dd($user);

        //make sure that if they press cancel it still doesn't throw error
        try {
            $SocialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }

        //update or create a new user
        $user = User::updateOrCreate([
            'provider_id' => $SocialUser->id,
            'provider' => $provider
        ], [
            'name' => $SocialUser->name,
            // 'username' => $SocialUser->nickname,
            'username' => User::generateUsername($SocialUser->nickname),
            'email' => $SocialUser->email,
            'provider_token' => $SocialUser->token
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}

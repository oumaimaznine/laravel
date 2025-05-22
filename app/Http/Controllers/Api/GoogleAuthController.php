<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'password' => bcrypt(Str::random(16)),
            'email_verified_at' => now(),
           
        ]
    );
    

    $token = JWTAuth::fromUser($user);

   
    $safeUserData = json_encode([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role_id' => $user->role_id,
    ]);

    return redirect('http://localhost:3000/google/callback?token=' . $token . '&user=' . urlencode($safeUserData));
}

}

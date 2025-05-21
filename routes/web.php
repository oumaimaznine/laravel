<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

//  Accueil
Route::get('/', function () {
    return view('welcome');
});

//  Admin Panel avec Voyager
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

//  Vérification d’email avec JWT + Redirection vers React
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::find($id);

    if (! $user) {
        return response('Utilisateur introuvable', 404);
    }

    // Vérifier que le hash est valide
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response('Lien invalide ou expiré.', 403);
    }

    // Si non vérifié, le marquer
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        Event::dispatch(new Verified($user));
    }

    // Forcer l’utilisation du driver API
    auth()->setDefaultDriver('api');
    $token = JWTAuth::fromUser($user);

    // Rediriger vers le frontend avec le token
    return redirect("http://localhost:3000/email-verified?token=$token");
})->middleware('signed')->name('verification.verify');
use Illuminate\Support\Facades\Mail;

Route::get('/send-test', function () {
    Mail::raw('Ceci est un test via Brevo SMTP', function ($msg) {
        $msg->to('oumaimaznine1@gmail.com') 
            ->subject('Test réel depuis Laravel et Brevo');
    });

    return ' E-mail envoyé !';
});


//  Test pour récupérer un token d’accès PayPal
Route::get('/test-paypal', function () {
    $clientId = config('services.paypal.client_id');
    $secret = config('services.paypal.secret');
    $url = config('services.paypal.base_url') . '/v1/oauth2/token';

    $headers = [
        "Authorization: Basic " . base64_encode("$clientId:$secret"),
        "Accept: application/json",
        "Accept-Language: en_US"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return response()->json([
        'status' => $httpcode,
        'response' => json_decode($response, true)
    ]);
});

//  Debug configuration PayPal
Route::get('/debug-paypal', function () {
    return [
        'client_id' => config('services.paypal.client_id'),
        'secret' => config('services.paypal.secret'),
        'base_url' => config('services.paypal.base_url'),
    ];
});

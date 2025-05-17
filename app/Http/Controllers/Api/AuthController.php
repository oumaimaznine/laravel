<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailCode;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Fonction d'enregistrement
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Générer un code aléatoire
        $verificationCode = Str::random(6);

        // Créer utilisateur avec email non vérifié
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
            'email_verified_at' => null,
        ]);

        // Envoyer e-mail contenant le code
        Mail::to($user->email)->send(new VerifyEmailCode($user));

        return response()->json([
            'message' => 'Compte créé. Un code de vérification a été envoyé à votre adresse email.',
            'user' => $user,
        ], 201);
    }

    // Fonction login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Identifiants incorrects'], 401);
        }

        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            return response()->json(['error' => 'Votre email n\'est pas encore vérifié.'], 403);
        }

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    // Vérification du code reçu par email
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Utilisateur introuvable'], 404);
        }

        if ($user->verification_code !== $request->code) {
            return response()->json(['error' => 'Code invalide'], 401);
        }

        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();

        return response()->json(['message' => 'Email vérifié avec succès']);
    }

    public function user()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    public function refresh()
    {
        return response()->json(['token' => auth()->refresh()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès']);
    }
}

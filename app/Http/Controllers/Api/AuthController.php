<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // Enregistrement avec vérification par email
    public function register(Request $request)
    {
        //  Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Création du user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Envoi de l'email de vérification
        event(new Registered($user));

        return response()->json([
            'message' => 'Inscription réussie. Vérifiez votre email pour activer votre compte.',
            'user' => $user,
        ], 201);
    }

    //  Connexion uniquement si email vérifié
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Identifiants incorrects'], 401);
        }

        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            return response()->json(['error' => 'Votre email n\'est pas encore vérifié.'], 403);
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token,
        ]);
    }

    //  Récupérer l’utilisateur connecté
    public function user()
    {
        return response()->json(auth()->user());
    }

    // Déconnexion
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    // Rafraîchir le token
    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh()
        ]);
    }

    // Mise à jour du profil
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès']);
    }
}

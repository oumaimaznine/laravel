<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailCode;

class AuthController extends Controller
{
    // Fonction pour l'enregistrement d'un nouvel utilisateur
    public function register(Request $request)
    {
        // Validation des données envoyées
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Si la validation échoue, on retourne les erreurs
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Génération d'un code de vérification aléatoire
        $verificationCode = Str::random(6);

        // Création de l'utilisateur avec le code de vérification
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
        ]);

        // Envoi du code par email
        Mail::to($user->email)->send(new VerifyEmailCode($user));

        // Génération d'un token JWT pour l'utilisateur
        $token = JWTAuth::fromUser($user);

        // Retour d'une réponse JSON avec le token et l'utilisateur
        return response()->json([
            'message' => 'Compte créé. Vérifiez votre email pour le code.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Fonction de connexion avec vérification des identifiants
    public function login(Request $request)
    {
        // Récupération des identifiants
        $credentials = $request->only('email', 'password');

        // Vérification avec JWT
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Retourner l'utilisateur connecté et le token
        return response()->json([
            'user' => Auth::user(),
            'token' => $token
        ]);
    }

    // Récupérer l'utilisateur actuellement connecté
    public function user()
    {
        return response()->json(auth()->user());
    }

    // Déconnexion de l'utilisateur (invalidation du token)
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    // Rafraîchir le token JWT
    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh()
        ]);
    }

    // Mise à jour du profil de l'utilisateur
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validation des nouvelles données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Mise à jour des champs
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès']);
    }

    // Vérification du code envoyé par email
    public function verifyEmail(Request $request)
    {
        // Validation de l'email et du code
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        // Recherche de l'utilisateur
        $user = User::where('email', $request->email)->first();

        // Si aucun utilisateur trouvé
        if (!$user) {
            return response()->json(['error' => 'Utilisateur introuvable'], 404);
        }

        // Vérification du code
        if ($user->verification_code !== $request->code) {
            return response()->json(['error' => 'Code invalide'], 401);
        }

        // Marquer l'email comme vérifié
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();

        return response()->json(['message' => 'Email vérifié avec succès']);
    }
}

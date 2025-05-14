<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //  Fonction d'enregistrement (création de compte)
    public function register(Request $request)
    {
        // Valider les données envoyées par l'utilisateur
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Retourner les erreurs de validation si elles existent
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Créer un nouvel utilisateur dans la base de données
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hacher le mot de passe
        ]);

        // Générer un token JWT pour cet utilisateur
        $token = JWTAuth::fromUser($user);

        // Retourner l'utilisateur et le token comme réponse JSON
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    //  Fonction de connexion (login)
    public function login(Request $request)
    {
        // Récupérer uniquement l'email et le mot de passe
        $credentials = $request->only('email', 'password');

        // Vérifier les identifiants avec JWT
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401); // Identifiants incorrects
        }

        // Retourner l'utilisateur connecté et le token
        return response()->json([
            'user' => Auth::user(),
            'token' => $token
        ]);
    }

    //  Récupérer les informations de l'utilisateur connecté
    public function user()
    {
        return response()->json(auth()->user());
    }

    //  Déconnexion (logout)
    public function logout()
    {
        auth()->logout(); // Invalider le token

        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    //  Rafraîchir le token JWT
    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh()
        ]);
    }

    //  Mettre à jour le profil de l'utilisateur connecté
    public function update(Request $request)
    {
        $user = auth()->user(); // Récupérer l'utilisateur connecté

        // Valider les nouvelles données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Mettre à jour les informations
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Créer une nouvelle commande
    public function store(Request $request)
    {
        // Création d'une commande avec l'utilisateur connecté
        $order = Order::create([
            'user_id' => Auth::id(),            // ID de l'utilisateur connecté
            'total' => $request->total,         // Montant total de la commande
            'status' => 'en_attente'            // Statut par défaut
        ]);

        // Retourner un message de succès avec les détails de la commande
        return response()->json([
            'message' => 'Commande créée avec succès',
            'order' => $order
        ]);
    }

    // Lister toutes les commandes de l'utilisateur connecté
    public function index()
    {
        // Retourner toutes les commandes de l'utilisateur connecté
        return Order::where('user_id', Auth::id())->get();
    }

    // Voir les détails d'une commande spécifique
    public function show($id)
    {
        // Rechercher une commande par ID appartenant à l'utilisateur connecté
        return Order::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail(); // Retourne 404 si non trouvée
    }

    // Mettre à jour le statut d'une commande (réservé à l'admin)
    public function updateStatus($id, Request $request)
    {
        // Rechercher la commande
        $order = Order::findOrFail($id);

        // Modifier son statut
        $order->status = $request->status;
        $order->save();

        // Retourner un message de succès
        return response()->json([
            'message' => 'Statut mis à jour avec succès'
        ]);
    }
}

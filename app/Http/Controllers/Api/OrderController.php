<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Créer une nouvelle commande
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $request->total,
            'status' => 'en_attente',
            'shipping_address' => $request->shipping_address ?? 'non spécifiée',
            'payment_method' => $request->payment_method ?? 'non spécifiée',
            'payment_status' => 'en_attente',
            'transaction_id' => $request->transaction_id ?? null,
        ]);

        // Enregistrer les items
        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']['id'],
                'quantity' => $item['quantity'],
                'price' => $item['product']['price'],
            ]);
        }

        return response()->json([
            'message' => 'Commande créée avec succès',
            'order' => $order
        ]);
    }

    // Lister les commandes de l'utilisateur connecté
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $orders = Order::with('items.product') // Charger produits avec items
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // Voir les détails d'une commande spécifique
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $order = Order::with('items.product')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json($order);
    }

    //  Mettre à jour le statut (admin uniquement)
    public function updateStatus($id, Request $request)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Statut mis à jour avec succès'
        ]);
    }
}

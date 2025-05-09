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
        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $request->total,
            'status' => 'en_attente',
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method ?? 'paypal',
            'payment_status' => $request->payment_status ?? 'paid',
            'transaction_id' => $request->transaction_id ?? null,
        ]);

        // Enregistrer les produits de la commande
        foreach ($request->cart as $item) {
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

    //  Voir toutes les commandes de l'utilisateur connecté
    public function index()
    {
        return Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    //  Voir les détails d'une commande spécifique
    public function show($id)
    {
        return Order::with('items.product')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    // Modifier le statut d'une commande (admin)
    public function updateStatus($id, Request $request)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'order' => $order
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function handleStripeSuccess(Request $request)
    {
        $user = Auth::user()->load('address');
        $cartItems = $request->input('items');
        $total = $request->input('total');
        $transactionId = $request->input('transaction_id');

        if (!$cartItems || !$total || !$transactionId) {
            return response()->json(['error' => 'Données manquantes'], 400);
        }

        $address = $user->address;
        $fullAddress = $address ? implode(', ', array_filter([
            $address->address ?? '',
            $address->city ?? '',
            $address->postal_code ?? '',
            $address->country ?? ''
        ])) : 'Adresse inconnue';

        // ✅ Créer la commande
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'payment_method' => 'stripe',
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'shipping_address' => $fullAddress,
            'status' => 'en_attente',
        ]);

        // ✅ Enregistrer les articles
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']['id'],
                'quantity' => $item['quantity'],
                'price' => $item['product']['price'],
            ]);
        }

        // ✅ Enregistrer le paiement
        Payment::create([
            'order_id' => $order->id,
            'amount' => $total,
            'status' => 'completed',
            'method' => 'stripe',
        ]);

        return response()->json(['message' => '✅ Commande Stripe enregistrée avec succès']);
    }
}

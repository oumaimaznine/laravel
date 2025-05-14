<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    private function getPaypalAccessToken()
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');
        $url = config('services.paypal.base_url') . '/v1/oauth2/token';

        $response = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post($url, ['grant_type' => 'client_credentials']);

        if ($response->failed()) return null;

        $data = $response->json();
        return $data['access_token'] ?? null;
    }

    public function handlePaypalSuccess(Request $request)
    {
        try {
            $user = Auth::user()->load('address'); 
            $userId = $user->id;

            $cartItems = $request->input('items');
            $total = $request->input('total');
            $paypalTransactionId = $request->input('transaction_id');

            if (!$cartItems || !$total || !$paypalTransactionId) {
                return response()->json(['error' => 'Données manquantes'], 400);
            }

            $accessToken = $this->getPaypalAccessToken();
            if (!$accessToken) return response()->json(['error' => 'Token PayPal manquant'], 500);

            $url = config('services.paypal.base_url') . "/v2/checkout/orders/{$paypalTransactionId}";
            $paypalResponse = Http::withToken($accessToken)->get($url);

            $paypalData = $paypalResponse->json();
            if ($paypalResponse->failed() || !isset($paypalData['status']) || $paypalData['status'] !== 'COMPLETED') {
                return response()->json(['error' => 'Transaction PayPal non validée'], 400);
            }

            $address = $user->address;
            if ($address) {
                $parts = array_filter([
                    $address->address ?? '',
                    $address->city ?? '',
                    $address->postal_code ?? '',
                    $address->country ?? ''
                ]);
                $fullAddress = implode(', ', $parts);
            } else {
                $fullAddress = 'Adresse inconnue';
            }

            $order = Order::create([
                'user_id' => $userId,
                'total' => $total,
                'payment_method' => 'paypal',
                'payment_status' => 'paid',
                'transaction_id' => $paypalTransactionId,
                'shipping_address' => $fullAddress,
                'status' => 'en_attente',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product']['price'],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'status' => 'completed',
                'method' => 'paypal',
            ]);

            return response()->json(['message' => 'Commande PayPal enregistrée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur', 'details' => $e->getMessage()], 500);
        }
    }

    public function handleCashOnDelivery(Request $request)
    {
        try {
            $user = Auth::user()->load('address'); 
            $cartItems = $request->input('items');
            $total = $request->input('total');

            if (!$cartItems || !$total) {
                return response()->json(['error' => 'Données manquantes'], 400);
            }

            $address = $user->address;
            if ($address) {
                $parts = array_filter([
                    $address->address ?? '',
                    $address->city ?? '',
                    $address->postal_code ?? '',
                    $address->country ?? ''
                ]);
                $fullAddress = implode(', ', $parts);
            } else {
                $fullAddress = 'Adresse inconnue';
            }

            \Log::info('Adresse utilisée pour la commande:', [
                'user_id' => $user->id,
                'full_address' => $fullAddress
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'payment_method' => 'cod',
                'payment_status' => 'en_attente',
                'shipping_address' => $fullAddress,
                'status' => 'en_attente',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product']['price'],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'status' => 'pending',
                'method' => 'cod',
            ]);

            return response()->json(['message' => 'Commande Cash enregistrée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur', 'details' => $e->getMessage()], 500);
        }
    }
}

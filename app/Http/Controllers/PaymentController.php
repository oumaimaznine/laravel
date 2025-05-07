<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    
    private function getPaypalAccessToken()
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');
        $url = config('services.paypal.base_url') . '/v1/oauth2/token';

        \Log::info('PayPal Credentials:', [
            'client_id' => $clientId,
            'url' => $url,
        ]);

        $response = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post($url, ['grant_type' => 'client_credentials']);

        if ($response->failed()) {
            \Log::error('Erreur d\'authentification PayPal: ' . $response->body());
            return null;
        }

        $data = $response->json();

        if (!isset($data['access_token'])) {
            \Log::error(' Token manquant dans réponse PayPal: ' . json_encode($data));
            return null;
        }

        return $data['access_token'];
    }

    /**
     * Enregistrer la commande après succès PayPal
     */
    public function handlePaypalSuccess(Request $request)
    {
        try {
            \Log::info(' Données reçues:', $request->all());

            $user = Auth::user();
            $userId = $user ? $user->id : $request->input('user_id');

            if (!$userId) {
                \Log::error(" user_id manquant !");
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $cartItems = $request->input('cart');
            $total = $request->input('total');
            $paypalTransactionId = $request->input('transaction_id');

            if (!$cartItems || !is_array($cartItems) || count($cartItems) === 0) {
                \Log::error("Panier vide ou invalide");
                return response()->json(['error' => 'Le panier est vide ou invalide'], 400);
            }

            if (!$total || !$paypalTransactionId) {
                \Log::error(" Données manquantes: total ou transaction_id");
                return response()->json(['error' => 'Données manquantes'], 400);
            }

            // Token PayPal
            $accessToken = $this->getPaypalAccessToken();
            if (!$accessToken) {
                return response()->json(['error' => 'Token PayPal manquant'], 500);
            }

            //  Vérification transaction PayPal
            $url = config('services.paypal.base_url') . "/v2/checkout/orders/{$paypalTransactionId}";
            $paypalResponse = Http::withToken($accessToken)->get($url);

            if ($paypalResponse->failed()) {
                \Log::error('Erreur vérification PayPal: ' . $paypalResponse->body());
                return response()->json(['error' => 'Échec de vérification PayPal'], 400);
            }

            $paypalData = $paypalResponse->json();
            \Log::info("Transaction PayPal:", $paypalData);

            if (!isset($paypalData['status']) || $paypalData['status'] !== 'COMPLETED') {
                \Log::error("Transaction non validée par PayPal");
                return response()->json(['error' => 'Transaction PayPal non validée'], 400);
            }

            //  Enregistrement de la commande
            $order = Order::create([
                'user_id' => $userId,
                'total' => $total,
                'payment_method' => 'paypal',
                'payment_status' => 'paid',
                'transaction_id' => $paypalTransactionId,
                 'shipping_address' => 'Adresse inconnue'
            ]);

            \Log::info(" Commande créée ID: " . $order->id);

            foreach ($cartItems as $item) {
                if (!isset($item['product']['id'])) {
                    \Log::warning(" Produit invalide:", $item);
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product']['price'],
                ]);
            }

            return response()->json(['message' => 'Commande enregistrée avec succès']);

        } catch (\Exception $e) {
            \Log::error("Exception handlePaypalSuccess: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'Erreur serveur',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}

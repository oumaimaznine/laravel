<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Voir le panier
    public function index()
    {
        $userId = Auth::id();
    
        if (!$userId) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }
    
        $cart = Cart::firstOrCreate(['user_id' => $userId]);
    
        return response()->json(
            $cart->items()->with('product.images')->get()
        );
    }
    

    // Ajouter un produit au panier
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $item = $cart->items()->where('product_id', $request->product_id)->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Produit ajouté au panier avec succès']);
    }

    // Modifier la quantité d'un article
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail($id);

        $item->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Quantité mise à jour avec succès']);
    }

    // Supprimer un article du panier
    public function destroy($id)
    {
        $item = CartItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Produit supprimé du panier avec succès']);
    }
}

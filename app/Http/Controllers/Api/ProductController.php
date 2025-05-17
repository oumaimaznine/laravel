<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Product::with(['images', 'category']);

        if ($request->has('promo') && $request->promo == 1) {
            $query->where('is_promo', true);
        }

        $products = $query->get();
        return response()->json($products);
    }

    
    public function show($id)
    {
        $product = Product::with(['images', 'category'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        return response()->json($product);
    }

   
    public function productsByCategory($id, Request $request)
    {
        $query = Product::with(['images', 'category'])->where('category_id', $id);

        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('id', 'asc');
        }

        $products = $query->paginate(8);
        return response()->json($products);
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_promo' => 'nullable|boolean',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'is_promo' => 'nullable|boolean',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Produit supprimé avec succès']);
    }

   
    public function search(Request $request)
    {
        $query = strtolower($request->query('query'));

        $results = Product::with('images')
            ->whereRaw('LOWER(name) LIKE ?', ["%$query%"])
            ->orWhereRaw('LOWER(description) LIKE ?', ["%$query%"])
            ->get();

        return response()->json($results);
    }
}

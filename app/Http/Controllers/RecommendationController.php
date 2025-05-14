<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function getRecommendations($productId)
    {
        $product = Product::findOrFail($productId);

    
        $sameCategory = Product::with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $productId)
            ->limit(4)
            ->get();

        $relatedProductIds = DB::table('order_items')
            ->whereIn('order_id', function($query) use ($productId) {
                $query->select('order_id')
                      ->from('order_items')
                      ->where('product_id', $productId);
            })
            ->where('product_id', '!=', $productId)
            ->select('product_id')
            ->groupBy('product_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(4)
            ->pluck('product_id');

        $alsoBought = Product::with('images')
            ->whereIn('id', $relatedProductIds)
            ->get();

        return response()->json([
            'same_category' => $sameCategory,
            'also_bought' => $alsoBought,
        ]);
    }
}

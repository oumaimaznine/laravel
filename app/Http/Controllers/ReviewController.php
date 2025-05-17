<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /* Afficher uniquement les avis approuvés d’un produit donne */
    public function getProductReviews($productId)
    {
        $reviews = Review::with('user')
            ->where('product_id', $productId)
            ->where('status', 'approved') // les avis validés
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    /*Enregistrer un nouvel avis (status = pending par défaut)*/
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id'    => auth()->id(),
            'product_id' => $request->product_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'status'     => 'pending', // en attente
        ]);

        return response()->json([
            'message' => 'Avis ajouté avec succès. En attente de modération.',
            'review'  => $review
        ], 201);
    }

    /* Approuver un avis (admin) */
    public function approveReview($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->save();

        return response()->json([
            'message' => 'Avis approuvé avec succès ',
            'review'  => $review
        ]);
    }

    /* Rejeter un avis (admin)*/
    public function rejectReview($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'rejected';
        $review->save();

        return response()->json([
            'message' => 'Avis rejeté avec succès ',
            'review'  => $review
        ]);
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ReviewController;






// =================== ROUTES PUBLIQUES ===================
// Ces routes sont accessibles sans authentification

// Authentification via réseaux sociaux (Facebook / Google)
Route::get('/login/facebook', [SocialAuthController::class, 'redirectToFacebook']);
Route::get('/login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

// Affichage des avis pour un produit
Route::get('/products/{id}/reviews', [ReviewController::class, 'getProductReviews']);



// Affichage des produits et des catégories
Route::get('/products', [ProductController::class, 'index']); // Tous les produits
Route::get('/products/{id}', [ProductController::class, 'show']); // Détail d’un produit
Route::get('/category/{id}/products', [ProductController::class, 'productsByCategory']); // Produits par catégorie
Route::get('/categories', [CategoryController::class, 'index']); // Toutes les catégories
Route::get('/categories/{id}', [CategoryController::class, 'show']); // Détail d'une catégorie
Route::get('/search', [ProductController::class, 'search']); // Recherche de produits
Route::get('/recommendations/{productId}', [RecommendationController::class, 'getRecommendations']); // Recommandations de produits

// Authentification classique
Route::post('/register', [AuthController::class, 'register']); // Inscription
Route::post('/login', [AuthController::class, 'login']); // Connexion


// =================== ROUTES PROTÉGÉES ===================
// Ces routes nécessitent une authentification via token (JWT)

Route::middleware('auth:api')->group(function () {

    // Paiement
    Route::post('/payment/paypal/success', [PaymentController::class, 'handlePaypalSuccess']); // Paiement PayPal validé
    Route::post('/payment/stripe', [StripeController::class, 'createPaymentIntent']); // Création d’un paiement Stripe
    Route::post('/payment/stripe/success', [StripeController::class, 'handleStripeSuccess']); // Paiement Stripe validé
    Route::post('/payment/cod', [PaymentController::class, 'handleCashOnDelivery']); // Paiement à la livraison

    // Ajout d’un avis client
    Route::post('/reviews', [ReviewController::class, 'store']); // Ajouter un avis
    Route::put('/admin/reviews/{id}/approve', [ReviewController::class, 'approveReview']);
    Route::put('/admin/reviews/{id}/reject', [ReviewController::class, 'rejectReview']);


    // Gestion du profil utilisateur
    Route::get('/user', [AuthController::class, 'user']); // Informations de l’utilisateur connecté
    Route::put('/user', [AuthController::class, 'update']); // Mise à jour du profil
    Route::post('/logout', [AuthController::class, 'logout']); // Déconnexion

    // Gestion du panier (cart)
    Route::get('/cart', [CartController::class, 'index']); // Voir le panier
    Route::post('/cart/items', [CartController::class, 'store']); // Ajouter un produit au panier
    Route::put('/cart/items/{id}', [CartController::class, 'update']); // Modifier un produit du panier
    Route::delete('/cart/items/{id}', [CartController::class, 'destroy']); // Supprimer un produit du panier

    // Gestion des produits (admin uniquement)
    Route::post('/products', [ProductController::class, 'store']); // Ajouter un produit
    Route::put('/products/{id}', [ProductController::class, 'update']); // Modifier un produit
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Supprimer un produit

    // Gestion des adresses de livraison
    Route::post('/address', [AddressController::class, 'store']); // Ajouter une adresse
    Route::get('/address', [AddressController::class, 'getAddress']); // Voir l’adresse de l’utilisateur
    Route::put('/address', [AddressController::class, 'update']); // Modifier une adresse
    Route::delete('/address', [AddressController::class, 'destroy']); // Supprimer une adresse

    // Commandes
    Route::post('/orders', [OrderController::class, 'store']); // Passer une commande
    Route::get('/orders', [OrderController::class, 'index']); // Voir toutes les commandes de l’utilisateur
    Route::get('/orders/{id}', [OrderController::class, 'show']); // Voir les détails d’une commande
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']); // Modifier le statut d’une commande (admin)
});

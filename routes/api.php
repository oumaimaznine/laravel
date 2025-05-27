<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
use App\Http\Controllers\ContactController;

// =================== ROUTES PUBLIQUES ===================
Route::post('/contact', [ContactController::class, 'send']);
// Authentification classique
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Connexion via réseaux sociaux
Route::get('/login/facebook', [SocialAuthController::class, 'redirectToFacebook']);
Route::get('/login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Produits & catégories
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/category/{id}/products', [ProductController::class, 'productsByCategory']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/search', [ProductController::class, 'search']);
Route::get('/recommendations/{productId}', [RecommendationController::class, 'getRecommendations']);

// Avis clients (visiteurs)
Route::get('/products/{id}/reviews', [ReviewController::class, 'getProductReviews']);

// =================== ROUTES PROTÉGÉES (auth:api) ===================

Route::middleware(['auth:api', 'verified.api'])->group(function () {

    // Paiements
    Route::post('/payment/paypal/success', [PaymentController::class, 'handlePaypalSuccess']);
    Route::post('/payment/stripe', [StripeController::class, 'createPaymentIntent']);
    Route::post('/payment/stripe/success', [StripeController::class, 'handleStripeSuccess']);
    Route::post('/payment/cod', [PaymentController::class, 'handleCashOnDelivery']);

    // Utilisateur connecté
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Panier
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::put('/cart/items/{id}', [CartController::class, 'update']);
    Route::delete('/cart/items/{id}', [CartController::class, 'destroy']);

    // Avis clients
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/admin/reviews/{id}/approve', [ReviewController::class, 'approveReview']);
    Route::put('/admin/reviews/{id}/reject', [ReviewController::class, 'rejectReview']);

    // Adresses
    Route::post('/address', [AddressController::class, 'store']);
    Route::get('/address', [AddressController::class, 'getAddress']);
    Route::put('/address', [AddressController::class, 'update']);
    Route::delete('/address', [AddressController::class, 'destroy']);

    // Commandes
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});

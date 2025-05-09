<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});



Route::get('/test-paypal', function () {
    $clientId = config('services.paypal.client_id');
    $secret = config('services.paypal.secret');
    $url = config('services.paypal.base_url') . '/v1/oauth2/token';

    $headers = [
        "Authorization: Basic " . base64_encode("$clientId:$secret"),
        "Accept: application/json",
        "Accept-Language: en_US"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return response()->json([
        'status' => $httpcode,
        'response' => json_decode($response, true)
    ]);
});


Route::get('/debug-paypal', function () {
    return [
        'client_id' => config('services.paypal.client_id'),
        'secret' => config('services.paypal.secret'),
        'base_url' => config('services.paypal.base_url'),
    ];
    
});

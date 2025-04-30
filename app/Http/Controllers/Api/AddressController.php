<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name'   => 'required|string|max:255',
                'last_name'    => 'required|string|max:255',
                'address'      => 'required|string|max:255',
                'city'         => 'required|string|max:255',
                'postal_code'  => 'nullable|string|max:20',
                'country'      => 'required|string|max:100',
                'phone'        => 'nullable|string|max:20',
            ]);

            $address = Address::create([
                'user_id'      => auth()->id(),
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'address'      => $request->address,
                'city'         => $request->city,
                'postal_code'  => $request->postal_code,
                'country'      => $request->country,
                'phone'        => $request->phone,
            ]);

            return response()->json([
                'message' => 'Adresse ajoutée avec succès',
                'address' => $address
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout adresse : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

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
                'first_name'  => 'required|string|max:255',
                'last_name'   => 'required|string|max:255',
                'phone'       => 'nullable|string|max:20',
                'address'     => 'required|string|max:255',
                'region'      => 'required|string|max:255',
                'city'        => 'required|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'country'     => 'required|string|max:255',
                'save_info'   => 'boolean',
            ]);

            $address = Address::create([
                'user_id'     => auth()->id(),
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'region'      => $request->region,
                'city'        => $request->city,
                'postal_code' => $request->postal_code,
                'country'     => $request->country,
                'save_info'   => $request->save_info ?? false,
            ]);

            return response()->json([
                'message' => 'Adresse enregistrée avec succès.',
                'address' => $address
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout adresse : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAddress()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $address = Address::where('user_id', $user->id)->latest()->first();

        if (!$address) {
            return response()->json(['message' => 'Aucune adresse trouvée'], 404);
        }

        return response()->json($address);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'required|string|max:255',
            'region'      => 'required|string|max:255',
            'city'        => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country'     => 'required|string|max:255',
        ]);

        $address = Address::where('user_id', $user->id)->latest()->first();

        if (!$address) {
            return response()->json(['error' => 'Adresse introuvable.'], 404);
        }

        $address->update([
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'region'      => $request->region,
            'city'        => $request->city,
            'postal_code' => $request->postal_code,
            'country'     => $request->country,
        ]);

        return response()->json([
            'message' => 'Adresse mise à jour avec succès.',
            'address' => $address
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $address = Address::where('user_id', $user->id)->first();

        if ($address) {
            $address->delete();
            return response()->json(['message' => 'Adresse supprimée avec succès.']);
        }

        return response()->json(['message' => 'Adresse non trouvée.'], 404);
    }
}

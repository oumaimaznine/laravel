<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'password' => 'string|min:6|nullable',
        ]);

        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }

        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save(); 

        return response()->json([
            'message' => 'Profil a été modifié.',
            'user' => $user,
        ]);
    }
}

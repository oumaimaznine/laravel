<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $user = auth()->user(); // utilisateur connecté via JWT

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        // envoyer un email à l’admin
        Mail::to('admin@nourrituredesfideles.ma')->send(new ContactMessageMail([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'utilisateur' => $user->name ?? 'Visiteur',
        ]));

        return response()->json(['message' => 'Message envoyé avec succès !']);
    }
}

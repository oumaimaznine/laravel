<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vérification de l'email</title>
</head>
<body>
    <h2>Bonjour {{ $user->name }},</h2>
    <p>Voici votre code de vérification :</p>
    <h1 style="color: blue">{{ $user->verification_code }}</h1>
    <p>Merci d'utiliser notre plateforme !</p>
</body>
</html>

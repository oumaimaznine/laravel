<!-- resources/views/emails/verify_code.blade.php -->

<h2>Bonjour {{ $user->name }}</h2>

<p>Merci pour votre inscription sur notre site.</p>

<p>Voici votre code de vérification :</p>

<h3 style="color: #2f855a;">{{ $code }}</h3>

<p>Copiez ce code dans votre application React pour activer votre compte.</p>

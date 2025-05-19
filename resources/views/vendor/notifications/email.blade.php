<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vérification de votre adresse email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f6f6;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .email-container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #222;
            margin-bottom: 15px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .btn {
            display: inline-block;
            color:#A3B18A;
            font-weight: bold;
            padding: 14px 30px;
         
            
            margin-top: 25px;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        }

        .footerm {
            margin-top: 40px;
            font-size: 14px;
            color: #777;
        }

        .footerm a {
            color: #3BD6F3;
            text-decoration: none;
        }

        .footerm a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Bonjour {{ $user->name ?? 'Utilisateur' }},</h2>

        <p>Merci de vous être inscrit sur <strong>Nourriture des Fidèles</strong> !</p>
        <p>Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous :</p>

        <a href="{{ $actionUrl }}" class="btn">Confirmer l'adresse email</a>

        <p style="margin-top: 20px;">Ce lien est valable pendant 24 heures.</p>
        <p> Si vous n’avez pas demandé cette inscription, ignorez simplement ce message.</p>

        <div class="footerm">
            — L’équipe Nourriture des Fidèles<br>
            <a href="https://www.nourrituredesfideles.com">www.nourrituredesfideles.com</a>
        </div>
    </div>
</body>
</html>

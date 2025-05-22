<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Vérification de votre adresse email</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f6f6f6;
      padding: 40px;
      color: #333;
    }
    .container {
      max-width: 600px;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .btn {
      display: inline-block;
     
      color: #A3B18A;
      padding: 14px 24px;
      margin-top: 20px;
      text-decoration: none;
    
      font-weight: bold;
    }
    .footers {
      margin-top: 30px;
      font-size: 14px;
      color: #888;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Bonjour {{ $user->name ?? 'Utilisateur' }},</h2>

    <p>Merci de vous être inscrit sur <strong>Nourriture des Fidèles</strong> !</p>
    <p>Pour activer votre compte, cliquez ci-dessous :</p>

    <a href="{{ $actionUrl }}" class="btn">Confirmer l'adresse email</a>

<hr style="margin: 30px 0; border: none; border-top: 1px solid #ccc;">

    <p style="margin-top: 20px;">Ce lien est valable pendant 24 heures.</p>
    <p>Si vous n’avez pas demandé cette inscription, ignorez ce message.</p>

    <div class="footers">
      — L’équipe Nourriture des Fidèles<br>
      www.nourrituredesfideles.com
    </div>
  </div>
</body>
</html>

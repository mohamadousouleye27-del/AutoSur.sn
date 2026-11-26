@php
  $title = match ($purpose) {
    'reset' => 'Réinitialisez votre mot de passe',
    'login' => 'Confirmez votre connexion',
    default => 'Confirmez votre inscription',
  };
  $intro = match ($purpose) {
    'reset' => 'Vous avez demandé à réinitialiser votre mot de passe. Voici votre code :',
    'login' => 'Pour finir de vous connecter, saisissez ce code :',
    default => 'Merci de rejoindre AutoSur. Pour confirmer votre adresse e-mail, saisissez ce code :',
  };
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f5fb;font-family:Arial,Helvetica,sans-serif;color:#1F2C50;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f5fb;padding:24px 12px;">
    <tr><td align="center">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;">

        <tr><td style="background:#364C84;padding:22px 32px;">
          <span style="font-size:24px;font-weight:bold;color:#ffffff;">AutoSur</span>
        </td></tr>

        <tr><td style="padding:32px;">
          <h1 style="margin:0 0 12px;font-size:21px;color:#364C84;">{{ $title }}</h1>
          <p style="margin:0 0 20px;font-size:15px;line-height:1.6;">Bonjour {{ $user->firstName() }}, {{ $intro }}</p>

          <div style="margin:0 0 20px;padding:18px;text-align:center;background:#f3f5fb;border-radius:12px;font-size:34px;font-weight:bold;letter-spacing:10px;color:#364C84;">{{ $code }}</div>

          <p style="margin:0 0 12px;font-size:14px;line-height:1.6;">Ce code expire dans {{ $minutes }} minutes. Ne le communiquez à personne : l'équipe AutoSur ne vous le demandera jamais.</p>
          <p style="margin:0;font-size:13px;line-height:1.6;color:#5b678a;">Si vous n'êtes pas à l'origine de cette demande, ignorez simplement ce message : rien ne sera modifié.</p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
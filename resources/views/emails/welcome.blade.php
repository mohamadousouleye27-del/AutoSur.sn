@php
  $intro = match ($user->role) {
    'garage'    => "Votre compte garage est en cours de validation par notre équipe. Dès qu'il sera validé, vous recevrez les demandes de visite technique des automobilistes et pourrez gérer vos rendez-vous.",
    'depanneur' => "Votre compte dépanneur est en cours de validation par notre équipe. Dès qu'il sera validé, vous pourrez recevoir les alertes SOS et accepter vos interventions.",
    'assurance' => "Votre compte assureur est en cours de validation par notre équipe. Dès qu'il sera validé, vous pourrez répondre aux demandes de devis et suivre vos souscriptions.",
    default     => "Votre compte est prêt. AutoSur réunit en un seul endroit tout ce dont vous avez besoin pour votre voiture.",
  };

  $steps = match ($user->role) {
    'client' => [
      'Ajoutez votre premier véhicule (avec sa photo).',
      "Rangez vos documents dans votre coffre-fort numérique.",
      "Recevez des rappels avant l'expiration de votre assurance ou de votre visite technique.",
    ],
    default => [
      "Gardez votre téléphone à portée : nous vous prévenons dès la validation de votre compte.",
      "Vous pourrez ensuite vous connecter avec votre numéro de téléphone et votre mot de passe.",
    ],
  };
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenue sur AutoSur</title>
</head>
<body style="margin:0;padding:0;background:#f3f5fb;font-family:Arial,Helvetica,sans-serif;color:#1F2C50;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f5fb;padding:24px 12px;">
    <tr><td align="center">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;">

        <tr><td style="background:#364C84;padding:24px 32px;">
          <span style="font-size:26px;font-weight:bold;color:#ffffff;">AutoSur</span>
        </td></tr>

        <tr><td style="padding:32px;">
          <h1 style="margin:0 0 12px;font-size:22px;color:#364C84;">Bienvenue, {{ $user->firstName() }} !</h1>
          <p style="margin:0 0 20px;font-size:15px;line-height:1.6;">{{ $intro }}</p>

          <p style="margin:0 0 8px;font-size:15px;font-weight:bold;">Pour bien démarrer :</p>
          <ul style="margin:0 0 24px;padding-left:20px;font-size:15px;line-height:1.7;">
            @foreach ($steps as $step)
              <li>{{ $step }}</li>
            @endforeach
          </ul>

          <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
            Un dernier geste : confirmez votre adresse e-mail pour recevoir nos messages importants.
          </p>

          <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
            <tr><td style="background:#364C84;border-radius:999px;">
              <a href="{{ $verifyUrl }}" style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:bold;color:#ffffff;text-decoration:none;">Confirmer mon adresse e-mail</a>
            </td></tr>
          </table>

          <p style="margin:0 0 4px;font-size:13px;color:#5b678a;">Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :</p>
          <p style="margin:0 0 24px;font-size:12px;word-break:break-all;color:#364C84;">{{ $verifyUrl }}</p>

          <p style="margin:0;font-size:14px;line-height:1.6;">Une question ? Répondez simplement à ce message : nous lisons tout.<br>— L'équipe AutoSur</p>
        </td></tr>

        <tr><td style="background:#f3f5fb;padding:16px 32px;font-size:12px;color:#5b678a;line-height:1.5;">
          Vous recevez ce message car un compte AutoSur a été créé avec cette adresse e-mail. Si ce n'est pas vous, ignorez-le : sans confirmation, l'adresse ne sera pas utilisée.
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
Bienvenue, {{ $user->firstName() }} !

@switch($user->role)
@case('garage')
Votre compte garage est en cours de validation par notre équipe. Vous recevrez les demandes de visite technique dès sa validation.
@break
@case('depanneur')
Votre compte dépanneur est en cours de validation par notre équipe. Vous pourrez recevoir les alertes SOS dès sa validation.
@break
@case('assurance')
Votre compte assureur est en cours de validation par notre équipe. Vous pourrez répondre aux demandes de devis dès sa validation.
@break
@default
Votre compte est prêt. Ajoutez votre premier véhicule, rangez vos documents dans le coffre-fort et recevez des rappels avant chaque échéance.
@endswitch

Confirmez votre adresse e-mail en ouvrant ce lien :
{{ $verifyUrl }}

Une question ? Répondez simplement à ce message.

L'équipe AutoSur
<?php

return [
    'client' => [
        ['label' => 'Tableau de bord', 'route' => 'client.dashboard',      'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['label' => 'Mes véhicules',   'route' => 'client.vehicles.index',  'match' => 'client.vehicles.*',  'icon' => 'M5 16l1.5-5a2 2 0 0 1 1.9-1.4h7.2a2 2 0 0 1 1.9 1.4L19 16M5 16h14M5 16v3h3v-3M16 16v3h3v-3'],
        ['label' => 'Coffre-fort',     'route' => 'client.documents.index', 'match' => 'client.documents.*', 'icon' => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'],
        ['label' => 'Alertes',         'route' => 'client.alerts.index', 'match' => 'client.alerts.*', 'badge' => true, 'icon' => 'M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10 21a2 2 0 0 0 4 0'],
        ['label' => 'Assurance',       'route' => 'client.insurance.index', 'match' => 'client.insurance.*', 'icon' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z'],
        ['label' => 'Visite technique', 'route' => 'client.inspections.index', 'match' => 'client.inspections.*', 'icon' => 'M3 5h18v16H3zM3 10h18M8 3v4M16 3v4'],
        ['label' => 'SOS Panne',        'route' => 'client.sos.index',         'match' => 'client.sos.*',         'icon' => 'M12 9v4M12 17h.01M10.3 3.9L2 18a2 2 0 0 0 1.7 3h16.6a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z'],
        ],

    'assurance' => [
        ['label' => 'Tableau de bord',   'route' => 'assurance.dashboard',             'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['label' => 'Demandes de devis', 'route' => 'assurance.requests.index', 'match' => 'assurance.requests.*',      'icon' => 'M7 3h7l5 5v13H7zM14 3v5h5M10 13h6M10 17h6'],
        ['label' => 'Mes offres',        'route' => 'assurance.offers.index',   'match' => 'assurance.offers.*',        'icon' => 'M20 12l-8 8-9-9V3h8zM7.5 7.5h.01'],
        ['label' => 'Souscriptions',     'route' => 'assurance.subscriptions.index', 'match' => 'assurance.subscriptions.*', 'icon' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6zM9 12l2 2 4-4'],
    ],

        'garage' => [
        ['label' => 'Tableau de bord', 'route' => 'garage.dashboard',          'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['label' => 'Rendez-vous',     'route' => 'garage.appointments.index', 'match' => 'garage.appointments.*', 'icon' => 'M3 5h18v16H3zM3 10h18M8 3v4M16 3v4'],
        ['label' => 'Avis', 'route' => 'garage.reviews.index', 'match' => 'garage.reviews.*', 'icon' => 'M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z'],
    ],

    'depanneur' => [
        ['label' => 'Tableau de bord',  'route' => 'depanneur.dashboard',      'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['label' => 'Demandes SOS',     'route' => 'depanneur.requests.index', 'match' => 'depanneur.requests.*', 'icon' => 'M12 9v4M12 17h.01M10.3 3.9L2 18a2 2 0 0 0 1.7 3h16.6a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z'],
        ['label' => 'Mes interventions', 'route' => 'depanneur.jobs.index',    'match' => 'depanneur.jobs.*',     'icon' => 'M20 6L9 17l-5-5'],
        ['label' => 'Avis', 'route' => 'depanneur.reviews.index', 'match' => 'depanneur.reviews.*', 'icon' => 'M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z'],
    ],

    'admin' => [
        ['label' => 'Tableau de bord', 'route' => 'admin.dashboard',        'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['label' => 'Utilisateurs',    'route' => 'admin.users.index',      'match' => 'admin.users.*',    'icon' => 'M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M21 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8'],
        ['label' => 'Partenaires',     'route' => 'admin.partners.index',   'match' => 'admin.partners.*', 'icon' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6zM9 12l2 2 4-4'],
        ['label' => 'Suivi comptable', 'route' => 'admin.finances.index',   'match' => 'admin.finances.*', 'icon' => 'M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
        ['label' => 'Paramètres',      'route' => 'admin.settings.edit',    'match' => 'admin.settings.*', 'icon' => 'M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6'],
                ['label' => 'Véhicules', 'route' => 'admin.vehicles.index',  'match' => 'admin.vehicles.*',  'icon' => 'M5 16l1.5-5a2 2 0 0 1 1.9-1.4h7.2a2 2 0 0 1 1.9 1.4L19 16M5 16h14M5 16v3h3v-3M16 16v3h3v-3'],
        ['label' => 'Documents', 'route' => 'admin.documents.index', 'match' => 'admin.documents.*', 'icon' => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'],
        ['label' => 'Avis',      'route' => 'admin.reviews.index',   'match' => 'admin.reviews.*',   'icon' => 'M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z'],
    ],
];
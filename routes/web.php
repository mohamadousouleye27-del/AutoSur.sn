<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\AlertController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\DocumentController;
use App\Http\Controllers\Client\InspectionController;
use App\Http\Controllers\Client\InsuranceQuoteController;
use App\Http\Controllers\Client\SosController;
use App\Http\Controllers\Client\VehicleController;
use App\Http\Controllers\Insurer\DashboardController as InsurerDashboardController;
use App\Http\Controllers\Insurer\OfferController;
use App\Http\Controllers\Insurer\QuoteRequestController;
use App\Http\Controllers\Insurer\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);

    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1');

    Route::get('/verification', [AuthController::class, 'showOtp'])->name('otp.show');
    Route::post('/verification', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/verification/renvoyer', [AuthController::class, 'resendOtp'])->name('otp.resend');
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {

    /* Espaces provisoires (construits aux étapes suivantes) */
    $spaces = [
        'garage'    => 'Garage',
        'depanneur' => 'Dépanneur',
        'admin'     => 'Administrateur',
    ];

    foreach ($spaces as $role => $label) {
        Route::middleware("role:$role")
            ->prefix($role)->name("$role.")
            ->group(function () use ($label) {
                Route::view('/', 'dashboards.placeholder', ['space' => $label])->name('dashboard');
            });
    }

    /* ---------- Espace client ---------- */
    Route::middleware('role:client')->prefix('client')->name('client.')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Véhicules
        Route::resource('vehicules', VehicleController::class)
            ->parameters(['vehicules' => 'vehicle'])
            ->except('show')
            ->names('vehicles');

        // Coffre-fort
        Route::get('documents/{document}/voir', [DocumentController::class, 'view'])->name('documents.view');
        Route::get('documents/{document}/telecharger', [DocumentController::class, 'download'])->name('documents.download');
        Route::resource('documents', DocumentController::class)
            ->parameters(['documents' => 'document'])
            ->except('show')
            ->names('documents');

        // Alertes
        Route::get('alertes', [AlertController::class, 'index'])->name('alerts.index');
        Route::post('alertes/tout-lire', [AlertController::class, 'readAll'])->name('alerts.read-all');
        Route::get('alertes/{id}/ouvrir', [AlertController::class, 'read'])->name('alerts.read');

        // Assurance
        Route::post('assurance/offres/{offer}/accepter', [InsuranceQuoteController::class, 'accept'])->name('insurance.accept');
        Route::resource('assurance', InsuranceQuoteController::class)
            ->parameters(['assurance' => 'quote'])
            ->only(['index', 'create', 'store', 'show', 'destroy'])
            ->names('insurance');

        // Visite technique
        Route::resource('visite-technique', InspectionController::class)
            ->parameters(['visite-technique' => 'inspection'])
            ->only(['index', 'create', 'store', 'destroy'])
            ->names('inspections');

        // SOS Panne (l'ordre compte : « nouveau » avant « {sos} »)
        Route::get('sos', [SosController::class, 'index'])->name('sos.index');
        Route::get('sos/nouveau', [SosController::class, 'create'])->name('sos.create');
        Route::post('sos', [SosController::class, 'store'])->middleware('throttle:5,10')->name('sos.store');
        Route::get('sos/{sos}', [SosController::class, 'show'])->name('sos.show');
        Route::get('sos/{sos}/statut', [SosController::class, 'status'])->name('sos.status');
        Route::delete('sos/{sos}', [SosController::class, 'destroy'])->name('sos.destroy');
    });

    /* ---------- Espace assurance ---------- */
    Route::middleware('role:assurance')->prefix('assurance')->name('assurance.')->group(function () {
        Route::get('/', InsurerDashboardController::class)->name('dashboard');

        Route::get('demandes', [QuoteRequestController::class, 'index'])->name('requests.index');
        Route::get('demandes/{quote}', [QuoteRequestController::class, 'show'])->name('requests.show');
        Route::post('demandes/{quote}/offre', [QuoteRequestController::class, 'store'])->name('requests.offer');

        Route::get('offres', [OfferController::class, 'index'])->name('offers.index');
        Route::delete('offres/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');

        Route::get('souscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    });
});
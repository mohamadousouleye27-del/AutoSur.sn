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
use App\Http\Controllers\Garage\AppointmentController;
use App\Http\Controllers\Garage\DashboardController as GarageDashboardController;
use App\Http\Controllers\Tow\DashboardController as TowDashboardController;
use App\Http\Controllers\Tow\SosJobController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Client\RatingController;
use App\Http\Controllers\PartnerReviewController;
use App\Http\Controllers\Admin\DocumentModerationController;
use App\Http\Controllers\Admin\ReviewModerationController;
use App\Http\Controllers\Admin\VehicleModerationController;



Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);

    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1');

    Route::get('/verification', [AuthController::class, 'showOtp'])->name('otp.show');
    Route::post('/verification', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/verification/renvoyer', [AuthController::class, 'resendOtp'])->name('otp.resend');

        Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'showRequest'])->name('password.forgot');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendCode'])->middleware('throttle:5,5')->name('password.send');
    Route::get('/mot-de-passe-oublie/code', [PasswordResetController::class, 'showReset'])->name('password.code');
    Route::post('/mot-de-passe-oublie/code', [PasswordResetController::class, 'reset'])->middleware('throttle:10,5')->name('password.update');

    Route::get('/connexion/verification', [TwoFactorController::class, 'show'])->name('twofa.show');
    Route::post('/connexion/verification', [TwoFactorController::class, 'verify'])->middleware('throttle:10,5')->name('twofa.verify');
    Route::post('/connexion/verification/renvoyer', [TwoFactorController::class, 'resend'])->middleware('throttle:5,5')->name('twofa.resend');

});

Route::post('/deconnexion', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {

        Route::middleware('role:client,garage,depanneur,assurance,admin')->group(function () {
        Route::get('securite', [SecurityController::class, 'edit'])->name('security.edit');
        Route::post('securite/mot-de-passe', [SecurityController::class, 'updatePassword'])->name('security.password');
        Route::post('securite/2fa', [SecurityController::class, 'toggleTwoFactor'])->name('security.twofa');
    });

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


                // Paiements
        Route::post('visite-technique/{inspection}/payer', [PaymentController::class, 'payInspection'])->name('inspections.pay');
        Route::get('paiements/{payment}/retour', [PaymentController::class, 'callback'])->name('payments.callback');
        Route::get('paiements/{payment}/simulation', [PaymentController::class, 'simulation'])->name('payments.simulation');
        Route::post('paiements/{payment}/simulation', [PaymentController::class, 'simulate'])->name('payments.simulate');


                // Avis
        Route::post('avis/visite-technique/{inspection}', [RatingController::class, 'inspection'])->name('ratings.inspection');
        Route::post('avis/sos/{sos}', [RatingController::class, 'sos'])->name('ratings.sos');
    });

    /* ---------- Espace assurance ---------- */
        Route::middleware(['role:assurance', 'partner'])->prefix('assurance')->name('assurance.')->group(function () {
        Route::get('/', InsurerDashboardController::class)->name('dashboard');

        Route::get('demandes', [QuoteRequestController::class, 'index'])->name('requests.index');
        Route::get('demandes/{quote}', [QuoteRequestController::class, 'show'])->name('requests.show');
        Route::post('demandes/{quote}/offre', [QuoteRequestController::class, 'store'])->name('requests.offer');

        Route::get('offres', [OfferController::class, 'index'])->name('offers.index');
        Route::delete('offres/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');

        Route::get('souscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    });


        /* ---------- Espace garage ---------- */
        Route::middleware(['role:garage', 'partner'])->prefix('garage')->name('garage.')->group(function () {
        Route::get('/', GarageDashboardController::class)->name('dashboard');

        Route::get('rendez-vous', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('rendez-vous/{appointment}/confirmer', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::post('rendez-vous/{appointment}/terminer', [AppointmentController::class, 'complete'])->name('appointments.complete');
        Route::post('rendez-vous/{appointment}/liberer', [AppointmentController::class, 'release'])->name('appointments.release');

        Route::get('avis', [PartnerReviewController::class, 'index'])->name('reviews.index');
    });

    /* ---------- Espace dépanneur ---------- */
        Route::middleware(['role:depanneur', 'partner'])->prefix('depanneur')->name('depanneur.')->group(function () {
        Route::get('/', [TowDashboardController::class, 'index'])->name('dashboard');
        Route::post('disponibilite', [TowDashboardController::class, 'toggle'])->name('availability');

        Route::get('demandes', [SosJobController::class, 'index'])->name('requests.index');
        Route::get('demandes/nouvelles', [SosJobController::class, 'count'])->name('requests.count');
        Route::get('demandes/{sos}', [SosJobController::class, 'show'])->name('requests.show');
        Route::post('demandes/{sos}/accepter', [SosJobController::class, 'accept'])->name('requests.accept');
        Route::post('demandes/{sos}/refuser', [SosJobController::class, 'decline'])->name('requests.decline');
        Route::post('demandes/{sos}/terminer', [SosJobController::class, 'complete'])->name('requests.complete');
        Route::post('demandes/{sos}/abandonner', [SosJobController::class, 'release'])->name('requests.release');

        Route::get('interventions', [SosJobController::class, 'history'])->name('jobs.index');

        Route::get('avis', [PartnerReviewController::class, 'index'])->name('reviews.index');
    });


        /* ---------- Espace administrateur ---------- */
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::get('utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('utilisateurs/{user}/statut', [UserController::class, 'toggle'])->name('users.toggle');

        Route::get('partenaires', [PartnerController::class, 'index'])->name('partners.index');
        Route::post('partenaires/{user}/valider', [PartnerController::class, 'approve'])->name('partners.approve');
        Route::post('partenaires/{user}/refuser', [PartnerController::class, 'reject'])->name('partners.reject');
        Route::post('partenaires/{user}/commission', [PartnerController::class, 'commission'])->name('partners.commission');

        Route::get('finances', [FinanceController::class, 'index'])->name('finances.index');

        Route::get('parametres', [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('parametres', [SettingController::class, 'update'])->name('settings.update');

        Route::post('utilisateurs/{user}/2fa', [UserController::class, 'resetTwoFactor'])->name('users.twofa');


        Route::get('vehicules', [VehicleModerationController::class, 'index'])->name('vehicles.index');
        Route::delete('vehicules/{vehicle}', [VehicleModerationController::class, 'destroy'])->name('vehicles.destroy');

        Route::get('documents', [DocumentModerationController::class, 'index'])->name('documents.index');
        Route::delete('documents/{document}', [DocumentModerationController::class, 'destroy'])->name('documents.destroy');

        Route::get('avis', [ReviewModerationController::class, 'index'])->name('reviews.index');
        Route::delete('avis/{rating}', [ReviewModerationController::class, 'destroy'])->name('reviews.destroy');
    });

});

Route::post('webhooks/paiement', [PaymentController::class, 'webhook'])
    ->middleware('throttle:60,1')
    ->name('webhooks.payment');


Route::get('/email/confirmation/{user}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('email.verify');

// Aperçu de l'e-mail dans le navigateur (développement uniquement)
if (app()->environment('local')) {
    Route::get('/apercu-email', fn () => new \App\Mail\WelcomeMail(\App\Models\User::first(), url('/')));
}    
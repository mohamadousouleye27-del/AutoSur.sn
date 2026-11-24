<?php

namespace App\Console\Commands;

use App\Services\SmsGateway;
use App\Support\Phone;
use Illuminate\Console\Command;

class TestSms extends Command
{
    protected $signature = 'sms:test {phone : Numéro, ex : 77 123 45 67}';

    protected $description = "Envoie un SMS de test avec la passerelle configurée";

    public function handle(SmsGateway $sms): int
    {
        $phone = Phone::normalize($this->argument('phone'));

        $this->info('Passerelle : ' . config('alerts.sms_driver') . " → {$phone}");

        if ($sms->send($phone, "AutoSur : test d'envoi de SMS. Tout fonctionne !")) {
            $this->info('SMS accepté (en mode « log », voir storage/logs/laravel.log).');

            return self::SUCCESS;
        }

        $this->error('Échec : voir storage/logs/laravel.log');

        return self::FAILURE;
    }
}
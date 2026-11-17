<?php

namespace Database\Seeders;

use App\Models\InsuranceOffer;
use App\Models\InsuranceQuote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InsuranceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $insurers = [
            ['Assurance Teranga (démo)', '+221770000001', 'teranga@demo.sn', 1.00],
            ['Sama Assurances (démo)',   '+221770000002', 'sama@demo.sn',    1.15],
        ];

        $base = ['tiers' => 90000, 'tiers_plus' => 140000, 'tous_risques' => 260000];

        $users = collect($insurers)->map(function ($i) {
            $user = User::where('phone', $i[1])->first() ?? tap(new User(), function (User $u) use ($i) {
                $u->forceFill([
                    'name'              => $i[0],
                    'phone'             => $i[1],
                    'email'             => $i[2],
                    'password'          => Hash::make('Demo@12345'),
                    'role'              => 'assurance',
                    'phone_verified_at' => now(),
                ])->save();
            });

            return [$user, $i[3]];
        });

        InsuranceQuote::where('status', 'open')->each(function (InsuranceQuote $quote) use ($users, $base) {
            foreach ($users as [$insurer, $factor]) {
                InsuranceOffer::firstOrCreate(
                    ['insurance_quote_id' => $quote->id, 'insurer_id' => $insurer->id],
                    [
                        'annual_premium' => (int) round($base[$quote->coverage] * $factor, -3),
                        'valid_until'    => now()->addDays(15),
                        'details'        => 'Responsabilité civile, défense et recours, assistance 24h/24.',
                    ]
                );
            }
        });
    }
}
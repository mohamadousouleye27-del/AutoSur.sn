<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\ExpiryReminder;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendExpiryAlerts extends Command
{
    protected $signature = 'alerts:send {--reset : Vide l\'historique des rappels déjà envoyés (pour les tests)}';

    protected $description = "Envoie les rappels d'échéance (assurance, visite technique, documents)";

    private int $sent = 0;

    public function handle(): int
    {
        if ($this->option('reset')) {
            DB::table('alert_logs')->truncate();
            $this->warn('Historique des rappels vidé.');
        }

        $fields = [
            'insurance_expires_at'       => "L'assurance",
            'technical_visit_expires_at' => 'La visite technique',
        ];

        Vehicle::with('user')->get()->each(function (Vehicle $v) use ($fields) {
            foreach ($fields as $field => $label) {
                $this->process(
                    $v->user,
                    $v->{$field},
                    "vehicle:{$v->id}:{$field}",
                    "{$label} de votre {$v->brand} {$v->model} ({$v->plate})",
                    route('client.vehicles.edit', $v, false),
                );
            }
        });

        Document::with('user')->whereNotNull('expires_at')->get()->each(function (Document $d) {
            $this->process(
                $d->user,
                $d->expires_at,
                "document:{$d->id}",
                "Le document « {$d->title} »",
                route('client.documents.edit', $d, false),
            );
        });

        $this->info("{$this->sent} rappel(s) envoyé(s).");

        return self::SUCCESS;
    }

    private function process(?User $user, ?CarbonInterface $date, string $base, string $subject, string $url): void
    {
        if (! $user || ! $date) {
            return;
        }

        $days = (int) today()->diffInDays($date, false);

        $stage = match (true) {
            $days < 0   => 'expired',
            $days === 0 => 'j0',
            $days <= 7  => 'j7',
            $days <= 30 => 'j30',
            default     => null,
        };

        if (! $stage) {
            return;
        }

        // Un seul rappel par étape et par échéance (la date fait partie de la clé : un renouvellement repart de zéro).
        $key = "{$base}:{$date->format('Y-m-d')}:{$stage}";
        $inserted = DB::table('alert_logs')->insertOrIgnore([
            'user_id'    => $user->id,
            'key'        => $key,
            'created_at' => now(),
        ]);

        if (! $inserted) {
            return;
        }

        $message = match ($stage) {
            'expired' => "{$subject} a expiré le {$date->format('d/m/Y')}.",
            'j0'      => "{$subject} expire aujourd'hui.",
            default   => "{$subject} expire dans {$days} jour" . ($days > 1 ? 's' : '') . " (le {$date->format('d/m/Y')}).",
        };

        $user->notify(new ExpiryReminder($message, $url, $stage));
        $this->sent++;
    }
}
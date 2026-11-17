<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'plate', 'brand', 'model', 'year', 'color', 'chassis_number',
        'insurance_expires_at', 'technical_visit_expires_at',
    ];

    protected $casts = [
        'insurance_expires_at'       => 'date',
        'technical_visit_expires_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Statut d'une échéance : libellé + classes Tailwind du badge. */
    public function status(string $field): array
    {
        $date = $this->{$field};

        if (! $date) {
            return ['label' => 'Non renseignée', 'class' => 'bg-slate-100 text-slate-500'];
        }
        if ($date->lt(today())) {
            return ['label' => 'Expirée', 'class' => 'bg-red-100 text-red-700'];
        }
        if ($date->lte(today()->addDays(30))) {
            return ['label' => 'Expire bientôt', 'class' => 'bg-amber-100 text-amber-800'];
        }

        return ['label' => 'Valide', 'class' => 'bg-brand-200 text-brand-700'];
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

}
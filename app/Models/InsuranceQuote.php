<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceQuote extends Model
{
    public const COVERAGES = [
        'tiers'        => 'Responsabilité civile (tiers)',
        'tiers_plus'   => 'Tiers étendu (vol, incendie, bris de glace)',
        'tous_risques' => 'Tous risques',
    ];

    public const USAGES = [
        'personnel'    => 'Usage personnel',
        'professionnel' => 'Usage professionnel',
        'transport'    => 'Transport de personnes (taxi, minibus…)',
    ];

    protected $fillable = [
        'vehicle_id', 'coverage', 'usage', 'start_date', 'notes', 'status', 'accepted_offer_id',
    ];

    protected $casts = ['start_date' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(InsuranceOffer::class, 'insurance_quote_id');
    }

    public function coverageLabel(): string
    {
        return self::COVERAGES[$this->coverage] ?? $this->coverage;
    }

    public function usageLabel(): string
    {
        return self::USAGES[$this->usage] ?? $this->usage;
    }

    public function badge(): array
    {
        if ($this->status === 'accepted') {
            return ['label' => 'Offre acceptée', 'class' => 'bg-brand-200 text-brand-700'];
        }
        if ($this->status === 'cancelled') {
            return ['label' => 'Annulée', 'class' => 'bg-slate-100 text-slate-500'];
        }

        $count = $this->offers_count ?? $this->offers()->count();

        return $count > 0
            ? ['label' => $count . ' offre' . ($count > 1 ? 's' : '') . ' reçue' . ($count > 1 ? 's' : ''), 'class' => 'bg-amber-100 text-amber-800']
            : ['label' => "En attente d'offres", 'class' => 'bg-slate-100 text-slate-600'];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SosRequest extends Model
{
    public const PROBLEMS = [
        'battery'  => 'Batterie à plat',
        'tire'     => 'Pneu crevé',
        'engine'   => 'Panne moteur',
        'fuel'     => 'Panne de carburant',
        'accident' => 'Accident',
        'other'    => 'Autre problème',
    ];

    protected $fillable = [
        'vehicle_id', 'problem', 'description', 'latitude', 'longitude', 'landmark', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function problemLabel(): string
    {
        return self::PROBLEMS[$this->problem] ?? $this->problem;
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['searching', 'accepted'], true);
    }

    public function hasPosition(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function embedUrl(): ?string
    {
        if (! $this->hasPosition()) {
            return null;
        }

        $lat = (float) $this->latitude;
        $lon = (float) $this->longitude;
        $bbox = implode(',', [$lon - 0.006, $lat - 0.004, $lon + 0.006, $lat + 0.004]);

        return "https://www.openstreetmap.org/export/embed.html?bbox={$bbox}&layer=mapnik&marker={$lat},{$lon}";
    }

    public function badge(): array
    {
        return match ($this->status) {
            'accepted'  => ['label' => 'Dépanneur en route',        'class' => 'bg-brand-200 text-brand-700'],
            'completed' => ['label' => 'Intervention terminée',     'class' => 'bg-green-100 text-green-700'],
            'cancelled' => ['label' => 'Annulée',                   'class' => 'bg-slate-100 text-slate-500'],
            default     => ['label' => "Recherche d'un dépanneur…", 'class' => 'bg-amber-100 text-amber-800'],
        };
    }


    /** Demandes encore ouvertes et non refusées par ce dépanneur. */
    public function scopeOpenFor($query, int $driverId)
    {
        return $query->where('status', 'searching')
            ->whereNull('driver_id')
            ->whereNotIn('id', DB::table('sos_request_declines')
                ->where('driver_id', $driverId)
                ->select('sos_request_id'));
    }

    public function directionsUrl(): ?string
    {
        return $this->hasPosition()
            ? "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}"
            : null;
    }

    public function rating(): MorphOne
    {
        return $this->morphOne(Rating::class, 'ratable');
    }
}
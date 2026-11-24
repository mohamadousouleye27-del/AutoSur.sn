<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class InspectionAppointment extends Model
{
    public const CATEGORIES = [
        'particulier' => ['label' => 'Voiture particulière',                              'fee' => 1500],
        'utilitaire'  => ['label' => 'Utilitaire / pick-up',                              'fee' => 2000],
        'transport'   => ['label' => 'Transport de personnes ou de marchandises',         'fee' => 3000],
    ];

    public const TIME_SLOTS = [
        'morning'   => 'Matin (8h - 12h)',
        'afternoon' => 'Après-midi (14h - 17h)',
    ];

    protected $fillable = [
        'vehicle_id', 'category', 'scheduled_date', 'time_slot', 'city', 'notes', 'fee', 'status',
    ];

    protected $casts = ['scheduled_date' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(User::class, 'garage_id');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category]['label'] ?? $this->category;
    }

    public function slotLabel(): string
    {
        return self::TIME_SLOTS[$this->time_slot] ?? $this->time_slot;
    }

    public function formattedFee(): string
    {
        return number_format($this->fee, 0, ',', ' ') . ' FCFA';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'], true);
    }

    public function badge(): array
    {
        return match ($this->status) {
            'confirmed' => ['label' => 'Confirmé',   'class' => 'bg-brand-200 text-brand-700'],
            'completed' => ['label' => 'Effectué',   'class' => 'bg-green-100 text-green-700'],
            'cancelled' => ['label' => 'Annulé',     'class' => 'bg-slate-100 text-slate-500'],
            default     => ['label' => 'En attente', 'class' => 'bg-amber-100 text-amber-800'],
        };
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function isPaid(): bool
    {
        return $this->payments->contains('status', 'paid');
    }

    public function rating(): MorphOne
    {
        return $this->morphOne(Rating::class, 'ratable');
    }
}
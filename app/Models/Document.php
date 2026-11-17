<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    public const TYPES = [
        'carte_grise'      => 'Carte grise',
        'assurance'        => "Attestation d'assurance",
        'visite_technique' => 'Visite technique',
        'permis'           => 'Permis de conduire',
        'facture'          => 'Facture',
        'autre'            => 'Autre',
    ];

    protected $fillable = [
        'vehicle_id', 'title', 'type', 'file_path',
        'original_name', 'mime', 'size', 'expires_at',
    ];

    protected $casts = ['expires_at' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? 'Autre';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime, 'image/');
    }

    public function humanSize(): string
    {
        return $this->size >= 1048576
            ? number_format($this->size / 1048576, 1, ',', ' ') . ' Mo'
            : max(1, round($this->size / 1024)) . ' Ko';
    }

    public function status(): array
    {
        if (! $this->expires_at) {
            return ['label' => 'Sans échéance', 'class' => 'bg-slate-100 text-slate-500'];
        }
        if ($this->expires_at->lt(today())) {
            return ['label' => 'Expiré', 'class' => 'bg-red-100 text-red-700'];
        }
        if ($this->expires_at->lte(today()->addDays(30))) {
            return ['label' => 'Expire bientôt', 'class' => 'bg-amber-100 text-amber-800'];
        }

        return ['label' => 'Valide', 'class' => 'bg-brand-200 text-brand-700'];
    }
}
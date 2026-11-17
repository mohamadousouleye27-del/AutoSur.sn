<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceOffer extends Model
{
    protected $fillable = [
        'insurance_quote_id', 'insurer_id', 'annual_premium', 'valid_until', 'details', 'status','commission_rate',
    ];

    protected $casts = ['valid_until' => 'date'];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(InsuranceQuote::class, 'insurance_quote_id');
    }

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'insurer_id');
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->lt(today());
    }

    public function formattedPremium(): string
    {
        return number_format($this->annual_premium, 0, ',', ' ') . ' FCFA';
    }

    public function commissionAmount(): int
    {
        return (int) round($this->annual_premium * $this->commission_rate / 100);
    }

    public function formattedCommission(): string
    {
        return number_format($this->commissionAmount(), 0, ',', ' ') . ' FCFA';
    }
}
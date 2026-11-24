<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'currency', 'provider', 'status',
        'reference', 'provider_session_id', 'checkout_url', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function formattedAmount(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }
}
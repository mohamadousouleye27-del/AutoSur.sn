<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Rating extends Model
{
    protected $fillable = ['user_id', 'partner_id', 'score', 'comment'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function ratable(): MorphTo
    {
        return $this->morphTo();
    }

    public function stars(): string
    {
        return str_repeat('★', $this->score) . str_repeat('☆', 5 - $this->score);
    }
}
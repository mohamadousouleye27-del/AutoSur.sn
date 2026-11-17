<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin'     => 'admin.dashboard',
            'garage'    => 'garage.dashboard',
            'depanneur' => 'depanneur.dashboard',
            'assurance' => 'assurance.dashboard',
            default     => 'client.dashboard',
        };
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function insuranceQuotes(): HasMany
    {
        return $this->hasMany(InsuranceQuote::class);
    }

    public function insuranceOffers(): HasMany
    {
        return $this->hasMany(InsuranceOffer::class, 'insurer_id');
    }

    public function inspectionAppointments(): HasMany
    {
        return $this->hasMany(InspectionAppointment::class);
    }

    public function sosRequests(): HasMany
    {
        return $this->hasMany(SosRequest::class);
    }
}

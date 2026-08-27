<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone',
        'city',
        'profession',
        'experience_years',
        'role',
        'monthly_goal',
        'branch',
        'contributes_properties',
        'cv_file_path',
        'hire_date',
        'status',
        'cedula',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'contributes_properties' => 'boolean',
            'hire_date' => 'date',
        ];
    }

    // --- RELACIONES CON EL RESTO DE LA PLATAFORMA ---

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function appointmentTrackings()
    {
        return $this->hasMany(AppointmentTracking::class);
    }

    public function tramites()
    {
        return $this->hasMany(Tramite::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function accountingExpenses()
    {
        return $this->hasMany(AccountingExpense::class);
    }

    public function salesAccountings()
    {
        return $this->hasMany(SalesAccounting::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }
    public function portfolioEntries()
{
    return $this->hasMany(ClientPortfolioEntry::class, 'advisor_id');
}
}
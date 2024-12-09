<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'account_type',
        'adresse',
        'profile_photo',
        'score',
        'time_remaining',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];




    public function serviteurDeDieu()
    {
        return $this->hasOne(ServiteurDeDieu::class);
    }


    public function chantre()
    {
        return $this->hasOne(Chantre::class);
    }


    public function fidele()
    {
        return $this->hasOne(Fidele::class);
    }

    public function serviteur_de_dieu()
    {
        return $this->hasOne(ServiteurDeDieu::class);
    }





    // Dans le modèle User
    public function dons()
    {
        return $this->belongsToMany(Don::class, 'users_dons')
            ->withPivot('reference_paiement', 'date_paiement', 'montant_paiement')
            ->withTimestamps();
    }


    public function postes()
    {
        return $this->belongsToMany(Post::class, 'postes_users', 'user_id', 'poste_id');
    }
}

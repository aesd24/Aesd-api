<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Don extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'objectif',
        'end_at',
        'status',
    ];

    // Relations
    // Un don peut être fait par plusieurs utilisateurs
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'users_dons');
    // }


    public function users()
    {
        return $this->belongsToMany(User::class, 'users_dons')
            ->withPivot('reference_paiement', 'date_paiement', 'montant_paiement')
            ->withTimestamps();
    }

    // Un don peut être géré par un administrateur
    public function administrateur()
    {
        return $this->belongsTo(Administrateur::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpportuniteJeune extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'post_profile',
        'exigence',
        'deadline',
        'localisation_du_poste',
        'is_published_at',
        'study_level',
        'experience',
        'type_contract',
    ];

    // Relations
    // Une opportunité jeune peut être associée à plusieurs utilisateurs
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'users_opportunites_jeunes');
    // }


    public function users()
    {
        return $this->belongsToMany(User::class, 'users_opportunites_jeunes')
            ->withPivot('cv', 'lettre_de_motivation', 'details')
            ->withTimestamps(); // Si tu veux également inclure les timestamps
    }

    // Une opportunité jeune peut être gérée par un administrateur
    // public function administrateur()
    // {
    //     return $this->belongsTo(Administrateur::class);
    // }

    public function administrateur()
    {
        return $this->belongsTo(Administrateur::class, 'administrateur_id');
    }
}

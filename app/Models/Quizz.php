<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizz extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'date',
    ];

    // Relations
    // Un quizz peut avoir plusieurs propositions de réponses
    public function propositionsDeReponses()
    {
        return $this->hasMany(PropositionDeReponse::class);
    }

    // Un quizz peut être associé à plusieurs utilisateurs
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'users_quizz');
    // }


    public function users()
    {
        return $this->belongsToMany(User::class, 'users_quizz')
            ->withPivot('reponses', 'points_obtenus')
            ->withTimestamps(); // Pour inclure les timestamps si besoin
    }

    // Un quizz peut être géré par un administrateur
    // public function administrateur()
    // {
    //     return $this->belongsTo(Administrateur::class);
    // }

    public function administrateur()
    {
        return $this->belongsTo(Administrateur::class, 'administrateur_id');
    }
}

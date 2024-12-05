<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrateur extends Model

// class  Administrateur extends User // Hérite de User
{
    use HasFactory;

    protected $fillable = [
        'id_card',
        'role',
        'user_id',
    ];

    // Relations
    // Un administrateur appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un administrateur peut avoir plusieurs quizz
    public function quizz()
    {
        return $this->hasMany(Quizz::class);
    }

    // Un administrateur peut avoir plusieurs sujets de discussion
    public function sujetsDeDiscussion()
    {
        return $this->hasMany(SujetDeDiscussion::class);
    }

    // Un administrateur peut avoir plusieurs opportunités jeunes
    public function opportunitesJeunes()
    {
        return $this->hasMany(OpportuniteJeune::class);
    }

    // Un administrateur peut avoir plusieurs dons
    public function dons()
    {
        return $this->hasMany(Don::class);
    }


    public function actualites()
    {
        return $this->hasMany(Actualite::class, 'administrateur_id');
    }

    public function opportunites()
    {
        return $this->hasMany(OpportuniteJeune::class, 'administrateur_id');
    }

}

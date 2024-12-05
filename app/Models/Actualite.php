<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actualite extends Model
{
    use HasFactory;

    protected $table = 'actualites';

    protected $fillable = [
        'titre',
        'contenu',
        'image',
        'date_publication',
        'date_expiration',
        'tags',
    ];


    /**
     * Relation : une actualité appartient à un administrateur.
     */
    public function administrateur()
    {
        return $this->belongsTo(Administrateur::class, 'administrateur_id');
    }

    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SujetDeDiscussion extends Model
{
    use HasFactory;

    protected $table = 'sujets_de_discussion';

    protected $fillable = [
        'theme',
        'date',
        'body',
    ];

    // Relations
    // Un sujet de discussion peut avoir plusieurs fidèles
    // public function fideles()
    // {
    //     return $this->belongsToMany(Fidele::class, 'fideles_sujets_de_discussion');
    // }

    // public function fideles()
    // {
    //     return $this->belongsToMany(Fidele::class, 'fideles_sujets_de_discussion')
    //         ->withPivot('Comment'); // Ajoute des colonnes supplémentaires si nécessaire
    // }



    public function fideles()
    {
        return $this->belongsToMany(Fidele::class, 'fideles_sujets_de_discussion', 'sujet_id', 'fidele_id')
            ->withPivot('Comment')
            ->withTimestamps();
    }

    public function chantres()
    {
        return $this->belongsToMany(Chantre::class, 'chantres_sujets_de_discussion', 'sujet_id', 'chantre_id')
            ->withPivot('Comment')
            ->withTimestamps();
    }


    public function serviteursDeDieu()
    {
        return $this->belongsToMany(ServiteurDeDieu::class, 'serviteurs_de_dieu_sujets_de_discussion', 'sujet_id', 'serviteur_de_dieu_id')
            ->withPivot('Comment')
            ->withTimestamps();
    }
}

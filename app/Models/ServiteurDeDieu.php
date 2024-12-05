<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiteurDeDieu extends Model
// class ServiteurDeDieu extends User // Hérite de User
{
    use HasFactory;

    // Indiquer le nom de la table
    protected $table = 'serviteurs_de_dieu';

    protected $fillable = [
        'is_main',
        'id_card_recto',
        'id_card_verso',
        'user_id',
        'church_id',
    ];

    // Relations
    // Un serviteur de Dieu appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // // Un serviteur de Dieu appartient à une église
    // public function church()
    // {
    //     return $this->belongsTo(Church::class);
    // }


    // Un serviteur de Dieu appartient à une église
    public function church()
    {
        return $this->belongsTo(Church::class, 'church_id'); // 'church_id' est la clé étrangère dans la table 'serviteurs_de_dieu'
    }


    public function sujetsDeDiscussion()
    {
        return $this->belongsToMany(SujetDeDiscussion::class, 'serviteurs_de_dieu_sujets_de_discussion', 'serviteur_de_dieu_id', 'sujet_id')
            ->withPivot('Comment')
            ->withTimestamps();
    }


    public function addCommentToSujet(SujetDeDiscussion $sujet, string $comment)
    {
        $this->sujetsDeDiscussion()->attach($sujet->id, ['Comment' => $comment]);
    }
}

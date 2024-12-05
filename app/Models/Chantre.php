<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chantre extends Model
// class  Chantre extends User // Hérite de User
{
    use HasFactory;

    protected $fillable = [
        'manager',
        'description',
        'user_id',
        'church_id',
    ];

    // Relations
    // Un chantre appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un chantre appartient à une église
    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    // Un chantre peut participer à plusieurs sujets de discussion
    // public function sujetsDeDiscussion()
    // {
    //     return $this->belongsToMany(SujetDeDiscussion::class, 'chantres_sujets_de_discussion');
    // }


    // public function sujetsDeDiscussion()
    // {
    //     return $this->belongsToMany(SujetDeDiscussion::class, 'chantres_sujets_de_discussion')
    //         ->withPivot('Comment'); // Si tu as une colonne 'Comment' dans la table pivot
    // }



    public function sujetsDeDiscussion()
    {
        return $this->belongsToMany(SujetDeDiscussion::class, 'chantres_sujets_de_discussion', 'chantre_id', 'sujet_id')
            ->withPivot('Comment')
            ->withTimestamps();
    }





    public function addCommentToSujet(SujetDeDiscussion $sujet, string $comment)
    {
        $this->sujetsDeDiscussion()->attach($sujet->id, ['Comment' => $comment]);
    }


}

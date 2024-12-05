<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fidele extends Model
// class Fidele extends User // Hérite de User


{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'church_id',
    ];

    // Relations
    // Un fidèle appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // Un fidèle appartient à une église
    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    // Un fidèle peut participer à plusieurs sujets de discussion
    // public function sujetsDeDiscussion()
    // {
    //     return $this->belongsToMany(SujetDeDiscussion::class, 'fideles_sujets_de_discussion');
    // }

    // public function sujetsDeDiscussion()
    // {
    //     return $this->belongsToMany(SujetDeDiscussion::class, 'fideles_sujets_de_discussion')
    //         ->withPivot('Comment'); // Ajoute des colonnes supplémentaires si nécessaire
    // }



    public function sujetsDeDiscussion()
    {
        return $this->belongsToMany(SujetDeDiscussion::class, 'fideles_sujets_de_discussion', 'fidele_id', 'sujet_id')
            ->withPivot('Comment')
            ->withTimestamps();
    }




    public function addCommentToSujet(SujetDeDiscussion $sujet, string $comment)
    {
        $this->sujetsDeDiscussion()->attach($sujet->id, ['Comment' => $comment]);
    }
}

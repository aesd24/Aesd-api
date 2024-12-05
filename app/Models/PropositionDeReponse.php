<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropositionDeReponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'exact',
        'quiz_id',
    ];

    // Relations
    // Une proposition de réponse appartient à un quizz
    public function quizz()
    {
        return $this->belongsTo(Quizz::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temoignage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'body',
        'published_at',
        'is_anonymous',
        'user_id',
    ];

    // Relations
    // Un témoignage appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

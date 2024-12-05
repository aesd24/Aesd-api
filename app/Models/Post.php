<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'contenu',
        'image',
        'published_at',
        'servant_id',
    ];

    // Relations
    // Un post appartient à un serviteur de Dieu
    public function servant()
    {
        return $this->belongsTo(ServiteurDeDieu::class);
    }


    

    public function users()
    {
        return $this->belongsToMany(User::class, 'postes_users', 'poste_id', 'user_id');
    }
}

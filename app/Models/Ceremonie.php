<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ceremonie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'media',
        'event_date',
    ];


    /**
     * Relation plusieurs-à-plusieurs avec le modèle Church
     */
    public function churches()
    {
        return $this->belongsToMany(Church::class, 'church_ceremonies', 'ceremony_id', 'church_id')
            ->withPivot('periode_time')
            ->withTimestamps();
    }



    // public function churches()
    // {
    //     return $this->belongsTo(Church::class, 'id_eglise');
    // }
}

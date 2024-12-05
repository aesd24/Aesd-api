<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'file',
        'church_id',
    ];



    public function church()
    {
        return $this->belongsToMany(Church::class, 'church_programmes')
                    ->withPivot('periode_time') // Ajouter le champ de la table pivot
                    ->withTimestamps(); // Inclure les timestamps de la table pivot
    }






    /**
     * Relation avec l'église (Church).
     */
    // public function church()
    // {
    //     return $this->belongsTo(Church::class, 'id_eglise');
    // }

}

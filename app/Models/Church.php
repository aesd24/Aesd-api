<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Church extends Model
{

    protected $table = 'churches'; // Indiquez le nom correct de la table

    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'adresse',
        'logo',
        'is_main',
        'description',
        'owner_servant_id',
        'type_church',
        'categorie',
        'main_church_id',
        'attestation_file_path',
        'validation_status'
    ];


    // Relations
    // Une église peut avoir plusieurs serviteurs de Dieu
    // public function serviteursDeDieu()
    // {
    //     return $this->hasMany(ServiteurDeDieu::class);
    // }



    // Une église peut avoir plusieurs serviteurs de Dieu
    public function serviteursDeDieu()
    {
        return $this->hasMany(ServiteurDeDieu::class, 'church_id');
    }




    // Relation pour le serviteur principal (optionnel)
    public function serviteurPrincipal()
    {
        return $this->belongsTo(ServiteurDeDieu::class, 'owner_servant_id');
    }

    // Une église peut avoir plusieurs fidèles
    public function fideles()
    {
        return $this->hasMany(Fidele::class);
    }

    // Une église peut avoir plusieurs chantres
    public function chantres()
    {
        return $this->hasMany(Chantre::class);
    }

    // Une église peut avoir plusieurs cérémonies





    // public function ceremonies()
    // {
    //     return $this->hasMany(Ceremonie::class, 'id_eglise');
    // }





    // Relation plusieurs-à-plusieurs avec le modèle Programme
    public function programmes()
    {
        return $this->belongsToMany(Programme::class, 'church_programmes')
            ->withPivot('periode_time') // Ajouter le champ de la table pivot
            ->withTimestamps(); // Inclure les timestamps de la table pivot
    }



    // public function programmes()
    // {
    //     return $this->hasMany(Programme::class, 'id_eglise');
    // }


    // public function owner()
    // {
    //     return $this->belongsTo(User::class, 'owner_servant_id');
    // }




    public function ceremonies()
    {
        return $this->hasMany(Ceremonie::class, 'id_eglise');
    }
}

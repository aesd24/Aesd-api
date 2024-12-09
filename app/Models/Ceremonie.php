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
        'id_eglise'
    ];



    public function churches()
    {
        return $this->belongsTo(Church::class, 'id_eglise');
    }
}

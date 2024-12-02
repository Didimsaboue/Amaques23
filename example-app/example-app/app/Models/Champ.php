<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Champ extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'referentiel_id','signature'];

    public function referentiel()
    {
        return $this->belongsTo(Referentiel::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class);
    }
}



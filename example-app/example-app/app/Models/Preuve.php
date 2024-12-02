<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preuve extends Model
{
    use HasFactory;

    protected $fillable = ['critere_id', 'description'];

    public function critere()
{
    return $this->belongsTo(Critere::class, 'critere_id');
}

public function fichier()
{
    return $this->hasOne(Fichier::class, 'idpreuve');
}


    public function evaluations()
    {
        return $this->hasMany(EvaluationInterne::class, 'idpreuve');
    }






}

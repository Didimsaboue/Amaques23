<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Critere extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'reference_id', 'signature'];

    public function reference()
    {
        return $this->belongsTo(Reference::class, 'reference_id');
    }

    public function preuve()
{
    return $this->hasMany(Preuve::class, 'critere_id');
}

public function preuves()
{
    return $this->hasMany(Preuve::class, 'critere_id');
}



    public function evaluations()
    {
        return $this->hasMany(EvaluationInterne::class, 'idcritere');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    use HasFactory;

    protected $fillable = ['fichier', 'idfiliere', 'idpreuve'];

    public function preuve()
{
    return $this->belongsTo(Preuve::class, 'idpreuve');
}

    public function filiere()
    {
        return $this->belongsTo(Filière::class, 'idfiliere');
    }
}

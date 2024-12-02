<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'champ_id', 'signature'];

    public function champ()
    {
        return $this->belongsTo(Champ::class);
    }

    public function criteres()
    {
        return $this->hasMany(Critere::class, 'reference_id');
    }
}



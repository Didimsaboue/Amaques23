<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referentiel extends Model
{
    use HasFactory;

    protected $fillable = ['name','signature'];

    public function champs(): HasMany
    {
        return $this->hasMany(Champ::class);
    }
}
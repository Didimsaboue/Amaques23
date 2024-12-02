<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filière extends Model
{
    use HasFactory;
    
    public function departement()
    {
        return $this->belongsTo(département::class, 'départements_id');
    }

    public function evaluationsInternes(): HasMany
    {
        return $this->hasMany(EvaluationInterne::class, 'idfiliere');
    }
}

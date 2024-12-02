<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiereinvite extends Model
{
    use HasFactory;

    protected $table = 'filieresinvites'; // Ensure this matches your table name

    protected $fillable = [
        'idfiliere', 
        'idcampagne', 
        'date_debut',
        'date_fin',
        'invitation',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'invitation' => 'boolean',
    ];

    public function invitation()
    {
        return $this->hasMany(Invitation::class);
    }

    public function filiere()
    {
        return $this->belongsTo(Filière::class, 'idfiliere');
    }
}

   
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class evaluationinterne extends Model
{
    use HasFactory;

    protected $table = 'evaluationinterne'; // Ensure this matches your table name

    protected $fillable = [
        'idcritere', 
        'idchamps', 
        'idcampagne', 
        'idpreuve', 
        'idfiliere', 
        'idfiliereinvite', 
        'score', 
        'commentaire'
    ];

    public function filiereinvite()
    {
        return $this->belongsTo(Filiereinvite::class, 'idfiliereinvite');
    }

    public function invitation()
    {
        return $this->hasMany(Invitation::class);
    }

    public function champ()
    {
        return $this->belongsTo(Champ::class, 'idchamps');
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filière::class, 'idfiliere');
    }

    public function critere()
{
    return $this->belongsTo(Critere::class, 'idcritere');
}

public function preuve()
{
    return $this->belongsTo(Preuve::class, 'idpreuve');
}   


    
}

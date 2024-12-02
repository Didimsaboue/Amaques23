<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class département extends Model
{
    use HasFactory;
    public function etablissement()
    {
        return $this->belongsTo(etablissement::class, 'etablissements_id');
    }
}

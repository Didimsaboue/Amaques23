<?php

namespace App\Http\Controllers;

use App\Models\EvaluationInterne;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function champs($filiereId)
    {
        // Récupérer les champs évalués pour la filière donnée
        $champs = EvaluationInterne::where('idfiliere', $filiereId)
            ->with('champ') // Eager load the related champ data
            ->get()
            ->unique('idchamps'); // Ensure unique fields

        return view('dashadmin.champs', compact('champs', 'filiereId'));
    }
}

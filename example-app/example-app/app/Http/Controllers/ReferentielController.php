<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Referentiel;
use App\Models\Champ;
use App\Models\Critere;
use App\Models\Reference;

class ReferentielController extends Controller
{
    public function index()
    {
        $referentiels = Referentiel::all();
        return view('dashadmin.dashboard', compact('referentiels'));
    }

    public function getData($id)
    {
        $champs = Champ::where('referentiel_id', $id)->get();
    
        $details = [];
        $totalReferences = 0;
        $totalCriteres = 0;

        foreach ($champs as $champ) {
            $referencesCount = Reference::where('champ_id', $champ->id)->count();
            $criteresCount = Critere::whereHas('reference', function($query) use ($champ) {
                $query->where('champ_id', $champ->id);
            })->count();

            $details[] = [
                'champs' => $champ->name,
                'references' => $referencesCount,
                'criteres' => $criteresCount,
            ];

            $totalReferences += $referencesCount;
            $totalCriteres += $criteresCount;
        }
    
        $champsCount = $champs->count();
    
        return response()->json([
            'champsCount' => $champsCount,
            'criteresCount' => $totalCriteres,
            'referencesCount' => $totalReferences,
            'details' => $details,
            'totalReferences' => $totalReferences,
            'totalCriteres' => $totalCriteres,
        ]);
    }

}

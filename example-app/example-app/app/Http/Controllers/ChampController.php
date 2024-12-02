<?php

namespace App\Http\Controllers;

use App\Models\EvaluationInterne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Champ;

class ChampController extends Controller
{
    public function resultats($filiereInviteId, $champId)
    {
        // Charger tous les critères avec leurs preuves et fichiers associés pour la filière invite et le champ spécifique
        $criteria = EvaluationInterne::where('idfiliere', $filiereInviteId)
            ->where('idchamps', $champId)
            ->with(['critere', 'critere.preuves', 'critere.preuves.fichier'])
            ->get();
    
        \Log::info('Criteria Data:', ['criteria' => $criteria]);
    
        // Récupérer le nom du champ associé
        $champ = Champ::find($champId);
        $nomChampAssocie = $champ->name ?? 'No Champ Associated';
    
        $criteresScores = [];
    
        foreach ($criteria as $evaluation) {
            $critereId = $evaluation->critere->id;
    
            // Ajouter ou mettre à jour le score du critère
            if (!isset($criteresScores[$critereId])) {
                $criteresScores[$critereId] = [
                    'critere' => $evaluation->critere->signature,
                    'score' => 0,
                ];
            }
    
            // Ajouter le score actuel à la somme totale
            $criteresScores[$critereId]['score'] += $evaluation->score;
        }
    
        // Calcul du taux de conformité pour le champ
        $totalEvaluations = EvaluationInterne::where('idchamps', $champId)
            ->where('idfiliere', $filiereInviteId)
            ->count();
        $positiveEvaluations = EvaluationInterne::where('idchamps', $champId)
            ->where('idfiliere', $filiereInviteId)
            ->where('score', 2)
            ->count();
        $tauxConformite = ($totalEvaluations > 0) ? ($positiveEvaluations * 100 / $totalEvaluations) : 0;
    
        return view('dashadmin.resultats', compact('criteria', 'criteresScores', 'filiereInviteId', 'champId', 'nomChampAssocie', 'tauxConformite'));
    }
    
    

    public function downloadFile($filename)
    {
        $path = storage_path('app/preuves/' . $filename);
        \Log::info('Download File Path:', ['path' => $path]);

        if (!file_exists($path)) {
            \Log::error('File not found:', ['path' => $path]);
            abort(404, 'File not found.');
        }

        return response()->download($path);
    }

    
    
}

<?php
//CampaignController.php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Models\EvaluationInterne;
use App\Models\Filière;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Invitation::get();
        return view('dashadmin.index', compact('campaigns'));
    }

    public function filieres($campaignId)
    {
        // Récupérer toutes les filières
        $filieres = Filière::all();

        $filiereStats = [];

        foreach ($filieres as $filiere) {
            // Vérifier si la filière a été évaluée dans EvaluationInterne pour la campagne donnée
            $hasEvaluations = EvaluationInterne::where('idcampagne', $campaignId)
                ->where('idfiliere', $filiere->id)
                ->exists();

            if ($hasEvaluations) {
                // Récupérer les IDs des champs évalués pour cette filière
                $evaluatedChampsIds = EvaluationInterne::where('idcampagne', $campaignId)
                    ->where('idfiliere', $filiere->id)
                    ->pluck('idchamps')
                    ->unique();

                // Calculer le taux de conformité pour chaque champ
                $tauxConformites = [];
                foreach ($evaluatedChampsIds as $champId) {
                    $totalEvaluations = EvaluationInterne::where('idchamps', $champId)
                        ->where('idcampagne', $campaignId)
                        ->where('idfiliere', $filiere->id)
                        ->count();

                    $positiveEvaluations = EvaluationInterne::where('idchamps', $champId)
                        ->where('idcampagne', $campaignId)
                        ->where('idfiliere', $filiere->id)
                        ->where('score', '>', 0)
                        ->count();

                    $tauxConformite = $totalEvaluations > 0 ? ($positiveEvaluations * 100 / $totalEvaluations) : 0;
                    $tauxConformites[$champId] = $tauxConformite;
                }

                // Calculer le taux de conformité moyen pour la filière
                $moyenneConformite = count($tauxConformites) > 0 ? array_sum($tauxConformites) / count($tauxConformites) : 0;

                // Ajouter les statistiques de la filière
                $filiereStats[] = [
                    'id' => $filiere->id,
                    'filiere' => $filiere->nom,
                    'nombreChampsEvalues' => count($evaluatedChampsIds),
                    'moyenneTauxConformite' => $moyenneConformite
                ];
            }
        }

        return view('dashadmin.filieres', compact('filiereStats'));
    }
}

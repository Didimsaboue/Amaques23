<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Champ;
use App\Models\User;
use App\Models\EvaluationInterne;
use App\Models\Invitation;
use App\Models\Fichier;
use App\Models\Filiereinvite;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Worksheet\Chart;
use QuickChart;

class Homecontroller extends Controller
{
    public function indexevaluation()
    {
        $user = auth()->user();
        $idFiliere = $user->filières_id;
        $isUserInvited = $user->invitation == 1;
        $hasActiveInvitation = $isUserInvited && Invitation::where('statue', 1)->exists();
        $champs = Champ::with('references.criteres.preuves')->get();
        $champsEvaluer = $champs->filter(function ($champ) use ($idFiliere) {
            foreach ($champ->references as $reference) {
                foreach ($reference->criteres as $critere) {
                    foreach ($critere->preuves as $preuve) {
                        if (EvaluationInterne::where('idpreuve', $preuve->id)->where('idfiliere', $idFiliere)->exists()) {
                            return true;
                        }
                    }
                }
            }
            return false;
        });
        $CHNEV = $champs->diff($champsEvaluer);
        $champNonEvaluer = $CHNEV->first();
        $tauxConformites = [];
        $nomsChamps = [];
        $moyenneConformite = 0;

        if ($champNonEvaluer === null) { // Tous les champs sont évalués
            foreach ($champs as $champ) {
                $totalEvaluations = EvaluationInterne::where('idchamps', $champ->id)->where('idfiliere', $idFiliere)->count();
                $positiveEvaluations = EvaluationInterne::where('idchamps', $champ->id)->where('idfiliere', $idFiliere)->where('score', '>', 0)->count(); // Assuming positive score is > 0
                $tauxConformite = $totalEvaluations > 0 ? ($positiveEvaluations * 100 / $totalEvaluations) : 0;
                $tauxConformites[$champ->id] = $tauxConformite;
                $nomsChamps[] = $champ;
            }
            $moyenneConformite = count($tauxConformites) > 0 ? array_sum($tauxConformites) / count($tauxConformites) : 0;
        }


        return view('layout.liste', compact('CHNEV', 'champNonEvaluer', 'hasActiveInvitation', 'tauxConformites', 'moyenneConformite', 'nomsChamps'));
    }
    public function evaluate(Request $request)
    {
        $data = $request->all();
        $campaghe =  Invitation::where('statue', 1)->first();
        foreach ($data['evaluations'] as $evaluation) {
            $score = 0;
            if ($evaluation['value'] === 'oui') {
                $score = 2;
            } elseif ($evaluation['value'] === 'non') {
                $score = -1;
            }
            $user = Auth::user();
            $result = evaluationinterne::create([
                'idcritere' => $evaluation['idcritere'],
                'idpreuve' => $evaluation['idpreuve'],
                'idfiliere' => $user->filières_id,
                'idchamps' => $data['idchamps'], // Ajouter idchamps ici
                'idcampagne'=>$campaghe->id,
                'score' => $score,
                'commentaire' => $evaluation['commentaire'] ?? null,
            ]);
           
            if ($request->hasFile('file-' . $evaluation['idpreuve'])) {
                $filePath = $request->file('file-' . $evaluation['idpreuve'])->store('preuves');
    
                Fichier::create([
                    'fichier' => $filePath,
                    'idpreuve' => $evaluation['idpreuve'],
                    'idfiliere' => $user->filières_id,
                ]);
            }
        }
    
        return redirect('/scores_champ');
    }
    
    public function getScores()
    {
        $user = auth()->user();
        $idFiliere = $user->filières_id;
    
        // Récupérer les champs évalués
        $champsEvaluer = EvaluationInterne::where('idfiliere', $idFiliere)
                                          ->groupBy('idchamps')
                                          ->pluck('idchamps');
    
        $result = [];
    
        // Vérifier s'il n'y a aucun champ évalué pour cet utilisateur
        if ($champsEvaluer->isEmpty()) {
            $message = "Vous n'avez pas encore évalué de champs.";
            return response()->json(['message' => $message], 200);
        }
    
        foreach ($champsEvaluer as $idchamps) {
            $champ = Champ::with(['references.criteres'])->find($idchamps);
            $criteresScores = [];
    
            foreach ($champ->references as $reference) {
                foreach ($reference->criteres as $critere) {
                    $score = EvaluationInterne::where('idcritere', $critere->id)
                                              ->where('idchamps', $idchamps)
                                              ->where('idfiliere', $idFiliere)
                                              ->sum('score');
                    $criteresScores[] = [
                        'critere' => $critere->signature, // assuming 'nom' is the name of the critere
                        'score' => $score
                    ];
                }
            }
    
            // Calcul du taux de conformité
            $totalEvaluations = EvaluationInterne::where('idchamps', $idchamps)
                                                 ->where('idfiliere', $idFiliere)
                                                 ->count();
            $positiveEvaluations = EvaluationInterne::where('idchamps', $idchamps)
                                                    ->where('idfiliere', $idFiliere)
                                                    ->where('score', 2)
                                                    ->count();
            $tauxConformite = ($totalEvaluations > 0) ? ($positiveEvaluations * 100 / $totalEvaluations) : 0;
    
            $result[] = [
                'champ' => $champ->name, // assuming 'nom' is the name of the champ
                'criteres' => $criteresScores,
                'tauxConformite' => $tauxConformite
            ];
        }
    
        return response()->json($result, 200);
    }
    public function generatePDF()
    {
        // Retrieve data via getDataForPdf method
        $data = $this->getDataForPdf();
    
        // Initialize chart images and compliance rate texts
        $chartImages = [];
        $tauxConformiteText = [];
        $labelsChamp = [];
        $tauxConformiteChamp = [];
    
        // Loop through each field from getDataForPdf() to generate individual charts
        foreach ($data['champs'] as $champData) {
            $labels = [];
            $scores = [];
            $colors = [];
    
            foreach ($champData['graph'] as $critereScore) {
                $labels[] = $critereScore['critere'];
                $score = (int) round($critereScore['score']);
                $scores[] = $score;
    
                // Define colors based on score to simulate a 3D effect
                $colors[] = $score > 0 ? '#078C03' : ($score < 0 ? '#F2E205' : '#F20505'); // Green for positive, Yellow for negative, Red for zero
            }
    
            // Create individual charts using QuickChart
            $quickChart = new QuickChart([
                'width' => 600,
                'height' => 400,
            ]);
    
            // Set chart configuration with a 3D appearance style
            $quickChart->setConfig("{
                type: 'bar',
                data: {
                    labels: " . json_encode($labels) . ",
                    datasets: [{
                        label: 'Scores per Criterion (3D effect)',
                        data: " . json_encode($scores) . ",
                        backgroundColor: " . json_encode($colors) . ",
                        barThickness: 35,
                        borderWidth: 2,
                        borderColor: 'rgba(0,0,0,0.5)', // Add border for 3D depth effect
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true, min: " . (min($scores) - 1) . ", max: " . (max($scores) + 1) . " }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutBounce'
                    },
                    elements: {
                        bar: {
                            borderSkipped: 'bottom',
                            borderRadius: 5 // Rounded corners for a 3D appearance
                        }
                    }
                }
            }");
    
            // Encode chart image in base64
            $chartImage = base64_encode(file_get_contents($quickChart->getUrl()));
            $chartImages[$champData['name']] = 'data:image/png;base64,' . $chartImage;
            $tauxConformiteText[$champData['name']] = round($champData['tauxConformite'] ?? 0, 2) . '% conformity';
            $labelsChamp[] = $champData['name'];
            $tauxConformiteChamp[] = round($champData['tauxConformite'] ?? 0, 2);
        }
    
        // Calculate the overall conformity rate
        $moyenneConformite = !empty($tauxConformiteChamp) ? array_sum($tauxConformiteChamp) / count($tauxConformiteChamp) : 0;
        $labelsChamp[] = 'Global Average';
        $tauxConformiteChamp[] = round($moyenneConformite, 2);
    
        // Create an overall chart using QuickChart
        $quickChartGlobal = new QuickChart(['width' => 600, 'height' => 400]);
        $backgroundColors = str_repeat('"#F2E205", ', count($labelsChamp) - 1) . '"#0511F2"';
        $quickChartGlobal->setConfig("{
            type: 'bar',
            data: {
                labels: " . json_encode($labelsChamp) . ",
                datasets: [{
                    label: 'Compliance Rate by Field',
                    data: " . json_encode($tauxConformiteChamp) . ",
                    backgroundColor: [$backgroundColors],
                    barThickness: 35,
                    borderWidth: 2,
                    borderColor: 'rgba(0,0,0,0.5)'
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: true, position: 'top' } },
                animation: { duration: 1500, easing: 'easeOutBounce' }
            }
        }");
    
        // Get the global chart image in base64
        $chartImageGlobal = base64_encode(file_get_contents($quickChartGlobal->getUrl()));
        $data['chartImages'] = $chartImages;
        $data['tauxConformiteText'] = $tauxConformiteText;
        $data['chartBase64Global'] = 'data:image/png;base64,' . $chartImageGlobal;
    
        // Generate the PDF with the prepared data
        $pdf = PDF::loadView('layout.rapport-auto-evaluation', $data)
                  ->setPaper('a4', 'portrait');
    
        // Download the PDF
        return $pdf->download('rapport-auto-evaluation.pdf');
    }
    
    
    private function getDataForPdf()
    {
        $user = auth()->user();
        $idFiliere = $user->filières_id;
        $champs = Champ::with('references.criteres.preuves')->get();
        $campagneIds = Invitation::where('statue', 1)
            ->pluck('id');
    
        $data = [
            'title' => 'Rapport d\'Autoévaluation',
            'authority' => 'Autorité Mauritanienne d\'Assurance Qualité de l\'Enseignement Supérieur',
            'champs' => [],
        ];
    
        foreach ($champs as $champ) {
            $criteresScores = [];
            $totalScore = 0;
            $totalEpreuves = 0;
    
            $champData = [
                'name' => $champ->name,
                'references' => [],
                'graph' => [],
                'taux_de_conformite' => 0, // Initialisation du taux de conformité
            ];
    
            foreach ($champ->references as $reference) {
                $referenceData = [
                    'signature' => $reference->signature,
                    'nom' => $reference->nom,
                    'criteres' => [],
                ];
    
                foreach ($reference->criteres as $critere) {
                    $critereData = [
                        'signature' => $critere->signature,
                        'nom' => $critere->nom,
                        'preuves' => [],
                    ];
    
                    foreach ($critere->preuves as $preuve) {
                        $evaluation = EvaluationInterne::where('idpreuve', $preuve->id)
                            ->where('idfiliere', $idFiliere)
                            ->whereIn('idcampagne', $campagneIds) // Attention : utilisez whereIn pour plusieurs campagnes
                            ->first();
    
                        $score = $evaluation->score ?? 0;
                        $response = $this->mapScoreToResponse($score);
    
                        $preuveData = [
                            'description' => $preuve->description,
                            'response' => ucfirst($response),
                            'commentaire' => ($score === 0) ? ($evaluation->commentaire ?? '') : '',
                            'fichier' => $evaluation && $evaluation->fichier ? Storage::url($evaluation->fichier->fichier) : null,
                        ];
    
                        // Ajouter la somme des scores et le nombre d'épreuves
                        $totalScore += $score;
                        $totalEpreuves++;
    
                        $critereData['preuves'][] = $preuveData;
                    }
    
                    // Calcul du score total par critère pour le graphe
                    $scoreTotalCritere = EvaluationInterne::where('idcritere', $critere->id)
                        ->where('idfiliere', $idFiliere)
                        ->whereIn('idcampagne', $campagneIds)
                        ->sum('score');
    
                    $criteresScores[] = [
                        'critere' => $critere->signature,
                        'score' => $scoreTotalCritere,
                    ];
    
                    $referenceData['criteres'][] = $critereData;
                }
    
                $champData['references'][] = $referenceData;
            }
    
            // Calcul du taux de conformité pour le champ
            if ($totalEpreuves > 0) {
                $tauxConformite = ($totalScore*100) / $totalEpreuves;
                $champData['tauxConformite'] = max(0, $tauxConformite);
            }
    
            // Ajout du graphe pour le champ
            $champData['graph'] = $criteresScores;
    
            $data['champs'][] = $champData;
        }
    
        return $data;
    }
    
    

private function mapScoreToResponse($score)
{
    switch ($score) {
        case 2:
            return 'oui';
        case 0:
            return 'na';
        case -1:
            return 'non';
        default:
            return 'non défini';
    }
}
// private function validatePdfData(array $data): bool
// {
//     // Check required variables
//     if (
//         empty($data['title']) ||
//         empty($data['authority']) ||
//         empty($data['champs']) ||
//         !is_array($data['champs']) ||
//         !isset($data['chartImages']) ||
//         !isset($data['tauxConformiteText']) ||
//         empty($data['chartBase64Global'])
//     ) {
//         return false;
//     }

//     // Validate structure of $champs
//     foreach ($data['champs'] as $champ) {
//         if (
//             !isset($champ['name']) ||
//             !is_array($champ['references'])
//         ) {
//             return false;
//         }

//         foreach ($champ['references'] as $reference) {
//             if (
//                 !isset($reference['signature'], $reference['nom']) ||
//                 !is_array($reference['criteres'])
//             ) {
//                 return false;
//             }

//             foreach ($reference['criteres'] as $critere) {
//                 if (
//                     !isset($critere['signature'], $critere['nom'], $critere['preuves']) ||
//                     !is_array($critere['preuves'])
//                 ) {
//                     return false;
//                 }

//                 foreach ($critere['preuves'] as $preuve) {
//                     if (
//                         !isset($preuve['description'], $preuve['response'], $preuve['commentaire'])
//                     ) {
//                         return false;
//                     }
//                 }
//             }
//         }
//     }

//     // If all checks pass, return true
//     return true;
// }
}

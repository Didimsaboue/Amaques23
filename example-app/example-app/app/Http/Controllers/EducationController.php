<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\Etablissement;
use App\Models\département;
use App\Models\Filière;
class EducationController extends Controller
{
    public function indexInstitutions()
    {
        $institutions = Institution::all();
        return view('dashadmin.institution', compact('institutions'));
    }
    public function storeInstitution(Request $request)
{
    // Valider les données du formulaire
    $request->validate([
        'nom' => 'required|string|max:255',
    ]);

    // Créer une nouvelle instance d'institution
    $institution = new Institution();
    $institution->nom = $request->nom;
    $institution->save();

    // Rediriger avec un message de succès
    return redirect()->route('institutions.index')->with('success', 'L\'institution a été ajoutée avec succès.');
}
public function updateInstitution(Request $request, $id)
{
    // Validation des données du formulaire
    $request->validate([
        'nom' => 'required|string|max:255',
    ]);

    try {
        // Recherche de l'institution à mettre à jour dans la base de données
        $institution = Institution::findOrFail($id);
        
        // Mise à jour des informations de l'institution
        $institution->nom = $request->nom;
        $institution->save();

        // Redirection avec un message de succès
        return redirect()->route('institutions.index')->with('success', 'L\'institution a été mise à jour avec succès.');
    } catch (\Exception $e) {
        // En cas d'erreur, redirection avec un message d'erreur
        return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la mise à jour de l\'institution. Veuillez réessayer.');
    }

}
public function destroyInstitution($id)
    {
        try {
            $institution = Institution::findOrFail($id);
            $institution->delete();
            return redirect()->route('institutions.index')->with('success', 'L\'institution a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la suppression de l\'institution. Veuillez réessayer.');
        }
    }
    public function indexEtablissement()
    {
        $etablissements = Etablissement::with('Institution:id,nom')->get();
        $institutions = Institution::all();
        return view('dashadmin.etablissement', compact('etablissements', 'institutions'));
    }
    public function updateEtablissement(Request $request, $id)
    {
        // Validation des données du formulaire
        $request->validate([
            'nom' => 'required|string|max:255',
            'institution' => 'nullable|integer|exists:institutions,id', // Permettre institution d'être null et vérifier qu'elle existe
        ]);
    
        try {
            // Récupérer l'établissement à mettre à jour
            $etablissement = Etablissement::findOrFail($id);
            
            // Mettre à jour les champs de l'établissement
            $etablissement->nom = $request->nom;
            $etablissement->institution_id = $request->institution; // Permettre null ici
    
            // Sauvegarder les modifications
            $etablissement->save();
    
            // Redirection avec un message de succès
            return redirect()->route('etablissement.index')->with('success', 'Établissement mis à jour avec succès!');
        } catch (\Exception $e) {
            // Redirection avec un message d'erreur en cas d'échec
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la mise à jour de l\'établissement. '.$e->getMessage());
        }
    }
    public function storeEtablissement(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'institution' => 'nullable|exists:institutions,id',
        ]);
        $etablissement = new Etablissement();
        $etablissement->nom = $validated['nom'];
        $etablissement->institution_id = $validated['institution']; 
        $etablissement->save();

        return redirect()->route('etablissement.index')->with('success', 'Établissement ajouté avec succès');
    }
public function destroyEtablissement($id)
    {
        try {
            $etablissement = Etablissement::findOrFail($id);
            $etablissement->delete();
            return redirect()->route('etablissement.index')->with('success', 'L\'Établissement a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la suppression de l\'Établissement. Veuillez réessayer.'.$e->getMessage());
        }
    }
    public function indexDepartement()
    {
        $departements = Département::with('etablissement.institution')->get();
        $etablissements = Etablissement::all();
        return view('dashadmin.departement', compact('departements','etablissements'));
    }
    public function updateDepartement(Request $request, $id)
    {
        // Validation des données du formulaire
        $request->validate([
            'nom' => 'required|string|max:255',
            'etablissement' => 'nullable|exists:etablissements,id', // Permettre institution d'être null et vérifier qu'elle existe
        ]);
    
        try {
            // Récupérer l'établissement à mettre à jour
            $département = Département::findOrFail($id);
            
            // Mettre à jour les champs de l'établissement
            $département->nom = $request->nom;
            $département->etablissements_id  = $request->etablissement; // Permettre null ici
    
            // Sauvegarder les modifications
            $département->save();
    
            // Redirection avec un message de succès
            return redirect()->route('departement.index')->with('success', 'Établissement mis à jour avec succès!');
        } catch (\Exception $e) {
            // Redirection avec un message d'erreur en cas d'échec
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la mise à jour de l\'établissement. '.$e->getMessage());
        }
    }
    public function storeDepartement(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'etablissement' => 'nullable|exists:etablissements,id',
        ]);
        $etablissement = new Département();
        $etablissement->nom = $validated['nom'];
        $etablissement->etablissements_id = $validated['etablissement']; 
        $etablissement->save();

        return redirect()->route('departement.index')->with('success', 'Établissement ajouté avec succès');
    }
public function destroyDepartement($id)
    {
        try {
            $departement = Département::findOrFail($id);
            $departement->delete();
            return redirect()->route('departement.index')->with('success', 'L\'Établissement a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la suppression de l\'Établissement. Veuillez réessayer.');
        }
    }
    public function indexFiliere()
    {
        $filieres = Filière::with('departement.etablissement.institution')->get();
        $departements = département::all();
        return view('dashadmin.filiere', compact('filieres','departements'));
    }
    public function updateFiliere(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'departements' => 'nullable|exists:départements,id', 
            'date_habilitation' => 'nullable|date',
            'date_accreditation' => 'nullable|date',
            'date_fin_accreditation' => 'nullable|date',
        ]);
    
        try {
            $filiere = Filière::findOrFail($id);
            $filiere->nom = $request->nom;
            $filiere->départements_id = $request->departements; // Permettre null ici
            $filiere->date_habilitation = $request->date_habilitation;
            $filiere->date_accreditation = $request->date_accreditation;
            $filiere->date_fin_accreditation = $request->date_fin_accreditation;
    
            // Sauvegarder les modifications
            $filiere->save();
    
            // Redirection avec un message de succès
            return redirect()->route('filiere.index')->with('success', 'La filière a été mise à jour avec succès!');
        } catch (\Exception $e) {
            // Redirection avec un message d'erreur en cas d'échec
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la mise à jour de la Filière. '.$e->getMessage());
        }
    }
    
    public function storeFiliere(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'departements' => 'nullable|exists:départements,id',
            'date_habilitation' => 'nullable|date',
            'date_accreditation' => 'nullable|date',
            'date_fin_accreditation' => 'nullable|date|after_or_equal:date_accreditation',
        ]);
    
        // Ensure date_fin_accreditation is 4 years after date_accreditation
        if ($request->date_accreditation) {
            $date_accreditation = new \DateTime($request->date_accreditation);
            $date_fin_accreditation = new \DateTime($request->date_fin_accreditation);
            $date_accreditation->modify('+4 years');
            if ($date_fin_accreditation < $date_accreditation) {
                return redirect()->back()->withErrors(['date_fin_accreditation' => 'La date de fin d\'accréditation doit être supérieure à la date d\'accréditation de 4 ans']);
            }
        }
    
        $filiere = new Filière();
        $filiere->nom = $validated['nom'];
        $filiere->départements_id = $validated['departements'];
        $filiere->date_habilitation = $validated['date_habilitation'];
        $filiere->date_accreditation = $validated['date_accreditation'];
        $filiere->date_fin_accreditation = $validated['date_fin_accreditation'];
        $filiere->save();
    
        return redirect()->route('filiere.index')->with('success', 'La filière a été ajoutée avec succès');
    }
    
    public function destroyFiliere($id)
    {
        try {
            $departement = Filière::findOrFail($id);
            $departement->delete();
            return redirect()->route('filiere.index')->with('success', 'La filière a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la suppression de la filière. Veuillez réessayer.'.$id);
        }
    }
    public function indexFiliereD()
    {
        $filieres = Filière::with('departement.etablissement.institution')
                            ->where('doctorat', 1)  
                            ->get();
        $filieresChoix = Filière::where('master', 1)
                            ->where('doctorat', 0)
                            ->get();
        return view('dashadmin.doctora', compact('filieres', 'filieresChoix'));
    }
    public function indexFiliereM()
    {
        $filieres = Filière::with('departement.etablissement.institution') ->where('master', 1)->get();
        $filieresChoix = Filière::with('departement.etablissement.institution') ->where('master', 0)->get();
        return view('dashadmin.master', compact('filieres','filieresChoix'));
    }
    public function storeFiliereD(Request $request)
    {
        $request->validate([
            'filiere' => 'exists:filières,id', // Permettre institution d'être null et vérifier qu'elle existe
        ]);
        try {
            $filiere = Filière::findOrFail($request->filiere);
            $filiere->doctorat = 1;
            $filiere->save();
            return redirect()->route('filiere.indexD')->with('success', 'La filière a été ajoutée aux filières de doctorat.');
        } catch (\Exception $e) {
            // Redirection avec un message d'erreur en cas d'échec
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de l\'ajout de la filière aux filières de doctorat.');
        }
    }
    public function storeFiliereM(Request $request)
    {
        $request->validate([
            'filiere' => 'exists:filières,id', // Permettre institution d'être null et vérifier qu'elle existe
        ]);
        try {
            $filiere = Filière::findOrFail($request->filiere);
            $filiere->master = 1;
            $filiere->save();
            return redirect()->route('filiere.indexM')->with('success', 'La filière a été ajoutée aux filières de master.');
        } catch (\Exception $e) {
            // Redirection avec un message d'erreur en cas d'échec
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de l\'ajout de la filière aux filières de master.'.$e->getMessage());
        }
    }
    public function destroyFiliereD($id)
    {
        try {
            $filiere = Filière::findOrFail($id);
            $filiere->doctorat=0;
            $filiere->save();
            return redirect()->route('filiere.indexD')->with('success', 'La filière a été supprimée avec succès des filières de doctorat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la suppression de la filière. Veuillez réessayer.'.$id);
        }
    }
    public function destroyFiliereM($id)
    {
        try {
            $filiere = Filière::findOrFail($id);
            $filiere->master=0;
            $filiere->doctorat=0;
            $filiere->save();
            return redirect()->route('filiere.indexM')->with('success', 'La filière a été supprimée avec succès des filières de master.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la suppression de la filière. Veuillez réessayer.'.$id);
        }
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Referentiel;
use Illuminate\Support\Facades\Hash;
use App\Models\Etablissement;
use App\Models\département;
use App\Models\Filière;
class RAQController extends Controller

{
    public function index()
    {
        $referentiels = Referentiel::all();
        return view('RAQ.dashboard', compact('referentiels'));
    }
       
public function update_profilR(Request $request)
{
    $user = Auth::user();
    
    // Validation des données
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'role' => 'required|string|in:RAQ,evaluateur_i,evaluateur_e',
    ]);

    // Mise à jour des informations
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->role = $request->input('role');
    $user->save();

    // Mettre à jour les données de session
    session([
        'user_name' => $user->name,
        'user_email' => $user->email,
        'user_role' => $user->role,
    ]);

    return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
}

public function updatePasswordR(Request $request)
    {
        $user = Auth::user();

        // Vérifier que le mot de passe actuel est correct
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Validation des données
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

      

        

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return redirect()->back()->with('success', 'Mot de passe mis à jour avec succès.');
    }
    public function indexDepartementR()
    {
        $user = Auth::user();
        $etablissement = $user->établissement;
    
        // Vérifiez si l'établissement existe
        if ($etablissement) {
            $institution = $etablissement->institution;
        } else {
            return redirect()->back()->with('error', 'Aucun établissement associé à cet utilisateur.');
        }
    
        // Récupérer les départements associés à l'établissement de l'utilisateur
        $departements = Département::where('etablissements_id', $etablissement->id)
            ->with('etablissement.institution')->get();
    
        return view('RAQ.departement', compact('departements', 'etablissement', 'institution'));
    }
    
    
    public function updateDepartementR(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);
    
        $etablissementId = Auth::user()->idetablissements;
    
        // Récupérer le département uniquement si lié à l'établissement de l'utilisateur connecté
        $departement = Département::where('id', $id)->where('etablissements_id', $etablissementId)->firstOrFail();
        
        $departement->nom = $request->nom;
        $departement->save();
    
        return redirect()->route('departement.indexR')->with('success', 'Département mis à jour avec succès');
    }
    
    public function storeDepartementR(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);
    
        $etablissementId = Auth::user()->idetablissements; 
    
        $departement = new Département();
        $departement->nom = $validated['nom'];
        $departement->etablissements_id = $etablissementId; // Lier le département à l'établissement de l'utilisateur
        $departement->save();
    
        return redirect()->route('RAQ.indexR')->with('success', 'Département ajouté avec succès');
    }
    
    public function destroyDepartementR($id)
    {
        $etablissementId = Auth::user()->idetablissements;
    
        // Récupérer et supprimer uniquement le département appartenant à l'établissement de l'utilisateur
        $departement = Département::where('id', $id)->where('etablissements_id', $etablissementId)->firstOrFail();
        $departement->delete();
    
        return redirect()->route('departement.indexR')->with('success', 'Département supprimé avec succès');
    }
    
    public function indexFiliereR()
    {
        $user = Auth::user();
        $etablissement = $user->établissement;
        $institution = $etablissement->institution; 
        $filieres = Filière::whereHas('departement', function($query) use ($etablissement) {
            $query->where('etablissements_id', $etablissement->id);
        })->with('departement.etablissement.institution')->get();
        $departements = Département::where('etablissements_id', $etablissement->id)
        ->with('etablissement.institution')->get();
    
        return view('RAQ.filiere', compact('filieres', 'departements', 'etablissement', 'institution'));
    }
    
    public function updateFiliereR(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'departements' => 'required|exists:départements,id',
            'date_habilitation' => 'nullable|date',
        ]);
    
        $etablissementId = Auth::user()->idetablissements;
    
        // Assurer que la filière et le département sont liés à l'établissement de l'utilisateur RAQ
        $filiere = Filière::whereHas('departement', function($query) use ($etablissementId) {
            $query->where('etablissements_id', $etablissementId);
        })->findOrFail($id);
    
        $filiere->nom = $request->nom;
        $filiere->départements_id = $request->departements;
        $filiere->date_habilitation = $request->date_habilitation;
        $filiere->save();
    
        return redirect()->route('filiere.indexR')->with('success', 'Filière mise à jour avec succès');
    }
    
    
    public function storeFiliereR(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'departements' => 'required|exists:départements,id',
            'date_habilitation' => 'nullable|date',
        ]);
    
        $etablissementId = Auth::user()->idetablissements;
    
        // Assurer que le département appartient à l'établissement de l'utilisateur RAQ
        $departement = Département::where('id', $validated['departements'])
            ->where('etablissements_id', $etablissementId)
            ->firstOrFail();
    
        $filiere = new Filière();
        $filiere->nom = $validated['nom'];
        $filiere->départements_id = $departement->id;
        $filiere->date_habilitation = $validated['date_habilitation'];
        $filiere->save();
    
        return redirect()->route('filiere.indexR')->with('success', 'Filière ajoutée avec succès');
    }
    
    
    public function destroyFiliereR($id)
    {
        $etablissementId = Auth::user()->idetablissements;
    
        // Récupérer et supprimer uniquement la filière appartenant à l'établissement de l'utilisateur
        $filiere = Filière::whereHas('departement', function($query) use ($etablissementId) {
            $query->where('etablissements_id', $etablissementId);
        })->findOrFail($id);
    
        $filiere->delete();
    
        return redirect()->route('filiere.indexR')->with('success', 'Filière supprimée avec succès');
    }
    
}

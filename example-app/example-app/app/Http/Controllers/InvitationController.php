<?php

//InvitationController.php

namespace App\Http\Controllers;
use App\Models\Champ;
use App\Models\Critere;
use App\Models\Referentiel;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitEmail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Preuve;
use App\Models\Reference;
use App\Models\Filière;
use App\Models\Filiereinvite;

class InvitationController extends Controller
{
    private function checkAndDisableExpiredInvitations()
{
    // Obtenir la date actuelle
    $currentDate = Carbon::now()->toDateString();
    
    // Récupérer les invitations expirées
    $expiredInvitations = Invitation::where('statue', true)
        ->whereDate('date_fin', '<', $currentDate)
        ->get();
    
    // Si des invitations expirées ont été trouvées
    if ($expiredInvitations->isNotEmpty()) {
        // Désactiver les invitations expirées
        $expiredInvitations->each(function ($invitation) {
            $invitation->update(['statue' => false]);
        });

        // Mettre à jour le champ 'invitation' de tous les utilisateurs ayant une invitation active
        User::where('invitation', 1)->update(['invitation' => 0]);
        Filiereinvite::where('invitation', 1)->update(['invitation' => 0]);
    }
}


    public function index()
    {
        $this->checkAndDisableExpiredInvitations(); 

        $invitations = Invitation::all();
        return view('dashadmin.invit', compact('invitations'));
    }

    public function invite($id)
    {
        $invitation = Invitation::findOrFail($id);
        $users = User::where('role', 'evaluateur_i')
                     ->where('invitation', 0)
                     ->get();
        $invitedUsers = User::where('role', 'evaluateur_i')
                            ->where('invitation', 1) 
                            ->get();
        return view('dashadmin.invite', compact('invitation', 'users', 'invitedUsers'));
    }

    public function sendInvitations(Request $request, $id)
    {
        $invitation = Invitation::findOrFail($id);

        // Obtenez l'heure actuelle
        $currentDateTime = Carbon::now();

        $request->validate([
            'emails' => 'required|array|min:1',
            'emails.*' => 'email',
        ]);

        $emails = $request->input('emails');
        $subject = 'Invitation à la campagne';

        foreach ($emails as $email) {
            // Attendez quelques secondes entre chaque envoi pour éviter d'être bloqué pour spam
            sleep(5);

            // Envoyer l'e-mail avec l'heure actuelle
            Mail::to($email)->later($currentDateTime, new InvitEmail($invitation, $subject, $currentDateTime));

            // Mettre à jour le champ 'invitation' à 1 pour cet utilisateur
            User::where('email', $email)->update(['invitation' => 1]);

         
        }

        return redirect()->route('invitations.invite', ['invitation' => $invitation->id])->with('success', 'Invitations envoyées avec succès.');
    }

    
    public function store(Request $request)
    {
        $this->checkAndDisableExpiredInvitations();// Vérifier et désactiver les campagnes expirées

        $existingActiveInvitation = Invitation::where('statue', true)->exists();

        if ($existingActiveInvitation && $request->input('statue')) {
            return redirect()->back()->withErrors(['error' => 'Une campagne active existe déjà.'])->withInput();
        }

        if ($request->input('date_debut') < date('Y-m-d')) {
            return redirect()->back()->withErrors(['error' => 'La date de début doit être postérieure à la date actuelle.'])->withInput();
        }
        if ($request->input('date_fin') <= $request->input('date_debut')) {
            return redirect()->back()->withErrors(['error' => 'La date de fin doit être ultérieure à la date de début.'])->withInput();
        }

        $request->validate([
            'nom' => 'required',
            'description' => 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'statue' => 'required|boolean'
        ]);

        Invitation::create($request->all());

        return redirect()->route('invitations.index')->with('success', 'Invitation créée avec succès.');
    }

    public function update(Request $request, Invitation $invitation)
    {
        
        $this->checkAndDisableExpiredInvitations();// Vérifier et désactiver les campagnes expirées

        $existingActiveInvitations = Invitation::where('statue', true)
                                                ->where('id', '!=', $invitation->id)
                                                ->exists();

        if ($existingActiveInvitations && $request->input('statue')) {
            return redirect()->back()->withErrors(['error' => 'Une autre campagne active existe déjà.'])->withInput();
        }

        if ($request->input('date_fin') <= $request->input('date_debut')) {
            return redirect()->back()->withErrors(['error' => 'La date de fin doit être ultérieure à la date de début.'])->withInput();
        }

        $request->validate([
            'nom' => 'required',
            'description' => 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'statue' => 'required|boolean',
        ]);

        $invitation->update($request->all());
        // Mettre à jour le champ 'invitation' de tous les utilisateurs ayant une invitation active

        // Mettre à jour le champ 'invitation' de tous les utilisateurs ayant une invitation active
        User::where('invitation', 1)->update(['invitation' => 0]);
        Filiereinvite::where('invitation', 1)->update(['invitation' => 0]);
        
        return redirect()->route('invitations.index')->with('success', 'Invitation mise à jour avec succès.');
    }


    public function referent()
    {
        $referentiels = Referentiel::all();
        return view('dashadmin.referentiel', compact('referentiels'));
    }

    public function ajouter_referentiel(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:referentiels',
        'signature' => 'required|string|max:255',
    ]);

    Referentiel::create([
        'name' => $request->name,
        'signature' => $request->signature,
    ]);

    return redirect()->route('show.referent')->with('success', 'Référentiel ajouté avec succès');
}


public function modifier_referentiel(Request $request, $referentielId)
{
    $referentiel = Referentiel::findOrFail($referentielId);

    $request->validate([
        'name' => 'required|string|max:255|unique:referentiels,name,' . $referentiel->id,
        'signature' => 'required|string|max:255',
    ]);

    $referentiel->update([
        'name' => $request->name,
        'signature' => $request->signature,
    ]);

    return redirect()->route('show.referent')->with('success', 'Référentiel modifié avec succès');
}

    public function supprimer_referentiel($id)
    {
        $referentiel = Referentiel::findOrFail($id);
        $referentiel->delete();

        return redirect()->route('show.referent')->with('success', 'Référentiel supprimé avec succès');
    }

    public function showChamps($referentielId)
    {
        $referentiel = Referentiel::with('champs')->findOrFail($referentielId);
        return view('dashadmin.champ', compact('referentiel'));
    }

    public function ajouter_champ(Request $request, $referentielId)
{
    // Validate data
    $request->validate([
        'name' => 'required|string|max:255|unique:champs',
        'signature' => 'required|string|max:255',
    ]);

    // Create a new field
    Champ::create([
        'name' => $request->name,
        'signature' => $request->signature,
        'referentiel_id' => $referentielId,
    ]);

    return redirect()->route('referents.champs', ['referentielId' => $referentielId]);
}


public function modifier_champ(Request $request, $champId)
{
    // Retrieve field by ID
    $champ = Champ::find($champId);

    // Check if field exists
    if (!$champ) {
        return redirect()->back()->with('error', 'Champ introuvable');
    }

    // Validate data
    $request->validate([
        'name' => 'required|string|max:255|unique:champs,name,' . $champ->id,
        'signature' => 'required|string|max:255',
    ]);

    try {
        // Update field attributes
        $champ->name = $request->name;
        $champ->signature = $request->signature;
        $champ->save();

        return redirect()->back()->with('success', 'Champ modifié avec succès');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
    }
}


    public function supprimer_champ($id)
    {
        // Delete field
        $champ = Champ::find($id);
        if ($champ) {
            $champ->delete();
            return redirect()->back()->with('success', 'Champ supprimé avec succès');
        } else {
            return redirect()->back()->with('error', 'Champ introuvable');
        }
    }


    public function showCriteres($referentielId, $champId, $referenceId)
    {
        $referentiel = Referentiel::findOrFail($referentielId);
        $champ = Champ::findOrFail($champId);
        $reference = Reference::with('criteres')->findOrFail($referenceId);
        return view('dashadmin.critere', compact('referentiel', 'champ', 'reference'));
    }
    

    public function ajouter_critere(Request $request, $referenceId)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:criteres,nom',
        'signature' => 'required|string|max:255',
    ]);

    Critere::create([
        'nom' => $request->name,
        'signature' => $request->signature,
        'reference_id' => $referenceId,
    ]);

    return redirect()->back()->with('success', 'Critère ajouté avec succès');
}



public function modifier_critere(Request $request, $critereId)
{
    $critere = Critere::findOrFail($critereId);

    $request->validate([
        'name' => 'required|string|max:255|unique:criteres,nom,' . $critere->id,
        'signature' => 'required|string|max:255',
    ]);

    $critere->update([
        'nom' => $request->name,
        'signature' => $request->signature,
    ]);

    return redirect()->back()->with('success', 'Critère modifié avec succès');
}


public function supprimer_critere($id)
{
    $critere = Critere::findOrFail($id);
    $critere->delete();

    return redirect()->back()->with('success', 'Critère supprimé avec succès');
}


public function showPreuves($referentielId, $champId, $referenceId, $critereId)

{
    $referentiel = Referentiel::with('champs')->findOrFail($referentielId);
    $champ = Champ::with('references')->findOrFail($champId);
    $reference = Reference::with('criteres')->findOrFail($referenceId);
    $critere = Critere::with('preuves')->findOrFail($critereId);
    return view('dashadmin.preuve', compact('referentiel', 'champ', 'reference', 'critere'));
}


public function ajouter_preuve(Request $request, $critereId)
{
    $request->validate([
        'element' => 'required|string|max:255',
    ]);

    Preuve::create([
        'description' => $request->element,
        'critere_id' => $critereId,
    ]);

    return redirect()->back()->with('success', 'Critère ajouté avec succès');
}

public function modifier_preuve(Request $request, $preuveId)
{
    $preuve = Preuve::findOrFail($preuveId);

    $request->validate([
        'description' => 'required|string|max:255',
    ]);

    $preuve->update(['description' => $request->description]);

    return redirect()->back()->with('success', 'Preuve modifiée avec succès');
}

public function supprimer_preuve($id)
{
    $preuve = Preuve::findOrFail($id);
    $preuve->delete();

    return redirect()->back()->with('success', 'Preuve supprimée avec succès');
}



public function showReferences($referentielId, $champId)
{
    $referentiel = Referentiel::findOrFail($referentielId);
    $champ = Champ::with('references')->findOrFail($champId);

    return view('dashadmin.reference', compact('referentiel', 'champ'));
}


public function ajouter_reference(Request $request, $champId)
{
    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'signature' => 'required|string|max:255',
    ]);

    // Create a new reference
    $reference = new Reference();
    $reference->nom = $request->input('name');
    $reference->signature = $request->input('signature');
    $reference->champ_id = $champId; // assuming there is a foreign key relationship
    $reference->save();

    // Retrieve the associated Referentiel ID using the Champ model
    $champ = Champ::findOrFail($champId);
    $referentielId = $champ->referentiel->id; // Access the referentiel_id through the referentiel relationship

    // Redirect back with a success message
    return redirect()->route('champs.references', ['referentielId' => $referentielId, 'champId' => $champId]);
}

public function modifier_reference(Request $request, $id)
{
    $reference = Reference::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255|unique:references,nom,' . $reference->id,
        'signature' => 'required|string|max:255',
    ]);

    $reference->update([
        'nom' => $request->name,
        'signature' => $request->signature
    ]);

    return redirect()->back()->with('success', 'Reference modifiée avec succès');
}

public function supprimer_reference($id)
{
    $reference = Reference::findOrFail($id);
    $reference->delete();

    return redirect()->back()->with('success', 'Reference supprimée avec succès');
}


}

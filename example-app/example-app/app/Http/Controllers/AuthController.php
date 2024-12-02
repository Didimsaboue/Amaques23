<?php

namespace App\Http\Controllers;

use App\Http\Requests\Requestlogin;
use App\Models\etablissement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\Filière;
use Illuminate\Validation\Rule; // Import Rule from Validation namespace


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['login', 'register']);
    }

    /**
     * Handle user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Vérifier l'authentification de l'utilisateur
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Stocker le nom et l'email dans la session
            $request->session()->put('user_name', $user->name);
            $request->session()->put('user_email', $user->email);
    
            if ($user->role === 'admin') {
                return redirect()->intended(route('dashadmin'));
            } elseif ($user->role === 'evaluateur_i') {
                return redirect()->intended(route('indexevaluation'));
            }elseif ($user->role === 'RAQ') {
                return redirect()->intended(route('dashRAQ'));
            } else {
                return redirect()->intended(route('register'));
            }
        } else {
            return redirect()->back()->with('error', 'Adresse email ou mot de passe incorrect.');
        }
    }
    
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Optionally add additional logic, such as sending verification emails

    return redirect(route('login'));
}


public function updatePassword(Request $request)
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
    
public function update_profil(Request $request)
    {
        $user = Auth::user();
        
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,evaluateur_i,evaluateur_e',
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
/*
    public function updateRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|string|in:admin,evaluateur_i,evaluateur_I', // Assurez-vous que le rôle est valide
        ]);
    
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $user->role = $request->role;
        $user->save();
    
        return response()->json(['user' => $user, 'message' => 'User role updated successfully'], 200);
    }
    public function delete(Request $request, $userId)
    {
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $user->delete();
    
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    
*/ 
        public function logout(Request $request)
        {
            Auth::logout();
            return redirect()->route('login');
        }
    
        public function store_admin(Request $request)
        {
            // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:evaluateur_i,evaluateur_e,admin', // Assurez-vous que le rôle est valide
        ]);
    
        // Création d'un nouvel utilisateur
        $utilisateur = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        return redirect()->back()->with('success', 'Administrateur ajouté avec succès');
        }
    
        public function update_admin(Request $request, $id)
        {
            $user = User::find($id);
    
            if (!$user) {
                return redirect()->back()->with('error', 'Administrateur non trouvé');
            }
    
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'role' => 'required|string|in:evaluateur_i,evaluateur,admin',
            ]);
    
            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
            ]);
    
            return redirect()->back()->with('success', 'Administrateur mis à jour avec succès');
        }
    
        public function destroy_admin($id)
        {
            $user = User::find($id);
    
            if ($user) {
                $user->delete();
                return redirect()->back()->with('success', 'Administrateur supprimé avec succès');
            } else {
                return redirect()->back()->with('error', 'Administrateur non trouvé');
            }
        }

    public function adminIndex()
    {

   $currentUserId = Auth::id();

    $users = User::where('id', '!=', $currentUserId)
                      ->where('role', 'admin')
                      ->get();

    return view('dashadmin.admin_users', compact('users'));
}

/*
    public function user()
    {
         // Récupérer l'ID de l'utilisateur connecté
    $currentUserId = Auth::id();
    
    // Récupérer les utilisateurs en excluant l'utilisateur connecté
    $utilisateurs = User::where('id', '!=', $currentUserId)->get();

    return view('dashadmin.users', compact('utilisateurs'));

    }
*/


    public function userExIndex()
    {
        $users = User::where('role', 'evaluateur_e')->get();
        return view('dashadmin.externe_users', compact('users'));
    
    }

    public function store_userEx(Request $request)
        {
            // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:evaluateur_i,evaluateur_e,admin', // Assurez-vous que le rôle est valide
        ]);
    
        // Création d'un nouvel utilisateur
        $utilisateur = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        return redirect()->back()->with('success', 'Utilisateur ajouté avec succès');
        }
    
        public function update_userEx(Request $request, $id)
{
    // Recherche de l'utilisateur à mettre à jour dans la base de données
    $user = User::find($id);

    if (!$user) {
        return redirect()->back()->with('error', 'Utilisateur non trouvé');
    }

    // Validation des données du formulaire
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        'role' => 'required|string|in:evaluateur_i,evaluateur_e,admin',
    ]);

    // Mise à jour des informations de l'utilisateur
    $user->update([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'role' => $validatedData['role'],
    ]);

    // Redirection avec un message de succès
    return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès');
}

    
        public function destroy_userEx($id)
        {
            $user = User::find($id);
    
            if ($user) {
                $user->delete();
                return redirect()->back()->with('success', 'Utilisateur supprimé avec succès');
            } else {
                return redirect()->back()->with('error', 'Utilisateur non trouvé');
            }
        }

        
public function userInIndex()
{
    $users = User::where('role', 'evaluateur_i')->get();
    $usedFiliereIds = $users->pluck('filières_id')->toArray(); // Récupérer les ID des filières utilisées par les évaluateurs internes
    $filieres = Filière::whereNotIn('id', $usedFiliereIds)->get(); // Sélectionner les filières qui ne sont pas utilisées
    return view('dashadmin.interne_users', compact('users', 'filieres'));
}
       
public function RAQIndex()
{
    $users = User::where('role', 'RAQ')->get();
    $usedetablissementIds = $users->pluck('idetablissements')->toArray(); 
    $etablissements = etablissement::whereNotIn('id', $usedetablissementIds)->get(); 
    return view('dashadmin.RAQ', compact('users', 'etablissements'));
}

public function store_userIn(Request $request)
{
    // Validation des données
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|string|in:evaluateur_i,evaluateur_e,admin', // Assurez-vous que le rôle est valide
        'fil' => 'nullable|exists:filières,id',
    ]);

    $utilisateur = new User();
    $utilisateur->name = $validated['name'];
    $utilisateur->email = $validated['email']; 
    $utilisateur->password = Hash::make($validated['password']);
    $utilisateur->role = $validated['role'];
    $utilisateur->filières_id = $validated['fil'];
    $utilisateur->save();

    return redirect()->back()->with('success', 'Utilisateur ajouté avec succès');
}

public function store_RAQ(Request $request)
{
    // Validation des données
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|string|in:RAQ', // Assurez-vous que le rôle est valide
        'fil' => 'nullable|exists:etablissements,id',
    ]);

    $utilisateur = new User();
    $utilisateur->name = $validated['name'];
    $utilisateur->email = $validated['email']; 
    $utilisateur->password = Hash::make($validated['password']);
    $utilisateur->role = $validated['role'];
    $utilisateur->idetablissements = $validated['fil'];
    $utilisateur->save();

    return redirect()->back()->with('success', 'Utilisateur ajouté avec succès');
}

public function update_userIn(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'required|string',
            'fil' => 'required|integer',
        ]);

        // Trouver l'utilisateur à modifier
        $utilisateur = User::find($id);
        if (!$utilisateur) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé');
        }

        // Mise à jour des informations de l'utilisateur
        $utilisateur->name = $request->name;
        $utilisateur->email = $request->email;
        if ($request->filled('password')) {
            $utilisateur->password = Hash::make($request->password);
        }
        $utilisateur->role = $request->role;
        $utilisateur->filières_id = $request->fil;

        $utilisateur->save();

        return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function update_RAQ(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'required|string',
            'fil' => 'required|integer',
        ]);

        // Trouver l'utilisateur à modifier
        $utilisateur = User::find($id);
        if (!$utilisateur) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé');
        }

        // Mise à jour des informations de l'utilisateur
        $utilisateur->name = $request->name;
        $utilisateur->email = $request->email;
        if ($request->filled('password')) {
            $utilisateur->password = Hash::make($request->password);
        }
        $utilisateur->role = $request->role;
        $utilisateur->idetablissements = $request->fil;

        $utilisateur->save();

        return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès');
    }

public function destroy_userIn($id)
{
    $user = User::find($id);

    if ($user) {
        $user->delete();
        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès');
    } else {
        return redirect()->back()->with('error', 'Utilisateur non trouvé');
    }
}

public function destroy_RAQ($id)
{
    $user = User::find($id);

    if ($user) {
        $user->delete();
        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès');
    } else {
        return redirect()->back()->with('error', 'Utilisateur non trouvé');
    }
}

        public function handle($request, Closure $next)
        {
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            }
    
            return $next($request);
        }

   

}




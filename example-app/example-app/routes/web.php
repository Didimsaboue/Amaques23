<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Homecontroller;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\RAQController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\ChampController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Login Routes
Route::get('/', function () {
    return view('authentification.pages-login');
})->name('login');

Route::post('/', [AuthController::class, 'login'])->name('login.post');

// Logout Route


// Registration Routes
Route::get('/register', function () {
    return view('authentification.register');
})->name('register');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register');



Route::middleware(['auth', 'redirectIfnotEVL_I'])->group(function () {
    // Route::get('/dash', function () {
    //     return view('layout.liste');
    // })->name('dash');

    Route::get('setlocale/{locale}', function ($locale) {
        if (array_key_exists($locale, config('app.locales'))) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    })->name('setlocale');    
    Route::get('/get-scores', [HomeController::class, 'getScores'])->name('get-scores');
    Route::get('/scores_champ', function () {
        return view('layout.scorechamp');
    });
    
    Route::get('/download-pdf', [HomeController::class, 'generatePDF'])->name('download.pdf');
    
    Route::get('/indexevaluation', [Homecontroller::class, 'indexevaluation'])->name('indexevaluation');
    Route::post('/evaluate', [Homecontroller::class, 'evaluate'])->name('evaluate');
});

Route::middleware(['auth','isRAQ'])->group(function () {
    Route::get('/RAQ', [RAQController::class, 'index'])->name('dashRAQ');
    Route::get('/profileR', function () {
        return view('RAQ.profile');
    })->name('profileR');
    Route::put('/profil/updateR', [RAQController::class, 'update_profilR'])->name('profil.updateR');
    Route::put('/profile/update-passwordR', [RAQController::class, 'updatePasswordR'])->name('profile.update-passwordR');
    Route::get('/departementR', [RAQController::class, 'indexDepartementR'])->name('departement.indexR');
    Route::put('/departementR/{id}', [RAQController::class, 'updateDepartementR'])->name('departement.updateR');
    Route::delete('/departementR/{id}', [RAQController::class, 'destroyDepartementR'])->name('departement.destroyR');
    Route::post('/departementR', [RAQController::class, 'storeDepartementR'])->name('departement.storeR');
    Route::get('/filiereR', [RAQController::class, 'indexFiliereR'])->name('filiere.indexR');
    Route::put('/filiereR/{id}', [RAQController::class, 'updateFiliereR'])->name('filiere.updateR');
    Route::delete('/filiereR/{id}', [RAQController::class, 'destroyFiliereR'])->name('filiere.destroyR');
    Route::post('/filiereR', [RAQController::class, 'storeFiliereR'])->name('filiere.storeR');
});
// Middleware for admin users
Route::middleware(['auth', 'redirectIfAdmin'])->group(function () {
   
    Route::get('/dashboard', [ReferentielController::class, 'index'])->name('dashadmin');

    // routes/web.php
Route::put('/profil/update', [AuthController::class, 'update_profil'])->name('profil.update');
Route::get('api/referentiel/{id}/data',[ReferentielController::class,'getData']);
// web.php
Route::put('/profile/update-password', [AuthController::class, 'updatePassword'])->name('profile.update-password');
Route::get('/resultat', [Homecontroller::class, 'evaluation_interne'])->name('resultat.EVIN');

    Route::get('/users', [AuthController::class, 'user'])->name('user');

    Route::get('/admins', function () {
        return view('dashadmin.admin');
    })->name('admin');
    
    Route::get('/dashboard', [ReferentielController::class, 'index'])->name('dashadmin');

    Route::get('/profile', function () {
        return view('dashadmin.profile');
    })->name('profile');

Route::get('/api/referentiel/{id}/data', [ReferentielController::class, 'getData']);

Route::post('/useradmin/ajouter', [AuthController::class, 'store_admin'])->name('useradmin.ajouter');
Route::put('/useradmin/{id}/modifier', [AuthController::class, 'update_admin'])->name('useradmin.modifier');
Route::delete('/useradmin/{id}/supprimer', [AuthController::class, 'destroy_admin'])->name('useradmin.supprimer');

Route::get('/admin/utilisateurs', [AuthController::class, 'adminIndex'])->name('admin.utilisateurs');
// routes/web.php
Route::get('/invitation', [InvitationController::class, 'index'])->name('invitations.index');
Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
Route::put('/invitations/{invitation}', [InvitationController::class, 'update'])->name('invitations.update');

Route::get('invitations/{invitation}/invite', [InvitationController::class, 'invite'])->name('invitations.invite');
Route::post('invitations/{invitation}/send', [InvitationController::class, 'sendInvitations'])->name('invitations.sendInvitations');

Route::delete('/invitations/{id}', [InvitationController::class, 'destroy'])->name('invitations.destroy');




Route::get('/institutions', [EducationController::class, 'indexInstitutions'])->name('institutions.index');
Route::post('/institutions', [EducationController::class, 'storeInstitution'])->name('institutions.store');
Route::put('/institutions/{id}', [EducationController::class, 'updateInstitution'])->name('institutions.update');
Route::delete('/institutions/{id}', [EducationController::class, 'destroyInstitution'])->name('institutions.destroy');




Route::get('/etablissement', [EducationController::class, 'indexEtablissement'])->name('etablissement.index');
Route::put('/etablissement/{id}', [EducationController::class, 'updateEtablissement'])->name('etablissement.update');
Route::delete('/etablissement/{id}', [EducationController::class, 'destroyEtablissement'])->name('etablissement.destroy');
Route::post('/etablissement', [EducationController::class, 'storeEtablissement'])->name('etablissement.store');
Route::get('/departement', [EducationController::class, 'indexDepartement'])->name('departement.index');
Route::put('/departement/{id}', [EducationController::class, 'updateDepartement'])->name('departement.update');
Route::delete('/departement/{id}', [EducationController::class, 'destroyDepartement'])->name('departement.destroy');
Route::post('/departement', [EducationController::class, 'storeDepartement'])->name('departement.store');
Route::get('/filiere', [EducationController::class, 'indexFiliere'])->name('filiere.index');
Route::put('/filiere/{id}', [EducationController::class, 'updateFiliere'])->name('filiere.update');
Route::delete('/filiere/{id}', [EducationController::class, 'destroyFiliere'])->name('filiere.destroy');
Route::post('/filiere', [EducationController::class, 'storeFiliere'])->name('filiere.store');
Route::get('/filiereD', [EducationController::class, 'indexFiliereD'])->name('filiere.indexD');
Route::delete('/filiereD/{id}', [EducationController::class, 'destroyFiliereD'])->name('filiere.destroyD');
Route::post('/filiereD', [EducationController::class, 'storeFiliereD'])->name('filiere.storeD');
Route::get('/filiereM', [EducationController::class, 'indexFiliereM'])->name('filiere.indexM');
Route::post('/filiereM', [EducationController::class, 'storeFiliereM'])->name('filiere.storeM');
Route::delete('/filiereM/{id}', [EducationController::class, 'destroyFiliereM'])->name('filiere.destroyM');






Route::get('/evaluateur_ex/utilisateurs', [AuthController::class, 'userExIndex'])->name('evaluateur_ex.utilisateurs');

Route::post('/userEx/ajouter', [AuthController::class, 'store_userEx'])->name('store_userEx');
Route::put('/userEx/{id}/modifier', [AuthController::class, 'update_userEx'])->name('update_userEx');
Route::delete('/userEx/{id}/supprimer', [AuthController::class, 'destroy_userEx'])->name('destroy_userEx');

Route::get('/evaluateur_in/utilisateurs', [AuthController::class, 'userInIndex'])->name('evaluateur_in.utilisateurs');
Route::post('/userIn/ajouter', [AuthController::class, 'store_userIn'])->name('store_userIn');
Route::delete('/userIn/{id}/supprimer', [AuthController::class, 'destroy_userIn'])->name('destroy_userIn');
Route::put('/evaluateur_in/utilisateurs/{id}', [AuthController::class, 'update_userIn'])->name('evaluateur_in.utilisateurs.update');
Route::get('/RAQ/utilisateurs', [AuthController::class, 'RAQIndex'])->name('RAQ.utilisateurs');
Route::post('/RAQ/ajouter', [AuthController::class, 'store_RAQ'])->name('store_RAQ');
Route::delete('/RAQ/{id}/supprimer', [AuthController::class, 'destroy_RAQ'])->name('destroy_RAQ');
Route::put('/RAQ/utilisateurs/{id}', [AuthController::class, 'update_RAQ'])->name('RAQ.utilisateurs.update');




Route::post('/referentiels/ajouter', [InvitationController::class, 'ajouter_referentiel'])->name('referentiel.ajouter');
Route::put('/referentiels/{referentiel}/modifier', [InvitationController::class, 'modifier_referentiel'])->name('referentiel.modifier');
Route::delete('/referentiels/{referentiel}/supprimer', [InvitationController::class, 'supprimer_referentiel'])->name('referentiel.supprimer');
Route::get('/referentiel/{referentielId}/champs', [InvitationController::class, 'showChamps'])->name('referents.champs');
Route::get('/referents', [InvitationController::class, 'referent'])->name('show.referent');


Route::post('/referentiel/{referentielId}/champ/ajouter', [InvitationController::class, 'ajouter_champ'])->name('champ.ajouter');
Route::put('/champs/{id}/modifier', [InvitationController::class, 'modifier_champ'])->name('champ.modifier');
Route::delete('/champ/{id}/supprimer', [InvitationController::class, 'supprimer_champ'])->name('champ.supprimer');


Route::get('/references/{referentielId}/{champId}/references', [InvitationController::class, 'showReferences'])->name('champs.references');
Route::post('/reference/ajouter/{champ_id}', [InvitationController::class, 'ajouter_reference'])->name('reference.ajouter');
Route::put('/reference/{id}/modifier', [InvitationController::class, 'modifier_reference'])->name('reference.modifier');
Route::delete('/references/{id}/supprimer', [InvitationController::class, 'supprimer_reference'])->name('reference.supprimer');

Route::get('/references/{referentielId}/{champId}/{referenceId}/criteres', [InvitationController::class, 'showCriteres'])->name('references.criteres');
Route::post('/critere/ajouter/{reference_id}', [InvitationController::class, 'ajouter_critere'])->name('critere.ajouter');
Route::put('/critere/{id}/modifier', [InvitationController::class, 'modifier_critere'])->name('critere.modifier');
Route::delete('/critere/{id}/supprimer', [InvitationController::class, 'supprimer_critere'])->name('critere.supprimer');

Route::get('/champs/{referentielId}/{champId}/{referenceId}/{critereTd}/criteres', [InvitationController::class, 'showPreuves'])->name('critere.preuves');

Route::post('critere/{critereId}/preuves', [InvitationController::class, 'ajouter_preuve'])->name('preuves.store');
Route::put('preuve/{preuveId}/modifier', [InvitationController::class, 'modifier_preuve'])->name('preuves.update');
Route::delete('preuve/{preuveId}/supprimer', [InvitationController::class, 'supprimer_preuve'])->name('preuves.destroy');


Route::get('setlocale/{locale}', function ($locale) {
    if (array_key_exists($locale, config('app.locales'))) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('setlocale');


Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('/notifications/passees', [NotificationController::class, 'pastNotifications'])->name('notifications.passees');
//Route::get('/check-campaign-end-date', [InvitationController::class, 'checkEndDate']);


// Route::get('/filieres/{id}/champs', [FiliereController::class, 'champs'])->name('filieres.champs');

//Route::get('/filiere/{id}/champs', [FiliereController::class, 'champs'])->name('champs.index');
//Route::get('/champs/{id}/resultats', [ChampController::class, 'resultats'])->name('champs.resultats');

// Route::get('/downloadfile/{filename}', [ChampController::class, 'downloadFile'])->name('downloadFile');
// Route::get('/filieres/{id}/champs', [FiliereController::class, 'champs'])->name('filieres.champs');
// Route::get('/champs/{filiereInviteId}/{champId}/resultats', [ChampController::class, 'resultats'])->name('champs.resultats');
// Route::get('/downloadFile/{filename}', [ChampController::class, 'downloadFile'])->name('downloadFile');

Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{id}/filieres', [CampaignController::class, 'filieres'])->name('campaigns.filieres');
Route::get('/filieres/{id}/champs', [FiliereController::class, 'champs'])->name('filieres.champs');
Route::get('/champs/{filiereInviteId}/{champId}/resultats', [ChampController::class, 'resultats'])->name('champs.resultats');
Route::get('/downloadFile/{filename}', [ChampController::class, 'downloadFile'])->name('downloadFile');
//Route::get('/get-scores', [ChampController::class, 'getScores'])->name('getScores');
// routes/web.php




});



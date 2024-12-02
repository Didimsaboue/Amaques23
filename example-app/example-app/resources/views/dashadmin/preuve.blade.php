@extends('dashadmin.home')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('assets/css/ajout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/edit.css') }}">
</head>
<main id="main" class="main">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('show.referent') }}">Les référentiels</a></li>
            <li class="breadcrumb-item"><a href="{{ route('referents.champs', $referentiel->id) }}">Les champs</a></li>
            <li class="breadcrumb-item"><a href="{{ route('champs.references', ['referentielId' => $referentiel->id , 'champId' => $champ->id]) }}">Les references</a></li>
            <li class="breadcrumb-item"><a href="{{ route('references.criteres', ['referentielId' => $referentiel->id , 'champId' => $champ->id, 'referenceId'=> $reference->id ]) }}">Les criteres</a></li>
            <li class="breadcrumb-item">Les éléments de preuves</li>
        </ol>
    </nav>
    <h2>Eléments de preuves pour le critère : {{ $critere->signature }}</h2>
    <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                @if(session('error'))
                  <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
      
                @if(session('success'))
                  <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Gestion des éléments des preuves :</h5>
                    <button id="ajouterBtn" class="btn btn-primary mb-3"><i class="bi bi-plus-lg"></i></button>
                  </div>
  
            <!-- Modal for the form -->
            <div id="formModal" class="modal" style="display: none;">
              <div class="modal-content">
                <span class="close">&times;</span>
                @if ($errors->any())
                  <div class="alert alert-danger">
                    <ul>
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
                <form id="ajouterForm" action="{{ route('preuves.store', $critere->id) }}" method="POST">
                  @csrf
                  <input type="hidden" name="critereId" value="{{ $critere->id }}">
                  <label for="element">Elément de preuve:</label>
                  <input type="text" id="element" name="element" required>
                  <br><br>
                  <button type="submit" class="btn btn-success">Soumettre</button>
                </form>
              </div>
            </div>
            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Element de preuve</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($critere->preuves as $preuve)
                                        <tr>
                                            <td>{{ $preuve->description }}</td>
                                            <td>
                                               <!-- Bouton pour ouvrir le modal de modification -->
            <button class="btn btn-sm modifierBtn" data-id="{{ $preuve->id }}" data-description="{{ $preuve->description }}"><i class="bi bi-pencil-fill text-warning"></i> Modifier</button>
            <!-- Formulaire de suppression -->
                                            </td>
                                        <td>
                                                <form action="{{ route('preuves.destroy', ['preuveId' => $preuve->id]) }}" method="POST" class="supprimerForm">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm"><i class="bi bi-trash-fill text-danger"></i>Supprimer</button>
                                                </form>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for the edit form -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT') <!-- Utilisez la méthode PUT pour la modification -->
                <input type="hidden" id="editPreuveId" name="preuveId">
                <label for="editDescription">Element de preuve:</label>
                <input type="text" id="editDescription" name="description" required>
                <br><br>
                <button type="submit" class="btn btn-success">Modifier</button>
            </form>
        </div>
    </div>
</main><!-- End #main -->

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
    const ajouterBtn = document.getElementById('ajouterBtn');
    const formModal = document.getElementById('formModal');
    const closeModalBtn = formModal.querySelector('.close');
    const modifierBtns = document.querySelectorAll('.modifierBtn'); // Sélectionnez tous les boutons de modification
    const editModal = document.getElementById('editModal');
    const editCloseBtn = editModal.querySelector('.close');

    // Event listener for the "Ajouter" button
    ajouterBtn.addEventListener('click', () => {
        formModal.style.display = "block"; // Afficher le modal d'ajout
    });

    // Event listener for close button of add form
    closeModalBtn.addEventListener('click', () => {
        formModal.style.display = "none"; // Masquer le modal d'ajout
    });

    // Event listener for closing add form when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === formModal) {
            formModal.style.display = "none"; // Masquer le modal d'ajout
        }
    });

    // Event listener for close button of edit form
    editCloseBtn.addEventListener('click', () => {
        editModal.style.display = "none"; // Masquer le modal de modification
    });

    // Event listener for closing edit form when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            editModal.style.display = "none"; // Masquer le modal de modification
        }
    });

    // Ajouter un gestionnaire d'événements pour chaque bouton "Modifier"
    modifierBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Ouvrir le modal de modification
            editModal.style.display = "block";
            // Définir la valeur de l'ID et description dans le formulaire de modification
            const preuveId = btn.getAttribute('data-id');
            const description = btn.getAttribute('data-description');
            document.getElementById('editPreuveId').value = preuveId;
            document.getElementById('editDescription').value = description;
            // Définir l'action du formulaire de modification avec l'ID correct
            document.getElementById('editForm').action = `/preuve/${preuveId}/modifier`;
        });
    });

    const supprimerForms = document.querySelectorAll('.supprimerForm');
    supprimerForms.forEach((form) => {
      form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this critere?')) {
          form.submit();
        }
      });
    });
});
</script>
@endsection

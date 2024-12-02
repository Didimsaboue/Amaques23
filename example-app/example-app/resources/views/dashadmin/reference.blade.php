@extends('dashadmin.home')

@section('content')
<head>
  <link rel="stylesheet" href="{{ asset('assets/css/ajout.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/edit.css') }}">
</head>
<main id="main" class="main">
  <nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('show.referent') }}">Les référentiels</a></li>
        <li class="breadcrumb-item"><a href="{{ route('referents.champs', $referentiel->id) }}">les champs</a></li>
        <li class="breadcrumb-item">les references</li>
    </ol>
  </nav>
  <h2>References de champ : {{ $champ->signature}}</h2>
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
              
            <h5 class="card-title">Gestion des references</h5>
            <!-- Button to open the modal -->

            <button id="ajouterBtn" class="btn btn-primary mb-3"><i class="bi bi-plus-lg"></i></button>
              </div>

            <!-- Modal for the form -->
            <div id="formModal" class="modal">
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
                <form id="ajouterForm" action="{{ route('reference.ajouter', ['champ_id' => $champ->id]) }}" method="POST">
                  @csrf
                  <label for="name">Description:</label>
                  <input type="text" id="name" name="name" required>
                  <br><br>
                  <label for="signature">Signature:</label>
                  <input type="text" id="signature" name="signature" required>
                  <br><br>
                  <button type="submit" class="btn btn-success">Soumettre</button>
                </form>              
              </div>
            </div>
            <table class="table data-table">
              <thead>
                <tr>
                  <th>Les critères</th>
                  <th>Description</th>
                  <th>Signature</th>
                  <th style="text-align: center">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($champ->references as $reference)
                <tr>
                    <td>
                        <a href="{{ route('references.criteres', ['referentielId' => $referentiel->id, 'champId' => $champ->id, 'referenceId' => $reference->id]) }}" class="btn btn-success">Vue</a>
                    </td>
                    <td>{{ $reference->nom }}</td>
                    <td>{{ $reference->signature }}</td>
                    <td>
                        <button class="btn btn-sm modifierBtn" data-id="{{ $reference->id }}" data-name="{{ $reference->nom }}" data-signature="{{ $reference->signature }}"><i class="bi bi-pencil-fill text-warning"></i> Modifier</button>
                    </td>
                    <td>
                        <form action="{{ route('reference.supprimer', $reference->id) }}" method="POST" class="supprimerForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm"><i class="bi bi-trash-fill text-danger"></i>Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            <!-- End Table with stripped rows -->
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal for the edit form -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <form id="editForm" action="" method="POST">
        @csrf
        @method('PUT') <!-- Utilisez la méthode PUT pour la modification -->
        <input type="hidden" id="editReferenceId" name="referenceId">
        <label for="editName">Description:</label>
        <input type="text" id="editName" name="name" required>
        <br><br>
        <label for="editSignature">Signature:</label>
        <input type="text" id="editSignature" name="signature" required>
        <br><br>
        <button type="submit" class="btn btn-success">Modifier</button>
      </form>
    </div>
  </div>
</main><!-- End #main -->

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const modal = document.getElementById("formModal");
    const ajouterBtn = document.getElementById("ajouterBtn");
    const span = document.getElementsByClassName("close")[0];

    ajouterBtn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

document.addEventListener('DOMContentLoaded', (event) => {
    const modifierBtns = document.querySelectorAll('.modifierBtn');
    const supprimerForms = document.querySelectorAll('.supprimerForm');
    const editModal = document.getElementById('editModal');
    const closeModalBtn = editModal.querySelector('.close');
    const editForm = editModal.querySelector('form');

    // Event listener for edit buttons
    modifierBtns.forEach((button) => {
        button.addEventListener('click', () => {
            const referenceId = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const signature = button.getAttribute('data-signature');

            openEditModal(referenceId, name, signature);
        });
    });

    // Function to open the edit modal
    function openEditModal(referenceId, name, signature) {
        document.getElementById('editReferenceId').value = referenceId;
        document.getElementById('editName').value = name;
        document.getElementById('editSignature').value = signature;
        document.getElementById('editForm').action = "{{ route('reference.modifier', ':id') }}".replace(':id', referenceId);
        editModal.style.display = "block";
    }

    // Event listener for delete forms
    supprimerForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this reference?')) {
                form.submit();
            }
        });
    });

    // Event listener for close button of edit form
    closeModalBtn.addEventListener('click', () => {
        editModal.style.display = "none";
    });

    // Event listener for closing edit form when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            editModal.style.display = "none";
        }
    });
});
</script>

@endsection

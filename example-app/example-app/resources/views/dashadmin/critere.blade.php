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
      <li class="breadcrumb-item"><a href="{{ route('champs.references', ['referentielId' => $referentiel->id, 'champId' => $champ->id]) }}">les references</a></li>
      <li class="breadcrumb-item">Les critères</li>
    </ol>
  </nav>
  <h2>Critères de reference : {{ $reference->signature }}</h2>
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
            <h5 class="card-title">Gestion des critères</h5>
            <button id="ajouterBtn" class="btn btn-primary mb-3"><i class="bi bi-plus-lg"></i></button>
          </div>
            
  
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
                <form id="ajouterForm" action="{{ route('critere.ajouter', ['reference_id' => $reference->id]) }}" method="POST">
                  @csrf
                  <input type="hidden" name="champ_id" value="{{ $reference->id }}">
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
                  <th>Les preuves</th>
                  <th>Description</th>
                  <th>Signature</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($reference->criteres as $critere)
                <tr>
                  <td>
                    <a href="{{ route('critere.preuves', ['referentielId' => $referentiel->id, 'champId' => $champ->id, 'referenceId' => $reference->id,'critereTd' => $critere->id,]) }}" class="btn btn-success">Vue</a>
                  </td>
                   <td>{{ $critere->nom }}</td>
                  <td>{{ $critere->signature }}</td>
                  <td>
                    <button class="btn btn-sm modifierBtn" data-id="{{ $critere->id }}"><i class="bi bi-pencil-fill text-warning"></i> Modifier</button>
                  </td>
                  <td>
                    <form action="{{ route('critere.supprimer', $critere->id) }}" method="POST" class="supprimerForm">
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
  </section>
  
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="editCritereId" name="critereId">
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

</main>

<script>
  document.addEventListener('DOMContentLoaded', (event) => {
  const ajouterBtn = document.getElementById("ajouterBtn");
  const formModal = document.getElementById("formModal");
  const editModal = document.getElementById('editModal');
  const span = document.getElementsByClassName("close");

  ajouterBtn.onclick = function() {
    formModal.style.display = "block";
  }

  Array.from(span).forEach((closeBtn) => {
    closeBtn.onclick = function() {
      formModal.style.display = "none";
      editModal.style.display = "none";
    }
  });

  window.onclick = function(event) {
    if (event.target == formModal) {
      formModal.style.display = "none";
    }
    if (event.target == editModal) {
      editModal.style.display = "none";
    }
  }

  const modifierBtns = document.querySelectorAll('.modifierBtn');
  modifierBtns.forEach((button) => {
    button.addEventListener('click', () => {
      const critereId = button.getAttribute('data-id');
      const row = button.closest('tr');
      const name = row.cells[2].innerText; // Index 2 pour le nom
      const signature = row.cells[1].innerText; // Index 1 pour la signature
      openEditModal(critereId, name, signature);
    });
  });

  function openEditModal(critereId, name, signature) {
    document.getElementById('editCritereId').value = critereId;
    document.getElementById('editName').value = name;
    document.getElementById('editSignature').value = signature;
    document.getElementById('editForm').action = "/critere/" + critereId + "/modifier";
    editModal.style.display = "block";
  }

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

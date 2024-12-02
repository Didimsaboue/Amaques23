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
        <li class="breadcrumb-item">les champs</li>
    </ol>
  </nav>
  <h2>Champs de référentiel : {{ $referentiel->signature }}</h2>
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
            <h5 class="card-title">Gestion des champs</h5>
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
    <form id="ajouterForm" action="{{ route('champ.ajouter', ['referentielId' => $referentiel->id]) }}" method="POST">
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
      <th>Les réferences</th>
      <th>Description</th>
      <th>Signature</th>
      <th style="text-align: center">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($referentiel->champs as $champ)
      <tr>
        <td>
          <a href="{{ route('champs.references', ['referentielId' => $referentiel->id, 'champId' => $champ->id]) }}" class="btn btn-success">Vue</a>
        </td>
        <td>{{ $champ->name }}</td>
        <td>{{ $champ->signature }}</td>
        <td>
          <button class="btn btn-sm modifierBtn" data-id="{{ $champ->id }}" data-name="{{ $champ->name }}" data-signature="{{ $champ->signature }}"><i class="bi bi-pencil-fill text-warning"></i> Modifier</button>
        </td>
        <td>
          <form action="{{ route('champ.supprimer', $champ->id) }}" method="POST" class="supprimerForm">
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
    <form id="editForm" action="" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" id="editChampId" name="champId">
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

    modifierBtns.forEach((button) => {
        button.addEventListener('click', () => {
            const champId = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const signature = button.getAttribute('data-signature');

            openEditModal(champId, name, signature);
        });
    });

    function openEditModal(champId, name, signature) {
        document.getElementById('editChampId').value = champId;
        document.getElementById('editName').value = name;
        document.getElementById('editSignature').value = signature;
        document.getElementById('editForm').action = "/champs/" + champId + "/modifier";
        editModal.style.display = "block";
    }

    closeModalBtn.addEventListener('click', () => {
        editModal.style.display = "none";
    });

    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            editModal.style.display = "none";
        }
    });

    // Event listener for delete forms
    supprimerForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this champ?')) {
                form.submit();
            }
        });
    });
});
</script>

@endsection

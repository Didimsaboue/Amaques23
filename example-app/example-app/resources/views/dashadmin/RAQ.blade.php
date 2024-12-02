@extends('dashadmin.home')

@section('content')
<head>
  <link rel="stylesheet" href="{{ asset('assets/css/ajout.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/edit.css') }}">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<main id="main" class="main">
  <div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Les RAQs</h1>
    </div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
            <li class="breadcrumb-item">les RAQs</li>
        </ol>
    </nav>
  </div>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="card-title">Les RAQs</h6>
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
                <form id="ajouterForm" action="{{ route('store_RAQ') }}" method="POST">
                  @csrf
                  <label for="name">Nom:</label>
                  <input type="text" id="name" name="name" required>
                  <br><br>
                  <label for="email">Email:</label>
                  <input type="email" id="email" name="email" required>
                  <br><br>
                  <label for="password">Mot de passe:</label>
                  <input type="password" id="password" name="password" required>
                  <br><br>
                  <label for="confirm_password">Confirmer le mot de passe:</label>
                  <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
                  <br><br>
                  <label for="role">Rôle:</label>
                  <select id="role" name="role" class="form-control" required>
                    <option value="RAQ">RAQ</option>
                  </select>
                  <br><br>
                  <label for="editFil">Etablissements:</label>
                  <select id="editFil" name="fil" class="form-control" required>
                    <option value="" disabled selected>Choisir un établissement</option>
                    @foreach($etablissements as $etablissement)
                      <option value="{{ $etablissement->id }}">{{ $etablissement->nom }}</option>
                    @endforeach
                  </select>
                  <br><br>
                  <button type="submit" class="btn btn-success">Soumettre</button>
                </form>
              </div>
            </div>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Etablissement</th>
                  <th colspan="2">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                  <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                      @if ($user->établissement)
                        {{ $user->établissement->nom }}
                      @else
                        Aucune etablissement associée
                      @endif
                    </td>
                    <td>
                      <button class="btn btn-sm modifierBtn" data-id="{{ $user->id }}" data-etablissement-id="{{ $user->idetablissements }}"><i class="bi bi-pencil-fill text-warning"></i> Modifier</button>
                    </td>
                    <td>
                      <form action="{{ route('destroy_RAQ', $user->id) }}" method="POST" class="supprimerForm">
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
      <form id="editForm" action="" method="POST"> <!-- Removed the action attribute -->
        @csrf
        @method('PUT')
        <input type="hidden" id="editUserId" name="userId">
        <label for="editName">Nom:</label>
        <input type="text" id="editName" name="name" required>
        <br><br>
        <label for="editEmail">Email:</label>
        <input type="email" id="editEmail" name="email" required>
        <br><br>
        <label for="editPassword">Mot de passe:</label>
        <input type="password" id="editPassword" name="password">
        <br><br>
        <label for="editRole">Rôle:</label>
        <select id="editRole" name="role" class="form-control" required>
          <option value="RAQ" selected>RAQ</option>
        </select>
        <br><br>
        <label for="editFil">Etablissements:</label>
        <select id="editFil" name="fil" class="form-control" required>
        
          @foreach($etablissements as $etablissement)
            <option value="{{ $etablissement->id }}">{{ $etablissement->nom }}</option>
          @endforeach
        </select>
        <br><br>
        <button type="submit" class="btn btn-success">Modifier</button>
      </form>
    </div>
  </div>
</main><!-- End #main -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
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
  
      const modifierBtns = document.querySelectorAll('.modifierBtn');
      const supprimerForms = document.querySelectorAll('.supprimerForm');
      const editModal = document.getElementById('editModal');
      const closeModalBtn = editModal.querySelector('.close');
      const editForm = editModal.querySelector('form');
      
      modifierBtns.forEach(button => {
          button.addEventListener('click', () => {
              const userId = button.getAttribute('data-id');
              const row = button.closest('tr');
              const name = row.cells[0].innerText.trim();
              const email = row.cells[1].innerText.trim();
              const etablissementId = button.getAttribute('data-etablissement-id');
              const role = "RAQ";
  
              document.getElementById('editUserId').value = userId;
              document.getElementById('editName').value = name;
              document.getElementById('editEmail').value = email;
              document.getElementById('editRole').value = role;
              document.getElementById('editFil').value = etablissementId;
  
              editForm.action = `/RAQ/utilisateurs/${userId}`;
              editModal.style.display = 'block';
          });
      });
  
      closeModalBtn.addEventListener('click', () => {
          editModal.style.display = "none";
      });
  
      window.addEventListener('click', (event) => {
          if (event.target === editModal) {
              editModal.style.display = "none";
          }
      });
  
      // Confirmation de la suppression avec SweetAlert
      supprimerForms.forEach(form => {
          form.addEventListener('submit', function(event) {
              event.preventDefault(); // Empêche l'envoi du formulaire directement
              Swal.fire({
                  title: 'Êtes-vous sûr ?',
                  text: "Cette action est irréversible !",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Oui, supprimer !',
                  cancelButtonText: 'Annuler'
              }).then((result) => {
                  if (result.isConfirmed) {
                      form.submit(); // Si confirmé, envoie le formulaire
                  }
              });
          });
      });
  });
  </script><script>
document.addEventListener('DOMContentLoaded', () => {
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

    const modifierBtns = document.querySelectorAll('.modifierBtn');
    const supprimerForms = document.querySelectorAll('.supprimerForm');
    const editModal = document.getElementById('editModal');
    const closeModalBtn = editModal.querySelector('.close');
    const editForm = editModal.querySelector('form');
    
    modifierBtns.forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.getAttribute('data-id');
            const row = button.closest('tr');
            const name = row.cells[0].innerText.trim();
            const email = row.cells[1].innerText.trim();
            const etablissementId = button.getAttribute('data-etablissement-id');
            const role = "RAQ";

            document.getElementById('editUserId').value = userId;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editFil').value = etablissementId;

            editForm.action = `/RAQ/utilisateurs/${userId}`;
            editModal.style.display = 'block';
        });
    });

    closeModalBtn.addEventListener('click', () => {
        editModal.style.display = "none";
    });

    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            editModal.style.display = "none";
        }
    });

    // Confirmation de la suppression avec SweetAlert
    supprimerForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Empêche l'envoi du formulaire directement
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Si confirmé, envoie le formulaire
                }
            });
        });
    });
});
</script>

  
@endsection

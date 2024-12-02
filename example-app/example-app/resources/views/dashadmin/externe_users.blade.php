@extends('dashadmin.home')
@section('content')
<head>
  <link rel="stylesheet" href="{{ asset('assets/css/ajout.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/edit.css') }}">
</head>
<main id="main" class="main">
  <div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Les évaluateur_externe</h1>
    </div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
            <li class="breadcrumb-item">les évaluateur_externe</li>
        </ol>
    </nav>
</div>
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
          
              
              <!-- Button to open the modal -->
              <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title">Les évaluateur_externe</h6>
                <button id="ajouterBtn" class="btn btn-primary mb-3"><i class="bi bi-plus-lg"></i></button>
              </div>

              <!-- Modal for the form -->
              <div id="formModal" class="modal">
                  <div class="modal-content">
                      <form id="ajouterForm" action="{{ route('store_userEx') }}" method="POST">
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
                          <label for="password">Confirmer le mot de passe:</label>
                          <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
                          <br><br>
                          <label for="role">Rôle:</label>
                          <select id="role" name="role" class="form-control" required>
                            <option value="evaluateur_e">évaluateur_Ex</option>
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
                   
                    <th colspan="2">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($users as $user)
                    <tr>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->email }}</td>
                     
                      <td>
                        <button class="btn btn-sm modifierBtn" data-id="{{ $user->id }}"><i class="bi bi-pencil-fill text-warning"></i> Modifier</button>
                      </td>
                      <td>
                        <form action="{{ route('destroy_userEx', $user->id) }}" method="POST" class="supprimerForm">
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
            <form id="editForm"  method="POST">
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
                <input type="password" id="editPassword" name="password" required>
                <br><br>
                <label for="editRole">Rôle:</label>
                <select id="editRole" name="role" class="form-control" required>
                  <option value="evaluateur_e">évaluateur_Ex</option>
                </select>
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
                const userId = button.getAttribute('data-id');
                const row = button.closest('tr');
                const name = row.cells[0].innerText;
                const email = row.cells[1].innerText;
                const role = row.cells[2].innerText;

                // Call the function to open the edit form with selected user data
                openEditModal(userId, name, email, role);
            });
        });

        // Function to open the edit modal
        function openEditModal(userId, name, email, role) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editForm').action = "/userEx/" + userId + "/modifier"; // Set the action of the form with user ID
            editModal.style.display = "block";
        }

        // Event listener for delete forms
        supprimerForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                if (confirm('Are you sure you want to delete this user?')) {
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


<script src="https://code.jquery.com/jquery-3.5.1.js"></script>

@endsection
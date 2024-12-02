@extends('RAQ.home')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Les Départements  associée a : {{ $institution? $institution->nom : '..'}} - {{ $etablissement? $etablissement->nom : '..'}}</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashRAQ') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Les Départements</li>
            </ol>
        </nav>
    </div>
  
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Tableau des départements</h5>
                        <button type="button" class="btn btn-primary btn-sm" id="addDepartementBtn">
                            <i class="bi bi-plus-lg">Ajouter</i>
                        </button>
                    </div>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Nom</th>
                          <th scope="col">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($departements as $departement)
                        <tr>
                          <th scope="row">{{ $loop->iteration }}</th>
                          <td>{{ $departement->nom }}</td>
                          
                          <td>
                            <button type="button" class="btn btn-sm transparent-button mr-2 editButton"
                            data-id="{{ $departement->id }}"
                            data-nom="{{ $departement->nom }}"
                            >
                            <i class="bi bi-pencil-fill text-warning"></i> Modifier
                        </button>
                            <button type="button" class="btn btn-sm transparent-button mr-2 deleteButton" data-toggle="modal" data-target="#confirmDeleteModal{{$departement->id}}">
                                <i class="bi bi-trash-fill text-danger"></i> Supprimer
                            </button>
                          </td>
                        </tr>
                        

                        <div class="modal fade" id="confirmDeleteModal{{$departement->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel{{$departement->id}}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteModalLabel{{$departement->id}}">Confirmation de suppression</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous sûr de vouloir supprimer ce département ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                        <form action="{{ route('departement.destroyR', $departement->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

@if(session('success'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "error",
            title: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 3000
        });
    </script>
@endif

<script>
    // Edit button functionality
    $('.editButton').on('click', function () {
        var id = $(this).data('id');
        var nom = $(this).data('nom').replace(/'/g, "&apos;");


       

        Swal.fire({
            title: 'Modifier Département',
            html: `
                <form id="editForm" action="/departementR/${id}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class='form-group'>
                        <label for='nomInput'>Nom:</label>
                        <input type='text' class='form-control' id='nomInput' name='nom' value='${nom}'>
                    </div>
                   
                </form>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Modifier',
            cancelButtonText: 'Annuler',
            preConfirm: () => {
                document.getElementById('editForm').submit();
            }
        });
    });

    // Add button functionality
    $('#addDepartementBtn').on('click', function () {
       

        Swal.fire({
            title: 'Ajouter Département',
            html: `
                <form id="addForm" action="{{ route('departement.storeR') }}" method="POST">
                    @csrf
                    <div class='form-group'>
                        <label for='nomInput'>Nom:</label>
                        <input type='text' class='form-control' id='nomInput' name='nom' required>
                    </div>
                  
                </form>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ajouter',
            cancelButtonText: 'Annuler',
            preConfirm: () => {
                document.getElementById('addForm').submit();
            }
        });
    });
</script>

@endsection

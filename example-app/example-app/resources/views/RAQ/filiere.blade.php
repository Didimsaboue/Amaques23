@extends('RAQ.home')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Les Filieres associée a : {{ $institution? $institution->nom : '..'}} - {{ $etablissement? $etablissement->nom : '..'}}</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashRAQ') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Les Filieres </li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Tableau des Filieres</h5>
                        <button type="button" class="btn btn-primary btn-sm" id="addDepartementBtn">
                          <i class="bi bi-plus-lg">ajouter</i>
                      </button>
                    </div>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Filiere</th>
                          <th scope="col">Département</th> 
                          <th scope="col">Actions</th>
                      
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($filieres as $filiere)
                        <tr>
                          <th scope="row">{{ $loop->iteration }}</th>
                          <td>{{ $filiere->nom }}</td>
                          <td>{{ $filiere->departement ? $filiere->departement->nom : 'N/A' }}</td>
                     
                          <td>
                            <button type="button" class="btn btn-lg transparent-button mr-2 showButton"
                            data-id="{{ $filiere->id }}"
                            data-nom="{{ $filiere->nom }}"
                            data-dep-id="{{ $filiere->departement ? $filiere->departement->id : null }}"
                            data-dep-nom="{{ $filiere->departement ? $filiere->departement->nom : 'N/A' }}"
                          
                            data-date-habilitation="{{ $filiere->date_habilitation }}">
                            <i class="bi bi-eye-fill text-primary"></i> 
                            </button>
                            <button type="button" class="btn btn-lg transparent-button mr-2 editButton"
                            data-id="{{ $filiere->id }}"
                            data-nom="{{ $filiere->nom }}"
                            data-dep-id="{{ $filiere->departement ? $filiere->departement->id : null }}"
                            data-dep-nom="{{ $filiere->departement ? $filiere->departement->nom : 'N/A' }}"
                            data-date-habilitation="{{ $filiere->date_habilitation }}"
                      >
                            <i class="bi bi-pencil-fill text-warning"></i> 
                        </button>
                            <button type="button" class="btn btn-lg transparent-button mr-2 deleteButton" data-toggle="modal" data-target="#confirmDeleteModal" data-filiere-id="{{$filiere->id}}">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
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
</main>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette filiere ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
   $('.editButton').on('click', function () {
        var id = $(this).data('id');
        var nom = $(this).data('nom').replace(/'/g, "&apos;");
        var depId = $(this).data('dep-id');
        var depNom = $(this).data('dep-nom').replace(/'/g, "&apos;");
        var dateHabilitation = $(this).data('date-habilitation');

        var depList = "<select id='depSelect' name='departements' class='form-control'>";
          if (depId) {
            depList += `<option value='${depId}' selected>${depNom}</option>`;
        } else {
            depList += `<option value='' selected>N/A</option>`;
        }
        @foreach($departements as $dep)
        depList += `<option value='{{ $dep->id }}'>{{ $dep->nom }}</option>`;
        @endforeach
        depList += "</select>";

        Swal.fire({
            title: 'Modifier Filiére',
            html: `
                <form id="editForm" action="/filiereR/${id}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class='form-group'>
                        <label for='nomInput'>Nom:</label>
                        <input type='text' class='form-control' id='nomInput' name='nom' value='${nom}'>
                    </div>
                    <div class='form-group'>
                        <label for='depSelect'>Département:</label>
                        ${depList}
                    </div>
                    <div class='form-group'>
                        <label for='dateHabilitationInput'>Date d'habilitation:</label>
                        <input type='date' class='form-control' id='dateHabilitationInput' name='date_habilitation' value='${dateHabilitation}'>
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

    $(document).ready(function () {
        $('.deleteButton').on('click', function () {
            var filiereId = $(this).data('filiere-id');
            var form = $('#confirmDeleteModal').find('.delete-form');
            form.attr('action', '/filiereR/' + filiereId);
            $('#confirmDeleteModal').modal('show');
        });
    });

  $('#addDepartementBtn').on('click', function () {
    var depList = "<select id='depSelect' name='departements' class='form-control'>";
    @foreach($departements as $dep)
    depList += `<option value='{{ $dep->id }}'>{{ $dep->nom }}</option>`;
    @endforeach
    depList += "</select>";

    Swal.fire({
        title: 'Ajouter Filiére',
        html: `
            <form id="addForm" action="{{ route('filiere.storeR') }}" method="POST">
                @csrf
                <div class='form-group'>
                    <label for='nomInput'>Nom:</label>
                    <input type='text' class='form-control' id='nomInput' name='nom' required>
                </div>
                <div class='form-group'>
                    <label for='depSelect'>Département:</label>
                    ${depList}
                </div>
                <div class='form-group'>
                    <label for='dateHabilitationInput'>Date d'habilitation:</label>
                    <input type='date' class='form-control' id='dateHabilitationInput' name='date_habilitation'>
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
$('.showButton').on('click', function () {
    var nom = $(this).data('nom');
    var depNom = $(this).data('dep-nom');
    var etaNom = $(this).data('eta-nom');
    var insNom = $(this).data('ins-nom');
    var dateHabilitation = $(this).data('date-habilitation');

    Swal.fire({
        title: 'Détails de la Filière',
        html: `
            <div class='form-group'>
                <label>Nom:</label>
                <p>${nom}</p>
            </div>
            <div class='form-group'>
                <label>Département:</label>
                <p>${depNom}</p>
            </div>
          
            <div class='form-group'>
                <label>Date d'habilitation:</label>
                <p>${dateHabilitation}</p>
            </div>
          
           
        `,
        icon: 'info',
        confirmButtonText: 'Fermer'
    });
});

</script> 
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
            timer: 70000
        });
    </script>
@endif

@endsection

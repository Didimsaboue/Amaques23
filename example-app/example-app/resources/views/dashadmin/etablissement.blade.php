@extends('dashadmin.home')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Les Établissements</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Les Établissements</li>
            </ol>
        </nav>
    </div>
  
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Tableau des établissements</h5>
                        <button type="button" class="btn btn-primary btn-sm" id="addEtablissementBtn">
                            <i class="bi bi-plus-lg">Ajouter</i>
                        </button>
                    </div>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Nom</th>
                          <th scope="col">Institution</th>
                          <th scope="col">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($etablissements as $etablissement)
                        <tr>
                          <th scope="row">{{ $loop->iteration }}</th>
                          <td>{{ $etablissement->nom }}</td>
                          <td>
                            @if($etablissement->institution)
                              {{ $etablissement->institution->nom }}
                            @else
                              N/A
                            @endif
                          </td>
                          <td>
                            <button type="button" class="btn btn-sm transparent-button mr-2 editButton"
                            data-id="{{ $etablissement->id }}"
                            data-nom="{{ $etablissement->nom }}"
                            data-institution-id="{{ $etablissement->institution ? $etablissement->institution->id : null }}"
                            data-institution-nom="{{ $etablissement->institution ? $etablissement->institution->nom : 'N/A' }}">
                            <i class="bi bi-pencil-fill text-warning"></i> Modifier
                        </button>
                            <button type="button" class="btn btn-sm transparent-button mr-2 deleteButton" data-toggle="modal" data-target="#confirmDeleteModal{{$etablissement->id}}" >
                                <i class="bi bi-trash-fill text-danger"></i> Supprimer
                            </button>
                          </td>
                        </tr>
                        <div class="modal fade" id="confirmDeleteModal{{$etablissement->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel{{$etablissement->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteModalLabel{{$etablissement->id }}">Confirmation de suppression</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous sûr de vouloir supprimer ce département ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                        <form action="{{ route('etablissement.destroy', $etablissement->id ) }}" method="POST">
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
            timer:70000
        });
    </script>
@endif
<script>
    // Edit button functionality
    $('.editButton').on('click', function () {
        var id = $(this).data('id');
        var nom = $(this).data('nom').replace(/'/g, "&apos;");
        var institutionId = $(this).data('institution-id');
        var institutionNom = $(this).data('institution-nom').replace(/'/g, "&apos;");

        var institutionsList = "<select id='institutionSelect' name='institution' class='form-control'>";
        if (institutionId) {
            institutionsList += `<option value='${institutionId}' selected>${institutionNom}</option>`;
        } else {
            institutionsList += `<option value='' selected>N/A</option>`;
        }
        @foreach($institutions as $inst)
            institutionsList += `<option value='{{ $inst->id }}'>{{ $inst->nom }}</option>`;
        @endforeach
        institutionsList += `<option value=''>N/A</option>`;
        institutionsList += "</select>";

        Swal.fire({
            title: 'Modifier Établissement',
            html: `
                <form id="editForm" action="/etablissement/${id}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class='form-group'>
                        <label for='nomInput'>Nom:</label>
                        <input type='text' class='form-control' id='nomInput' name='nom' value='${nom}'>
                    </div>
                    <div class='form-group'>
                        <label for='institutionSelect'>Institution:</label>
                        ${institutionsList}
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
    $('#addEtablissementBtn').on('click', function () {
    var institutionsList = "<select id='institutionSelect' name='institution' class='form-control'>";
    @foreach($institutions as $inst)
        institutionsList += `<option value='{{ $inst->id }}'>{{ $inst->nom }}</option>`;
    @endforeach
    institutionsList += `<option value=''>N/A</option>`;
    institutionsList += "</select>";

    Swal.fire({
        title: 'Ajouter Établissement',
        html: `
            <form id="addForm" action="{{ route('etablissement.store') }}" method="POST">
                @csrf
                <div class='form-group'>
                    <label for='nomInput'>Nom:</label>
                    <input type='text' class='form-control' id='nomInput' name='nom' required>
                </div>
                <div class='form-group'>
                    <label for='institutionSelect'>Institution:</label>
                    ${institutionsList}
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
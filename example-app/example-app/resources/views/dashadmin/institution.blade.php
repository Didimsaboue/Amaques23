@extends('dashadmin.home')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>les institutions</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
                <li class="breadcrumb-item">les institutions</li>
            </ol>
        </nav>
    </div>
  
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">les institutions</h5> 
                            <button type="button" class="btn btn-primary btn-lg" id="addInstitutionBtn">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($institutions as $institution)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $institution->nom }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="button" class="btn btn-sm transparent-button mr-2" data-toggle="modal" data-target="#editInstitutionModal{{$institution->id}}">
                                                <i class="bi bi-pencil-fill text-warning"></i> Modifier
                                            </button>
                                            <button type="button" class="btn btn-sm transparent-button mr-2 deleteButton" data-toggle="modal" data-target="#confirmDeleteModal{{$institution->id}}" >
                                                <i class="bi bi-trash-fill text-danger"></i> Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <div class="modal fade" id="editInstitutionModal{{$institution->id}}" tabindex="-1" role="dialog" aria-labelledby="editInstitutionModalLabel{{$institution->id}}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editInstitutionModalLabel{{$institution->id}}">Modifier une institution</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route('institutions.update', $institution->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="nom{{$institution->id}}">Nom de l'institution</label>
                                                        <input type="text" class="form-control" id="nom{{$institution->id}}" name="nom" value="{{ $institution->nom }}" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="confirmDeleteModal{{$institution->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel{{$institution->id}}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmDeleteModalLabel{{$institution->id}}">Confirmation de suppression</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Êtes-vous sûr de vouloir supprimer ce institution ?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                <form action="{{ route('institutions.destroy', $institution->id) }}" method="POST">
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

<div class="modal fade" id="addInstitutionModal" tabindex="-1" role="dialog" aria-labelledby="addInstitutionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInstitutionModalLabel">Ajouter une institution</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('institutions.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="nom">Nom de l'institution</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- 
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
                Êtes-vous sûr de vouloir supprimer cette institution ?
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
</div> --}}

    


<script>
    var addInstitutionBtn = document.getElementById('addInstitutionBtn');
    var addInstitutionModal = document.getElementById('addInstitutionModal');
    
    addInstitutionBtn.addEventListener('click', function () {
        $(addInstitutionModal).modal('show');
    });

    // $(document).ready(function () {
    //     $('.deleteButton').on('click', function () {
    //         var institutionId = $(this).data('institution-id');
    //         var form = $('#confirmDeleteModal').find('.delete-form');
    //         form.attr('action', '/institutions/' + institutionId);
    //         $('#confirmDeleteModal').modal('show');
    //     });
    // });
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
            timer: 7000
        });
    </script>
@endif

@endsection

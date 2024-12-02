@extends('dashadmin.home')

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>campagnes</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
                <li class="breadcrumb-item">campagnes</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#createInvitationModal"><i class="bi bi-plus"></i>Créer une Campagne</button>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success mt-2">
                <p>{{ $message }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-2">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table class="table table-bordered mt-2">
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Statue</th>
                <th>Actions</th>
            </tr>
            @foreach ($invitations as $invitation)
            <tr>
                <td>{{ $invitation->nom }}</td>
                <td>{{ $invitation->description }}</td>
                <td>{{ date('Y-m-d', strtotime($invitation->date_debut)) }}</td>
                <td>{{ date('Y-m-d', strtotime($invitation->date_fin)) }}</td>
                <td>{{ $invitation->statue ? 'Active' : 'Inactive' }}</td>
                <td>
                    <button class="btn btn-info" data-toggle="modal" data-target="#showInvitationModal{{ $invitation->id }}"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-warning" data-toggle="modal" data-target="#editInvitationModal{{ $invitation->id }}"><i class="fas fa-edit"></i></button>
                    @if ($invitation->statue)
                    <a href="{{ route('invitations.invite', $invitation->id) }}" class="btn btn-success"><i class="fas fa-envelope"></i></a>
                @endif
                </td>
            </tr>

            <!-- Show Modal -->
            <div class="modal fade" id="showInvitationModal{{ $invitation->id }}" tabindex="-1" aria-labelledby="showInvitationModalLabel{{ $invitation->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="showInvitationModalLabel{{ $invitation->id }}">{{ $invitation->nom }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Description:</strong> {{ $invitation->description }}</p>
                            <p><strong>Date de début:</strong> {{ $invitation->date_debut }}</p>
                            <p><strong>Date de fin:</strong> {{ $invitation->date_fin }}</p>
                            <p><strong>Statue:</strong> {{ $invitation->statue ? 'Active' : 'Inactive' }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editInvitationModal{{ $invitation->id }}" tabindex="-1" aria-labelledby="editInvitationModalLabel{{ $invitation->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editInvitationModalLabel{{ $invitation->id }}">Modifier {{ $invitation->nom }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('invitations.update', $invitation->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="nom">Nom:</label>
                                    <input type="text" name="nom" value="{{ $invitation->nom }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description:</label>
                                    <textarea name="description" class="form-control" required>{{ $invitation->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="date_debut">Date de début:</label>
                                    <input type="date" name="date_debut" value="{{ $invitation->date_debut->format('Y-m-d') }}" class="form-control" disabled required>
                                </div>
                                <div class="form-group">
                                    <label for="date_fin">Date de fin:</label>
                                    <input type="date" name="date_fin" value="{{ $invitation->date_fin->format('Y-m-d') }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="statue">Statue:</label>
                                    <select name="statue" class="form-control" required>
                                        <option value="1" {{ $invitation->statue ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$invitation->statue ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </table>

        <!-- Create Modal -->
        <div class="modal fade" id="createInvitationModal" tabindex="-1" aria-labelledby="createInvitationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createInvitationModalLabel">Créer une Campagne</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('invitations.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nom">Nom:</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="date_debut">Date de début:</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="date_fin">Date de fin:</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="statue">Statue:</label>
                                <select name="statue" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
@push('scripts')
<script>
    @if ($errors->has('error'))
        alert('{{ $errors->first('error') }}');
    @endif
</script>
@endpush

@endsection

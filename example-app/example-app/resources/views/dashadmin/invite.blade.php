@extends('dashadmin.home')

@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Inviter des utilisateurs à la campagne: {{ $invitation->nom }}</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Les campagnes</a></li>
                <li class="breadcrumb-item active">Inviter</li>
            </ol>
        </nav>
    </div>
  
    <section class="section">
    @if ($message = Session::get('success'))
        <div class="alert alert-success mt-2">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger mt-2">
            <p>{{ $message }}</p>
        </div>
    @endif

    <form action="{{ route('invitations.sendInvitations', $invitation->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <input type="text" id="search" class="form-control" placeholder="Rechercher ">
        </div>

        <table class="table table-bordered mt-2" id="usersTable">
            <thead>
                <tr>
                    <th>Sélectionner</th>
                    <th>Email</th>
                    <th>Institution</th>
                    <th>Etablissement</th>
                    <th>Filière</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>
                        <input type="checkbox" name="emails[]" value="{{ $user->email }}">
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->filiere)
                            {{ $user->filiere->departement->etablissement->institution->nom }}
                        @else
                            Aucune filière associée
                        @endif
                    </td>
                    <td>
                        @if ($user->filiere)
                            {{ $user->filiere->departement->nom }}
                        @else
                            Aucune filière associée
                        @endif
                        
                    </td>
                    <td>
                        @if ($user->filiere)
                            {{ $user->filiere->nom }}
                        @else
                            Aucune filière associée
                        @endif
                        
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Aucun utilisateur trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary" id="sendButton" >Envoyer</button>
    </form>

    <h2>Utilisateurs déjà invités</h2>
    <table class="table table-bordered mt-2">
        <thead>
            <tr>
                <th>Email</th>
                <th>Institution</th>
                <th>Etablissement</th>
                <th>Filière</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invitedUsers as $user)
            <tr>
                <td>{{ $user->email }}</td>
                <td>
                    @if ($user->filiere)
                        {{ $user->filiere->departement->etablissement->institution->nom }}
                    @else
                        Aucune filière associée
                    @endif
                </td>
                <td>
                    @if ($user->filiere)
                        {{ $user->filiere->nom }}
                    @else
                        Aucune filière associée
                    @endif
                </td>
                <td>
                    @if ($user->filiere)
                        {{ $user->filiere->departement->nom }}
                    @else
                        Aucune filière associée
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Aucun utilisateur invité</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </section>
</main>

@push('scripts')
<script>
    // Recherche dans le tableau des utilisateurs
    document.getElementById('search').addEventListener('keyup', function() {
        var searchText = this.value.toLowerCase();
        var rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach(function(row) {
            var email = row.cells[1].innerText.toLowerCase();
            var institution = row.cells[2].innerText.toLowerCase();
            var etablissement = row.cells[3].innerText.toLowerCase();
            var filiere = row.cells[4].innerText.toLowerCase();
            if (email.includes(searchText) || institution.includes(searchText) || etablissement.includes(searchText) || filiere.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Activer/désactiver le bouton Envoyer
    document.querySelectorAll('input[name="emails[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var sendButton = document.getElementById('sendButton');
            var checked = document.querySelectorAll('input[name="emails[]"]:checked').length > 0;
            sendButton.disabled = !checked;
        });
    });
</script>
@endpush

@endsection

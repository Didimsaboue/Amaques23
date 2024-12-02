@extends('dashadmin.home')

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Les campagnes</a></li>
                <li class="breadcrumb-item active">Champs évalués</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h4>Champs évalués</h4>
                <table class="table table-bordered mt-2" id="usersTable">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Résultat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($champs as $champ)
                            <tr>
                                <td>
                                    @if ($champ->champ)
                                        {{ $champ->champ->name }}
                                    @else
                                        Aucune champ associée
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('champs.resultats', ['filiereInviteId' => $filiereId, 'champId' => $champ->idchamps]) }}" class="btn btn-primary">Voir les résultats</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Aucun champ évalué trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
@endsection

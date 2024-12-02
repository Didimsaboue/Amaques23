@extends('dashadmin.home')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Filières invitées</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Les campagnes</a></li>
                    <li class="breadcrumb-item active">Filières invitées</li>
                </ol>
            </nav>
        </div>
        
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filières invitées</h5>
                    <table class="table table-bordered mt-2" id="usersTable">
                        <thead>
                            <tr>
                                <th>Filière</th>
                                <th>Nombre de champs évalués</th>
                                <th>Moyenne du taux de conformité (%)</th>
                                <th>Champs évalués</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($filiereStats as $stat)
                                <tr>
                                    <td>{{ $stat['filiere'] }}</td>
                                   
                                    <td>{{ $stat['nombreChampsEvalues'] }}</td>
                                    <td>{{ number_format($stat['moyenneTauxConformite'], 2) }}%</td>
                                    <td>
                                        <a href="{{ route('filieres.champs', $stat['id']) }}" class="btn btn-primary">Voir les champs évalués</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Aucune filière invitée trouvée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
@endsection

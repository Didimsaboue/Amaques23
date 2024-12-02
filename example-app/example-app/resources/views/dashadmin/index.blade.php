@extends('dashadmin.home')

@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Liste des campagnes</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
                <li class="breadcrumb-item">Les campagnes</li>
            </ol>
        </nav>
    </div>
  
    <section class="section">

        <table class="table table-bordered mt-2" id="usersTable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Filières</th>

                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                <tr>
                    <td>
                        {{ $campaign->nom }}
                    </td>
                    <td>{{ $campaign->description }}</td>
                    <td><a href="{{ route('campaigns.filieres', $campaign->id) }}">Voir les filières invitées</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</main>
@endsection

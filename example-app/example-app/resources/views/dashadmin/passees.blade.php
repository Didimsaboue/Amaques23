@extends('dashadmin.home')

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Les notifications</h1>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
                <li class="breadcrumb-item active">Les notifications</li>
            </ol>
        </nav>
    </div>
  
    <section class="section">
        <div class="container">
            <h2>Notifications passées</h2>
            <ul>
                @forelse($notifications as $notification)
                    <li>
                        <strong>{{ $notification->title }}</strong>
                        <p>{{ $notification->message }}</p>
                        <p>{{ $notification->created_at }}</p>
                    </li>
                @empty
                    <li>Pas de notifications passées</li>
                @endforelse
            </ul>
        </div>
    </section>
</main>
@endsection

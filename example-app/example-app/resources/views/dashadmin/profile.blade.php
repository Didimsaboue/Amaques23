@extends('dashadmin.home')
@section('content')
<main id="main" class="main">
    <!-- resources/views/layouts/app.blade.php -->

    <div class="pagetitle">
        <h1>Profile</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">dashboard</a></li>
                <li class="breadcrumb-item">Profil</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <img src="assets/img/amaqes2.png" alt="Profile" style="width: 150px; height: 100px;">
                        <h2>{{ session('user_name') }}</h2>
                        <h3>Admin</h3>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Vue d'ensemble</button>
                            </li>
                            @if(session('user_email') != 'amaqes@gmail.com')
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Modifier Profil</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Changer Mot de Passe</button>
                            </li>
                            @endif
                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <h5 class="card-title">À propos</h5>
                                <p class="small fst-italic">Est un employé d'AMAQES.</p>

                                <h5 class="card-title">Détails du profil</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label ">Nom</div>
                                    <div class="col-lg-9 col-md-8">{{ session('user_name') }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Role</div>
                                    <div class="col-lg-9 col-md-8">Admin</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8">{{ session('user_email') }}</div>
                                </div>
                                @if(session('user_email') == 'amaqes@gmail.com')
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Mot de passe</div>
                                    <div class="col-lg-9 col-md-8">12AB*/mr$</div>
                                </div>
                                @endif
                            </div>

              <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                <!-- Profile Edit Form -->
                @if(session('success'))
                  <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                  <div class="alert alert-danger">
                    <ul>
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
                
                <div class="row mb-3">
                  <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Image de profil</label>
                  <div class="col-md-8 col-lg-9">
                      <img src="{{ asset('assets/img/amaqes2.png') }}" alt="Profile">
                  </div>
                </div>
                <form method="POST" action="{{ route('profil.update') }}">
                  @csrf
                  @method('PUT')
                  
                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Nom</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="name" type="text" class="form-control" id="fullName" value="{{ session('user_name') }}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="role" class="col-md-4 col-lg-3 col-form-label">Rôle</label>
                    <div class="col-md-8 col-lg-9">
                      <select id="role" name="role" class="form-control" required>
                        <option value="admin" {{ session('user_role') == 'admin' ? 'selected' : '' }}>admin</option>
                      </select>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="email" type="email" class="form-control" id="Email" value="{{ session('user_email') }}">
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                  </div>
                </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password admin qui login Form  -->
                 
                  @if($errors->any())
                    <div class="alert alert-danger">
                      <ul>
                        @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
  
                  <!-- Change Password Form -->
                  <form method="POST" action="{{ route('profile.update-password') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Mot de passe actuel</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="current_password" type="password" class="form-control" id="currentPassword" required>
                      </div>
                    </div>
  
                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Nouveau mot de passe</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="new_password" type="password" class="form-control" id="newPassword" required>
                      </div>
                    </div>
  
                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Confirmer le nouveau mot de passe</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="new_password_confirmation" type="password" class="form-control" id="renewPassword" required>
                      </div>
                    </div>
  
                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->



</html>
@endsection

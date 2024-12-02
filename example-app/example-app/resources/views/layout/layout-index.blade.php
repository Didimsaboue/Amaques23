<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>crud</title>
    <link href="{{ asset('assets/img/amaqes2.png') }}" rel="icon">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  
  <link rel="stylesheet" href="{{ asset('assets/css/nav/style.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
 
</head>
<body>
<nav class="navbar">
    <div class="navbar-container container">
        <input type="checkbox" name="" id="">
        <div class="hamburger-lines">
            <span class="line line1"></span>
            <span class="line line2"></span>
            <span class="line line3"></span>
        </div>
        <ul class="menu-items">
            <li><a href="/indexevaluation">Page d'accueil</a></li>
            <li><a href="/scores_champ">Résultat</a></li>
            <li><a href="#testimonials" id="profile-link" class="nn">Se déconnecter</a></li>
        </ul>
        <div class="logo">
            <img src="{{ asset('assets/img/amaqes2.png') }}" alt="Logo" height="30">
            <h3 style="display: inline-block; margin-left: 0px;">Autorité Mauritanienne d'Assurance <br>Qualité L'enseignemt Superieur</h3>
        </div>
        <div class="profile-dropdown">
            <a href="{{route('logout')}}" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
</nav>
@yield('content')
<footer id="footer" class="footer">
    <div class="copyright">
      Tous Droit Réservés &copy; 2024 <a href="https://amaqes.mr//"><strong><span>AMAQES</span></strong></a>. 
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Developper par <a href="http://supnum.mr/"><strong><span>SupNum</span></strong></a>
    </div>
  </footer><!-- End Footer -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
<script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
{{-- <script src="{{ asset('assets/js/main.js') }}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var profileLink = document.getElementById('profile-link');
        var profileDropdown = document.querySelector('.profile-dropdown');

        profileLink.addEventListener('click', function(event) {
            event.preventDefault();
            profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
        });

        // Fermez le menu déroulant lorsque vous cliquez n'importe où en dehors de celui-ci
        window.addEventListener('click', function(event) {
            if (!event.target.matches('#profile-link')) {
                profileDropdown.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>


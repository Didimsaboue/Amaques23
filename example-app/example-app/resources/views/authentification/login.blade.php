<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>crud</title>
   
  <link rel="stylesheet" href="{{ asset('assets/css/login/style.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  
</head>
<body>
    <div class="login-form">
  
        <h1>Login</h1>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

      

        <form action="{{ route('login') }}" method="POST">
            @csrf <!-- Ajoutez ceci pour protéger votre formulaire contre les attaques CSRF -->
            <input type="text" name="email" placeholder="Email"  value="{{old('email')}}" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <div class="custom-control custom-chekbox">
                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                <label class="custom-control-label" for="remember-me">Remember Me</label>
            </div>
        </form>
       
        <a href="#">Forgot password?</a>
        <div class="signup-link">
            <p>Don't have an account? <a href="/register">Sign up</a></p>
        </div>
    </div>
    
</body>

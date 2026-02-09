<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        header {
            padding: 16px 0;
            margin-bottom: 24px;
            margin-left: 5%;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        header img {
            height: 150px;
        }

        .main-container {
            display: flex;
            justify-content: space-between;
            padding: 0 5%;
        }

        .left-column {
            width: 13%;
            margin-right: 2%;
            padding-top: 2%;
        }

        .right-column {
            width: 30%;
            margin-right: auto;  
            margin-left: 10%;  
            padding-top: 2%; 
            padding-bottom: 7%;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .container {
            margin-top: 30px;
        }

        .button-24 {
            background: #003366;
            border: 1px solid #003366;
            box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
            box-sizing: border-box;
            color: #FFFFFF;
            cursor: pointer;
            display: inline-block;
            font-family: nunito, roboto, proxima-nova, "proxima nova", sans-serif;
            font-size: 16px;
            font-weight: 800;
            min-height: 40px;
            padding: 12px 14px;
            text-align: center;
            text-rendering: geometricprecision;
            text-transform: none;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: middle;
            width: 100%;
            text-decoration: none;
        }

        .card-header-blau {
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #003366;
            color: white;
            padding-top: 3%;
            padding-bottom: 3%;
            text-align: center;
        }

        .dozenten-card {
            margin-top: 25%;
        }

        .button-24:hover {
            background-color: initial;
            color: #003366;
        }
        .button-24-hover {
            background-color: initial;
            color: #003366;
        }
        .button-24:active {
            background-color: initial;
            color: #003366; 
        }

        .form-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
        }

        .card-header {
            font-size: 1.5rem; 
            font-weight: bold;
            background-color: #003366;
            color: white;
        }
        
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <a href="{{ route('home') }}">
            <img src="/images/ostfalia-logo.jpg" alt="Ostfalia Logo">
        </a>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Left Column (Navigation) -->
        <div class="left-column">
            <div class="btn-group-vertical w-100">
                <a href="{{ route('welcome') }}" class="button-24 {{ request()->routeIs('welcome') ? 'button-24-hover' : '' }}">Home</a>
                
                @auth
                    @if(auth()->user()->roll == 'Prof')
                        <a href="{{ route('prof.dashboard') }}" class="button-24 {{ request()->routeIs('prof.dashboard') ? 'button-24-hover' : '' }}">Ihre Thesis</a> 
                        <a href="{{ route('prof.geheimthesis') }}" class="button-24 {{ request()->routeIs('prof.geheimthesis') ? 'button-24-hover' : '' }}">Ihre Geheim-Thesis</a>
                        <a href="{{ route('prof.uebersicht') }}" class="button-24 {{ request()->routeIs('prof.uebersicht') ? 'button-24-hover' : '' }}">Ihre Übersicht</a>
                    @endif
                    @if(auth()->user()->roll == 'Admin')
                        <a href="{{ route('admin-dashboard') }}" class="button-24 {{ request()->routeIs('admin-dashboard') ? 'button-24-hover' : '' }}">Admin Dashboard</a>
                    @endif
                @endauth
                
                @auth
                    @if(auth()->check() && auth()->user()->roll == 'Student')
                        <a href="{{ route('student.merkliste') }}" class="button-24 {{ request()->routeIs('student.merkliste') ? 'button-24-hover' : '' }}">Merkliste</a>
                    @endif
                @endauth
                
                @auth
                    <a href="{{ route('logout') }}" class="button-24"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Abmelden
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="button-24 {{ request()->routeIs('login') ? 'button-24-hover' : '' }}">Anmelden</a>
                    <a href="{{ route('register') }}" class="button-24 {{ request()->routeIs('register') ? 'button-24-hover' : '' }}">Registrieren</a>
                @endauth
                
                <a href="{{ route('kontakt') }}" class="button-24 {{ request()->routeIs('kontakt') ? 'button-24-hover' : '' }}">Kontakt</a>
            </div>
            
            
            @if(auth()->check())
                <div class="card dozenten-card">
                    <div class="card-header-blau">
                        @if(auth()->user()->roll == 'Prof')
                            Dozenteninfos
                        @elseif(auth()->user()->roll == 'Student')
                            Studenteninfos
                        @elseif(auth()->user()->roll == 'Admin')
                            Admininfos
                        @endif
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->roll == 'Prof')
                            <p><strong>Präfix:</strong> {{ auth()->user()->praefix }}</p>
                        @endif
                        <p><strong>Vorname:</strong> {{ auth()->user()->name }}</p>
                        <p><strong>Nachname:</strong> {{ auth()->user()->nachname }}</p>
                        <p><strong>E-Mail:</strong> {{ auth()->user()->email }}</p>
                        @if(auth()->user()->roll == 'Prof')
                            <a href="{{ route('prof.edit') }}" class="button-24 {{ request()->routeIs('prof.edit') ? 'button-24-hover' : '' }}">Profil bearbeiten</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="right-column">
            <div class="card">
                <div class="card-header text-center">
                    <span class="w-100">Registrieren</span>
                </div>

            <!-- Display Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Container -->
            <div class="form-container">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Präfix (Only for Prof) -->
                    <div class="mb-3" id="praefixDiv" style="display: none;">
                        <label for="praefix" class="form-label">Präfix</label>
                        <input type="text" class="form-control @error('praefix') is-invalid @enderror" id="praefix" name="praefix" value="{{ old('praefix') }}">
                        @error('praefix')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">First Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        <small class="text-muted">Erlaubt sind Buchstaben, Leerzeichen & Bindestriche. Erster Buchstabe groß</small>
                        @error('name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nachname" class="form-label">Last Name</label>
                        <input type="text" class="form-control @error('nachname') is-invalid @enderror" id="nachname" name="nachname" value="{{ old('nachname') }}" required>
                        <small class="text-muted">Erlaubt sind Buchstaben, Leerzeichen & Bindestriche. Erster Buchstabe groß</small>
                        @error('nachname')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        <small class="form-text text-muted">Ostfalia-E-Mail erforderlich (Format: id123456@ostfalia.de)</small>
                        @error('email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        <small class="text-muted">Mind. 8 Zeichen: Groß- u. Kleinbuchstaben sowie Sonderzeichen (!@#$%^&*).</small>
                        @error('password')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role Selection (Radio Buttons) -->
                    <div class="mb-3">
                        <label class="form-label">Role</label><br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="role_admin" name="roll" value="Admin" {{ old('roll') == 'Admin' ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_admin">
                                Admin
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="role_prof" name="roll" value="Prof" {{ old('roll') == 'Prof' ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_prof">
                                Prof
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="role_student" name="roll" value="Student" {{ old('roll') == 'Student' || !old('roll') ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_student">
                                Student
                            </label>
                        </div>
                        @error('roll')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Internal Code (Only for Admin or Prof) -->
                    <div class="mb-3" id="internalCodeDiv" style="display: none;">
                        <label for="internal_code" class="form-label">Internal Code</label>
                        <input type="text" class="form-control @error('internal_code') is-invalid @enderror" id="internal_code" name="internal_code" value="{{ old('internal_code') }}">
                        @error('internal_code')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="button-24">Registrieren</button>

           
                    <div class="mt-3 text-center">
                        <p>Haben Sie bereits ein Konto? <a href="{{ route('login') }}">Hier einloggen</a></p>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to manage visibility of fields based on selected role
        const praefixDiv = document.getElementById('praefixDiv');
        const internalCodeDiv = document.getElementById('internalCodeDiv');
        const rollRadios = document.querySelectorAll('input[name="roll"]');

        function updateVisibility() {
            const selectedRole = document.querySelector('input[name="roll"]:checked')?.value;
            
            // Internal code visible only for Admin or Prof
            if (selectedRole === 'Admin' || selectedRole === 'Prof') {
                internalCodeDiv.style.display = 'block';
            } else {
                internalCodeDiv.style.display = 'none';
            }

            // Präfix visible only for Prof
            if (selectedRole === 'Prof') {
                praefixDiv.style.display = 'block';
            } else {
                praefixDiv.style.display = 'none';
            }
        }

        // Event listener for role radio buttons
        rollRadios.forEach((radio) => {
            radio.addEventListener('change', updateVisibility);
        });

        // On page load, update visibility based on selected role
        window.addEventListener('DOMContentLoaded', updateVisibility);
    </script>

</body>
</html>

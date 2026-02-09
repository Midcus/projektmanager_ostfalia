<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        header { padding: 16px 0; margin-bottom: 24px; margin-left: 5%; text-align: left; border-bottom: 1px solid #ddd; }
        header img { height: 150px; }
        .main-container { display: flex; justify-content: space-between; padding: 0 5%; }
        .left-column { width: 13%; margin-right: 2%; padding-top: 2%; }
        .right-column { width: 30%; margin-right: auto; margin-left: 10%; padding-top: 2%; }
        .error { color: red; font-size: 14px; }
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
            background-position: 0 0;
            color: #003366;
        }

        .form-container { border: 1px solid #ddd; padding: 20px; border-radius: 5px; background-color: #f9f9f9; box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px; }
        .card-header { font-size: 1.5rem; font-weight: bold; background-color: #003366; color: white; }

    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}">
            <img src="/images/ostfalia-logo.jpg" alt="Ostfalia Logo">
        </a>
    </header>

    <div class="main-container">
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
                    <span class="w-100">Konto aktivieren</span>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-container">
                    <form method="POST" action="{{ route('activation.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            <small class="text-muted">Geben Sie die E-Mail ein, die Sie bei der Registrierung verwendet haben.</small>
                            @error('email')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="activation_code" class="form-label">Aktivierungscode</label>
                            <input type="text" class="form-control @error('activation_code') is-invalid @enderror" id="activation_code" name="activation_code" value="{{ old('activation_code') }}" required>
                            <small class="text-muted">Geben Sie den 6-stelligen Code ein, den Sie per E-Mail erhalten haben.</small>
                            @error('activation_code')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="button-24">Aktivieren</button>
                    </form>

                    @if(Session::get('resend_attempts', 0) >= 5)
                        <form method="GET" action="{{ route('activation.resend') }}" class="mt-3">
                            <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="{{ config('app.recaptcha.site_key') }}"></div>
                                @error('g-recaptcha-response')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="button-24">Code erneut senden</button>
                        </form>
                    @else
                        <div class="text-center mt-3">
                            <p>Code nicht erhalten? <a href="{{ route('resend.form') }}">Code erneut senden</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
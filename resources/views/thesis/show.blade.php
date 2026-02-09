<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thesis Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
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

        .button-24:hover {
            background-color: initial;
            color: #003366;
        }
        .button-24-hover {
            background-color: initial;
            color: #003366;
        }

        .button-24:active {
            opacity: .5;
        }

        header {
            padding: 16px 0;
            margin-bottom: 24px;
            margin-left: 5%;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #003366;
            color: white;
        }

        .container {
            margin-top: 30px;
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
            width: 85%;
            padding-top: 2%;
            padding-bottom: 200px;
        }

        .form-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
        }

        .beschreibung-container {
            font-family: Arial, sans-serif;
        }

        .pdf-preview {
            width: 100%;
            height: 80vh;
            border: 1px solid #ddd;
            margin-top: 10px;
        }

        .alert {
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            padding: 20px 30px;
            font-size: 1.2rem;
            z-index: 1050;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-close {
            color: white;
            background-color: transparent;
            border: none;
            font-size: 1.5rem;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            min-width: 120px;
        }

        .card-title {
            font-weight: 900;
            font-size: 2rem;
        }

        .info-box {
            border: 1px solid #ddd;
            background-color: transparent;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
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

        .label-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

    </style>
</head>

<body>
    <header>
        <a href="{{ route('welcome') }}">
            <img src="/images/ostfalia-logo.jpg" alt="Ostfalia Logo">
        </a>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" onclick="closeAlert('success-alert')"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" id="error-alert" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" onclick="closeAlert('error-alert')"></button>
        </div>
    @endif

    <script>
        function closeAlert(alertId) {
            document.getElementById(alertId).style.display = 'none';
        }
    </script>

    <div class="main-container">
        <div class="left-column">
            <div class="btn-group-vertical w-100">
                <a href="{{ route('welcome') }}" class="button-24 {{ request()->routeIs('welcome') ? 'button-24-hover' : '' }}">Home</a>
                
                @auth
                    @if(auth()->user()->roll == 'Prof')
                        <a href="{{ route('prof.dashboard') }}" class="button-24 {{ request()->routeIs('prof.dashboard') ? 'button-24-hover' : '' }}">Meine Thesen</a> 
                        <a href="{{ route('prof.geheimthesis') }}" class="button-24 {{ request()->routeIs('prof.geheimthesis') ? 'button-24-hover' : '' }}">Privat-Thesen</a>
                        <a href="{{ route('prof.uebersicht') }}" class="button-24 {{ request()->routeIs('prof.uebersicht') ? 'button-24-hover' : '' }}">Übersicht</a>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Thesis Details</span>
                    <div class="button-group">
                        <!-- Interesse/Interesse zurückziehen -->
                        @if(auth()->check() && auth()->user()->roll == 'Student')
                            <form action="{{ route('thesis.interesse', $thesis->id) }}" method="POST" style="display:inline;" id="interesse-form-{{ $thesis->id }}">
                                @csrf
                                @if(in_array(auth()->user()->email, $thesis->interesse ?? []))
                                    <button type="submit" class="btn btn-danger" onclick="return confirmInteresse(event, '{{ $studentEmail ? explode('@', $studentEmail)[0] : '' }}', true)">Interesse zurückziehen</button>
                                @else
                                    <button type="submit" class="btn btn-primary" onclick="return confirmInteresse(event, '{{ $studentEmail ? explode('@', $studentEmail)[0] : '' }}', false)">Interesse</button>
                                @endif
                            </form>
                        @endif

                        <!-- Bearbeiten/ Löschen -->
                        @if(auth()->check() && auth()->user()->roll == 'Prof' && auth()->user()->id === $thesis->prof_id)
                            <a href="{{ route('thesis.edit', $thesis->id) }}" class="btn btn-success">
                                <strong>Bearbeiten</strong>
                            </a>
                            <form action="{{ route('thesis.loeschen', $thesis->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Sind Sie sicher, dass Sie diese Thesis löschen möchten?');">
                                    <strong>Löschen</strong>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="card-title">{{ $thesis->name }}</h4>
                    <p class="card-text">
                        <div class="info-box">
                            <strong class="label-title">Betreuer:</strong> {{ $thesis->betreuer }}
                        </div>
                        <div class="info-box">
                            <strong class="label-title">Beschreibung:</strong> 
                            <div class="beschreibung-container">
                                {!! nl2br(e($thesis->description)) !!}
                            </div>
                        </div>
                        <div class="info-box">
                            <strong class="label-title">Kenntnisse:</strong>
                            @foreach(explode(' ', $thesis->kenntnisse) as $kenntnis)
                                <span class="badge bg-secondary">{{ $kenntnis }}</span>
                            @endforeach
                        </div>
                        <div class="info-box">
                            <strong class="label-title">Projektart:</strong>
                            @if(!empty($thesis->projektart))
                                @foreach($thesis->projektart as $art)
                                    <span class="badge bg-primary">{{ $art }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Keine Projektart ausgewählt</span>
                            @endif
                        </div>
                        <div class="info-box">
                            <strong class="label-title">Status:</strong>
                            @if($thesis->display_status === 'Aktiv')
                                <span class="badge bg-success">Aktiv</span>
                            @elseif($thesis->display_status === 'Inaktiv')
                                <span class="badge bg-danger">Inaktiv</span>
                            @else
                                <span class="badge bg-secondary">{{ $thesis->display_status }}</span>
                            @endif
                        </div>

                        @if(auth()->check() && auth()->user()->roll == 'Prof' && auth()->user()->id === $thesis->prof_id)
                            <div class="info-box">
                                <strong class="label-title">Notiz:</strong> 
                                <div class="notiz-container">
                                    {!! $thesis->notiz ? nl2br(e($thesis->notiz)) : 'Keine Notiz' !!}
                                </div>
                            </div>


                            @if($thesis->display_status !== 'Angebot')
                                <div class="info-box">
                                    <strong class="label-title">Semester:</strong> {{ $thesis->semester ?? 'N/A' }}
                                </div>
                                <div class="info-box">
                                    <strong class="label-title">Startdatum:</strong> {{ $thesis->startdatum ?? 'N/A' }}
                                </div>
                                <div class="info-box">
                                    <strong class="label-title">Enddatum:</strong> {{ $thesis->enddatum ?? 'N/A' }}
                                </div>
                                <div class="info-box">
                                    <strong class="label-title">Vortragdatum:</strong> {{ $thesis->vortragdatum ?? 'N/A' }}
                                </div>
                            @endif

                            <div class="info-box">
                                <strong class="label-title">Geheim:</strong> {{ $thesis->geheim === 'yes' ? 'Ja' : 'Nein' }}
                            </div>
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <h4>Projekt Dateien (PDFs)</h4>
                @if($thesis->pdf_1 || $thesis->pdf_2)
                    @if($thesis->pdf_1)
                        <p><strong>PDF 1:</strong> 
                            <a href="{{ asset('storage/' . $thesis->pdf_1) }}" target="_blank" class="btn btn-success">
                                Download PDF 1
                            </a>
                        </p>
                        <iframe src="{{ asset('storage/' . $thesis->pdf_1) }}" class="pdf-preview"></iframe>
                    @endif

                    @if($thesis->pdf_2)
                        <p><strong>PDF 2:</strong> 
                            <a href="{{ asset('storage/' . $thesis->pdf_2) }}" target="_blank" class="btn btn-success">
                                Download PDF 2
                            </a>
                        </p>
                        <iframe src="{{ asset('storage/' . $thesis->pdf_2) }}" class="pdf-preview"></iframe>
                    @endif
                @else
                    <p class="text-muted">Kein PDF hochgeladen.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        function confirmInteresse(event, studentEmailPart, isWithdrawal = false) {
            event.preventDefault();
            const message = isWithdrawal 
                ? 'Möchten Sie Ihr Interesse an dieser Thesis zurückziehen?' 
                : 'Achtung: Andere Studenten, die ebenfalls Interesse an dieser Thesis haben, können anhand Ihrer ID ' 
              + studentEmailPart 
              + ' erkennen, dass Sie interessiert sind. Sie könnten Sie dann z.B über die Ostfalia E-Mail, Moodle oder StudIP kontaktieren. Möchten Sie fortfahren?';
        
            const confirmed = confirm(message);
            if (confirmed) {
                event.target.closest('form').submit();
            }
            return false;
        }
    </script>
</body>
</html>
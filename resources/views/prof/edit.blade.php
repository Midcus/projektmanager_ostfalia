<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            width: 85%;
            padding-top: 2%;
            padding-bottom: 200px; 
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

        .button-24:hover {
            background-color: initial;
            color: #003366;
        }
        .button-24-hover {
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
        <a href="{{ route('welcome') }}">
            <img src="/images/ostfalia-logo.jpg" alt="Ostfalia Logo">
        </a>
    </header>

    <!-- Main Content -->
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
            
            <div class="card dozenten-card">
                <div class="card-header-blau">Dozenteninfos</div>
                <div class="card-body">
                    <p><strong>Präfix:</strong> {{ auth()->user()->praefix }}</p>
                    <p><strong>Vorname:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Nachname:</strong> {{ auth()->user()->nachname }}</p>
                    <p><strong>E-Mail:</strong> {{ auth()->user()->email }}</p>
                    <a href="{{ route('prof.edit') }}" class="button-24">Profil bearbeiten</a>
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="card">
                <div class="card-header">
                    <span class="w-100">Thesis Bearbeiten</span>
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

                <div class="form-container">
                    <form id="profile-form" method="POST" action="{{ route('prof.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Präfix -->
                        <div class="mb-3">
                            <label for="praefix" class="form-label">Präfix</label>
                            <input type="text" class="form-control @error('praefix') is-invalid @enderror" id="praefix" name="praefix" 
                                value="{{ old('praefix', auth()->user()->praefix) }}">
                            @error('praefix')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">First Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="nachname" class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('nachname') is-invalid @enderror" id="nachname" name="nachname" 
                                value="{{ old('nachname', auth()->user()->nachname) }}" required>
                            @error('nachname')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Nút Submit -->
                        <button type="button" class="btn button-24 " id="update-btn">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Popup -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Profilaktualisierung bestätigen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Sie werden als <span id="full-name"></span> angezeigt.
                </div>
                <div class="modal-footer">
                    <!-- Chỉnh sửa nút Close thành "Weiter bearbeiten" -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nein, zurück zur Bearbeitung</button>
                    <button type="button" class="btn btn-primary" id="confirm-update">Bestätigen und weiter</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('update-btn').addEventListener('click', function() {

            var praefix = document.getElementById('praefix').value;
            var firstName = document.getElementById('name').value;
            var lastName = document.getElementById('nachname').value;


            var fullName = praefix + ' ' + firstName + ' ' + lastName;


            document.getElementById('full-name').textContent = fullName;


            var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            confirmationModal.show();
        });

        document.getElementById('confirm-update').addEventListener('click', function() {

            document.getElementById('profile-form').submit();
        });
    </script>


</body>
</html>

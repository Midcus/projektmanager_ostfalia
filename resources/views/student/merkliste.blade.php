<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merkliste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
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
            background-position: 0 0;
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

        .table-wrapper {
            max-height: 70vh;
            overflow-y: auto;
            border: 1px solid #ddd;
            margin-bottom: 16px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }


        .table th:nth-child(1), .table td:nth-child(1) { width: 5%; }    /* ID */
        .table th:nth-child(2), .table td:nth-child(2) { width: 14%; font-weight: bold; font-size: 110%; } /* Name */
        .table th:nth-child(3), .table td:nth-child(3) { width: 34.5%; } /* Beschreibung */
        .table th:nth-child(4), .table td:nth-child(4) { width: 9%; }    /* Projektart */
        .table th:nth-child(5), .table td:nth-child(5) { width: 8.5%; }  /* Betreuer  */
        .table th:nth-child(6), .table td:nth-child(6) { width: 9%; }    /* Kenntnisse  */
        .table th:nth-child(7), .table td:nth-child(7) { width: 7%; }    /* Interest  */
        .table th:nth-child(8), .table td:nth-child(8) { width: 7%; }    /* Resttag */
        .table th:nth-child(9), .table td:nth-child(9) { width: 6%; }    /* Status*/

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            overflow: hidden;
        }

        .table th {
            background-color: #003366;
            font-weight: bold;
            color: white;
        }

        .search-input {
            width: 100%;
            padding: 5px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #003366;
            color: #fff;
            border-radius: 4px;
            margin-right: 4px;
        }


        .name-container, 
        .beschreibung-container, 
        .projektart-container, 
        .betreuer-container, 
        .kenntnisse-container, 
        .interesse-container, 
        .days-remaining-container,
        .status-container {
            line-height: 1.2em;
            max-height: 8em; 
            overflow-y: auto;
            word-wrap: break-word;
        }

        .sortable {
            position: relative;
            cursor: pointer;
        }

        .sortable:after {
            color: #888;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .sortable:hover {
            background-color: #ffffff;
            color: #003366;
        }

        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .btn-success {
            font-size: 1rem;
            padding: 4px 16px;
            margin: 0;
        }

        .dozenten-card {
            margin-top: 25%;
        }

        .alert {
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            padding: 50px 70px;
            width: auto;
            font-size: 1.2rem;
            z-index: 1050;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .btn-close {
            color: white;
            background-color: transparent;
            border: none;
            font-size: 1.5rem;
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
        {!! session('success') !!}
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Ihre Merkliste</span>
                </div>

                <div class="table-wrapper">
                    <table class="table" id="thesisTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="0">ID ⇅</th>
                                <th class="sortable" data-column="1">Name ⇅</th>
                                <th>Beschreibung</th>
                                <th class="sortable" data-column="3">Projektart ⇅</th>
                                <th class="sortable" data-column="4">Betreuer ⇅</th>
                                <th>Kenntnisse</th>
                                <th class="sortable" data-column="6">Interest ⇅</th>
                                <th class="sortable" data-column="7">Resttag ⇅</th>
                                <th class="sortable" data-column="8">Status ⇅</th>
                            </tr>
                            <tr>
                                <td><input type="text" class="search-input" data-column="0" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="1" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="2" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="3" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="4" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="5" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="6" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="7" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="8" placeholder="🔍"></td> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($theses as $thesis)
                                <tr>
                                    <td>{{ $thesis->id }}</td>
                                    <td>
                                        <div class="name-container">
                                            <a href="{{ route('thesis.show', $thesis->id) }}" style="color: #003366; text-decoration: none;">
                                                {{ $thesis->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="beschreibung-container">
                                            {!! nl2br(e($thesis->description)) !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="projektart-container">
                                            @if(!empty($thesis->projektart))
                                                @foreach($thesis->projektart as $art)
                                                    <span class="badge">{{ $art }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="betreuer-container">
                                            {{ $thesis->betreuer }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="kenntnisse-container">
                                            @foreach(explode(' ', $thesis->kenntnisse) as $kenntnis)
                                                <span class="badge">{{ $kenntnis }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <div class="interesse-container">
                                            @if(!empty($thesis->interesse))
                                                @foreach($thesis->interesse as $email)
                                                    <span class="badge bg-success">{{ $email }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Keine Interessenten</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="days-remaining-container">
                                            {{ $thesis->days_remaining }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status-container">
                                            {{ $thesis->status }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Keine Thesis in Ihrer Merkliste.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInputs = document.querySelectorAll('.search-input');
            const table = document.getElementById('thesisTable');
            const rows = table.querySelectorAll('tbody tr');

            searchInputs.forEach(input => {
                input.addEventListener('input', function () {
                    const columnIndex = this.getAttribute('data-column');
                    const filter = this.value.toLowerCase();

                    rows.forEach(row => {
                        const cell = row.cells[columnIndex];
                        if (cell) {
                            const cellText = cell.textContent.toLowerCase();
                            if (cellText.includes(filter)) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }
                        }
                    });
                });
            });

            const headers = document.querySelectorAll('.sortable');
            headers.forEach(header => {
                header.addEventListener('click', function () {
                    const table = document.getElementById('thesisTable');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const columnIndex = header.getAttribute('data-column');
                    const isAscending = header.classList.toggle('ascending');

                    rows.sort((a, b) => {
                        const cellA = a.cells[columnIndex].textContent.trim().toLowerCase();
                        const cellB = b.cells[columnIndex].textContent.trim().toLowerCase();

                        if (!isNaN(cellA) && !isNaN(cellB)) {
                            return isAscending ? cellA - cellB : cellB - cellA;
                        }
                        return isAscending
                            ? cellA.localeCompare(cellB)
                            : cellB.localeCompare(cellA);
                    });

                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    </script>
</body>
</html>
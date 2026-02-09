<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prof Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
            background-color: initial;
            background-position: 0 0;
            color: #003366;
            opacity: .5;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
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
            border: 1px solid #ddd;
            margin-bottom: 16px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table th:nth-child(1), .table td:nth-child(1) { width: 5%; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 15%; font-weight: bold; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 39.3%; }
        .table th:nth-child(4), .table td:nth-child(4) { width: 9%; }
        .table th:nth-child(5), .table td:nth-child(5) { width: 11%; }
        .table th:nth-child(6), .table td:nth-child(6) { width: 7.2%; }
        .table th:nth-child(7), .table td:nth-child(7) { width: 7%; }
        .table th:nth-child(8), .table td:nth-child(8) { width: 6.5%; }

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

        .beschreibung-container, .name-container, .interesse-container, .kenntnisse-container {
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
        .btn-success {
            font-size: 1rem;
            padding: 4px 16px;
            margin: 0;
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

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-item.active .page-link {
            background-color: #003366;
            border-color: #003366;
            color: white;
        }

        .pagination .page-link {
            color: #003366;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            color: #003366;
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
            <!-- Navigation Buttons -->
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Meine Privat-Thesen</span>
                    <a href="{{ route('thesis.create') }}" class="btn btn-success">
                        <strong>Thesis Erstellen</strong>
                    </a>
                </div>

                <div class="table-wrapper">
                    <table class="table" id="thesisTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="0">ID ⇅</th>
                                <th class="sortable" data-column="1">Name ⇅</th>
                                <th>Beschreibung</th>
                                <th class="sortable" data-column="3">Projektart ⇅</th>
                                <th class="sortable" data-column="4">Kenntnisse ⇅</th>
                                <th class="sortable" data-column="5">Interest ⇅</th>
                                <th class="sortable" data-column="6">Status ⇅</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td><input type="text" class="search-input" data-column="0" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="1" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="2" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="3" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="4" placeholder="🔍"></td>
                                <td><input type="text" class="search-input" data-column="5" placeholder="🔍"></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($theses as $thesis)
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
                                        @if(!empty($thesis->projektart))
                                            @foreach($thesis->projektart as $art)
                                                <span class="badge">{{ $art }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
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
                                                <span class="text-muted"></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $thesis->status }}</td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                            <a href="{{ route('thesis.edit', $thesis->id) }}" class="btn btn-warning">Edit</a>
                                            <form action="{{ route('thesis.loeschen', $thesis->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Sind Sie sicher, dass Sie diese Thesis löschen möchten?');">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            @if ($theses->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $theses->previousPageUrl() }}" rel="prev">Previous</a>
                                </li>
                            @endif

                            @foreach ($theses->getUrlRange(1, $theses->lastPage()) as $page => $url)
                                <li class="page-item {{ $theses->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            @if ($theses->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $theses->nextPageUrl() }}" rel="next">Next</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                            @endif
                        </ul>
                    </nav>


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
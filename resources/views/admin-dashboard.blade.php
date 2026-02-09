<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            outline: 0;
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

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #003366;
            font-weight: bold;
            color: white;
        }


        .table th:nth-child(1), .table td:nth-child(1) {
            width: 6%;
        }
        .table th:nth-child(2), .table td:nth-child(2) {
            width: 20%;
        }
        .table th:nth-child(3), .table td:nth-child(3) {
            width: 18%;
        }
        .table th:nth-child(4), .table td:nth-child(4) {
            width: 17%;
        }
        .table th:nth-child(5), .table td:nth-child(5) {
            width: 20%;
        }
        .table th:nth-child(6), .table td:nth-child(6) {
            width: 10%;
        }
        .table th:nth-child(7), .table td:nth-child(7) {
            width: 9%;
        }




        .search-input {
            width: 100%;
            padding: 5px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
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

        .card-header-blau {
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #003366;
            color: white;
            padding-top: 3%;
            padding-bottom: 3%;
            text-align: center;
        }

        .btn-success {
            font-size: 1rem;
            padding: 4px 16px;
            margin: 0;
        }

        .dozenten-card {
            margin-top: 25%;
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

    <script>
        function closeAlert() {
            document.getElementById('success-alert').style.display = 'none';
        }
    </script>

    <!-- Main Content -->
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
        <!-- Right Column (Manage Users or Content) -->
        <div class="right-column">
            <h2>Admin Dashboard</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-wrapper">
                <table class="table table-bordered" id="adminTable">
                    <thead>
                        <tr>
                            <th class="sortable" data-column="0">ID ⇅</th>
                            <th class="sortable" data-column="1">Praefix ⇅</th>
                            <th class="sortable" data-column="2">Name ⇅</th>
                            <th class="sortable" data-column="3">Nachname ⇅</th>
                            <th class="sortable" data-column="4">Email ⇅</th>
                            <th class="sortable" data-column="5">Role ⇅</th>
                            <th>Actions</th>
                        </tr>
                        <tr>
                            <td><input type="text" class="search-input" data-column="0" placeholder="🔍"></td>
                            <td><input type="text" class="search-input" data-column="1" placeholder="🔍"></td>
                            <td><input type="text" class="search-input" data-column="2" placeholder="🔍"></td>
                            <td><input type="text" class="search-input" data-column="3" placeholder="🔍"></td>
                            <td><input type="text" class="search-input" data-column="4" placeholder="🔍"></td>
                            <td><input type="text" class="search-input" data-column="5" placeholder="🔍"></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->praefix }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->nachname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roll }}</td>
                                <td>
                                    @if(auth()->user()->id !== $user->id) 
                                        <form action="{{ route('admin.delete-user', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary" disabled>Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInputs = document.querySelectorAll('.search-input');
            const table = document.getElementById('adminTable');
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
                    const table = document.getElementById('adminTable');
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
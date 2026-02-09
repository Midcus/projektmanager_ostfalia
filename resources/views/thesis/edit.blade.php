<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Thesis</title>
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
            padding: 20px;
        }

        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #003366;
            color: white;
        }

        .hidden {
            display: none;
        }

        .card {
            margin-bottom: 20px;
            box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
        }

        .card-body {
            padding: 15px;
        }

        .char-count {
            font-size: 0.9em;
            color: #666;
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


        .small-input {
            width: 15% !important;
            min-width: 100px;
        }

        .attribute-card-header {
            font-size: 1.25rem;
            font-weight: bold; 
        }

        .optional-text {
            font-size: 0.8rem; 
            font-style: italic;
            color: #666; 
        }

        .hidden {
            display: none;
            transition: opacity 0.3s ease;
        }

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
            <div class="card-header">
                <span class="w-100">Thesis Bearbeiten</span>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

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
                <form action="{{ route('thesis.update', $thesis->id) }}" method="POST" enctype="multipart/form-data" id="thesisForm">
                    @csrf
                    @method('PUT')

                    <!-- Card 1: Thesis Name -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label attribute-card-header"><strong>Thesis Name *</strong></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $thesis->name) }}" required>
                                <small class="char-count" id="nameCount">0/250</small>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Beschreibung -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="description" class="form-label attribute-card-header"><strong>Beschreibung *</strong></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $thesis->description) }}</textarea>
                                <small class="char-count" id="descriptionCount">0/7500</small>
                                <small class="text-muted">Tipp: Verwenden Sie "-" für Aufzählungspunkte.</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 2.1: Notiz -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="notiz" class="form-label attribute-card-header">
                                    <strong>Notiz</strong> <small class="optional-text">(optional)</small>
                                </label>
                                <textarea class="form-control @error('notiz') is-invalid @enderror" id="notiz" name="notiz" rows="5">{{ old('notiz', $thesis->notiz) }}</textarea>
                                <small class="char-count" id="notizCount">0/7500</small>
                                <small class="text-muted">Tipp: Verwenden Sie "-" für Aufzählungspunkte.</small>
                                @error('notiz')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Projektart -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3" id="projektartContainer">
                                <label class="form-label attribute-card-header"><strong>Projektart *</strong></label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input projektart-checkbox" name="projektart[]" value="Teamprojekt" id="teamprojekt" {{ in_array('Teamprojekt', old('projektart', $thesis->projektart ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="teamprojekt">Teamprojekt</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input projektart-checkbox" name="projektart[]" value="Studienarbeit" id="studienarbeit" {{ in_array('Studienarbeit', old('projektart', $thesis->projektart ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="studienarbeit">Studienarbeit</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input projektart-checkbox" name="projektart[]" value="Bachelorthesis" id="bachelorthesis" {{ in_array('Bachelorthesis', old('projektart', $thesis->projektart ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bachelorthesis">Bachelorthesis</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input projektart-checkbox" name="projektart[]" value="Masterthesis" id="masterthesis" {{ in_array('Masterthesis', old('projektart', $thesis->projektart ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="masterthesis">Masterthesis</label>
                                </div>
                                @error('projektart')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Geheim -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="geheim" class="form-label attribute-card-header"><strong>Handelt es sich um eine private Thesis, die für andere nicht sichtbar ist? *</strong></label>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="geheim" id="geheim_yes" value="yes" {{ old('geheim', $thesis->geheim) === 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="geheim_yes">Ja</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="geheim" id="geheim_no" value="no" {{ old('geheim', $thesis->geheim) === 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="geheim_no">Nein</label>
                                </div>
                                <small class="text-muted">Eine private Thesis ist ausschließlich für Sie sichtbar.</small>
                                @error('geheim')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Kenntnisse -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="kenntnisse" class="form-label attribute-card-header"><strong>Kenntnisse *</strong></label>
                                <input type="text" class="form-control @error('kenntnisse') is-invalid @enderror" id="kenntnisse" name="kenntnisse" value="{{ old('kenntnisse', $thesis->kenntnisse) }}" required>
                                <small class="char-count" id="kenntnisseCount">0/10 Wörter</small>
                                <small class="text-muted">Tipp: Trennen Sie Kenntnisse mit Leerzeichen (z.B. "Mathe Physik Code Elektrotechnik" wird als 4 Kenntnisse angezeigt).</small>
                                @error('kenntnisse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 6: Semester -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="semester_type" class="form-label attribute-card-header">
                                    <strong>Semester Type</strong> <small class="optional-text">(optional)</small>
                                </label>
                                <select class="form-select @error('semester_type') is-invalid @enderror" id="semester_type" name="semester_type">
                                    <option value="N/A" {{ old('semester_type', $thesis->semester ? substr($thesis->semester, 0, 2) : 'N/A') == 'N/A' ? 'selected' : '' }}>N/A</option>
                                    <option value="WS" {{ old('semester_type', $thesis->semester ? substr($thesis->semester, 0, 2) : 'N/A') == 'WS' ? 'selected' : '' }}>WS (Wintersemester)</option>
                                    <option value="SS" {{ old('semester_type', $thesis->semester ? substr($thesis->semester, 0, 2) : 'N/A') == 'SS' ? 'selected' : '' }}>SS (Sommersemester)</option>
                                </select>
                                <small class="text-muted">Diese Angabe hilft Ihnen, Ihre Thesen nach Abschluss besser zu verwalten.</small>
                                @error('semester_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3" id="semesterYearGroup" style="display: {{ old('semester_type', $thesis->semester ? substr($thesis->semester, 0, 2) : 'N/A') === 'N/A' ? 'none' : 'block' }};">
                                <label for="semester_year" class="form-label attribute-card-header">
                                    <strong>Semester Year</strong> <small class="optional-text">(4 digits) (optional)</small>
                                </label>
                                <input type="text" class="form-control @error('semester_year') is-invalid @enderror" id="semester_year" name="semester_year" pattern="\d{4}" placeholder="z.B. 2025" value="{{ old('semester_year', $thesis->semester ? substr($thesis->semester, 2) : '') }}">
                                <small class="char-count" id="semesterYearCount">0/4</small>
                                @error('semester_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 7: Status -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label attribute-card-header"><strong>Status *</strong></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="Angebot" {{ old('status', $thesis->status) == 'Angebot' ? 'selected' : '' }}>Angebot</option>
                                    <option value="Aktiv" {{ old('status', $thesis->status) == 'Aktiv' ? 'selected' : '' }}>Aktiv</option>
                                    <option value="Fertig" {{ old('status', $thesis->status) == 'Fertig' ? 'selected' : '' }}>Fertig</option>
                                    <option value="Idle" {{ old('status', $thesis->status) == 'Idle' ? 'selected' : '' }}>Idle</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 {{ $thesis->status === 'Aktiv' || $thesis->status === 'Fertig' ? '' : 'hidden' }}" id="startdatumGroup">
                                <label for="startdatum" class="form-label attribute-card-header">
                                    <strong>Startdatum</strong> <small class="optional-text">(optional)</small>
                                </label>
                                <input type="date" class="form-control @error('startdatum') is-invalid @enderror" id="startdatum" name="startdatum" value="{{ old('startdatum', $thesis->startdatum ? \Carbon\Carbon::parse($thesis->startdatum)->format('Y-m-d') : '') }}">
                                @error('startdatum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 {{ $thesis->status === 'Aktiv' || $thesis->status === 'Fertig' ? '' : 'hidden' }}" id="enddatumGroup">
                                <label for="enddatum" class="form-label attribute-card-header">
                                    <strong>Enddatum</strong> <small class="optional-text">(optional)</small>
                                </label>
                                <input type="date" class="form-control @error('enddatum') is-invalid @enderror" id="enddatum" name="enddatum" value="{{ old('enddatum', $thesis->enddatum ? \Carbon\Carbon::parse($thesis->enddatum)->format('Y-m-d') : '') }}">
                                @error('enddatum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 {{ $thesis->status === 'Aktiv' || $thesis->status === 'Fertig' ? '' : 'hidden' }}" id="vortragdatumGroup">
                                <label for="vortragdatum" class="form-label attribute-card-header">
                                    <strong>Vortragdatum</strong> <small class="optional-text">(optional)</small>
                                </label>
                                <input type="date" class="form-control @error('vortragdatum') is-invalid @enderror" id="vortragdatum" name="vortragdatum" value="{{ old('vortragdatum', $thesis->vortragdatum ? \Carbon\Carbon::parse($thesis->vortragdatum)->format('Y-m-d') : '') }}">
                                @error('vortragdatum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>



                    <!-- Card 8: PDFs -->
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="pdf_1" class="form-label attribute-card-header">
                                    <strong>Upload PDF 1 (Max 5MB)</strong><small class="optional-text"> (optional)</small>
                                </label>
                                @if($thesis->pdf_1)
                                    <div class="existing-pdf" id="existing-pdf-1">
                                        <p>Aktuelle Datei: <a href="{{ Storage::url($thesis->pdf_1) }}" target="_blank">PDF 1</a></p>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="delete_pdf_1" id="delete_pdf_1" value="1">
                                            <label class="form-check-label" for="delete_pdf_1">Aktuelle PDF 1 löschen</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('pdf_1') is-invalid @enderror {{ $thesis->pdf_1 ? 'hidden' : '' }}" id="pdf_1" name="pdf_1" accept=".pdf">
                                @error('pdf_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="pdf_2" class="form-label attribute-card-header">
                                    <strong>Upload PDF 2 (Max 5MB)</strong><small class="optional-text"> (optional)</small>
                                </label>
                                @if($thesis->pdf_2)
                                    <div class="existing-pdf" id="existing-pdf-2">
                                        <p>Aktuelle Datei: <a href="{{ Storage::url($thesis->pdf_2) }}" target="_blank">PDF 2</a></p>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="delete_pdf_2" id="delete_pdf_2" value="1">
                                            <label class="form-check-label" for="delete_pdf_2">Aktuelle PDF 2 löschen</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('pdf_2') is-invalid @enderror {{ $thesis->pdf_2 ? 'hidden' : '' }}" id="pdf_2" name="pdf_2" accept=".pdf">
                                @error('pdf_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="button-24">Thesis speichern</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const statusSelect = document.getElementById('status');
    const projektartCheckboxes = document.querySelectorAll('.projektart-checkbox');
    const startdatumGroup = document.getElementById('startdatumGroup');
    const enddatumGroup = document.getElementById('enddatumGroup');
    const vortragdatumGroup = document.getElementById('vortragdatumGroup');
    const semesterTypeSelect = document.getElementById('semester_type');
    const semesterYearGroup = document.getElementById('semesterYearGroup');


    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const notizInput = document.getElementById('notiz');
    const kenntnisseInput = document.getElementById('kenntnisse');
    const semesterYearInput = document.getElementById('semester_year');
    const nameCount = document.getElementById('nameCount');
    const descriptionCount = document.getElementById('descriptionCount');
    const notizCount = document.getElementById('notizCount');
    const kenntnisseCount = document.getElementById('kenntnisseCount');
    const semesterYearCount = document.getElementById('semesterYearCount');
    const pdf1Input = document.getElementById('pdf_1'); 
    const pdf2Input = document.getElementById('pdf_2'); 


    const NAME_MAX = 250;
    const TEXTAREA_MAX = 7500; 
    const KENNTNISSE_MAX_WORDS = 10;
    const SEMESTER_YEAR_MAX = 4;
    const MAX_FILE_SIZE = 5 * 1024 * 1024; 


    function checkFileSize(input) {
        if (input.files && input.files[0]) {
            const fileSize = input.files[0].size;
            if (fileSize > MAX_FILE_SIZE) {
                alert(`Die Datei "${input.files[0].name}" darf nicht größer als 5MB sein.`);
                input.value = ''; 
                return false;
            }
        }
        return true;
    }

    function handleStatusChange() {
        const status = statusSelect.value;
        const isAktivOrFertig = status === 'Aktiv' || status === 'Fertig';

        startdatumGroup.classList.toggle('hidden', !isAktivOrFertig);
        enddatumGroup.classList.toggle('hidden', !isAktivOrFertig);
        vortragdatumGroup.classList.toggle('hidden', !isAktivOrFertig);

        if (isAktivOrFertig) {
            let checkedCount = 0;
            projektartCheckboxes.forEach(checkbox => {
                if (checkbox.checked) checkedCount++;
                checkbox.disabled = checkedCount >= 1 && !checkbox.checked;
            });
        } else {
            projektartCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
        }
    }

    function handleProjektartChange() {
        const status = statusSelect.value;
        if (status === 'Aktiv' || status === 'Fertig') {
            let checkedCount = 0;
            projektartCheckboxes.forEach(checkbox => {
                if (checkbox.checked) checkedCount++;
                checkbox.disabled = checkedCount >= 1 && !checkbox.checked;
            });
        }
    }

    function handleSemesterTypeChange() {
        const selected = semesterTypeSelect.value;
        semesterYearGroup.style.display = (selected === 'WS' || selected === 'SS') ? 'block' : 'none';
    }


    function updateNameCounter() {
        const currentLength = nameInput.value.length;
        nameCount.textContent = `${currentLength}/${NAME_MAX}`;
        if (currentLength > NAME_MAX) {
            nameInput.value = nameInput.value.substring(0, NAME_MAX);
            nameCount.textContent = `${NAME_MAX}/${NAME_MAX}`;
        }
    }

    function updateDescriptionCounter() {
        const currentLength = descriptionInput.value.length;
        descriptionCount.textContent = `${currentLength}/${TEXTAREA_MAX}`;
        if (currentLength > TEXTAREA_MAX) {
            descriptionInput.value = descriptionInput.value.substring(0, TEXTAREA_MAX);
            descriptionCount.textContent = `${TEXTAREA_MAX}/${TEXTAREA_MAX}`;
        }
    }

    function updateNotizCounter() {
        const currentLength = notizInput.value.length;
        notizCount.textContent = `${currentLength}/${TEXTAREA_MAX}`;
        if (currentLength > TEXTAREA_MAX) {
            notizInput.value = notizInput.value.substring(0, TEXTAREA_MAX);
            notizCount.textContent = `${TEXTAREA_MAX}/${TEXTAREA_MAX}`;
        }
    }

    function updateKenntnisseCounter() {
        const words = kenntnisseInput.value.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;
        kenntnisseCount.textContent = `${wordCount}/${KENNTNISSE_MAX_WORDS} Wörter`;
        if (wordCount > KENNTNISSE_MAX_WORDS) {
            kenntnisseInput.value = words.slice(0, KENNTNISSE_MAX_WORDS).join(' ');
            kenntnisseCount.textContent = `${KENNTNISSE_MAX_WORDS}/${KENNTNISSE_MAX_WORDS} Wörter`;
        }
    }

    function updateSemesterYearCounter() {
        const currentLength = semesterYearInput.value.length;
        semesterYearCount.textContent = `${currentLength}/${SEMESTER_YEAR_MAX}`;
        if (currentLength > SEMESTER_YEAR_MAX) {
            semesterYearInput.value = semesterYearInput.value.substring(0, SEMESTER_YEAR_MAX);
            semesterYearCount.textContent = `${SEMESTER_YEAR_MAX}/${SEMESTER_YEAR_MAX}`;
        }
    }


    nameInput.addEventListener('input', updateNameCounter);
    descriptionInput.addEventListener('input', updateDescriptionCounter);
    notizInput.addEventListener('input', updateNotizCounter);
    kenntnisseInput.addEventListener('input', updateKenntnisseCounter);
    semesterYearInput.addEventListener('input', updateSemesterYearCounter);


    pdf1Input.addEventListener('change', () => checkFileSize(pdf1Input));
    pdf2Input.addEventListener('change', () => checkFileSize(pdf2Input));


    window.addEventListener('DOMContentLoaded', () => {
        handleStatusChange();
        handleSemesterTypeChange();
        updateNameCounter();
        updateDescriptionCounter();
        updateNotizCounter();
        updateKenntnisseCounter();
        updateSemesterYearCounter();
    });

    statusSelect.addEventListener('change', handleStatusChange);
    projektartCheckboxes.forEach(checkbox => checkbox.addEventListener('change', handleProjektartChange));
    semesterTypeSelect.addEventListener('change', handleSemesterTypeChange);

    document.getElementById('thesisForm').addEventListener('submit', function (e) {

        if (!checkFileSize(pdf1Input) || !checkFileSize(pdf2Input)) {
            e.preventDefault();
            return;
        }

        const status = statusSelect.value;
        const checkedProjektart = document.querySelectorAll('.projektart-checkbox:checked').length;
        const startdatum = document.getElementById('startdatum').value.trim();
        const enddatum = document.getElementById('enddatum').value.trim();
        const vortragdatum = document.getElementById('vortragdatum').value.trim();
        const semesterType = document.getElementById('semester_type').value;
        const semesterYear = document.getElementById('semester_year').value.trim();

        if (status === 'Aktiv' || status === 'Fertig') {
            if (checkedProjektart !== 1) {
                e.preventDefault();
                alert('Bei Status "Aktiv" oder "Fertig" darf nur eine Projektart ausgewählt werden.');
                return;
            }

            let missingFields = [];
            let showPopup1 = false;

            if (status === 'Aktiv' && !startdatum) {
                missingFields.push('Startdatum');
                showPopup1 = true;
            }

            if (status === 'Fertig') {
                if (!startdatum) missingFields.push('Startdatum');
                if (!enddatum) missingFields.push('Enddatum');
                if (!vortragdatum) missingFields.push('Vortragdatum');
                showPopup1 = missingFields.length > 0;
            }

            if (showPopup1) {
                const confirmation1 = confirm(
                    `Achtung: Sie haben folgende Felder nicht ausgefüllt, obwohl der Status von der Thesis "${status}" ist: \n\n` +
                    missingFields.map(field => `- ${field}`).join('\n') +
                    `\n\nMöchten Sie fortfahren?\n\nOK = Fortfahren\nCancel = Nein, weiterbearbeiten`
                );

                if (!confirmation1) {
                    e.preventDefault();
                    return;
                }

                let semesterValue = semesterType === 'N/A' ? 'N/A' : (semesterType && semesterYear ? `${semesterType}${semesterYear}` : '');
                let confirmation2;

                if (semesterValue && semesterValue !== 'N/A') {
                    confirmation2 = confirm(
                        `Ihre Thesis wurde im ${semesterValue} abgeschlossen und wird entsprechend in der Prof-Übersicht einsortiert. Sind Sie mit diesen Angaben einverstanden?\n\nOK = Fortfahren\nCancel = Nein, weiterbearbeiten`
                    );
                } else {
                    confirmation2 = confirm(
                        `Sie haben kein Semester angegeben, in dem diese Thesis abgeschlossen wurde. Dies könnte die spätere Verwaltung erschweren. Möchten Sie trotzdem fortfahren?\n\nOK = Fortfahren\nCancel = Nein, weiterbearbeiten`
                    );
                }

                if (!confirmation2) {
                    e.preventDefault();
                    return;
                }
            }
        }
    });
</script>
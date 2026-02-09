<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Aktivierungscode</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        h1 {
            color: #003366;
        }
        h2 {
            color: #003366;
            font-size: 24px;
        }
        a {
            color: #003366;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Willkommen bei TeamProjekt!</h1>
    <p>Vielen Dank für Ihre Registrierung. Bitte verwenden Sie den folgenden Aktivierungscode, um Ihr Konto zu aktivieren:</p>
    @if(isset($code))
        <h2>{{ $code }}</h2>
    @else
        <p>Es tut uns leid, der Aktivierungscode konnte nicht geladen werden. Bitte kontaktieren Sie den Support.</p>
    @endif
    <p>Dieser Code ist 24 Stunden lang gültig. Geben Sie ihn auf der <a href="{{ url('/activation') }}">Aktivierungsseite</a> ein:</p>
    <p>Wenn Sie sich nicht registriert haben, ignorieren Sie diese E-Mail bitte.</p>
    <p>Mit freundlichen Grüßen,<br>Ihr TeamProjekt-Team</p>
</body>
</html>
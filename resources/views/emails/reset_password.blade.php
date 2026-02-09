<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort-Reset-Code</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #003366; color: white; padding: 10px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .code { font-size: 24px; font-weight: bold; color: #003366; text-align: center; margin: 20px 0; }
        .button { display: inline-block; padding: 10px 20px; background-color: #003366; color: white; text-decoration: none; border-radius: 5px; }
        .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Passwort-Reset-Code</h1>
        </div>

        <div class="content">
            <p>Sehr geehrte/r Nutzer/in,</p>
            <p>Sie haben eine Anfrage zum Zurücksetzen Ihres Passworts gestellt. Bitte verwenden Sie den folgenden Code, um Ihr Passwort zurückzusetzen:</p>
            
            <div class="code">{{ $resetCode }}</div>
            
            <p>Der Code ist gültig bis: <strong>{{ $expiresAt->format('d.m.Y H:i:s') }}</strong></p>
            
        </div>

        <div class="footer">
            <p>Dies ist eine automatisch generierte E-Mail. Bitte antworten Sie nicht direkt auf diese Nachricht.</p>
        </div>
    </div>
</body>
</html>
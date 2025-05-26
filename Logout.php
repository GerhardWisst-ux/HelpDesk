<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php
    session_start();
    session_destroy();
    ?>
    <!DOCTYPE html>
    <html lang="de">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Logout</title>
        <script>
            // Weiterleitung nach 3 Sekunden
            setTimeout(() => {
                window.location.href = 'Login.php';
            }, 1000);
        </script>
    </head>

    <body>
        <!-- <p>Logout erfolgreich. Du wirst nun auf die Loginseite weitergeleitet...</p> -->
    </body>

    </html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Helpdesk Registrierung </title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Gesamte Seite mit Flexbox zentrieren */
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
        }

        /* Layout mit Navbar */
        #content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 56px);
            /* Abzug der Navbar-Höhe */
        }

        /* Login-Box Styling */
        #login-box {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Abstand zwischen Feldern */
        .input_box {
            margin-bottom: 20px;
        }

        /* Button Styling */
        .btn-login {
            width: 100%;
            padding: 10px;
        }

        /* Link Styling */
        #register-link a {
            font-size: 14px;
            text-decoration: none;
            color: #333;
        }

        #register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <?php
    if (headers_sent()) {
        die("Headers wurden bereits gesendet.");
    }
    ob_start();
    session_start();

    require 'db.php';
   

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        // foreach ($_POST as $key => $value) {
        //     echo htmlspecialchars($key) . " = " . htmlspecialchars($value) . "<br>";
        // }

        $error = false;
        $email = trim($_POST['email']);
        $passwort = $_POST['passwort'];
        $passwort2 = $_POST['passwort2'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Bitte eine gültige E-Mail-Adresse eingeben.";
            $error = true;
        }
        if (strlen($passwort) == 0) {
            $errorMessage = "Bitte ein Passwort angeben.";
            $error = true;
        }
        if ($passwort !== $passwort2) {
            $errorMessage = "Die Passwörter müssen übereinstimmen.";
            $error = true;
        }

        if (!$error) {
            $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $statement->execute(['email' => $email]);
            $user = $statement->fetch();

            if ($user) {
                $errorMessage = "Diese E-Mail-Adresse ist bereits registriert.";
            } else {
                $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
                $statement = $pdo->prepare("INSERT INTO users (email, passwort) VALUES (:email, :passwort)");
                $result = $statement->execute(['email' => $email, 'passwort' => $passwort_hash]);

                if ($result) {
                    header("Location: Login.php");
                    exit();
                } else {
                    $errorMessage = "Beim Abspeichern ist ein Fehler aufgetreten.";
                }
            }
        }
    }
    ?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="TicketUebersicht.php"><i class="fa-solid fa-house"></i></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="TicketUebersicht.php" class="nav-link">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a href="Customer.php" class="nav-link">Kunden</a>
                    </li>
                    <li class="nav-item">
                        <a href="Prioritaeten.php" class="nav-link">Prioritäten</a>
                    </li>
                    <li class="nav-item">
                        <a href="Stati.php" class="nav-link">Stati</a>
                    </li>
                    <li class="nav-item">
                        <a href="Impressum.php" class="nav-link">Impressum</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Inhalt mit Login-Form -->
    <div id="content">
        <div id="login-box">
            <h1 class="text-center">HelpDesk Registrierung</h1>
            <form id="loginform" method="post" action="?register=1" class="login_form needs-validation" novalidate>
                <div class="input_box">
                    <label for="email" class="text-dark">Benutzer:</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-Mail eingeben"
                        required>
                </div>
                <div class="input_box">
                    <label for="passwort" class="text-dark">Passwort:</label><br>
                    <input type="password" name="passwort" placeholder="Passwort eingeben" required id="passwort"
                        class="form-control">
                </div>
                <div class="input_box">
                    <label for="passwort2" class="text-dark">Passwort bestätigen:</label><br>
                    <input type="password" name="passwort2" placeholder="Passwort erneut eingeben" required
                        id="passwort2" class="form-control">
                </div>
                <div class="input_box">
                    <button type="submit" style="width: 100%;" class="btn btn-secondary" name="register" id="register">
                        Speichern
                    </button>
                </div>

                <div id="error-link" class="text-danger">
                    <?php if (isset($errorMessage))
                        echo $errorMessage; ?>
                </div>
                <div id="login-link" class="text-center">
                    <a href="Login.php">Login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>
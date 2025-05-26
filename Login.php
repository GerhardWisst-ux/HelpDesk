<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Gesamte Seite mit Flexbox zentrieren */
        body, html {
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
            height: calc(100vh - 56px); /* Abzug der Navbar-Höhe */
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
require 'db.php';
session_start(); // Session starten

$_SESSION['userid'] = "";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $passwort = trim($_POST['passwort']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Ungültige E-Mail-Adresse.";           
    } else {
        try {
            $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $statement->execute(['email' => $email]);
            $user = $statement->fetch();

            if ($user !== false && password_verify($passwort, $user['passwort'])) {
                $_SESSION['userid'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                // Redirect nur, wenn Bedingung erreicht
                header("Location: TicketUebersicht.php");
                exit();
            } else {
                $errorMessage = "E-Mail oder Passwort war ungültig.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Datenbankfehler: " . $e->getMessage();
        }
    }
}
?>


    <!-- Navbar -->
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
            <h1 class="text-center">HelpDesk Login</h1>

            <form method="post" action="?login=1" class="needs-validation" novalidate>
                <div class="input_box">
                    <label for="email" class="text-dark">Benutzer:</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-Mail eingeben" required>
                </div>
                <div class="input_box">
                    <label for="password" class="text-dark">Passwort:</label>
                    <input type="password" name="passwort" id="password" class="form-control"
                        placeholder="Passwort eingeben" required>
                </div>
                <div class="input_box">
                    <button type="submit" class="btn btn-secondary btn-login">Anmelden</button>
                </div>

                <div id="error-link" class="text-danger">
                    <?php if (isset($errorMessage)) echo $errorMessage; ?>
                </div>
                <div id="register-link" class="text-center">
                    <a href="Register.php">Registrierung</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>

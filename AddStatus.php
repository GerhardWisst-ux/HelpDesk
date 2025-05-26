<!DOCTYPE html>
<html>

<?php
ob_start();
session_start();
if ($_SESSION['userid'] == "") {
    header('Location: Login.php'); // zum Loginformular
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HelpDesk Status hinzufügen</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <style>
        /* Allgemeine Einstellungen */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        /* Tabelle Margins */
        .custom-container table {
            margin-left: 1.2rem !important;
            margin-right: 1.2rem !important;
            width: 98%;
        }

        .me-4 {
            margin-left: 1.2rem !important;
        }

        /* Spaltenbreiten optimieren */
        @media screen and (max-width: 767px) {

            .custom-container table {
                margin-left: 0.2rem !important;
                margin-right: 0.2rem !important;
                width: 98%;
            }

            .me-4 {
                margin-left: 0.2rem !important;
            }

        }
    </style>
</head>

<body>

    <?php

    require 'db.php';

    // Fehler anzeigen
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Prüfen, ob die Verbindung zur Datenbank steht
    if (!$pdo) {
        die("Datenbankverbindung fehlgeschlagen: " . mysqli_connect_error());
    }

    if (!isset($_SESSION['userid'])) {
        header('Location: Login.php');
    }

    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'];
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

    <div id="addPriority">
        <form action="AddStatusEntry.php" method="post">
            <div class="custom-container">
                <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
                    <h1>HelpDesk</h1>
                    <p>Status Eintrag hinzufügen</p>
                </div>

                <div class="container-fluid mt-3">
                    <div class="row">
                        <div class="col-12 text-end">
                            <?php echo "<span>Angemeldet als: " . htmlspecialchars($email) . "</span>"; ?>
                            <a class="btn btn-primary" title="Abmelden von HelpDesk" href="logout.php">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <br>
            </div>
            <br>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Beschreibung:</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" id="description" name="description" required>
                    <small id="descriptionError" class="text-danger"></small>
                </div>
            </div>

            <div class="form-group row me-4">
                <div class="col-sm-offset-2 col-sm-10">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i></button>
                    <a href="Stati.php" title="Zurück zun den Stati" class="btn btn-primary"><i class="fa fa-arrow-left"
                            aria-hidden="true"></i></a>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Heutiges Datum automatisch setzen
        document.addEventListener("DOMContentLoaded", function () {
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById("datum").value = formattedDate;
        });

        function NavBarClick() {
            const topnav = document.getElementById("myTopnav");
            if (topnav.className === "topnav") {
                topnav.className += " responsive";
            } else {
                topnav.className = "topnav";
            }
        }

        function toggleCustomInput(select) {

            const customInput = document.getElementById('custom-input');
            if (select.value === 'custom') {
                customInput.classList.remove('d-none');
                customInput.removeAttribute('disabled');
                customInput.setAttribute('required', 'required');
            } else {
                customInput.classList.add('d-none');
                customInput.setAttribute('disabled', 'disabled');
                customInput.removeAttribute('required');
                customInput.value = '';
            }

            const customLabel = document.getElementById('custom-label');
            if (select.value === 'custom') {
                customLabel.classList.remove('d-none');
            } else {
                customLabel.classList.add('d-none');
            }

            const Label = document.getElementById('label');
            if (select.value === 'custom') {
                Label.classList.add('d-none');
            } else {
                Label.classList.remove('d-none');
            }

            // Debug-Ausgabe
            console.log("Aktueller Wert:", select.value || "Keiner ausgewählt");
        }

        document.querySelector('form').addEventListener('submit', function (e) {
            const descriptionInput = document.getElementById('description');
            const descriptionError = document.getElementById('descriptionError');
            const value = descriptionInput.value.trim();

            // Regeln
            const minLength = 3;
            const maxLength = 100;
            const alphanumericRegex = ^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$;

            let errorMessage = '';

            // Validierungsregeln prüfen
            if (value.length < minLength) {
                errorMessage = `Die Beschreibung muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (value.length > maxLength) {
                errorMessage = `Die Beschreibung darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(value)) {
                errorMessage = 'Die Beschreibung darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (errorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                descriptionError.textContent = errorMessage;
            } else {
                descriptionError.textContent = ''; // Fehlernachricht zurücksetzen
            }
        });
    </script>
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
    <title>HelpDesk TicketDetail hinzufügen</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

    <style>
        /* === Grundlayout === */
        html,
        body {
            height: 100%;
            margin: 0;
            background-color: #dedfe0ff;
            /* hellgrau statt reinweiß */
        }


        /* Wrapper nimmt die volle Höhe ein und ist Flex-Container */
        .wrapper {
            min-height: 100vh;
            /* viewport height */
            display: flex;
            flex-direction: column;
        }

        /* Container oder Content-Bereich wächst flexibel */
        .container {
            flex: 1;
            /* nimmt den verfügbaren Platz ein */
        }

        /* Footer bleibt unten */
        footer {
            /* kein spezielles CSS nötig, wenn wrapper und container wie oben */
        }

        /* === Karten-Design mit Schatten === */
        .card {
            font-size: 0.9rem;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            /* leichter Schatten */
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.01);
            /* kleine Hover-Interaktion */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            font-size: 1.1rem;
        }

        .card-body p {
            margin-bottom: 0.5rem;
        }

        .card-img-top {
            height: 200px;
            /* Einheitliche Höhe */
            object-fit: cover;
            /* Bild wird beschnitten, nicht verzerrt */
        }

        /* === Navbar Design === */
        .navbar-custom {
            background: linear-gradient(to right, #cce5f6, #e6f2fb);
            border-bottom: 1px solid #b3d7f2;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #0c2c4a;
            font-weight: 500;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus {
            color: #04588c;
            text-decoration: underline;
        }

        .custom-header {
            background: linear-gradient(to right, #2a55e0ff, #4670e4ff);
            /* dunkles, klassisches Grün */
            border-bottom: 2px solid #0666f7ff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 1rem 1rem;
        }

        .btn-darkgreen {
            background-color: #0d3dc2ff;
            border-color: #145214;
            color: #fff;
        }

        .btn-darkgreen:hover {
            background-color: #0337e4ff;
            ;
            border-color: #2146beff;
        }

        .btn {
            border-radius: 50rem;
            /* pill-shape */
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
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

    require_once 'includes/header.php';
    ?>

    <div id="addTicket">
        <form action="AddTicketDetailEntry.php" method="post">
            <header class="custom-header py-2 text-white">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <!-- Titel zentriert -->
                        <div class="col-12 text-center mb-2 mb-md-0">
                            <h2 class="h4 mb-0">Helpdesk - Ticket Detail bearbeiten</h2>
                        </div>

                        <!-- Benutzerinfo + Logout -->
                        <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
                            <!-- Auf kleinen Bildschirmen: eigene Zeile für E-Mail -->
                            <div class="d-block d-md-inline mb-1 mb-md-0">
                                <span class="me-2">Angemeldet als: <?= htmlspecialchars($_SESSION['email']) ?></span>
                            </div>
                            <!-- Logout-Button -->
                            <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
                                <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            <br>
            <div class="mx-2">
                <div class="form-group row me-4">
                    <label class="col-sm-2 col-form-label text-dark">Ticket-ID:</label>
                    <div class="col-sm-10">
                        <input id="ticketid" maxlength="150" class="form-control" type="text"
                            value="<?= $_SESSION['TicketID'] ?>" disabled>
                        <input type="hidden" name="ticketid" value="<?= $_SESSION['TicketID'] ?>">
                    </div>
                </div>
                <div class="form-group row me-4">
                    <label class="col-sm-2 col-form-label text-dark">Beschreibung:</label>
                    <div class="col-sm-10">
                        <input class="form-control" maxlength="150" type="text" id="description" name="description"
                            required>
                        <small id="descriptionError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row me-4">
                    <label class="col-sm-2 col-form-label text-dark">Bemerkung:</label>
                    <div class="col-sm-10">
                        <input class="form-control" maxlength="5000" type="text" id="notes" name="notes" required>
                        <small id="notesError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row me-4">
                    <label class="col-sm-2 col-form-label text-dark">Berechnete Stunden:</label>
                    <div class="col-sm-10">
                        <input id="billingHours" class="form-control" type="number" name="billingHours">
                        <small id="billingHoursError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row me-4">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i></button>
                        <a href="ShowTickets.php" title="Zurück zur Hauptübersicht" class="btn btn-primary"><i
                                class="fa fa-arrow-left" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

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

            // Regeln
            const minLength = 3;
            const maxLength = 100;

            const descriptionInput = document.getElementById('description');
            const descriptionError = document.getElementById('descriptionError');
            const valueDescription = descriptionInput.value.trim();


            let errorMessage = '';

            // Validierung für Beschreibung
            if (valueDescription.length < minLength) {
                errorMessage = `Die Beschreibung muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valueDescription.length > maxLength) {
                errorMessage = `Die Beschreibung darf maximal ${maxLength} Zeichen lang sein.`;
            }

            if (errorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                descriptionError.textContent = errorMessage;
            } else {
                descriptionError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const notesInput = document.getElementById('notes');
            const notesError = document.getElementById('notesError');
            const valuenotes = notesInput.value.trim();

            let notesErrorMessage = '';

            // Validierung für Bemerkung            
            if (valuenotes.length < minLength) {
                notesErrorMessage = `Die Bemerkung muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valuenotes.length > maxLength) {
                notesErrorMessage = `Die Bemerkung darf maximal ${maxLength} Zeichen lang sein.`;
            }

            if (notesErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                notesError.textContent = notesErrorMessage;
            } else {
                notesError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const createddateInput = document.getElementById('createddate');
            const createddateError = document.getElementById('createddateError');
            const valuecreateddate = createddateInput.value.trim();

            let createddateErrorMessage = '';

            // Validierung für Bemerkung
            if (!isValidDate(valuecreateddate)) {
                createddateErrorMessage = `Datum ist ungültig.`;
            }

            if (createddateErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                createddateError.textContent = createddateErrorMessage;
            }
            else {
                createddateError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const duedateInput = document.getElementById('duedate');
            const duedateError = document.getElementById('duedateError');
            const valueduedate = duedateInput.value.trim();

            let duedateErrorMessage = '';

            // Validierung für Bemerkung
            if (!isValidDate(valueduedate)) {
                duedateErrorMessage = `Datum Zu Erledigen bis ist ungültig.`;
            }

            if (duedateErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                duedateError.textContent = duedateErrorMessage;
            } else {
                duedateError.textContent = ''; // Fehlernachricht zurücksetzen
            }
        });

        function isValidDate(dateString) {
            // Versuchen, ein Datum aus dem String zu erstellen
            const date = new Date(dateString);

            // Prüfen, ob das Datum gültig ist
            return !isNaN(date.getTime());
        }

    </script>
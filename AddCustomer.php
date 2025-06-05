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
    <title>HelpDesk Kunde hinzufügen</title>

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

        .topnav {
            background-color: #2d3436;
            overflow: hidden;
            display: flex;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .topnav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .topnav a:hover {
            background-color: rgb(161, 172, 169);
            color: #2d3436;
        }

        .topnav .icon {
            display: none;
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


            .topnav a:not(:first-child) {
                display: none;
            }

            .topnav a.icon {
                display: block;
                font-size: 30px;
            }

            .topnav.responsive {
                position: relative;
            }

            .topnav.responsive .icon {
                position: absolute;
                right: 0;
                top: 0;
            }

            .topnav.responsive a {
                display: block;
                text-align: left;
            }
        }

        /* Responsive Design */
        @media screen and (max-width: 600px) {
            .topnav a:not(:first-child) {
                display: none;
            }

            .topnav a.icon {
                display: block;
                font-size: 30px;
            }

            .topnav.responsive {
                position: relative;
            }

            .topnav.responsive .icon {
                position: absolute;
                right: 0;
                top: 0;
            }

            .topnav.responsive a {
                display: block;
                text-align: left;
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


    <div id="addCustomer">
        <form action="AddCustomerEntry.php" method="post">
            <div class="custom-container">
                <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
                    <h1>HelpDesk</h1>
                    <p>Kunde hinzufügen</p>
                </div>

                <div class="container-fluid mt-3">
                    <div class="row">
                        <div class="col-12 text-end">
                            <?php echo "<span>Angemeldet als: " . htmlspecialchars($email) . "</span>"; ?>
                            <a class="btn btn-primary" title="Abmelden vomn HelpDesk" href="logout.php">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <br>
            </div>
            <br>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Firma:</label>
                <div class="col-sm-10">
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="firma" name="firma" required>
                        <small id="firmaError" class="text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Zusatz:</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" id="zusatz" name="zusatz">
                    <small id="zusatzError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Straße:</label>
                <div class="col-sm-10">
                    <input id="street" class="form-control" type="text" name="street">
                    <small id="streetError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">ZIP:</label>
                <div class="col-sm-10">
                    <input id="zip" class="form-control" type="number" name="zip">
                    <small id="zipError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Ort:</label>
                <div class="col-sm-10">
                    <input id="location" class="form-control" type="text" name="location">
                    <small id="locationError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Telefon:</label>
                <div class="col-sm-3">
                    <input id="telefon" class="form-control" type="text" name="telefon">
                    <small id="telefonError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Fax:</label>
                <div class="col-sm-3">
                    <input id="fax" class="form-control" type="text" name="fax">
                    <small id="faxError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Mail:</label>
                <div class="col-sm-3">
                    <input id="mail" class="form-control" type="text" name="mail">
                    <small id="mailError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Internet:</label>
                <div class="col-sm-10">
                    <input id="internet" class="form-control" type="text" name="internet">
                    <small id="internetError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Kunde seit:</label>
                <div class="col-sm-1">
                    <input id="kundeseit" class="form-control" type="date" name="kundeseit">
                    <small id="kundeseitError" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group row me-4">
                <div class="col-sm-offset-2 col-sm-100">
                    <button class="btn btn-primary" title="Fügt Kunden hinzu" type="submit"><i
                            class="fas fa-save"></i></button>
                    <a href="Customer.php" title="Zurück zur Kundenübersicht" class="btn btn-primary"><i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>
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
            const firmaInput = document.getElementById('firma');
            const firmaError = document.getElementById('firmaError');
            const value = firmaInput.value.trim();

            const minLength = 3;
            const maxLength = 100;
            const alphanumericRegex = /^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$+$/;

            let firmaErrorMessage = '';

            // Validierung für Nachnamen
            if (value.length < minLength) {
                firmaErrorMessage = `Der Nachname muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (value.length > maxLength) {
                firmaErrorMessage = `Der Nachname darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(value)) {
                firmaErrorMessage = 'Der Nachname darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (firmaErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                firmaError.textContent = firmaErrorMessage;
            } else {
                firmaError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const zusatzInput = document.getElementById('zusatz');
            const zusatzError = document.getElementById('zusatzError');
            const valueVorname = zusatzInput.value.trim();

            let zusatzErrorMessage = '';

            // Validierung für Vornamen
            if (valueVorname.length < minLength) {
                zusatzErrorMessage = `Der Vorname muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valueVorname.length > maxLength) {
                zusatzErrorMessage = `Der Vorname darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(valueVorname)) {
                zusatzErrorMessage = 'Der Vorname darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (zusatzErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                zusatzError.textContent = zusatzErrorMessage;
            } else {
                zusatzError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const streetInput = document.getElementById('street');
            const streetError = document.getElementById('streetError');
            const valueStreet = streetInput.value.trim();

            let streetErrorMessage = '';

            // Validierung für Straße
            if (valueStreet.length < minLength) {
                streetErrorMessage = `Die Straße muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valueStreet.length > maxLength) {
                streetErrorMessage = `Die Straße darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(valueStreet)) {
                streetErrorMessage = 'Die Straße darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (streetErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                streetError.textContent = streetErrorMessage;
            } else {
                streetError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const locationInput = document.getElementById('location');
            const locationError = document.getElementById('locationError');
            const valueLocation = locationInput.value.trim();

            let locationErrorMessage = '';

            // Validierung für Ort
            if (valueLocation.length < minLength) {
                locationErrorMessage = `Der Ort muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valueLocation.length > maxLength) {
                locationErrorMessage = `Der Ort darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(valueLocation)) {
                locationErrorMessage = 'Der Ort darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (locationErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                locationError.textContent = locationErrorMessage;
            } else {
                locationError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const telefonInput = document.getElementById('telefon');
            const telefonError = document.getElementById('telefonError');
            const valueTelefon = telefonInput.value.trim();

            let telefonErrorMessage = '';

            // Validierung für Telefon
            if (valueTelefon.length < minLength) {
                telefonErrorMessage = `Das Telefon muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valueTelefon.length > maxLength) {
                telefonErrorMessage = `Das Telefon darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(valueTelefon)) {
                telefonErrorMessage = 'Das Telefon darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (telefonErrorMessage) {
                e.preventDefault();
                telefonError.textContent = telefonErrorMessage;
            } else {
                telefonError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const faxInput = document.getElementById('fax');
            const faxError = document.getElementById('faxError');
            const valuefax = faxInput.value.trim();

            let faxErrorMessage = '';

            // Validierung für fax
            if (valuefax.length < minLength) {
                faxErrorMessage = `Das Fax muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valuefax.length > maxLength) {
                faxErrorMessage = `Das Fax darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(valuefax)) {
                faxErrorMessage = 'Das Fax darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (faxErrorMessage) {
                e.preventDefault();
                faxError.textContent = faxErrorMessage;
            } else {
                faxError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const mailInput = document.getElementById('mail');
            const mailError = document.getElementById('mailError');
            const valuemail = mailInput.value.trim();

            let mailErrorMessage = '';

            // Validierung für Mail
            if (valuemail.length < minLength) {
                mailErrorMessage = `Die Mailadresse muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valuemail.length > maxLength) {
                mailErrorMessage = `Die Mailadresse darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(valuemail)) {
                mailErrorMessage = 'Die Mailadresse darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (mailErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                mailError.textContent = mailErrorMessage;
            } else {
                mailError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const internetInput = document.getElementById('internet');
            const internetError = document.getElementById('internetError');
            const valueinternet = internetInput.value.trim();

            let internetErrorMessage = '';

            // Validierung für Internet
            if (valueinternet.length < minLength) {
                internetErrorMessage = `Die Internetadressee muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valueinternet.length > maxLength) {
                internetErrorMessage = `Die Internetadresse darf maximal ${maxLength} Zeichen lang sein.`;
            } else if (!alphanumericRegex.test(valueinternet)) {
                internetErrorMessage = 'Die Internetadresse darf nur Buchstaben, Zahlen und Leerzeichen enthalten.';
            }

            if (internetErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                internetError.textContent = internetErrorMessage;
            } else {
                internetError.textContent = ''; // Fehlernachricht zurücksetzen
            }

            const kundeseitInput = document.getElementById('kundeseit');
            const kundeseitError = document.getElementById('kundeseitError');
            const valuekundeseit = kundeseitInput.value.trim();

            let kundeseitErrorMessage = '';

            // Validierung für KundeSeit
            if (valuekundeseit.length = 0) {
                kundeseitErrorMessage = `Kundeseit muss gefüllt seinsein.`;
            }
            else if (valuekundeseit.length <> 4) {
                kundeseitErrorMessage = 'Kundeseit muß 4 stellig sein.';
            }

            if (kundeseitErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                kundeseitError.textContent = kundeseitErrorMessage;
            } else {
                kundeseitError.textContent = ''; // Fehlernachricht zurücksetzen
            }
        });

    </script>
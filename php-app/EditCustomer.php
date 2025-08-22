<!DOCTYPE html>
<html>

<?php
ob_start();
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['userid'] == "") {
    header('Location: Login.php'); // zum Loginformular
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HelpDesk Kunde bearbeiten</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
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

    $email = $_SESSION['email'];

    // Abfrage der E-Mail vom Login
    $email = $_SESSION['email'];
    if (isset($_GET['CustomerID'])) {
        $id = $_GET['CustomerID'];
        $_SESSION['CustomerID'] = $_GET['CustomerID'];
        $email = $_SESSION['email'];
        $sql = "Select * FROM customer WHERE CustomerID = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Keine CustomerID angegeben.";
    }
    require_once 'includes/header.php';
    ?>

    <div id="editcustomer">
        <form action="EditCustomerEntry.php" method="post">
            <header class="custom-header py-2 text-white">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <!-- Titel zentriert -->
                        <div class="col-12 text-center mb-2 mb-md-0">
                            <h2 class="h4 mb-0">Helpdesk - Kunde bearbeiten</h2>
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
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Firma:</label>
                    <div class="col-sm-10">
                        <input id="firma" class="form-control" type="text" name="firma"
                            value="<?= htmlspecialchars($result['Firma']) ?>" required>
                        <small id="firmaError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Zusatz:</label>
                    <div class="col-sm-10">
                        <input id="zusatz" class="form-control" type="text" name="zusatz"
                            value="<?= htmlspecialchars($result['Zusatz']) ?>" required>
                        <small id="zusatzError" class="text-danger"></small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Straße:</label>
                    <div class="col-sm-10">
                        <input id="street" class="form-control" type="text" name="street"
                            value="<?= htmlspecialchars($result['Street']) ?>">
                        <small id="streetError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">PLZ:</label>
                    <div class="col-sm-10">
                        <input id="zip" class="form-control" type="text" name="zip"
                            value="<?= htmlspecialchars($result['ZIP']) ?>">
                        <small id="zipError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Ort:</label>
                    <div class="col-sm-10">
                        <input id="location" class="form-control" type="text" name="location"
                            value="<?= htmlspecialchars($result['Location']) ?>">
                        <small id="locationError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Telefon:</label>
                    <div class="col-sm-2">
                        <input id="telefon" class="form-control" type="text" name="telefon"
                            value="<?= htmlspecialchars($result['Telefon']) ?>">
                        <small id="telefonError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Fax:</label>
                    <div class="col-sm-2">
                        <input id="fax" class="form-control" type="text" name="fax"
                            value="<?= htmlspecialchars($result['Fax']) ?>">
                        <small id="faxError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Mail:</label>
                    <div class="col-sm-2">
                        <input id="mail" class="form-control" type="text" name="mail"
                            value="<?= htmlspecialchars($result['Mail']) ?>">
                        <small id="mailError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Internet:</label>
                    <div class="col-sm-10">
                        <input id="internet" class="form-control" type="text" name="internet"
                            value="<?= htmlspecialchars($result['Internet']) ?>">
                        <small id="internetError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Kunde seit:</label>
                    <div class="col-sm-1">
                        <input id="kundeseit" class="form-control" type="date" name="kundeseit"
                            value="<?= htmlspecialchars($result['KundeSeit']) ?>">
                        <small id="kundeseitError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Stundensatz:</label>
                    <div class="col-sm-1">
                        <input id="priceperhour" min="0" max="500" step="0.01" class="form-control" type="number"
                            name="priceperhour" value="<?= htmlspecialchars($result['PricePerHour']) ?>">
                        <small id="priceperhourError" class="text-danger"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Aktiv:</label>
                    <div class="col-sm-1">
                        <?php $isActive = $result['Active']; ?>
                        <input type="checkbox" name="active" id="active" class="custom-checkbox" value="yes"
                            <?= $isActive == 1 ? 'checked' : '' ?>>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-dark">Bemerkung:</label>
                    <div class="col-sm-10">
                        <input id="notes" class="form-control" type="text" name="notes"
                            value="<?= htmlspecialchars($result['Notes']) ?>">
                    </div>
                </div>

                <div class="form-group row mt-2">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button class="btn btn-primary" title="Speichert Kunden ab" type="submit"><i
                                class="fas fa-save"></i></button>
                        <a href="AddTicket.php" title="Ticket für Kunden erstellen" class="btn btn-primary"><span>
                                <i aria-hidden="true" class="fa-solid fa-ticket"></i></span></a>'
                        <a href="Customer.php" title="Zurück zur Übersicht Kunden" class="btn btn-primary"><span>
                                <i class="fa fa-arrow-left" aria-hidden="true"></i></span></a>'
                    </div>
                </div>

            </div>

        </form>
    </div>

     <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>


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


            let firmaErrorMessage = '';

            // Validierung für Nachnamen
            if (value.length < minLength) {
                firmaErrorMessage = `Die Firma muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (value.length > maxLength) {
                firmaErrorMessage = `Der Firma darf maximal ${maxLength} Zeichen lang sein.`;
            }

            if (firmaErrorMessage) {
                e.preventDefault(); // Verhindert das Absenden des Formulars
                firmaError.textContent = firmaErrorMessage;
            } else {
                firmaError.textContent = ''; // Fehlernachricht zurücksetzen
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
                internetErrorMessage = `Die Internetadresse muss mindestens ${minLength} Zeichen lang sein.`;
            } else if (valueinternet.length > maxLength) {
                internetErrorMessage = `Die Internetadresse darf maximal ${maxLength} Zeichen lang sein.`;
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
            } else if (valuekundeseit.length < > 4) {
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
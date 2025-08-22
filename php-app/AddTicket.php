<!DOCTYPE html>
<html>

<?php
ob_start();
session_start();
if ($_SESSION['userid'] == "") {
    header('Location: Login.php'); // zum Loginformular
}
// CSRF-Token erzeugen
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HelpDesk Ticket hinzufügen</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
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

    if (!isset($_SESSION['userid'])) {
        header('Location: Login.php');
    }

    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'];

    require_once 'includes/header.php';
    ?>

    <div id="addTicket">
        <form action="AddTicketEntry.php" method="post">
            <input type="hidden" id="csrf_token" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <header class="custom-header py-2 text-white">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <!-- Titel zentriert -->
                        <div class="col-12 text-center mb-2 mb-md-0">
                            <h2 class="h4 mb-0">Helpdesk - Ticket hinzufügen</h2>
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
                <form method="post" action="dein_form_action.php">
                    <!-- Datum -->
                    <div class="form-group row me-4">
                        <label class="col-sm-2 col-form-label text-dark">Datum:</label>
                        <div class="col-sm-2">
                            <input class="form-control" type="date" id="createddate" name="createddate" required>
                            <small id="createddateError" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Zu erledigen bis -->
                    <div class="form-group row me-4">
                        <label class="col-sm-2 col-form-label text-dark">Zu erledigen bis:</label>
                        <div class="col-sm-2">
                            <input class="form-control" type="date" id="duedate" name="duedate" required>
                            <small id="duedateError" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Beschreibung -->
                    <div class="form-group row me-4">
                        <label class="col-sm-2 col-form-label text-dark">Beschreibung:</label>
                        <div class="col-sm-10">
                            <input class="form-control" maxlength="100" type="text" id="description" name="description"
                                required>
                            <small id="descriptionError" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Bemerkung -->
                    <div class="form-group row me-4">
                        <label class="col-sm-2 col-form-label text-dark">Bemerkung:</label>
                        <div class="col-sm-10">
                            <input class="form-control" maxlength="5000" type="text" id="notes" name="notes" required>
                            <small id="notesError" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Kunde -->
                    <div class="form-group row me-4">
                        <label class="col-sm-2 col-form-label text-dark">Kunde:</label>
                        <div class="col-sm-3">
                            <select class="form-control" id="customerid" name="customerid">
                                <?php
                                $sql = "SELECT * FROM customer";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . htmlspecialchars($row['CustomerID']) . "'>" . htmlspecialchars($row['Firma']) . "</option>";
                                }
                                ?>
                                <option value="custom">Wert eingeben</option>
                            </select>
                            <input id="custom-customerid" class="form-control mt-2 d-none" type="text"
                                name="custom_customer" placeholder="Wert eingeben">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-group row me-4">
                        <label class="col-sm-2 col-form-label text-dark">Status:</label>
                        <div class="col-sm-2">
                            <select class="form-control" id="statusid" name="statusid">
                                <?php
                                $sql = "SELECT * FROM status";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . htmlspecialchars($row['StatusID']) . "'>" . htmlspecialchars($row['Description']) . "</option>";
                                }
                                ?>
                                <option value="custom">Wert eingeben</option>
                            </select>
                            <input id="custom-statusid" class="form-control mt-2 d-none" type="text"
                                name="custom_status" placeholder="Wert eingeben">
                        </div>
                    </div>

                    <!-- Priorität -->
                    <div class="form-group row me-4">
                        <label class="col-sm-2 col-form-label text-dark">Priorität:</label>
                        <div class="col-sm-2">
                            <select class="form-control" id="priorityid" name="priorityid">
                                <?php
                                $sql = "SELECT * FROM priority";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . htmlspecialchars($row['PriorityID']) . "'>" . htmlspecialchars($row['Description']) . "</option>";
                                }
                                ?>
                                <option value="custom">Wert eingeben</option>
                            </select>
                            <input id="custom-priorityid" class="form-control mt-2 d-none" type="text"
                                name="custom_priority" placeholder="Wert eingeben">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="form-group row me-4">
                        <div class="col-sm-12">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i></button>
                            <a href="TicketUebersicht.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- JS -->
            <script src="js/jquery.min.js"></script>
            <script src="js/bootstrap.bundle.min.js"></script>
            <script src="js/jquery.dataTables.min.js"></script>
            <script src="js/dataTables.min.js"></script>
            <script src="js/dataTables.responsive.min.js"></script>

            <!-- Validierung & Custom Input JS -->
            <script>
                document.addEventListener("DOMContentLoaded", function () {

                    // Heutiges Datum automatisch setzen
                    const today = new Date();
                    const formattedDate = today.toISOString().split('T')[0];
                    document.getElementById("createddate").value = formattedDate;

                    const form = document.querySelector('form');

                    // Funktionen
                    function validateLength(input, min, max, errorElement, fieldName) {
                        const value = input.value.trim();
                        let errorMessage = '';
                        if (value.length < min) errorMessage = `${fieldName} muss mindestens ${min} Zeichen lang sein.`;
                        else if (value.length > max) errorMessage = `${fieldName} darf maximal ${max} Zeichen lang sein.`;
                        errorElement.textContent = errorMessage;
                        input.classList.toggle('is-invalid', !!errorMessage);
                        return !errorMessage;
                    }

                    function validateDate(input, errorElement, fieldName) {
                        const value = input.value.trim();
                        const date = new Date(value);
                        let errorMessage = '';
                        if (!value || isNaN(date.getTime())) errorMessage = `${fieldName} ist ungültig.`;
                        errorElement.textContent = errorMessage;
                        input.classList.toggle('is-invalid', !!errorMessage);
                        return !errorMessage;
                    }

                    function validateDueDate(createdInput, dueInput, errorElement) {
                        const createdDate = new Date(createdInput.value);
                        const dueDate = new Date(dueInput.value);
                        let errorMessage = '';
                        if (dueDate < createdDate) errorMessage = "Zu erledigen bis darf nicht vor dem Erstellungsdatum liegen.";
                        errorElement.textContent = errorMessage;
                        dueInput.classList.toggle('is-invalid', !!errorMessage);
                        return !errorMessage;
                    }

                    function toggleCustomInput(select, customInputId) {
                        const customInput = document.getElementById(customInputId);
                        if (select.value === 'custom') {
                            customInput.classList.remove('d-none');
                            customInput.required = true;
                        } else {
                            customInput.classList.add('d-none');
                            customInput.required = false;
                            customInput.value = '';
                        }
                    }

                    // Submit
                    form.addEventListener('submit', function (e) {
                        let valid = true;
                        valid &= validateLength(document.getElementById('description'), 3, 100, document.getElementById('descriptionError'), "Beschreibung");
                        valid &= validateLength(document.getElementById('notes'), 3, 5000, document.getElementById('notesError'), "Bemerkung");
                        valid &= validateDate(document.getElementById('createddate'), document.getElementById('createddateError'), "Datum");
                        valid &= validateDate(document.getElementById('duedate'), document.getElementById('duedateError'), "Zu erledigen bis") &&
                            validateDueDate(document.getElementById('createddate'), document.getElementById('duedate'), document.getElementById('duedateError'));
                        if (!valid) e.preventDefault();
                    });

                    // Custom Input Events
                    document.querySelectorAll('select').forEach(select => {
                        select.addEventListener('change', function () {
                            const customId = 'custom-' + this.id;
                            toggleCustomInput(this, customId);
                        });
                    });

                });
            </script>
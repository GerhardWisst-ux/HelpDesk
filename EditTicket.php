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

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>

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

  $email = $_SESSION['email'];

  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];
  if (isset($_GET['TicketID'])) {
    $id = $_GET['TicketID'];
    $_SESSION['TicketID'] = $_GET['TicketID'];
    $email = $_SESSION['email'];
    $sql = "Select * FROM ticket WHERE TicketID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
    echo "Keine TicketID angegeben.";
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

  <div id="editticket">
    <form action="EditTicketEntry.php" method="post">
      <div class="custom-container">
        <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
          <h1>HelpDesk</h1>
          <p>Ticket bearbeiten</p>
        </div>
        <br>
        <div class="form-group row me-4">
          <div class="container-fluid mt-3">
            <div class="row">
              <div class="col-12 text-end" style="text-align: right;">
                <?php echo "<span>Angemeldet als: " . $email . "</span>"; ?>
                <a class="btn btn-primary" title="Abmelden von HelpDesk" href="logout.php"><span><i
                      class="fa fa-sign-out" aria-hidden="true"></i></span></a>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">TicketID:</label>
            <div class="col-sm-3">
              <input id="ticketid" class="form-control" type="text" name="ticketid"
                value="<?= htmlspecialchars($result['TicketID']) ?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Datum:</label>
            <div class="col-sm-3">
              <input id="createddate" class="form-control" type="date" name="createddate"
                value="<?= htmlspecialchars($result['CreatedDate']) ?>">
              <small id="createddateError" class="text-danger"></small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Zu erledigen bis:</label>
            <div class="col-sm-3">
              <input id="duedate" class="form-control" type="date" name="duedate"
                value="<?= htmlspecialchars($result['DueDate']) ?>">
              <small id="duedateError" class="text-danger"></small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Beschreibung:</label>
            <div class="col-sm-10">
              <input id="description" class="form-control" type="text" name="description"
                value="<?= htmlspecialchars($result['Description']) ?>">
              <small id="descriptionError" class="text-danger"></small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Bemerkung:</label>
            <div class="col-sm-10">
              <input id="notes" class="form-control" type="text" name="notes"
                value="<?= htmlspecialchars($result['Notes']) ?>">
              <small id="notesError" class="text-danger"></small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Kunde:</label>
            <div class="col-sm-3">
              <select id="customerid" class="form-control" name="customerid">
                <?php
                // SQL-Abfrage, um Kundendaten zu holen
                $sql = "SELECT CustomerID, Firma FROM customer";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  // Markieren der aktuellen Auswahl
                  $selected = ($result['CustomerID'] == $row['CustomerID']) ? "selected" : "";
                  echo $selected;
                  echo "<option value='" . htmlspecialchars($row['CustomerID']) . "' $selected>" . htmlspecialchars($row['Firma']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Status:</label>
            <div class="col-sm-3">
              <select id="statusid" class="form-control" name="statusid">
                <?php
                // SQL-Abfrage, um Kundendaten zu holen
                $sql = "SELECT Description FROM status";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  // Markieren der aktuellen Auswahl
                  $selected = ($result['StatusID'] == $row['StatusID']) ? "selected" : "";
                  echo "<option value='" . htmlspecialchars($row['StatusID']) . "' $selected>" . htmlspecialchars($row['Description']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Priorität:</label>
            <div class="col-sm-3">
              <select id="priorityid" class="form-control" name="priorityid">
                <?php
                // SQL-Abfrage, um Kundendaten zu holen
                $sql = "SELECT Description FROM priority";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  // Markieren der aktuellen Auswahl
                  $selected = ($result['PriorityID'] == $row['PriorityID']) ? "selected" : "";
                  echo "<option value='" . htmlspecialchars($row['PriorityID']) . "' $selected>" . htmlspecialchars($row['Description']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group row me-4">
          <div class="col-sm-offset-2 col-sm-10">
            <button class="btn btn-primary" title="Speichert Ticket ab" type="submit"><i
                class="fas fa-save"></i></button>
            <a href="TicketUebersicht.php" title="Zurück zur Übersicht Tickets" class="btn btn-primary"><span>
                <i class="fa fa-arrow-left" aria-hidden="true"></i></span></a>'
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
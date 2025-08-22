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
  <title>HelpDesk Status bearbeiten</title>
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

  if (isset($_GET['StatusID'])) {
    $StatusID = $_GET['StatusID'];
    $_SESSION['StatusID'] = $_GET['StatusID'];
    $email = $_SESSION['email'];
    $sql = "Select * FROM status WHERE StatusID = :StatusID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['StatusID' => $StatusID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
    echo "Keine StatusID angegeben.";
  }
  require_once 'includes/header.php';
  ?>



  <div id="editStatus">
    <form action="EditStatusEntry.php" method="post">
      <header class="custom-header py-2 text-white">
        <div class="container-fluid">
          <div class="row align-items-center">

            <!-- Titel zentriert -->
            <div class="col-12 text-center mb-2 mb-md-0">
              <h2 class="h4 mb-0">Helpdesk - Status bearbeiten</h2>
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
        <div class="form-group row me-2">
          <label class="col-sm-2 col-form-label text-dark">StatusID:</label>
          <div class="col-sm-10">
            <input class="form-control" type="text" name="StatusID" value="<?= htmlspecialchars($result['StatusID']) ?>"
              disabled>
          </div>
        </div>
        <div class="form-group row me-2">
          <label class="col-sm-2 col-form-label text-dark">Beschreibung:</label>
          <div class="col-sm-10">
            <input id="description" class="form-control" type="text" name="description"
              value="<?= htmlspecialchars($result['Description']) ?>" required>
            <small id="descriptionError" class="text-danger"></small>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-sm-12">
            <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i></button>
            <a href="Stati.php" title="Zurück zu den Stati" class="btn btn-primary"><span>
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

    function NavBarClick() {
      const topnav = document.getElementById("myTopnav");
      if (topnav.className === "topnav") {
        topnav.className += " responsive";
      } else {
        topnav.className = "topnav";
      }
    }

    document.querySelector('form').addEventListener('submit', function (e) {
      const descriptionInput = document.getElementById('description');
      const descriptionError = document.getElementById('descriptionError');
      const value = descriptionInput.value.trim();

      // Regeln
      const minLength = 3;
      const maxLength = 100;
      const alphanumericRegex = ^ [a - zA - Z0 - 9äöüÄÖÜß\s\-: ] + $;

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
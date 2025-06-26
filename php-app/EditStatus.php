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

  <div id="editStatus">
    <form action="EditStatusEntry.php" method="post">
      <div class="custom-container">
        <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
          <h1>HelpDesk</h1>
          <p>Status bearbeiten</p>
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
          <div class="form-group row me-2">
            <label class="col-sm-2 col-form-label text-dark">StatusID:</label>
            <div class="col-sm-10">
              <input class="form-control" type="text" name="StatusID"
                value="<?= htmlspecialchars($result['StatusID']) ?>" disabled>
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
            <div class="col-sm-offset-2 col-sm-10">
              <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i></button>
              <a href="Stati.php" title="Zurück zu den Stati" class="btn btn-primary"><span>
                  <i class="fa fa-arrow-left" aria-hidden="true"></i></span></a>'
            </div>
          </div>
    </form>
  </div>

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
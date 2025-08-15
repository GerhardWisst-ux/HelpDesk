<?php
ob_start();
// Session starten, falls noch nicht erfolgt
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SESSION['userid'] == "") {
  // Wenn kein Benutzer angemeldet ist, weiterleiten zur Login-Seite
  header("Location: Login.php");
}
require_once 'db.php';


?>

<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Helpdesk - Impressum</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

  require_once 'includes/header.php';
  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];
  ?>

  <div class="wrapper">
    <header class="custom-header py-2 text-white">
      <div class="container-fluid">
        <div class="row align-items-center">

          <!-- Titel zentriert -->
          <div class="col-12 text-center mb-2 mb-md-0">
            <h2 class="h4 mb-0">Helpdesk - Impressum</h2>
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
    <div class="container-fluid mt-4">
      <div class="custom-container">
        <a href="TicketUebersicht.php" title="Zurück zur Hauptübersicht" class="btn btn-primary btn-sm me-4 "><i
            class="fa fa-arrow-left" aria-hidden="true"></i></a>
        <p><strong>Das HelpDesk-Programm wurde 2025 erstellt<br>
            Author : Gerhard Wißt<br>
            Langjährige Programmiererfahrung unter .NET, ASP.NET, C#, VB.NET, PHP, Blazor,
            Webtechnologien und SQL-Server. <br> Erstellt Webanwendungen und datenbankgestützte
            Applikationen.
          </strong>
        </p>
        <p class="me-4" style="text-align:left" style="text-align:left">
        <h3 class="me-4" style="text-align:left">Allgemeine Angaben</h3>
        <p class="me-4" style="text-align:left"><b>Internet:</b> <a href="www.Kassenbuch.de"
            target="_blank">www.helpdesk.de</a></p>

        <p class="me-4" style="text-align:left" style="text-align:left"><b>Name des
            Diensteanbieters:</b> EDV Beratung Wißt Einzelunternehmen</>

        <p class="me-4" style="text-align:left"><b>Vertreten durch:</b> Gerhard Wißt</p>

        <br>
        <h3 class="me-4" style="text-align:left">Anschrift und Kontakt</h3>
        <p class="me-4" style="text-align:left">Augsburger Straße. 717</p>

        <p class="me-4" style="text-align:left">70329 Stuttgart</p>

        <p class="me-4" style="text-align:left"><b>Telefon:</b> <a href="tel:015208750327">015208750327</a>
        </p>

        <p class="me-4" style="text-align:left"><b>Email:</b> <a href="mailto:g.wisst@web.de">g.wisst@web.de</a><br><br>
        </p>

        <h3 class="me-4" style="text-align:left">Haftung für Links</h3>
        <p class="me-4" style="text-align:left">Unser Angebot enthält Links zu externen Webseiten Dritter,
          auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch
          keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter
          oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der
          Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der
          Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist
          jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von
          Rechtsverletzungen werden wir derartige Links umgehend entfernen.</p>

        <br>
        <h3 class="me-4" style="text-align:left">Haftung für Inhalte</h3>
        <p class="me-4" style="text-align:left">Die Inhalte unserer Seiten wurden mit größter Sorgfalt
          erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch
          keine Gewähr übernehmen. Wir sind als Diensteanbieter jedoch nicht verpflichtet, übermittelte
          oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf
          eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung
          von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche
          Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich.
          Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend
          entfernen.</p>

        <h3 class="me-4" style="text-align:left"> Urheberrecht</h3>
        <p class="me-4" style="text-align:left">Die durch die Seitenbetreiber erstellten Inhalte und Werke
          auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung,
          Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der
          schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser
          Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf
          dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet.
          Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine
          Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei
          Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.</p>

      </div>
    </div>
  </div>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
  <script>
    function NavBarClick() {
      var x = document.getElementById("myTopnav");
      if (x.className === "topnav") {
        x.className += " responsive";
      } else {
        x.className = "topnav";
      }
    }
  </script>


</body>

</html>
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
    <title>HelpDesk Impressum</title>

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

        h1 {
            text-align: center;
        }

        p {
            text-align: center;
        }      

        label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .me-4 {
            margin-left: 1.2rem !important;
        }
       
    </style>
</head>

<body>

    <?php

    require 'db.php';
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
    <div id="bestaende">
        <form id="bestaendeform">
            <div class="custom-container">
                <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
                    <h1>HelpDesk</h1>
                    <p>Impressum</p>
                </div>

                <div class="container-fluid mt-3">
                    <div class="row">
                        <div class="col-12 text-end">
                            <?php echo "<span>Angemeldet als: " . htmlspecialchars($email) . "</span>"; ?>
                            <a class="btn btn-primary" title="Abmelden vom Kassenbuch" href="logout.php">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="custom-container">
                    <a href="TicketUebersicht.php" title="Zurück zur Hauptübersicht"
                        class="btn btn-primary btn-sm me-4 "><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
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

                    <p class="me-4" style="text-align:left"><b>Email:</b> <a
                            href="mailto:g.wisst@web.de">g.wisst@web.de</a><br><br></p>

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
        </form>

</body>

</html>
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
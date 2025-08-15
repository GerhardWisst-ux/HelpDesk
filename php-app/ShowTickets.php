<html>

<?php
ob_start();
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['userid'] == "") {
  header('Location: Login.php');
  exit; // Wichtig, um das Skript zu stoppen
}
?>

<head>
  <title>HelpDesk</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS -->
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

  $yearFilter = date("Y");

  $monatNumFilter = 0;
  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];

  $TicketID = $_GET['TicketID'];
  $_SESSION['TicketID'] = $_GET['TicketID'];

  require_once 'includes/header.php';
  ?>


  <form id="bestaendeform">
    <header class="custom-header py-2 text-white">
      <div class="container-fluid">
        <div class="row align-items-center">

          <!-- Titel zentriert -->
          <div class="col-12 text-center mb-2 mb-md-0">
            <h2 class="h4 mb-0">Helpdesk - Anzeigen Tickets</h2>
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
      <?php
      echo '<div class="btn-toolbar mx-2" role="toolbar" aria-label="Toolbar with button groups">';
      echo '<div class="btn-group" role="group" aria-label="First group">';
      echo '<a href="AddTicket.php" title="Eintrag hinzufügen" class="btn btn-primary btn-sm me-4"><span><i class="fa fa-plus" aria-hidden="true"></i></span></a>';
      echo '</div>';

      echo '<div class="btn-group me-2" role="group" aria-label="First group">';
      echo '<a href=CreateInvoice.php?TicketID=' . $TicketID . ' title="Rechnung erzeugen" class="btn btn-primary btn-sm"><span><i class="fa-solid fa-file-pdf"></i></span></a>';
      echo '</div>';
      echo '</div>';
      echo '</div><br>';

      // Abrufen der verfügbaren Monate
      $sql = "SELECT DISTINCT DATE_FORMAT(CreatedDate, '%Y-%m') AS monat, PriorityID FROM ticket WHERE Userid = " . $userid . " ORDER BY PriorityID DESC, CreatedDate DESC, Day(CreatedDate) DESC";
      $stmt = $pdo->query($sql);

      echo '<form method="GET" action="" style="display: flex; flex-direction: column; gap: 10px;">';

      // Erste Zeile: Labels
      echo '<div id="divLabels" class="mx-2" style="display: flex; justify-content: space-between; width: 30%;">';
      echo '<label for="monat" class="label me-4" style="width: 50%; text-align: left;">Tickets im Monat:</label>';
      echo '</div>';

      // Zweite Zeile: Eingabefelder
      echo '<div id ="divlocales: Inputsrest: rest: " style="display: flex; justify-content: space-between; width: 30%;">';

      // Dropdown für Bewegungen im Monat
      echo '<select id="monat" name="monat" class="form-control me-4" style="width: 200px;" onchange="this.form.submit()">';
      echo '<option value="">Alle Monate</option>';

      // Combobox mit den verfügbaren Monaten
      setlocale(LC_TIME, 'de_DE.UTF-8', 'de_DE', 'deu_deu'); // Locale auf Deutsch setzen
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monat = $row['monat'];
        $timestamp = DateTime::createFromFormat('Y-m', $monat)->getTimestamp(); // Zeitstempel aus Monat erzeugen
      
        $monatNames = [
          1 => 'Januar',
          2 => 'Februar',
          3 => 'März',
          4 => 'April',
          5 => 'Mai',
          6 => 'Juni',
          7 => 'Juli',
          8 => 'August',
          9 => 'September',
          10 => 'Oktober',
          11 => 'November',
          12 => 'Dezember'
        ];

        $monatNum = (new DateTime($monat . '-01'))->format('n'); // 'n' gibt die Monatszahl zurück
        $monatFormatted = $monatNames[$monatNum] . ' ' . (new DateTime($monat . '-01'))->format('Y');
        $selected = isset($_GET['monat']) && $_GET['monat'] == $monat ? 'selected' : '';
        echo "<option value=\"$monat\" $selected>$monatFormatted</option>";
      }

      echo '</select><br>';

      // Wenn ein Monat ausgewählt wurde, dann Buchungen filtern
      if (isset($_GET['monat']) && preg_match('/^\d{4}-\d{2}$/', $_GET['monat'])) {
        $monatFilter = $_GET['monat'];
      } else {
        $monatFilter = '';
      }
      $monatNumFilter = (new DateTime($monatFilter . '-01'))->format('n'); // 'n' gibt die Monatszahl zurück
      
      if ($monatFilter <> '')
        $yearFilter = substr($monatFilter, 0, 4);

      //echo $monatFilter;
      if ($monatFilter <> '') {
        $startDatum = $monatFilter . "-01";
        $endDatum = date("Y-m-t", strtotime($startDatum)); // Letzter Tag des Monats
        $sql = "SELECT * FROM ticket 
            WHERE CreatedDate BETWEEN :startDatum AND :endDatum 
            AND userid = :userid           
            ORDER BY PriorityID DESC, CreatedDate DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['startDatum' => $startDatum, 'endDatum' => $endDatum, 'userid' => $userid]);

      } else {
        //Wenn kein Monat ausgewählt wurde, alle Tickets anzeigen
        $sql = "SELECT * FROM ticket WHERE ticketid = :ticketid AND userid = :userid  ORDER BY PriorityID DESC, CreatedDate DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ticketid' => $TicketID, 'userid' => $userid]);
      }


      echo '</div>';

      echo '</div>';

      ?>

      <?php

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Datum ins deutsche Format umwandeln
      
        //$_SESSION("TicketID") = $row['TicketID'];
        $formattedDateCreate = (new DateTime($row['CreatedDate']))->format('Y-m-d');
        $formattedDateDue = (new DateTime($row['DueDate']))->format('Y-m-d');
        $formattedDateClosed = (new DateTime($row['ClosedDate']))->format('Y-m-d');


        echo "
    <div class='row mx-2'>
        <!-- Spalte 1 -->
        <div class='col-md-6'>
            <div class='d-flex align-items-center mb-1'>
                <label for='TicketID' class='col-sm-2 col-form-label text-dark'>TicketID</label>
                <input style='text-align:right; width:150px;' type='text' class='form-control' value='" . htmlspecialchars($TicketID) . "' id='TicketID'>
            </div>
            <div class='d-flex align-items-center mb-1'>
                <label for='Description' class='col-sm-2 col-form-label text-dark'>Beschreibung</label>
                <textarea class='form-control' style='width:80%;' id='Notes' rows='2'>" . htmlspecialchars($row['Description']) . "</textarea>
            </div>
            <div class='d-flex align-items-start mb-1'>
                <label for='Notes' class='col-sm-2 col-form-label text-dark' style='align-self: flex-start;'>Bemerkung</label>
                <textarea class='form-control' style='width:80%;' id='Notes' rows='6'>" . htmlspecialchars($row['Notes']) . "</textarea>
            </div>
        </div>

        <!-- Spalte 2 -->
        <div class='col-md-6'>
            <div class='d-flex align-items-center mb-1'>
                <label for='CustomerID' class='col-sm-2 col-form-label text-dark'>Kunde</label>
                <select class='form-control' id='status-dropdown' onchange='toggleCustomInput(this)' name='CustomerID'>
            ";

        // PHP-Block zur Ausgabe der Optionen
        $sql = "SELECT * FROM customer Order by Firma";
        $stmtCustomer = $pdo->prepare($sql);
        $stmtCustomer->execute();

        $currentCustomerID = $row['CustomerID'];

        while ($statusRow = $stmtCustomer->fetch(PDO::FETCH_ASSOC)) {
          $selected = ($statusRow['CustomerID'] === $currentCustomerID) ? 'selected' : '';
          echo "<option value='" . htmlspecialchars($statusRow['CustomerID']) . "' $selected>" . htmlspecialchars($statusRow['Firma'] . ", " . $statusRow['Firstname']) . "</option>";
        }

        echo "
                      <option value='custom'>Wert eingeben</option>
                  </select>
           </div>

            <div class='d-flex align-items-center mb-1'>
                <label for='CreatedDate' class='col-sm-2 col-form-label text-dark'>Datum</label>
                <input style='width:150px;' type='date' class='form-control' value='" . htmlspecialchars($formattedDateCreate) . "' required>
            </div>
            <div class='d-flex align-items-center mb-1'>
                <label for='DueDate' class='col-sm-2 col-form-label text-dark'>Zu erledigen Bis</label>
                <input style='width:150px;' type='date' class='form-control' value='" . htmlspecialchars($formattedDateDue) . "' required>
            </div>
            <div class='d-flex align-items-center mb-1'>
                <label for='ClosedDate' class='col-sm-2 col-form-label text-dark'>Geschlossen</label>
                <input style='width:150px;' type='date' class='form-control' id='ClosedDate' value='" . htmlspecialchars($formattedDateClosed) . "' required>
            </div>
                                    <div class='d-flex align-items-center mb-1'>
                            <label for='StatusID' class='col-sm-2 col-form-label text-dark'>Status</label>
                            <select class='form-control' style='width:150px;' id='status-dropdown' onchange='toggleCustomInput(this)' name='StatusID'>
            ";

        // PHP-Block zur Ausgabe der Optionen
        $sql = "SELECT * FROM status Order by Description";

        $stmtStatus = $pdo->prepare($sql);
        $stmtStatus->execute();

        $currentStatusID = $row['StatusID'];

        while ($statusRow = $stmtStatus->fetch(PDO::FETCH_ASSOC)) {
          $selected = ($statusRow['StatusID'] === $currentStatusID) ? 'selected' : '';
          echo "<option value='" . htmlspecialchars($statusRow['StatusID']) . "' $selected>" . htmlspecialchars($statusRow['Description']) . "</option>";
        }


        echo "
                    <option value='custom'>Wert eingeben</option>
                </select>
            </div>
            <div class='d-flex align-items-center mb-1'>
                <label for='PriorityID' class='col-sm-2 col-form-label text-dark'>Priorität</label>
                <select class='form-control' style='width:150px;' id='priority-dropdown' onchange='toggleCustomInput(this)' name='PriorityID'>
            ";

        // PHP-Block zur Ausgabe der Optionen
        $sql = "SELECT * FROM priority Order by Description";
        $stmtPriority = $pdo->prepare($sql);
        $stmtPriority->execute();

        $currentPriorityID = $row['PriorityID'];

        while ($statusRow = $stmtPriority->fetch(PDO::FETCH_ASSOC)) {
          $selected = ($statusRow['PriorityID'] === $currentPriorityID) ? 'selected' : '';
          echo "<option value='" . htmlspecialchars($statusRow['PriorityID']) . "' $selected>" . htmlspecialchars($statusRow['Description']) . "</option>";
        }

        echo "
                    <option value='custom'>Wert eingeben</option>
                </select>
            </div>
        </div>
    </div>

    <a href='AddTicketDetail.php' title='Eintrag hinzufügen' class='btn btn-primary btn-sm me-4'><span><i class='fa fa-plus' aria-hidden='true'></i></span></a><br>'

'
    <div class='custom-container'>
    <table id='TableTicketDetail' class='display nowrap'>
        <thead>
            <tr>
                <th style='visibility:hidden;max-width:0px;vertical-align:top;'>Detail ID</th>
                <th style='vertical-align:top;'>Datum</th>
                <th style='vertical-align:top;'>Beschreibung</th>
                <th style='vertical-align:top;' class='visible-column'>Bemerkung</th>
                <th style='vertical-align:top;'>Stunden</th>
                <th></th>
            </tr>
        </thead>
        <tbody>";

        $sql = "SELECT * FROM TicketDetail WHERE TicketID = :ticketID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ticketID' => $TicketID]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $formattedDate = (new DateTime($row['ServiceDateTime']))->format('d.m.Y H.i');
          echo "<tr>
                    <td style='visibility:hidden;max-width:0px;'>{$row['TicketDetailID']}</td>
                    <td style='vertical-align: top;'>{$formattedDate}</td>
                    <td style='vertical-align: top;'>{$row['Description']}</td>
                    <td style='vertical-align: top;' class='visible-column'>{$row['Notes']}</td>
                    <td style='vertical-align: top; width:7%; white-space: nowrap;' class='betrag-right'>" . number_format($row['BillingHours'], 2, '.', ',') . "</td>
                    <td style='vertical-align: top; width:3%; white-space: nowrap;'>
                        <a href='EditTicketDetail.php?id={$row['TicketDetailID']}' style='width:60px;' title='Ticket Detail bearbeiten' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a>
                        <a href='DeleteTicketDetail.php?id={$row['TicketDetailID']}' style='width:60px;' data-id={$row['TicketDetailID']} title='Ticket Detail löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>
                    </td>
                </tr>";
        }
        ";
           
        </tbody>
        <tfoot>";

        // Anzahl Stunden berechnen
        $sql = "SELECT COUNT(*) AS anzahl FROM ticketdetail INNER JOIN ticket ON ticket.TicketID = ticketdetail.TicketID WHERE ticket.ticketid = :ticketid AND ticket.UserID = :userid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ticketid' => $TicketID, 'userid' => $userid]);
        $resultCount = $stmt->fetch(PDO::FETCH_ASSOC);

        // Summe Stunden berechnen
        $sql = "SELECT SUM(BillingHours) AS sumStunden FROM ticketdetail INNER JOIN ticket ON ticket.TicketID = ticketdetail.TicketID WHERE ticket.ticketid = :ticketid AND ticket.UserID = :userid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ticketid' => $TicketID, 'userid' => $userid]);
        $resultSum = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "
        
                <tr>
                    <td colspan='6' style='text-align: left; font-weight: bold;'>Anzahl Besuche: " . number_format($resultCount['anzahl'], 0, '.', '.') . "</td>
                </tr>
                <tr>
                    <td colspan='6' style='text-align: left; font-weight: bold;'>Summe Stunden: " . number_format($resultSum['sumStunden'], 2, '.', '.') . "</td>
                </tr>
            
              </tfoot>
          </table>
      </div>";
      }

      $format = "txt"; //Moeglichkeiten: csv und txt
      
      $datum_zeit = date("d.m.Y H:i:s");
      $ip = $_SERVER["REMOTE_ADDR"];
      $site = $_SERVER['REQUEST_URI'];
      $browser = $_SERVER["HTTP_USER_AGENT"];

      $monate = array(1 => "Januar", 2 => "Februar", 3 => "Maerz", 4 => "April", 5 => "Mai", 6 => "Juni", 7 => "Juli", 8 => "August", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember");
      $monat = date("n");
      $jahr = date("y");

      $dateiname = "logs/log_" . $monate[$monat] . "_$jahr.$format";

      $header = array("Datum", "IP", "Seite", "Browser");
      $infos = array($datum_zeit, $ip, $site, $browser);

      if ($format == "csv") {
        $eintrag = '"' . implode('", "', $infos) . '"';
      } else {
        $eintrag = implode("\t", $infos);
      }

      $write_header = !file_exists($dateiname);

      $datei = fopen($dateiname, "a");

      if ($write_header) {
        if ($format == "csv") {
          $header_line = '"' . implode('", "', $header) . '"';
        } else {
          $header_line = implode("\t", $header);
        }

        fputs($datei, $header_line . "\n");
      }

      fputs($datei, $eintrag . "\n");
      fclose($datei);

      ?>
    </div>
    
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Löschbestätigung</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
          </div>
          <div class="modal-body">
            Möchten Sie diese Position wirklich löschen?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Löschen</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div id="deleteToast" class="toast toast-green" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <strong class="me-auto">Benachrichtigung</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          Ticket wurde gelöscht.
        </div>
      </div>
    </div>
  </form>

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

  <script>
    $(document).ready(function () {
      let deleteId = null; // Speichert die ID für die Löschung

      $('.delete-button').on('click', function (event) {
        event.preventDefault();
        deleteId = $(this).data('id'); // Hole die ID aus dem Button-Datenattribut
        //alert(deleteId);
        $('#confirmDeleteModal').modal('show'); // Zeige das Modal an
      });

      $('#confirmDeleteBtn').on('click', function () {
        if (deleteId) {
          // Dynamisches Formular erstellen und absenden
          const form = $('<form>', {
            action: 'DeleteTicketDetail.php',
            method: 'POST'
          }).append($('<input>', {
            type: 'hidden',
            name: 'id',
            value: deleteId
          }));

          $('body').append(form);
          form.submit();
        }
        $('#confirmDeleteModal').modal('hide'); // Schließe das Modal

        // Zeige den Toast an
        var toast = new bootstrap.Toast($('#deleteToast')[0]);
        toast.show();
      });
    });

    function NavBarClick() {
      const topnav = document.getElementById("myTopnav");
      if (topnav.className === "topnav") {
        topnav.className += " responsive";
      } else {
        topnav.className = "topnav";
      }
    }

    $(document).ready(function () {
      $('#TicketDetail').DataTable({
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
        },
        responsive: true,
        pageLength: 25
              columnDefs: [{
          targets: 1,
          visible: true
        } // Sichtbarkeit der Spalten einstellen
        ]
      });
    });

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
  </script>

</body>

</html>
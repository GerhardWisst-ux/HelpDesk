<?php
ob_start();
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['userid'] === "") {
  header('Location: Login.php');
  exit;
}

require 'db.php';

$yearFilter = date("Y");
$monatNumFilter = 0;
$email = $_SESSION['email'] ?? '';
$userid = $_SESSION['userid'];

$TicketID = isset($_GET['TicketID']) ? (int) $_GET['TicketID'] : 0;
$_SESSION['TicketID'] = $TicketID;
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HelpDesk</title>

  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="css/responsive.dataTables.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
      #TableTicketDetail {
      width: 100%;
      font-size: 0.9rem;
    }

    #TableTicketDetail tbody tr:hover {
      background-color: #f1f5ff;
    }   
  </style>
</head>

<body>
  <div class="wrapper">

    <?php require_once 'includes/header.php'; ?>

    <form id="bestaendeform" method="GET" action="">
      <header class="custom-header py-2 text-white">
        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="col-12 text-center mb-2 mb-md-0">
              <h2 class="h4 mb-0">Helpdesk - Anzeigen Tickets</h2>
            </div>
            <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
              <div class="d-block d-md-inline mb-1 mb-md-0">
                <span class="me-2">Angemeldet als: <?= htmlspecialchars($email) ?></span>
              </div>
              <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
                <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
              </a>
            </div>
          </div>
        </div>
      </header>

      <div class="mx-2 mt-3">
        <div class="btn-toolbar mx-2" role="toolbar" aria-label="Toolbar">
          <div class="btn-group me-2" role="group">
            <a href="AddTicket.php" title="Eintrag hinzufügen" class="btn btn-primary btn-sm"><i class="fa fa-plus"
                aria-hidden="true"></i></a>
          </div>
          <?php if ($TicketID > 0): ?>
            <div class="btn-group" role="group">
              <a href="CreateInvoice.php?TicketID=<?= $TicketID ?>" title="Rechnung erzeugen"
                class="btn btn-primary btn-sm"><i class="fa-solid fa-file-pdf"></i></a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <?php
      // Monate für Filter laden
      $sqlMonate = "SELECT DISTINCT DATE_FORMAT(CreatedDate, '%Y-%m') AS monat FROM ticket WHERE Userid = :uid ORDER BY monat DESC";
      $stmtMonate = $pdo->prepare($sqlMonate);
      $stmtMonate->execute(['uid' => $userid]);

      $monatFilter = '';
      if (isset($_GET['monat']) && preg_match('/^\d{4}-\d{2}$/', $_GET['monat'])) {
        $monatFilter = $_GET['monat'];
        $yearFilter = substr($monatFilter, 0, 4);
        $monatNumFilter = (int) date('n', strtotime($monatFilter . '-01'));
      }

      // Tickets je nach Filter laden
      if ($monatFilter !== '') {
        $startDatum = $monatFilter . '-01';
        $endDatum = date('Y-m-t', strtotime($startDatum));
        $sql = "SELECT * FROM ticket WHERE CreatedDate BETWEEN :start AND :ende AND userid = :uid ORDER BY DueDate, PriorityID DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start' => $startDatum, 'ende' => $endDatum, 'uid' => $userid]);
      } else {
        // Fallback: spezifisches Ticket
        $sql = "SELECT * FROM ticket WHERE ticketid = :tid AND userid = :uid ORDER BY PriorityID DESC, DueDate DESC, PriorityID DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['tid' => $TicketID, 'uid' => $userid]);
      }
      ?>


      <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <?php
        $formattedDateCreate = $row['CreatedDate'] ? (new DateTime($row['CreatedDate']))->format('Y-m-d') : '';
        $formattedDateDue = $row['DueDate'] ? (new DateTime($row['DueDate']))->format('Y-m-d') : '';
        $formattedDateClosed = $row['ClosedDate'] ? (new DateTime($row['ClosedDate']))->format('Y-m-d') : '';
        ?>

        <div class="row mx-2">
          <div class="col-md-6">
            <div class="row mb-2">
              <label for="TicketID" class="col-sm-3 col-form-label text-dark">TicketID</label>
              <div class="col-sm-9">
                <input type="text" class="form-control text-end" value="<?= htmlspecialchars((string) $TicketID) ?>"
                  id="TicketID" disabled>
              </div>
            </div>

            <div class="row mb-2">
              <label for="Description" class="col-sm-3 col-form-label text-dark">Beschreibung</label>
              <div class="col-sm-9">
                <textarea class="form-control" id="Description"
                  rows="2"><?= htmlspecialchars($row['Description'] ?? '') ?></textarea>
              </div>
            </div>

            <div class="row mb-2">
              <label for="Notes" class="col-sm-3 col-form-label text-dark">Bemerkung</label>
              <div class="col-sm-9">
                <textarea class="form-control" id="Notes" rows="6"><?= htmlspecialchars($row['Notes'] ?? '') ?></textarea>
              </div>
            </div>
          </div>


          <div class="col-md-6">
            <div class="d-flex align-items-center mb-1">
              <label for="CustomerID" class="col-sm-2 col-form-label text-dark">Kunde</label>
              <select class="form-control col-sm-3 col-form-label text-dark" id="customer-dropdown" name="CustomerID"
                disabled>
                <?php
                $sqlC = "SELECT * FROM customer ORDER BY Firma";
                $stmtCustomer = $pdo->prepare($sqlC);
                $stmtCustomer->execute();
                $currentCustomerID = $row['CustomerID'] ?? null;
                while ($c = $stmtCustomer->fetch(PDO::FETCH_ASSOC)) {
                  $selected = ((string) $c['CustomerID'] === (string) $currentCustomerID) ? 'selected' : '';
                  echo "<option value='" . htmlspecialchars((string) $c['CustomerID']) . "' $selected>" . htmlspecialchars($c['Firma'] . ", " . $c['Firstname']) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="row mb-2">
              <label for="CreatedDate" class="col-sm-2 col-form-label text-dark">Datum</label>
              <div class="col-sm-4">
                <input type="date" class="form-control" id="CreatedDate"
                  value="<?= htmlspecialchars($formattedDateCreate) ?>" readonly>
              </div>
            </div>

            <div class="row mb-2">
              <label for="DueDate" class="col-sm-2 col-form-label text-dark">Zu erledigen bis</label>
              <div class="col-sm-4">
                <input type="date" class="form-control" id="DueDate" value="<?= htmlspecialchars($formattedDateDue) ?>">
              </div>
            </div>

            <div class="row mb-2">
              <label for="ClosedDate" class="col-sm-2 col-form-label text-dark">Geschlossen</label>
              <div class="col-sm-4">
                <input type="date" class="form-control" id="ClosedDate"
                  value="<?= htmlspecialchars($formattedDateClosed) ?>">
              </div>
            </div>

            <div class="row mb-2">
              <label for="status-dropdown" class="col-sm-2 col-form-label text-dark">Status</label>
              <div class="col-sm-4">
                <select class="form-control" style="width:150px;" id="status-dropdown" name="StatusID" disabled>
                  <?php
                  $sqlP = "SELECT * FROM status ORDER BY Description";
                  $stmtStatus = $pdo->prepare($sqlP);
                  $stmtStatus->execute();
                  $currentStatusID = $row['StatusID'] ?? null;
                  while ($p = $stmtStatus->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ((string) $p['StatusID'] === (string) $currentStatusID) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars((string) $p['StatusID']) . "' $selected>" . htmlspecialchars($p['Description']) . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="d-flex align-items-center mb-1">
              <label for="priority-dropdown" class="col-sm-2 col-form-label text-dark">Priorität</label>
              <select class="form-control" style="width:150px;" id="priority-dropdown" name="PriorityID" disabled>
                <?php
                $sqlP = "SELECT * FROM priority ORDER BY Description";
                $stmtPriority = $pdo->prepare($sqlP);
                $stmtPriority->execute();
                $currentPriorityID = $row['PriorityID'] ?? null;
                while ($p = $stmtPriority->fetch(PDO::FETCH_ASSOC)) {
                  $selected = ((string) $p['PriorityID'] === (string) $currentPriorityID) ? 'selected' : '';
                  echo "<option value='" . htmlspecialchars((string) $p['PriorityID']) . "' $selected>" . htmlspecialchars($p['Description']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
        </div>
      <?php endwhile; ?>

      <div class="indent-3px mb-2">

        <div class="mx-2 mb-2">
          <a href="AddTicketDetail.php" title="Eintrag hinzufügen" class="btn btn-primary btn-sm"><i class="fa fa-plus"
              aria-hidden="true"></i></a>
        </div>

        <div class="custom-container mx-2 mb-4">
          <table id="TableTicketDetail" class="display nowrap" style="width:100%">
            <thead>
              <tr>
                <th style="visibility:hidden;max-width:0px;">Detail ID</th>
                <th>Datum</th>
                <th>Beschreibung</th>
                <th class="visible-column">Bemerkung</th>
                <th>Stunden</th>
                <th>Aktion</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sqlD = "SELECT * FROM TicketDetail WHERE TicketID = :ticketID";
              $stmtD = $pdo->prepare($sqlD);
              $stmtD->execute(['ticketID' => $TicketID]);
              while ($d = $stmtD->fetch(PDO::FETCH_ASSOC)) {
                $formattedDate = $d['ServiceDateTime'] ? (new DateTime($d['ServiceDateTime']))->format('d.m.Y H:i') : '';
                echo "<tr>" .
                  "<td style='visibility:hidden;max-width:0px;'>" . htmlspecialchars((string) $d['TicketDetailID']) . "</td>" .
                  "<td>" . htmlspecialchars($formattedDate) . "</td>" .
                  "<td>" . htmlspecialchars($d['Description']) . "</td>" .
                  "<td class='visible-column'>" . htmlspecialchars($d['Notes']) . "</td>" .
                  "<td class='betrag-right'>" . number_format((float) $d['BillingHours'], 2, '.', ',') . "</td>" .
                  "<td>" .
                  "<a href='EditTicketDetail.php?id=" . urlencode((string) $d['TicketDetailID']) . "' title='Ticket Detail bearbeiten' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a> " .
                  "<a href='DeleteTicketDetail.php?id=" . urlencode((string) $d['TicketDetailID']) . "' data-id='" . htmlspecialchars((string) $d['TicketDetailID']) . "' title='Ticket Detail löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>" .
                  "</td>" .
                  "</tr>";
              }

              // Aggregationen
              $sqlCnt = "SELECT COUNT(*) AS anzahl FROM ticketdetail INNER JOIN ticket ON ticket.TicketID = ticketdetail.TicketID WHERE ticket.ticketid = :ticketid AND ticket.UserID = :userid";
              $stmtCnt = $pdo->prepare($sqlCnt);
              $stmtCnt->execute(['ticketid' => $TicketID, 'userid' => $userid]);
              $resultCount = $stmtCnt->fetch(PDO::FETCH_ASSOC);

              $sqlSum = "SELECT SUM(BillingHours) AS sumStunden FROM ticketdetail INNER JOIN ticket ON ticket.TicketID = ticketdetail.TicketID WHERE ticket.ticketid = :ticketid AND ticket.UserID = :userid";
              $stmtSum = $pdo->prepare($sqlSum);
              $stmtSum->execute(['ticketid' => $TicketID, 'userid' => $userid]);
              $resultSum = $stmtSum->fetch(PDO::FETCH_ASSOC);
              ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="6" style="text-align:left;font-weight:bold;">Anzahl Positionen:
                  <?= number_format((float) ($resultCount['anzahl'] ?? 0), 0, '.', '.') ?>
                </td>
              </tr>
              <tr>
                <td colspan="6" style="text-align:left;font-weight:bold;">Summe Stunden:
                  <?= number_format((float) ($resultSum['sumStunden'] ?? 0), 2, '.', '.') ?>
                </td>
              </tr>
            </tfoot>
          </table>

        </div>

    </form>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Löschbestätigung</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
          </div>
          <div class="modal-body">Möchten Sie diese Position wirklich löschen?</div>
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
        <div class="toast-body">Ticket wurde gelöscht.</div>
      </div>
    </div>

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

    <script>
      $(function () {
        // DataTable init
        $('#TableTicketDetail').DataTable({
          language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json' },
          responsive: {
            details: {
              display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                  var data = row.data();
                  return 'Details zu ' + data[1];
                }
              }),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table'
              })
            }
          },
          pageLength: 25,
          columnDefs: [
            { targets: 0, visible: false } // versteckt die ID-Spalte
          ]
        });

        // Delete-Modal
        let deleteId = null;
        $(document).on('click', '.delete-button', function (e) {
          e.preventDefault();
          deleteId = $(this).data('id');
          $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function () {
          if (deleteId) {
            const form = $('<form>', { action: 'DeleteTicketDetail.php', method: 'POST' })
              .append($('<input>', { type: 'hidden', name: 'id', value: deleteId }));
            $('body').append(form);
            form.trigger('submit');
          }
          $('#confirmDeleteModal').modal('hide');
          const toast = new bootstrap.Toast($('#deleteToast')[0]);
          toast.show();
        });
      });
    </script>
  </div>
</body>

</html>
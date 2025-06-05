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
    <title>HelpDesk Ticketübersicht</title>

    <!-- CSS -->
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

        .dataTables_wrapper .dataTables_length select,
      .dataTables_wrapper .dataTables_filter,
      .dataTables_info {
            margin-left: 1.2rem !important;
            margin-right: 0.8rem !important;
        }

        .me-4 {
            margin-left: 1.2rem !important;
        }

        .urgent-priority {
            background-color: rgb(116, 58, 63);
            /* Rot für hohe Priorität */
        }

        .high-priority {
            background-color: rgb(80, 48, 51);
            /* Rot für hohe Priorität */
        }

        .completed {
            background-color: #d4edda;
            /* Grün für abgeschlossene Tickets */
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

            .dataTables_wrapper .dataTables_length select,
            .dataTables_wrapper .dataTables_filter,
            #TableTickets_info {
                margin-left: 1.2rem !important;
                margin-right: 0.8rem !important;
            }

            #TableTickets td,
            #TableTickets th {
                white-space: nowrap;
                font-size: 12px;
                /* Schriftgröße anpassen */
            }

            #TableTickets td:nth-child(1),
            #TableTickets td:nth-child(2),
            #TableTickets td:nth-child(3),
            #TableTickets th:nth-child(4),
            #TableTickets td:nth-child(5),
            #TableTickets td:nth-child(6) {
                display: table-cell;

            }

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

    <div id="ticketuebersicht">
        <form id="ticketuebersichtform">
            <div class="custom-container">
                <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
                    <h1>HelpDesk</h1>
                    <p>Ticketübersicht</p>
                </div>

                <div class="container-fluid mt-3">
                    <div class="row">
                        <div class="col-12 text-end">
                            <?php echo "<span>Angemeldet als: " . htmlspecialchars($email) . "</span>"; ?>
                            <a class="btn btn-primary" title="Abmelden von HelpDesk" href="logout.php">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php

                echo '<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">';
                echo '<div class="btn-group" role="group" aria-label="First group">';
                echo '<a href="AddTicket.php" title="Eintrag hinzufügen" class="btn btn-primary btn-sm me-4"><span><i class="fa fa-plus" aria-hidden="true"></i></span></a>';
                echo '</div>';

                echo '<div class="btn-group me-2" role="group" aria-label="First group">';
                echo '<a href="CreatePDF.php" title="PDF erzeugen" class="btn btn-primary btn-sm"><span><i class="fa-solid fa-file-pdf"></i></span></a>';
                echo '</div>';
                echo '</div>';
                echo '</div><br>';

                ?>
                <br>
                <div class="custom-container">
                    <table id="TableTickets" class="display nowrap">
                        <thead>
                            <tr>
                                <th>TicketID</th>
                                <th>Beschreibung</th>
                                <th>Datum</th>
                                <th>Zu erledigen bis</th>
                                <th>Kunde</th>
                                <th>Priorität</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT TicketID, ticket.Description, CreatedDate, DueDate, ClosedDate, ticket.PriorityID, priority.Description  as PriorityText, priority.SortOrder, ticket.StatusID, status.Description as StatusText, ticket.CustomerID, customer.Firma, customer.Zusatz FROM ticket inner join status on ticket.StatusID = status.StatusID inner join priority on ticket.PriorityID = priority.PriorityID inner join customer on ticket.CustomerID = customer.CustomerID where ticket.UserID = :userid ORDER BY CreatedDate DESC";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(['userid' => $userid]);

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $formattedDateCreate = (new DateTime($row['CreatedDate']))->format('d.m.Y');
                                $formattedDateDue = (new DateTime($row['DueDate']))->format('d.m.Y');
                                $formattedDateClosed = (new DateTime($row['ClosedDate']))->format('d.m.Y');

                                if ($formattedDateClosed == '30.11.-0001')
                                    $formattedDateClosed = "";

                                $class = '';
                                if ($row['PriorityID'] == 8) {
                                    $class = 'urgent-priority';
                                } elseif ($row['PriorityID'] == 1) {
                                    $class = 'high-priority';
                                }


                                echo "<tr class='$class'>
                                    <td>{$row['TicketID']}</td>
                                    <td>{$row['Description']}</td>
                                    <td>{$formattedDateCreate}</td>
                                    <td>{$formattedDateDue}</td>
                                    <td>{$row['Firma']}, {$row['Zusatz']}</td>
                                    <td>{$row['PriorityText']}</td>
                                    <td>{$row['StatusText']}</td>                                                                                                             
                                    
                                    <td style='vertical-align: top; width:7%; white-space: nowrap;'>                                        
                                        <a href='ShowTickets.php?TicketID={$row['TicketID']}' style='width:60px;' title='Ticketübersicht' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a>
                                        <a href='EditTicket.php?TicketID={$row['TicketID']}' style='width:60px;' title='Ticket bearbeiten' class='btn btn-primary btn-sm'><i class='fa-solid fa-ticket'></i></a>
                                        <a href='DeleteTicket.php?TicketID={$row['TicketID']}' data-id={$row['TicketID']} style='width:60px;' title='Ticket löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>
                                    </td>
                                    
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Bootstrap Modal -->
                <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteModalLabel">Löschbestätigung</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Schließen"></button>
                            </div>
                            <div class="modal-body">
                                Möchten Sie dieses Ticket wirklich löschen?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Abbrechen</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Löschen</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Toast -->
                <div class="toast-container position-fixed top-0 end-0 p-3">
                    <div id="deleteToast" class="toast toast-green" role="alert" aria-live="assertive"
                        aria-atomic="true">
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

        <script>
          $(document).ready(function () {
                let deleteId = null; // Speichert die ID für die Löschung

                $('.delete-button').on('click', function (event) {
                    event.preventDefault();
                    deleteId = $(this).data('id'); // Hole die ID aus dem Button-Datenattribut
                    alert(deleteId);
                    $('#confirmDeleteModal').modal('show'); // Zeige das Modal an
                });

                $('#confirmDeleteBtn').on('click', function () {
                    if (deleteId) {
                        // Dynamisches Formular erstellen und absenden
                        const form = $('<form>', {
                            action: 'DeleteTicket.php',
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
                var x = document.getElementById("myTopnav");
                if (x.className === "topnav") {
                    x.className += " responsive";
                } else {
                    x.className = "topnav";
                }
            }

            $(document).ready(function () {
                $('#TableTickets').DataTable({
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
                    },
                    search: {
                        return: true
                    },
                    responsive: true,
                    pageLength: 10,
                    autoWidth: false,
                    columnDefs: [
                        { type: 'date', targets: 7 } // Die Spalte mit `Datum`
                    ],
                    order: [[2, 'desc']], // Sortiere nach der zweiten Spalte (CreatedDate)
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
                    }
                });
            });
        </script>

</body>

</html>
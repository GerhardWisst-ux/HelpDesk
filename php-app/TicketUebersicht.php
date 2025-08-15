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
    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'];

    require_once 'includes/header.php';
    ?>

    <div id="stati">
        <form id="staiform">
            <header class="custom-header py-2 text-white">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <!-- Titel zentriert -->
                        <div class="col-12 text-center mb-2 mb-md-0">
                            <h2 class="h4 mb-0">Helpdesk - Ticketübersicht</h2>
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
            <?php

            echo '<div class="btn-toolbar mx-2 mt-2" role="toolbar" aria-label="Toolbar with button groups">';
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
            <div class="custom-container mx-2">
                <table id="TableTickets" class="display nowrap">
                    <thead>
                        <tr>
                            <th>TicketID</th>
                            <th>Beschreibung</th>
                            <th>Bemerkung</th>
                            <th>Datum</th>
                            <th>Zu erledigen bis</th>
                            <th>Kunde</th>
                            <th>Priorität</th>
                            <th>Status</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT TicketID, ticket.Description, ticket.Notes, CreatedDate, DueDate, ClosedDate, ticket.PriorityID, priority.Description  as PriorityText, priority.SortOrder, ticket.StatusID, status.Description as StatusText, ticket.CustomerID, customer.Firma, customer.Zusatz FROM ticket inner join status on ticket.StatusID = status.StatusID inner join priority on ticket.PriorityID = priority.PriorityID inner join customer on ticket.CustomerID = customer.CustomerID where ticket.UserID = :userid ORDER BY CreatedDate DESC";
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
                                    <td>{$row['Notes']}</td>
                                    <td>{$formattedDateCreate}</td>
                                    <td>{$formattedDateDue}</td>
                                    <td>{$row['Firma']} {$row['Zusatz']}</td>
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
                    // alert(deleteId);
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
                        { type: 'date', targets: 3 } // "Datum" ist die vierte Spalte
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
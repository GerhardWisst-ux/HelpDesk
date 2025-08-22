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
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        #TableTickets {
            width: 100%;
            font-size: 0.9rem;
        }

        #TableTickets tbody tr:hover {
            background-color: #f1f5ff;
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
                            <a class="btn btn-darkblue btn-sm" title="Abmelden vom Webshop" href="logout.php">
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
            <div class="d-flex mb-3 mx-2">
                <!-- Filter nach Kunde -->
                <div class="me-3">
                    <select id="filterKunde" style="text-align: left;" class="form-select btn btn-secondary">
                        <option value="">Kunden (Alle)</option>
                    </select>
                </div>
                <!-- Filter nach Status -->
                <div class="me-3">
                    <select id="filterStatus" style="text-align: left;" class="form-select btn btn-primary">
                        <option value="">Status (Alle)</option>
                    </select>
                </div>

                <!-- Filter nach Priorität -->
                <div class="me-3">
                    <select id="filterPriority" style="text-align: left;" class="form-select btn btn-success">
                        <option value="">Priorität (Alle)</option>
                    </select>
                </div>

                <!-- Reset-Button -->
                <div>
                    <button id="resetFilters" class="btn btn-secondary">Filter zurücksetzen</button>
                </div>
            </div>
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
                        $sql = "SELECT TicketID, ticket.Description, ticket.Notes, CreatedDate, DueDate, ClosedDate, ticket.PriorityID, priority.Description as PriorityText, priority.SortOrder, ticket.StatusID, status.Description as StatusText, ticket.CustomerID, customer.Firma, customer.Zusatz 
        FROM ticket 
        INNER JOIN status ON ticket.StatusID = status.StatusID 
        INNER JOIN priority ON ticket.PriorityID = priority.PriorityID 
        INNER JOIN customer ON ticket.CustomerID = customer.CustomerID 
        WHERE ticket.UserID = :userid 
        ORDER BY DueDate DESC";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['userid' => $userid]);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $formattedDateCreate = (new DateTime($row['CreatedDate']))->format('d.m.Y');
                            $formattedDateDue = (new DateTime($row['DueDate']))->format('d.m.Y');

                            $formattedDateClosed = "";
                            if (!empty($row['ClosedDate']) && $row['ClosedDate'] !== "0000-00-00") {
                                $formattedDateClosed = (new DateTime($row['ClosedDate']))->format('d.m.Y');
                            }

                            // CSS-Klasse bestimmen
                            $class = '';
                            if ($row['PriorityID'] == 8) {
                                $class = 'urgent-priority';
                            } elseif ($row['PriorityID'] == 1) {
                                $class = 'high-priority';
                            }

                            // Überfällige Tickets prüfen
                            $dueDateObj = new DateTime($row['DueDate']);
                            $today = new DateTime();
                            if ($dueDateObj < $today && ($row['StatusID'] == 3 || $row['StatusID'] == 1)) {
                                $class .= ' overdue';
                            }

                            echo "<tr class='$class'>
                                <td>{$row['TicketID']}</td>
                                <td style='white-space:normal;'>{$row['Description']}</td>
                                <td style='white-space:normal;'>{$row['Notes']}</td>
                                <td data-order='{$row['CreatedDate']}'>{$formattedDateCreate}</td>
                                <td data-order='{$row['DueDate']}'>{$formattedDateDue}</td>
                                <td style='white-space:normal;'>{$row['Firma']} {$row['Zusatz']}</td>
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
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.min.js"></script>
        <script src="js/dataTables.responsive.min.js"></script>

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
                var table = $('#TableTickets').DataTable({
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
                    },
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
                    pageLength: 10,
                    autoWidth: false,
                    order: [[4, 'desc']] // sortiere nach DueDate
                });

                // Dropdowns füllen
                function fillDropdown(columnIndex, dropdownId) {
                    var uniqueValues = [];
                    table.column(columnIndex).data().each(function (value) {
                        if (value && $.inArray(value, uniqueValues) === -1) {
                            uniqueValues.push(value);
                        }
                    });
                    uniqueValues.sort();
                    $.each(uniqueValues, function (i, val) {
                        $(dropdownId).append('<option value="' + val + '">' + val + '</option>');
                    });
                }

                fillDropdown(5, '#filterKunde');     // Kunde
                fillDropdown(6, '#filterPriority');  // Priorität
                fillDropdown(7, '#filterStatus');    // Status

                // Filter-Events
                $('#filterStatus').on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    table.column(7).search(val ? '^' + val + '$' : '', true, false).draw();
                });

                $('#filterPriority').on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    table.column(6).search(val ? '^' + val + '$' : '', true, false).draw();
                });

                $('#filterKunde').on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    table.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
                });

                // Reset-Button
                $('#resetFilters').on('click', function () {
                    $('#filterKunde, #filterPriority, #filterStatus').val('');
                    table.columns().search('').draw();
                });
            });
        </script>


</body>

</html>
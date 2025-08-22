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
    <title>HelpDesk Stati</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>     
        #TableStati {
            width: 100%;
            font-size: 0.9rem;
        }

        #TableStati tbody tr:hover {
            background-color: #f1f5ff;
        }
     
    </style>
</head>

<body>

    <?php

    require 'db.php';
    $email = $_SESSION['email'];

    require_once 'includes/header.php';

    // CSRF-Token erzeugen
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>

    <div id="stati">
        <form id="staiform">
            <input type="hidden" id="csrf_token" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <header class="custom-header py-2 text-white">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <!-- Titel zentriert -->
                        <div class="col-12 text-center mb-2 mb-md-0">
                            <h2 class="h4 mb-0">Helpdesk - Stati</h2>
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
            echo '<a href="AddStatus.php" title="Status hinzufügen" class="btn btn-primary btn-sm me-4"><span><i
                                class="fa fa-plus" aria-hidden="true"></i></span></a>';
            echo '</div>';

            echo '</div><br>';

            ?>
            <br>
            <div class="table-responsive mx-2">
                <table id="TableStati" class="display nowrap table table-striped w-100">
                    <thead>
                        <tr>
                            <th>Beschreibung</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM status";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                            echo "<tr>
                                    <td>{$row['Description']}</td>
                                    <td style='vertical-align: top; width:7%; white-space: nowrap;'>
                                        <a href='EditStatus.php?StatusID={$row['StatusID']}' style='width:60px;' title='Status bearbeiten' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a>
                                        <a href='DeleteStatus.php?id={$row['StatusID']}' data-id={$row['StatusID']} style='width:60px;' title='Status löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>
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
                            Möchten Sie diesen Eintrag wirklich löschen?
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
                        Eintrag wurde gelöscht.
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
                    //alert(deleteId);
                    $('#confirmDeleteModal').modal('show'); // Zeige das Modal an
                });

                $('#confirmDeleteBtn').on('click', function () {
                    if (deleteId) {
                        const form = $('<form>', {
                            action: 'DeleteStatus.php',
                            method: 'POST'
                        }).append($('<input>', {
                            type: 'hidden',
                            name: 'id',
                            value: deleteId
                        })).append($('<input>', {
                            type: 'hidden',
                            name: 'csrf_token',
                            value: $('#csrf_token').val() // <- Das Session-Token wird übernommen
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                    $('#confirmDeleteModal').modal('hide');

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
                $('#TableStati').DataTable({
                    language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
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
                    scrollX: false,
                    pageLength: 50,
                    autoWidth: false
                });
            });
        </script>
</body>

</html>
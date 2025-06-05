<?php

require 'db.php';

session_start();
$email = $_SESSION['email'];
$userid = $_SESSION['userid'];
?>

<!doctype html>
<html lang="en">

<head>
	<title>HelpDesk - Rechnungsübericht</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

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
		#TableInvoices_info {
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
			.dataTables_wrapper .dataTables_filter {
				margin-left: 0.2rem !important;

			}

			#TableInvoices td,
			#TableInvoices th {
				white-space: nowrap;
				font-size: 12px;
				/* Schriftgröße anpassen */
			}

			#TableInvoices td:nth-child(1),
			#TableInvoices td:nth-child(2),
			#TableInvoices td:nth-child(3),
			#TableInvoices th:nth-child(4),
			#TableInvoices td:nth-child(5),
			#TableInvoices td:nth-child(6) {
				display: table-cell;

			}

		}
	</style>
</head>

<body>
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
						<a href="Invoices.php" class="nav-link">Rechnungen</a>
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
	<div class="custom-container">
		<div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
			<h1>HelpDesk</h1>
			<p>Rechnungsübersicht</p>
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
	</div>
	<div class="custom-container">
		<div class="row">
			<div class="col-lg-12">
				<br>
				<br>
				<table id="TableInvoices" class="display nowrap">
					<thead>
						<tr>
							<th>Rechnungsnummer</th>
							<th>Kunde</th>
							<th>Straße</th>
							<th>Ort</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php

						$sql = "SELECT T1.MST_ID, T1.INV_NO, T1.CUSTOMER_NAME, T1.STREET, T1.ADDRESS FROM INVOICE_MST T1";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							?>
							<tr>
								<td><?php echo $row['INV_NO']; ?></td>
								<td><?php echo $row['CUSTOMER_NAME']; ?></td>
								<td><?php echo $row['STREET']; ?></td>
								<td><?php echo $row['ADDRESS']; ?></td>
								<td style="text-align:right;">
									<a href="pdf_maker.php?MST_ID=<?php echo $row['MST_ID']; ?>&ACTION=VIEW"
										class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Zeige PDF</a>
									&nbsp;&nbsp;
									<a href="pdf_maker.php?MST_ID=<?php echo $row['MST_ID']; ?>&ACTION=DOWNLOAD"
										class="btn btn-danger"><i class="fa fa-download"></i> Download PDF</a>
									<a href="DeleteInvoice.php?MST_ID=<?php echo $row['MST_ID']; ?>"
										data-id="<?php echo $row['MST_ID']; ?>" style='
										width:60px; height:38px;' title='Rechnung löschen' class='btn btn-danger btn-sm delete-button'><i
											class='fa-solid fa-trash'></i></a>
								</td>
							</tr>
							<?php
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
                            Möchten Sie diese Rechnung wirklich löschen?
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
                        Rechnung wurde gelöscht.
                    </div>
                </div>
            </div>
		</div>
	</div>
</body>

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
					action: 'DeleteInvioce.php',
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

	$('#TableInvoices').DataTable({
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
			{ targets: 4 }
		],

		language: {
			url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
		}
	});


</script>

</html>
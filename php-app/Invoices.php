<?php

session_start();

// Fehler anzeigen
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['userid'])) {
	header('Location: Login.php');
}

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
	<link href="css/style.css" rel="stylesheet">
</head>

<body>

	<?php

	require 'db.php';

	// Prüfen, ob die Verbindung zur Datenbank steht
	if (!$pdo) {
		die("Datenbankverbindung fehlgeschlagen: " . mysqli_connect_error());
	}

	require_once 'includes/header.php';
	?>
	<header class="custom-header py-2 text-white">
		<div class="container-fluid">
			<div class="row align-items-center">

				<!-- Titel zentriert -->
				<div class="col-12 text-center mb-2 mb-md-0">
					<h2 class="h4 mb-0">Helpdesk - Rechnungsübersicht</h2>
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
	<div class="custom-container mt-2 mx-2">
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
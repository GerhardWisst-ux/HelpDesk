<?php
ob_start();
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['userid'] == "") {
    header('Location: Login.php');
    exit;
}

require 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$formErrors = [];
$successMessage = '';

$zusatz = $firma = $zip = $street = $location = $telefon = $fax = '';
$active = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zusatz = trim($_POST['zusatz'] ?? '');
    $firma = trim($_POST['firma'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $fax = trim($_POST['fax'] ?? '');
    $active = isset($_POST['active']) ? 1 : 0;

    // Pflichtfelder prüfen
    if (!$firma)
        $formErrors[] = "Firma ist Pflichtfeld.";
    if (!$zip)
        $formErrors[] = "PLZ ist Pflichtfeld.";
    if (!$street)
        $formErrors[] = "Straße ist Pflichtfeld.";
    if (!$location)
        $formErrors[] = "Ort ist Pflichtfeld.";

    // Länge prüfen
    if (strlen($firma) < 3)
        $formErrors[] = "Firma muss mindestens 3 Zeichen haben.";
    if (strlen($zip) < 3)
        $formErrors[] = "PLZ muss mindestens 3 Zeichen haben.";
    if (strlen($street) < 3)
        $formErrors[] = "Straße muss mindestens 3 Zeichen haben.";
    if (strlen($location) < 2)
        $formErrors[] = "Ort muss mindestens 2 Zeichen haben.";

    // PLZ nur Zahlen prüfen
    if (!preg_match('/^\d{3,10}$/', $zip))
        $formErrors[] = "PLZ darf nur Zahlen enthalten.";

    // Telefon/Fax nur gültige Zeichen
    $phoneRegex = '/^[0-9\s\+\-]*$/';
    if ($telefon && !preg_match($phoneRegex, $telefon))
        $formErrors[] = "Telefon enthält ungültige Zeichen.";
    if ($fax && !preg_match($phoneRegex, $fax))
        $formErrors[] = "Fax enthält ungültige Zeichen.";

    if (empty($formErrors)) {
        $stmt = $pdo->prepare("INSERT INTO customer (Zusatz, Firma, ZIP, Street, Location, Telefon, Fax, Active)
                               VALUES (:zusatz, :firma, :zip, :street, :location, :telefon, :fax, :active)");
        $stmt->execute([
            'zusatz' => $zusatz,
            'firma' => $firma,
            'zip' => $zip,
            'street' => $street,
            'location' => $location,
            'telefon' => $telefon,
            'fax' => $fax,
            'active' => $active
        ]);
        $successMessage = "Kunde erfolgreich hinzugefügt!";
        $zusatz = $firma = $zip = $street = $location = $telefon = $fax = '';
        $active = 1;
    }
}
require_once 'includes/header.php';
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HelpDesk Kunde hinzufügen</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div class="wrapper">
        <header class="custom-header py-2 text-white">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0 text-center flex-grow-1">Helpdesk - Kunde hinzufügen</h2>
                <div class="text-end">
                    <span class="me-2">Angemeldet als: <?= htmlspecialchars($_SESSION['email']) ?></span>
                    <a class="btn btn-darkgreen btn-sm" href="logout.php"><i class="fa fa-sign-out"></i> Ausloggen</a>
                </div>
            </div>
        </header>
        <div class="container-fluid my-5">
            <h2>Neuen Kunden hinzufügen</h2>
            <form method="post" action="">
                <div class="mb-3">
                    <label>Zusatz</label>
                    <input type="text" class="form-control" name="zusatz" value="<?= htmlspecialchars($zusatz) ?>">
                </div>
                <div class="mb-3">
                    <label>Firma *</label>
                    <input type="text" class="form-control" name="firma" value="<?= htmlspecialchars($firma) ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label>PLZ *</label>
                    <input type="number" class="form-control" name="zip" value="<?= htmlspecialchars($zip) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Straße *</label>
                    <input type="text" class="form-control" name="street" value="<?= htmlspecialchars($street) ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label>Ort *</label>
                    <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($location) ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label>Telefon</label>
                    <input type="text" class="form-control" name="telefon" value="<?= htmlspecialchars($telefon) ?>">
                </div>
                <div class="mb-3">
                    <label>Fax</label>
                    <input type="text" class="form-control" name="fax" value="<?= htmlspecialchars($fax) ?>">
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input checkbox-lg" name="active" id="active" <?= $active ? 'checked' : '' ?>>
                    <label class="form-check-label" for="active">Aktiv</label>
                </div>

                <!-- Fehlerausgabe -->
                <?php if (!empty($formErrors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($formErrors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($successMessage): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> </button>
                <a href="Customer.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- Optional: Live-JS Validierung -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const descriptionInput = document.getElementById('description');

            descriptionInput.addEventListener('input', function () {
                const value = descriptionInput.value.trim();
                const minLength = 3;
                const maxLength = 100;
                const regex = /^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$/;
                let error = '';

                if (value.length < minLength) error = `Mindestens ${minLength} Zeichen erforderlich.`;
                else if (value.length > maxLength) error = `Maximal ${maxLength} Zeichen erlaubt.`;
                else if (!regex.test(value)) error = 'Nur Buchstaben, Zahlen, Leerzeichen und Bindestriche erlaubt.';

                descriptionInput.classList.toggle('is-invalid', !!error);
                descriptionInput.classList.toggle('is-valid', !error);
            });
        });
    </script>
</body>

</html>
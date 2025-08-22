<!DOCTYPE html>
<html>
<?php
ob_start();
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['userid'] == "") {
    header('Location: Login.php');
    exit;
}

require 'db.php';

// Fehler anzeigen
error_reporting(E_ALL);
ini_set('display_errors', 1);

$formErrors = [];
$successMessage = '';
$description = '';
$SortOrder = "";

// Form-Handling auf derselben Seite
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description'] ?? '');
    $SortOrder = trim($_POST['sortorder'] ?? '');

    // Validierung
    if (strlen($description) < 3) {
        $formErrors[] = "Die Beschreibung muss mindestens 3 Zeichen lang sein.";
    } elseif (strlen($description) > 100) {
        $formErrors[] = "Die Beschreibung darf maximal 100 Zeichen lang sein.";
    } elseif (!preg_match('/^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$/', $description)) {
        $formErrors[] = "Die Beschreibung darf nur Buchstaben, Zahlen, Leerzeichen und Bindestriche enthalten.";
    }

    // Wenn keine Fehler, in DB speichern
    if (empty($formErrors)) {
        $stmt = $pdo->prepare("INSERT INTO priority (Description, SortOrder) VALUES (:description, :sortorder)");
        $stmt->execute(['description' => $description, 'sortorder' => $SortOrder]);
        $successMessage = "Priorität erfolgreich hinzugefügt!";
        $description = ""; // Inputfeld zurücksetzen
    }
}

require_once 'includes/header.php';
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HelpDesk Priorität hinzufügen</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <header class="custom-header py-2 text-white">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0 text-center flex-grow-1">Helpdesk - Priorität hinzufügen</h2>
                <div class="text-end">
                    <span class="me-2">Angemeldet als: <?= htmlspecialchars($_SESSION['email']) ?></span>
                    <a class="btn btn-darkgreen btn-sm" href="logout.php"><i class="fa fa-sign-out"></i> Ausloggen</a>
                </div>
            </div>
        </header>

        <div class="container-fluid my-4">
            <form id="PrioritaetenForm" method="post" action="">
                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Beschreibung:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="description" id="description"
                            value="<?= htmlspecialchars($description) ?>" required>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Sort-Order:</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="sortorder" id="sortorder"
                            value="<?= htmlspecialchars($SortOrder) ?>" required>
                    </div>
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

                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> </button>
                        <a href="Stati.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
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
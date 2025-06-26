<head>
  <title>HelpDesk Hinzufügen Priorität</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php

require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description']);

    // Regeln
    $minLength = 3;
    $maxLength = 100;
    $alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$/';

    // Validierung
    if (strlen($description) < $minLength) {
        die("Fehler: Die Beschreibung muss mindestens $minLength Zeichen lang sein.");
    }
    if (strlen($description) > $maxLength) {
        die("Fehler: Die Beschreibung darf maximal $maxLength Zeichen lang sein.");
    }
    if (!preg_match($alphanumericRegex, $description)) {
        die("Fehler: Die Beschreibung darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $description = $_POST['description'];
    

    $sql = "INSERT INTO status (Description) VALUES (:description)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['description' => $description]);

    echo "Priorität hinzugefügt!";
    sleep(3);
    header('Location: Stati.php'); // Zurück zur Übersicht
    
}
?>
</body>
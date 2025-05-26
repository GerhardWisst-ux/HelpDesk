<head>
  <title>HelpDesk Prioritäten - Eintrag löschen</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
</head>

<body>
  <?php
  require 'db.php';
  session_start();

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['StatusID'])) {
    $statusId = $_POST['StatusID'];
    // Löschen aus der Datenbank
} else {
    echo "Keine Status-ID empfangen.";
}

  echo $StatusID;

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['StatusID'])) {
    $statusId = (int) $_POST['StatusID']; // Typensicherheit

    $sql = "DELETE FROM status WHERE StatusID = :StatusID";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([':StatusID' => $statusId])) {
        echo "Status erfolgreich gelöscht.";
    } else {
        echo "Fehler beim Löschen.";
    }

    sleep(1);
    header('Location: Stati.php'); // Zurück zur Übersicht
} 
else
{
    echo "Ungültige Anfrage.";
}

  ?>
</body>
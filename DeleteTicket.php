<head>
  <title>HelpDesk Prioritäten - Eintrag löschen</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
</head>

<body>
  <?php
  require 'db.php';
  session_start();

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TicketID'])) {
    $TicketID = $_POST['TicketID'];    
} else {
    echo "Keine TicketID empfangen.";
}

  echo $TicketID;

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TicketID'])) {
    $TicketID = (int) $_POST['TicketID']; // Typensicherheit

    $sql = "DELETE FROM ticket WHERE TicketID = :TicketID";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([':TicketID' => $TicketID])) {
        echo "Ticket erfolgreich gelöscht.";
    } else {
        echo "Fehler beim Löschen.";
    }

    sleep(1);
    header('Location: TicketUebersicht.php'); // Zurück zur Übersicht
} 
else
{
    echo "Ungültige Anfrage.";
}

  ?>
</body>
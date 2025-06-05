<head>
  <<title>HelpdDesk Tickets - Eintrag löschen</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</head>

<body>
  
  <?php
  require 'db.php';
  session_start();
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM ticket WHERE TicketID	= :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);    
    echo "Ticket - Ticket mit der ID" . $id . " wurde gelöscht!";
    sleep(1);
    header('Location: TicketUebersicht.php'); // Zurück zur Übersicht
    
  } else {
    echo "Ungültige Anfrage.";
  }

  

  ?>
</body>
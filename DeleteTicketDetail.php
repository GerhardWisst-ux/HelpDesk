<head>
  <title>Buchunngen - Eintrag löschen</title>
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

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_POST['TicketDetailID'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM ticketdetail WHERE TicketDetailID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    echo "TicketDetail - Position mit der ID" . $id . " wurde gelöscht!";
    sleep(1);
    header('Location: TicketUebersicht.php'); // Zurück zur Übersicht
  
    exit();
  } else {
    echo "Ungültige Anfrage.";
  }

  ?>
</body>
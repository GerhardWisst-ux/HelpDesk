<head>
  <title>HelpDesk Prioritäten - Eintrag löschen</title>
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

  $PriorityID = $_GET['PriorityID'];

  echo $PriorityID;

  // if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['PriorityID'])) {
  //   $PriorityID = intval($_GET['PriorityID']);
  //   $sql = "DELETE FROM priority WHERE PriorityID = :priorityID";
  //   $stmt = $pdo->prepare($sql);
  //   $stmt->execute(['priorityID' => $PriorityID]);
  //   //$stmt-"Helpdesk Prioritäten - Eintrag mit der ID" . $PriorityID . " wurde gelöscht!";
  //   sleep(1);
  //   header('Location: Prioritaeten.php'); // Zurück zur Übersicht  
    
  //   exit();
  // } else {
  //   echo "Ungültige Anfrage.";
  // }

  ?>
</body>
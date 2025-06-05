<head>
  <title>HelpDesk Rechnungen - Rechnung löschen</title>
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

  // print_r($_POST);
  // return;
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM invoice_mst WHERE MST_ID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);    

    $sql = "DELETE FROM invoice_det WHERE MST_ID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);    

    echo "Rechnungen - Rechnung mit der ID" . $id . " wurde gelöscht!";
    sleep(1);
    header('Location: Invoices.php'); 
    
  } else {
    echo "Ungültige Anfrage.";
  }


  ?>
</body>
<head>
  <title>HelpDesk Ticket Hinzufügen</title>
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
    $alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$+$/';

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

    $notes = trim($_POST['notes']);

    // Regeln
    $minLength = 3;
    $maxLength = 100;
    $alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:.]+$/';

    // Validierung
    if (strlen($notes) < $minLength) {
      die("Fehler: Die Bemerkung muss mindestens $minLength Zeichen lang sein.");
    }
    if (strlen($notes) > $maxLength) {
      die("Fehler: Die Bemerkung darf maximal $maxLength Zeichen lang sein.");
    }
    if (!preg_match($alphanumericRegex, $notes)) {
      die("Fehler: Die Bemerkung darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
    }

    $createddate = trim($_POST['createddate']);
    if (!validateDate($createddate)) {
      die("Fehler: Datum ist kein gültiges Datum");
    }

    $dueDate = trim($_POST['duedate']);
    if (!validateDate($dueDate)) {
      die("Fehler: Zu erledigen bis ist kein gültiges Datum");
    }

    $statusid = trim($_POST['statusid']);    
    if (!ctype_digit($statusid) || (int) $statusid == 0) {
      die("Fehler: Wert in StatusID ist nicht gültig " . $statusid);
    }
    $statusid = (int) $statusid;   

    $priorityid = trim($_POST['priorityid']);
    if (!ctype_digit($priorityid) || (int) $priorityid == 0) {
      die("Fehler: Wert in PrioritätID ist nicht gültig " . $statusid);
    }
    $priorityid = (int) $priorityid; 

    $customerid = trim($_POST['customerid']);
    if (!ctype_digit($customerid) || (int) $customerid == 0) {
      die("Fehler: Wert in KundenID ist nicht gültig " . $customerid);
    }
    $customerid = (int) $customerid; 

  }

  function validateDate($date, $format = 'Y-m-d')
  {
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && strtolower($d->format($format)) === strtolower($date);
  }


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Createddate = $_POST['createddate'];
    $DueDate = $_POST['duedate'];
    $description = $_POST['description'];
    $notes = $_POST['notes'];
    $customerid = $_POST['customerid'];
    $statusid = $_POST['statusid'];
    $priorityid = $_POST['priorityid'];
    $userid = $_SESSION['userid'];

    $sql = "INSERT INTO ticket (Createddate, DueDate, description, customerid, priorityid, notes,statusid, userid ) VALUES (:Createddate, :DueDate, :description, :customerid, :priorityid , :notes, :statusid, :userid)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['Createddate' => $Createddate, 'DueDate' => $DueDate, 'description' => $description, 'customerid' => $customerid, 'priorityid' => $priorityid, 'notes' => $notes, 'statusid' => $statusid, 'userid' => $userid]);

    echo "Ticket hinzugefügt!";
    sleep(1);
    header('notes: TicketUebersicht.php'); // Zurück zur Übersicht
  
  }
  ?>
</body>
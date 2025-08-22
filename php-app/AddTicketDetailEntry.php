  <?php

  require 'db.php';
  session_start();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description']);

    // Regeln
    $minLength = 3;
    $maxLength = 100;
    $alphanumericRegex = '/[A-Za-zäöüßÄÖÜß\s\-]+(?:\s\d+[a-zA-Z]?)?$/';

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
    $alphanumericRegex = '/[A-Za-zäöüßÄÖÜß\s\-]+(?:\s\d+[a-zA-Z]?)?$/';

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
  
    $billingHours = trim(string: $_POST['billingHours']);
    
    if (!ctype_digit($billingHours) || (int) $billingHours == 0) {
      die("Fehler: Wert in billingHours ist nicht gültig " . $billingHours);
    }
  }
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $TicketID = $_SESSION['TicketID'];
    $description = $_POST['description'];
    $notes = $_POST['notes'];
    $billingHours = $_POST['billingHours'];

    $sql = "INSERT INTO ticketdetail (TicketID, ServiceDateTime, Description, Notes, BillingHours) VALUES (:TicketID, :ServiceDateTime, :description, :notes, :BillingHours)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['TicketID' => $TicketID, 'ServiceDateTime' => date('Y-m-d H:i'), 'description' => $description, 'notes' => $notes, 'BillingHours' => $billingHours]);

    echo "TicketDetail hinzugefügt!";
    sleep(1);
    header('Location: ShowTickets.php?TicketID=' . $_SESSION['TicketID']); // Zurück zur Übersicht    
  
  }
  ?>

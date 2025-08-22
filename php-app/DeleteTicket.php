 
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

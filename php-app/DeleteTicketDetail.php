  <?php
  require 'db.php';
  session_start();

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TicketDetailID'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM ticketdetail WHERE TicketDetailID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    echo "TicketDetail - Position mit der ID" . $id . " wurde gelöscht!";
    sleep(1);
    header('Location: ShowTickets.php'); // Zurück zur aufrufenden Seite
  
    exit();
  } else {
    echo "Ungültige Anfrage.";
  }

  ?>

<?php
require 'db.php';
session_start();

// Regeln
$alphanumericRegex = '/[A-Za-zäöüßÄÖÜß\s\-]+(?:\s\d+[a-zA-Z]?)?$/';
$minLength = 3;
$maxLength = 100;


if (!isset($_SESSION['TicketID']) || $_SESSION['TicketID'] == "") {
    echo "Keine TicketID angegeben.";
    exit();
}

$description = trim($_POST['description']);
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

// Validierung
if (strlen($notes) < $minLength) {
    die("Fehler: Die Bemerkung muss mindestens $minLength Zeichen lang sein.");
}
if (strlen($notes) > $maxLength) {
    die("Fehler: Die Bemerkung darf maximal $maxLength Zeichen lang sein.");
}
if (!preg_match($alphanumericRegex, $notes)) {
    die("Fehler: Die Beschreibung darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
}



$TicketID = $_SESSION['TicketID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // print_r($_POST);
    $TicketDetailID = $_SESSION['TicketDetailID'];
    $TicketID = $_SESSION['TicketID'];
    $servicedatetime = date('Y-m-d H:i');        
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $notes = htmlspecialchars($_POST['notes'], ENT_QUOTES, 'UTF-8');
    $billingHours = htmlspecialchars($_POST['billingHours'], ENT_QUOTES, 'UTF-8');

    try {
        // Update-Statement
        $sql = "UPDATE ticketdetail 
                SET ticketID = :ticketID,
                    servicedatetime = :servicedatetime,
                    billingHours = :billingHours,                       
                    description = :description,
                    notes = :notes 
                WHERE TicketDetailID = :TicketDetailID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'TicketDetailID' => $TicketDetailID,
            'ticketID' => $TicketID,
            'servicedatetime' => $servicedatetime,            
            'billingHours' => $billingHours,           
            'description' => $description,
            'notes' => $notes,
        ]);

        echo "Position mit der ID" . $TicketDetailID . " wurde upgedatet!";
        header('Location: ShowTickets.php?TicketID=' . $_SESSION['TicketID']); // Zurück zur Übersicht    
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        echo "Griebus";
        //exit();
    }
}
?>
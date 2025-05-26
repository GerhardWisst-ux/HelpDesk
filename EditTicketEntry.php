<?php
require 'db.php';
session_start();

// Regeln
$alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$/';
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

$dueDate = trim($_POST['createddate']);
if (!validateDate($dueDate)) {
    die("Fehler: Datum ist kein gültiges Datum");
}

$dueDate = trim($_POST['duedate']);
if (!validateDate($dueDate)) {
    die("Fehler: Zu erledigen bis ist kein gültiges Datum");
}

$statusid = trim($_POST['statusid']);
echo $statusid;
if (!is_int($statusid) || $statusid == 0) {
    die("Fehler: Wert in Status ist nicht gültig " . $statusid);
}

$priorityid = trim($_POST['priorityid']);
echo $priorityid;
if (!is_int($priorityid) || $priorityid == 0) {
    die("Fehler: Wert in Priortät ist nicht gültig:" . $priorityid);
}

$customerid = trim($_POST['customerid']);
echo $customerid;
if (!is_int($customerid) || $customerid == 0) {
    die("Fehler: Wert in Kunden ist nicht gültig:" . $customerid);
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && strtolower($d->format($format)) === strtolower($date);
}

$TicketID = $_SESSION['TicketID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $duedate = htmlspecialchars($_POST['duedate'], ENT_QUOTES, 'UTF-8');
    $createddate = htmlspecialchars($_POST['createddate'], ENT_QUOTES, 'UTF-8');
    $statusid = htmlspecialchars($_POST['statusid'], ENT_QUOTES, 'UTF-8');
    $customerid = htmlspecialchars($_POST['customerid'], ENT_QUOTES, 'UTF-8');
    $priorityid = htmlspecialchars($_POST['priorityid'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $notes = htmlspecialchars($_POST['notes'], ENT_QUOTES, 'UTF-8');

    try {
        // Update-Statement
        $sql = "UPDATE ticket 
                SET duedate = :duedate, 
                    createddate = :createddate,                     
                    statusid = :statusid,   
                    customerid = :customerid,
                    priorityid = :priorityid,
                    description = :description,
                    notes = :notes 
                WHERE TicketID = :TicketID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'TicketID' => $TicketID,
            'duedate' => $duedate,
            'createddate' => $createddate,
            'statusid' => $statusid,
            'customerid' => $customerid,
            'priorityid' => $priorityid,
            'description' => $description,
            'notes' => $notes,
        ]);

        echo "Position mit der ID" . $TicketID . " wurde upgedatet!";
        header('Location: TicketUebersicht.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>
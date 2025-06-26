<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description']);

    // Regeln
    $minLength = 3;
    $maxLength = 100;
    $alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:]+$/';

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
}

if (!isset($_SESSION['PriorityID']) || $_SESSION['PriorityID'] == "") {
    echo "Keine PriorityID angegeben.";
    exit();
}

$PriorityID = $_SESSION['PriorityID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $Description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    
    try {
        // Update-Statement
        $sql = "UPDATE priority 
                SET Description = :description
                WHERE PriorityID = :priorityID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'priorityID' => $PriorityID,
            'description' => $Description
        ]);       
        header('Location: Prioritaeten.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>

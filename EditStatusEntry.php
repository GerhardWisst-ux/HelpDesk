<?php
require 'db.php';
session_start();

if (!isset($_SESSION['StatusID']) || $_SESSION['StatusID'] == "") {
    echo "Keine StatusID angegeben.";
    exit();
}

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

$StatusID = $_SESSION['StatusID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $Description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    
    try {
        // Update-Statement
        $sql = "UPDATE status 
                SET Description = :description
                WHERE StatusID = :StatusID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'StatusID' => $StatusID,
            'description' => $Description
        ]);       
        header('Location: Stati.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>

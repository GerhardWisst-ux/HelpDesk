<?php
require 'db.php';
session_start();


if (!isset($_SESSION['CustomerID']) || $_SESSION['CustomerID'] == "") {
    echo "Keine CustomerID angegeben.";
    exit();
}

$active = 0;

// Regeln
$minLength = 3;
$maxLength = 100;
$alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:.]+$/';

$zusatz = trim($_POST['zusatz']);
$firma = trim($_POST['firma']);
$street = trim($_POST['street']);
$location = trim($_POST['location']);
$mail = trim($_POST['mail']);
$internet = trim($_POST['internet']);
$telefon = trim($_POST['telefon']);
$fax = trim($_POST['fax']);
$zip = trim($_POST['zip']);

// Validierung
if (strlen($zusatz) > $maxLength) {
    die("Fehler: Der Zusatz darf maximal $maxLength Zeichen lang sein.");
}
if (!preg_match($alphanumericRegex, $zusatz)) {
    die("Fehler: Der Zusatz darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
}

if (strlen($firma) < $minLength) {
    die("Fehler: Die Firma muss mindestens $minLength Zeichen lang sein.");
}
if (strlen($firma) > $maxLength) {
    die("Fehler: Die Firma darf maximal $maxLength Zeichen lang sein.");
}
if (!preg_match($alphanumericRegex, $firma)) {
    die("Fehler: Die Firma darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
}

if (strlen($street) < $minLength) {
    die("Fehler: Die Straße muss mindestens $minLength Zeichen lang sein.");
}
if (strlen($street) > $maxLength) {
    die("Fehler: Die Straße darf darf maximal $maxLength Zeichen lang sein.");
}
if (!preg_match($alphanumericRegex, $street)) {
    die("Fehler: Die Straße darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
}

if (strlen($location) < $minLength) {
    die("Fehler: Der Ort muss mindestens $minLength Zeichen lang sein.");
}
if (strlen($location) > $maxLength) {
    die("Fehler: Der Ort darf darf maximal $maxLength Zeichen lang sein.");
}
if (!preg_match($alphanumericRegex, $location)) {
    die("Fehler: Der Ort darf nur Buchstaben, Zahlen und Leerzeichen enthalten.");
}

if (strlen($mail) > $maxLength) {
    die("Fehler: Die Mail darf darf maximal $maxLength Zeichen lang sein.");
}

if (strlen($internet) < $minLength) {
    die("Fehler: Die Internet-Adresse muss mindestens $minLength Zeichen lang sein.");
}
if (strlen($internet) > $maxLength) {
    die("Fehler: Die Internet-Adresse darf darf maximal $maxLength Zeichen lang sein.");
}


// Um sicherzustellen, dass die URL gültig ist, füge "http://" hinzu, falls nicht vorhanden
if (!preg_match('/^https?:\/\//', $internet)) {
    $internet = "http://" . $internet;
}

if (!filter_var($internet, FILTER_VALIDATE_URL)) {
    die("Die URL ist ungültig.");
}

// Regulärer Ausdruck für die E-Mail-Validierung
$regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
if (!preg_match($regex, $mail)) {
    die("E-Mail-Adresse ist ungültig..");
}

$customerID = $_SESSION['CustomerID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zusatz = htmlspecialchars($_POST['zusatz'], ENT_QUOTES, 'UTF-8');
    $firma = htmlspecialchars($_POST['firma'], ENT_QUOTES, 'UTF-8');
    $street = htmlspecialchars($_POST['street'], ENT_QUOTES, 'UTF-8');
    $location = htmlspecialchars($_POST['location'], ENT_QUOTES, 'UTF-8');
    $mail = htmlspecialchars($_POST['mail'], ENT_QUOTES, 'UTF-8');
    $internet = htmlspecialchars($_POST['internet'], ENT_QUOTES, 'UTF-8');
    $telefon = htmlspecialchars($_POST['telefon'], ENT_QUOTES, 'UTF-8');
    $fax = htmlspecialchars($_POST['fax'], ENT_QUOTES, 'UTF-8');
    $zip = htmlspecialchars($_POST['zip'], ENT_QUOTES, 'UTF-8');


    if (!isset($_POST['active']) || $_POST['active'] == "")
        $active = 0;
    else
        $active = 1;
   
    try {
        // Update-Statement
        $sql = "UPDATE customer 
                SET zusatz = :zusatz, 
                    firma = :firma, 
                    street = :street, 
                    zip = :zip,   
                    location = :location,   
                    mail = :mail,
                    internet = :internet,
                    telefon = :telefon,
                    fax = :fax,
                    active = :active 
                WHERE CustomerID = :customerID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'customerID' => $customerID,
            'zusatz' => $zusatz,
            'firma' => $firma,
            'street' => $street,
            'zip' => $zip,
            'location' => $location,
            'mail' => $mail,
            'internet' => $internet,
            'telefon' => $telefon,
            'fax' => $fax,
            'active' => $active
        ]);

        echo "Position mit der ID" . $id . " wurde upgedatet!";
        header('Location: Customer.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>
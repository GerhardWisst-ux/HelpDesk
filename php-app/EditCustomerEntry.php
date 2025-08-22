<?php
declare(strict_types=1);

/*
 * Sicherheits-Header (früh senden)
 */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");

/* Sichere Session-Cookies (vor session_start) */
session_set_cookie_params([
    'httponly' => true,
    'secure'   => true, // Nur aktivieren, wenn HTTPS verwendet wird
    'samesite' => 'Strict'
]);
session_start();

/* DB-Verbindung laden */
require 'db.php';
if ($pdo instanceof PDO) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

/* Nur POST zulassen */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nur POST erlaubt.');
}

/* CSRF-Prüfung */
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('CSRF-Token ungültig.');
}

/* Nutzerprüfung */
$userid = $_SESSION['userid'] ?? null;
if (!$userid || !ctype_digit((string)$userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}

/* CustomerID prüfen */
$customerID = $_SESSION['CustomerID'] ?? null;
if (!$customerID || !ctype_digit((string)$customerID)) {
    http_response_code(400);
    exit('Keine gültige CustomerID angegeben.');
}

/* Status */
$active = 0;

/* Validierungsparameter */
$minLength = 3;
$maxLength = 100;
$alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:.]+$/';

/**
 * Hilfsfunktion: Eingabe prüfen
 */
function validateInput(string $value, string $field, int $min, int $max, string $pattern): void {
    $len = mb_strlen($value);
    if ($len < $min) {
        exit("Fehler: $field muss mindestens $min Zeichen lang sein.");
    }
    if ($len > $max) {
        exit("Fehler: $field darf maximal $max Zeichen lang sein.");
    }
    if (!preg_match($pattern, $value)) {
        exit("Fehler: $field enthält ungültige Zeichen.");
    }
}

/* Eingaben bereinigen */
$firma        = trim($_POST['firma'] ?? '');
$zusatz       = trim($_POST['zusatz'] ?? '');
$street       = trim($_POST['street'] ?? '');
$location     = trim($_POST['location'] ?? '');
$mail         = trim($_POST['mail'] ?? '');
$internet     = trim($_POST['internet'] ?? '');
$telefon      = trim($_POST['telefon'] ?? '');
$fax          = trim($_POST['fax'] ?? '');
$zip          = trim($_POST['zip'] ?? '');
$priceperhour = trim($_POST['priceperhour'] ?? '');

/* Validierung */
validateInput($firma, 'Firma', $minLength, $maxLength, $alphanumericRegex);
validateInput($street, 'Straße', $minLength, $maxLength, $alphanumericRegex);
validateInput($location, 'Ort', $minLength, $maxLength, $alphanumericRegex);
if ($zusatz && !preg_match($alphanumericRegex, $zusatz)) {
    exit('Fehler: Zusatz enthält ungültige Zeichen.');
}
if (mb_strlen($internet) > $maxLength) {
    exit("Fehler: Die Internet-Adresse darf maximal $maxLength Zeichen lang sein.");
}

try {
    $pdo->beginTransaction();

    // **Duplikatsprüfung**
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM customer WHERE firma = :firma AND userid = :userid AND CustomerID != :customerID
    ");
    $check->execute([
        ':firma'      => $firma,
        ':userid'     => (int)$userid,
        ':customerID' => (int)$customerID
    ]);

    if ((int)$check->fetchColumn() > 0) {
        $pdo->rollBack();
        header('Location: Customer.php?exists=1', true, 303);
        exit;
    }

    // **Update-Statement**
    $sql = "
        UPDATE customer 
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
        WHERE CustomerID = :customerID
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':customerID' => $customerID,
        ':zusatz'     => $zusatz,
        ':firma'      => $firma,
        ':street'     => $street,
        ':zip'        => $zip,
        ':location'   => $location,
        ':mail'       => $mail,
        ':internet'   => $internet,
        ':telefon'    => $telefon,
        ':fax'        => $fax,
        ':active'     => $active
    ]);

    $pdo->commit();

    // CSRF-Token invalidieren
    unset($_SESSION['csrf_token']);

    header('Location: Customer.php?success=1', true, 303);
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ($e instanceof PDOException && $e->getCode() === '23000') {
        header('Location: Customer.php?exists=1', true, 303);
        exit;
    }

    error_log('Customer-Update-Fehler: ' . $e->getMessage());
    http_response_code(500);
    exit('Ein Fehler ist aufgetreten. Bitte später erneut versuchen.');
}

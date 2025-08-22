<?php
declare(strict_types=1);

/*
 * Sicherheits-Header (früh senden)
 * Hinweis: Passe die CSP an, falls du externe Skripte/Styles brauchst.
 */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Referrer-Policy: no-referrer-when-downgrade");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");

/* Sichere Session-Cookies (vor session_start) */
session_set_cookie_params([
    'httponly' => true,
    'secure' => true, // Nur aktivieren, wenn HTTPS verwendet wird
    'samesite' => 'Strict'
]);
session_start();

/* DB-Verbindung laden (PDO im Exception-Modus empfohlen) */
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

// TicketDetailID prüfen
if (!isset($_SESSION['TicketDetailID']) || !ctype_digit((string) $_SESSION['TicketDetailID'])) {
    die("Fehler: Ungültige TicketDetailID.");
}
$TicketDetailID = (int) $_SESSION['TicketDetailID'];

// TicketID prüfen
if (!isset($_SESSION['TicketID']) || !ctype_digit((string) $_SESSION['TicketID'])) {
    die("Fehler: Ungültige TicketID.");
}
$TicketID = (int) $_SESSION['TicketID'];

// description prüfen
$description = trim($_POST['description']);
if (strlen($description) < $minLength || strlen($description) > $maxLength) {
    die("Fehler: Beschreibung ungültig.");
}
if (!preg_match($alphanumericRegex, $description)) {
    die("Fehler: Beschreibung enthält unzulässige Zeichen.");
}

// notes prüfen
$notes = trim($_POST['notes']);
if (strlen($notes) < $minLength || strlen($notes) > $maxLength) {
    die("Fehler: Bemerkung ungültig.");
}
if (!preg_match($alphanumericRegex, $notes)) {
    die("Fehler: Bemerkung enthält unzulässige Zeichen.");
}

// billingHours prüfen
$billingHours = trim($_POST['billingHours']);
if (!is_numeric($billingHours) || $billingHours < 0 || $billingHours > 1000) {
    die("Fehler: Ungültige Stundenangabe.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE ticketdetail 
                SET ticketID = :ticketID,                    
                    billingHours = :billingHours,                       
                    description = :description,
                    notes = :notes 
                WHERE TicketDetailID = :TicketDetailID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'TicketDetailID' => $TicketDetailID,
            'ticketID' => $TicketID,
            'billingHours' => $billingHours,
            'description' => $description,
            'notes' => $notes,
        ]);

        header('Location: ShowTickets.php?TicketID=' . $TicketID);
        exit();
    } catch (PDOException $e) {
        error_log("DB-Fehler: " . $e->getMessage());
        die("Fehler beim Aktualisieren. Bitte später erneut versuchen.");
    }
}
?>
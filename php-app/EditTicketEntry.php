<?php
declare(strict_types=1);

/* Sicherheits-Header (früh senden) */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");

/* Sichere Session-Cookies */
session_set_cookie_params([
    'httponly' => true,
    'secure'   => true, // Nur aktivieren, wenn HTTPS aktiv ist
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

/* Ticket-ID prüfen */
$TicketID = $_SESSION['TicketID'] ?? null;
if (!$TicketID || !ctype_digit((string)$TicketID)) {
    http_response_code(400);
    exit('Keine gültige TicketID angegeben.');
}

/* Validierungsregeln */
$alphanumericRegex = '/^[A-Za-z0-9äöüÄÖÜß\s\-.]+$/u';
$minLength = 3;
$maxLength = 100;

/* Hilfsfunktion */
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

function validateDate(string $date, string $format = 'Y-m-d'): bool {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/* Eingaben bereinigen */
$description = trim($_POST['description'] ?? '');
$notes       = trim($_POST['notes'] ?? '');
$createddate = trim($_POST['createddate'] ?? '');
$duedate     = trim($_POST['duedate'] ?? '');
$statusid    = trim($_POST['statusid'] ?? '');
$priorityid  = trim($_POST['priorityid'] ?? '');
$customerid  = trim($_POST['customerid'] ?? '');

/* Validierung */
validateInput($description, 'Beschreibung', $minLength, $maxLength, $alphanumericRegex);
validateInput($notes, 'Bemerkung', $minLength, $maxLength, $alphanumericRegex);

if (!validateDate($createddate)) {
    exit('Fehler: Erstellungsdatum ist ungültig.');
}
if (!validateDate($duedate)) {
    exit('Fehler: Fälligkeitsdatum ist ungültig.');
}

foreach (['statusid' => $statusid, 'priorityid' => $priorityid, 'customerid' => $customerid] as $field => $val) {
    if (!ctype_digit($val) || (int)$val <= 0) {
        exit("Fehler: $field enthält einen ungültigen Wert.");
    }
}

try {
    /* Update in DB */
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
        'TicketID'    => $TicketID,
        'duedate'     => $duedate,
        'createddate' => $createddate,
        'statusid'    => $statusid,
        'customerid'  => $customerid,
        'priorityid'  => $priorityid,
        'description' => $description,
        'notes'       => $notes,
    ]);

    /* CSRF-Token rotieren */
    unset($_SESSION['csrf_token']);

    header('Location: TicketUebersicht.php?success=1', true, 303);
    exit;
} catch (Throwable $e) {
    error_log('Ticket-Update-Fehler: ' . $e->getMessage());
    http_response_code(500);
    exit('Ein Fehler ist aufgetreten. Bitte später erneut versuchen.');
}

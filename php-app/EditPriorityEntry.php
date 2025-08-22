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

/* Nutzerprüfung */
$userid = $_SESSION['userid'] ?? null;
if (!$userid || !ctype_digit((string) $userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}

/* Validierungsparameter */
$minLength = 3;
$maxLength = 100;
$alphanumericRegex = '/^[a-zA-Z0-9äöüÄÖÜß\s\-:.]+$/';

/* Eingaben bereinigen */
$description = trim((string) ($_POST['description'] ?? ''));

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


/* Datenbank-Operation */
try {
    $pdo->beginTransaction();

    // Duplikatsprüfung pro Nutzer
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM priority WHERE description = :description AND userid = :userid
    ");
    $check->execute([
        ':description' => $description,
        ':userid' => (int) $userid
    ]);
    if ($check->fetchColumn() > 0) {
        $pdo->rollBack();
        header('Location: Prioritaeten.php?exists=1', true, 303);
        exit;
    }

    // Update-Statement
    $sql = "UPDATE priority 
                SET Description = :description
                WHERE priorityID = :priorityID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'priorityID' => $priorityID,
        'description' => $Description
    ]);

    $pdo->commit();

    // CSRF-Token nach erfolgreichem POST rotieren
    unset($_SESSION['csrf_token']);

    header('Location: Prioritaeten.php?success=1', true, 303);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ($e instanceof PDOException && $e->getCode() === '23000') {
        header('Location: Prioritaeten.php?exists=1', true, 303);
        exit;
    }
    error_log('Prioritaeten-Zpdate-Fehler: ' . $e->getMessage());
    http_response_code(500);
    exit('Ein Fehler ist aufgetreten. Bitte später erneut versuchen.');
}

?>
<?php
declare(strict_types=1);

/*
 * Sicherheits-Header
 */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header('X-XSS-Protection: 0'); // Moderne Browser nutzen CSP
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

/* HTTPS erzwingen */
// if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     http_response_code(403);
//     exit('Verbindung muss über HTTPS erfolgen.');
// }

/* Sichere Session-Cookies */
session_set_cookie_params([
    'httponly' => true,
    'secure'   => true,
    'samesite' => 'Strict'
]);
session_start();

/* DB laden (PDO im Exception-Modus) */
require 'db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* Nur POST zulassen */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nur POST erlaubt.');
}

// echo $_SESSION['csrf_token'] . '<br>';
// echo $_POST['csrf_token'];

/* CSRF-Token prüfen */
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
if (empty($userid) || !ctype_digit((string)$userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}

/* ID prüfen */
if (empty($_POST['id']) || !ctype_digit((string)$_POST['id'])) {
    http_response_code(400);
    exit('Ungültige ID.');
}

$id = (int)$_POST['id'];

/* Löschaktion ausführen */
try {
    $stmt = $pdo->prepare("DELETE FROM status WHERE StatusId = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    /* Logging der Löschung */
    $log = sprintf("[%s] User %d deleted id %d\n", date('c'), $userid, $id);
    file_put_contents(__DIR__ . '/delete.log', $log, FILE_APPEND);

    /* Ausgabe für Benutzer (escaped) */
    echo "Helpdesk Status - Position " . htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') . " wurde gelöscht!";

    /* CSRF-Token nach Verwendung erneuern */
    unset($_SESSION['csrf_token']);

    /* Weiterleitung */
    header('Location: Stati.php');
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    exit('Fehler beim Löschen: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<?php
$current = basename($_SERVER['SCRIPT_NAME']); // z. B. index.php oder cart.php
//echo $current;
$prefix = (strpos($_SERVER['SCRIPT_NAME'], '/') !== false) ? '../' : '';

// Session starten, falls noch nicht erfolgt
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin prüfen
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

?>

<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= $prefix ?>php-app/TicketUebersicht.php">HelpDesk</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
      <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">            
                <li class="nav-item">
                     <a class="nav-link <?= ($current == 'TicketUebersicht.php') ? 'active text-success' : '' ?>"
                        href="<?= $prefix ?>php-app/TicketUebersicht.php">Tickets</a>
                </li>
                <li class="nav-item">
                     <a class="nav-link <?= ($current == 'Customer.php') ? 'active text-success' : '' ?>"
                        href="<?= $prefix ?>php-app/Customer.php">Kunden</a>                    
                </li>
                <li class="nav-item">
                      <a class="nav-link <?= ($current == 'Prioritaeten.php') ? 'active text-success' : '' ?>"
                        href="<?= $prefix ?>php-app/Prioritaeten.php">Prioritäten</a>
                </li>
                <li class="nav-item">
                     <a class="nav-link <?= ($current == 'Stati.php') ? 'active text-success' : '' ?>"
                        href="<?= $prefix ?>php-app/Stati.php">Stati</a>
                </li>
                <li class="nav-item">
                     <a class="nav-link <?= ($current == 'Impressum.php') ? 'active text-success' : '' ?>"
                        href="<?= $prefix ?>php-app/Impressum.php">Impressum</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
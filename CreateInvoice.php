<?php

require 'db.php';
session_start();

$userid = $_SESSION['userid'];


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $TicketID = (int) $_GET['TicketID']; // Typensicherheit
    $dateTime = new DateTime();
    $rechnungsnummer = "RE" . str_pad($TicketID, 2, '0', STR_PAD_LEFT) . "-" . $dateTime->format('Y');
    $street = "";
    $loction = "";
    $customer = "";
    $customernr = 0;
    $productname = "";
    $amount = 0;
    $sql = "SELECT * FROM `ticket` INNER Join customer on ticket.CustomerID= customer.CustomerID WHERE TicketID = :ticketid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticketid' => $TicketID]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $street = $row['Street'];
        $location = $row['Location'];
        $customer = $row['Firma'];
        $customernr = $row['CustomerID'];
    }

    // Kopfsatz schreiben
    $sql = "INSERT INTO invoice_mst (INV_NO, CUSTOMER_NAME, STREET, ADDRESS) VALUES (:INV_NO, :CUSTOMER_NAME, :STREET, :ADDRESS)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['INV_NO' => $rechnungsnummer, 'CUSTOMER_NAME' => $customer, 'STREET' => $street, 'ADDRESS' => $location]);
    $last_id = $pdo->lastInsertId();

    $sql = "SELECT * FROM `ticketdetail` INNER Join ticket on ticketdetail.TicketID= ticket.TicketID WHERE ticket.TicketID = :ticketid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticketid' => $TicketID]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mst_id = $last_id;
        $productname = $row['Notes'];
        $amount = $row['BillingHours'] * 50;
        $positiontax = 1;

        // Detailsätze schreiben
        $sqlIns = "INSERT INTO invoice_det (MST_ID, PRODUCT_NAME, AMOUNT, NUMBER, POSITIONTAX, Hours) VALUES (:MST_ID, :PRODUCT_NAME, :AMOUNT, :NUMBER, :POSITIONTAX, :Hours)";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute(['MST_ID' => $mst_id, 'PRODUCT_NAME' => $productname, 'AMOUNT' => $amount, 'NUMBER' => 70, 'POSITIONTAX' => $positiontax ,'Hours' => $row['BillingHours']]);
    }

    $closedDate = $dateTime->format('Y-m-d H:i:s');
    $sql = "UPDATE ticket SET ClosedDate = :closedDate, StatusID = :statusid  WHERE TicketID = :ticketID";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':closedDate' => $closedDate,
        ':statusid' => 6,
        ':ticketID' => $TicketID
    ]);

    echo "Rechnung erfolgreich erzeugt.";

    sleep(1);
    header('Location: Invoices.php'); // Zurück zur Übersicht
} else {
    echo "Ungültige Anfrage.";
    //  echo "Fehler beim Erzeugen der Rechnung.";
}
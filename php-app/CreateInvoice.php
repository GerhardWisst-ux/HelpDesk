<?php

require 'db.php';
session_start();

$userid = $_SESSION['userid'];
$iAnzahl = 0;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT Count(*) AS Anzahl from `invoice_mst` WHERE USERID = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userid' => $userid]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $iAnzahl = $row['Anzahl'];
    }

    $TicketID = (int) $_GET['TicketID'];
    $dateTime = new DateTime();
    $rechnungsnummer = "RE" . str_pad($iAnzahl + 1, 2, '0', STR_PAD_LEFT) . "-" . $dateTime->format('Y');
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
    $sql = "INSERT INTO invoice_mst (INV_NO, CUSTOMER_NAME, STREET, ADDRESS, USERID) VALUES (:INV_NO, :CUSTOMER_NAME, :STREET, :ADDRESS, :USERID)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['INV_NO' => $rechnungsnummer, 'CUSTOMER_NAME' => $customer, 'STREET' => $street, 'ADDRESS' => $location, 'USERID' => $userid]);
    $last_id = $pdo->lastInsertId();

    $sql = "SELECT * FROM `ticketdetail` INNER Join ticket on ticketdetail.TicketID= ticket.TicketID WHERE ticket.TicketID = :ticketid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticketid' => $TicketID]);

    while ($rowOuter = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mst_id = $last_id;
        $productname = $rowOuter['Notes'];
        $amount = $rowOuter['BillingHours'];

        $sql = "SELECT CustomerID, PricePerHour FROM customer WHERE CustomerID= :customerid";
        $stmtInner = $pdo->prepare($sql);
        $stmtInner->execute(['customerid' => $customernr]);

        $price = 0;
        while ($rowInner = $stmtInner->fetch(PDO::FETCH_ASSOC)) {
            $price = $rowInner['PricePerHour'];
            $amount = $amount * $price;
        }

        $positiontax = 1;

        // Detailsätze schreiben
        $sqlIns = "INSERT INTO invoice_det (MST_ID, PRODUCT_NAME, AMOUNT, NUMBER, POSITIONTAX, Hours, USERID) 
               VALUES (:MST_ID, :PRODUCT_NAME, :AMOUNT, :NUMBER, :POSITIONTAX, :Hours, :USERID)";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([
            'MST_ID' => $mst_id,
            'PRODUCT_NAME' => $productname,
            'AMOUNT' => $amount,
            'NUMBER' => $price,
            'POSITIONTAX' => $positiontax,
            'Hours' => $rowOuter['BillingHours'], // Verwende $rowOuter statt $row
            'USERID' => $userid
        ]);
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
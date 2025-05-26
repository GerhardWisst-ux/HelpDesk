<?php
 
require 'db.php';
session_start();

$userid = $_SESSION['userid'];

$kassen_nummer = $userid;
$HelpDesk_datum = date("d.m.Y");
$lieferdatum = date("d.m.Y");
$pdfAuthor = "EDV Beratung Wißt"; 

if (!file_exists('Images/Ticket.png')) {
    echo 'Fehler: Logo-Datei nicht gefunden.';
}

$HelpDesk_header = '
<img src="Images/Ticket.png">
';
 
$HelpDesk_empfaenger = 'Firma: HelpDesk GmbH, Im Neuen Berg 32, 70327 Stuttgart';

$HelpDesk_empfaenger = "<b>" . htmlspecialchars($HelpDesk_empfaenger, ENT_QUOTES, 'UTF-8') . "</b>";
 
// $HelpDesk_footer = "Dieses PDF Dokument wurde mit dem HelpDesk erstellt:
$HelpDesk_footer = "
 
";
 
 // Wenn kein Monat ausgewählt wurde, alle Buchungen anzeigen
 $sql = "SELECT TicketID, ticket.Description, ticket.Notes, CreatedDate, DueDate, ClosedDate, ticket.PriorityID, priority.Description  as PriorityText, priority.SortOrder, ticket.StatusID, status.Description as StatusText, ticket.CustomerID, customer.Firma, customer.Zusatz FROM ticket inner join status on ticket.StatusID = status.StatusID inner join priority on ticket.PriorityID = priority.PriorityID inner join customer on ticket.CustomerID = customer.CustomerID where ticket.UserID = :userid ORDER BY CreatedDate DESC";      
 $stmt = $pdo->prepare($sql);
 $stmt->execute(['userid' => $userid]);   
 
$pdfName = "HelpDesk_Auszug_".$kassen_nummer.".pdf";
  
//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
 
// Erstellung des HTML-Codes. Dieser HTML-Code definiert das Aussehen eures PDFs.
// tcpdf unterstützt recht viele HTML-Befehle. Die Nutzung von CSS ist allerdings
// stark eingeschränkt.
 
$html = '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
	<tr>
	   <td>'.nl2br(trim($HelpDesk_header)).'</td>
	   <td style="text-align: right">			
			Datum: '.$HelpDesk_datum.'<br>			
		</td>
	</tr>
 
	<tr>
		 <td style="font-size:1.3em; font-weight: bold;">
<br><br>
HelpDesk - alle Tickets
<br>
		 </td>
	</tr>
 
 
	<tr>
		<td colspan="2">'.nl2br(trim($HelpDesk_empfaenger)).'</td>
	</tr>
</table>
<br><br><br>
 
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
    <tr style="background-color: #cccccc; padding:5px;">        
        <td style="text-align: left; width: 100px;"><b>Datum</b></td>
        <td style="text-align: left; width: 250px;"><b>Beschreibung</b></td>
        <td style="text-align: left; width: 125px;"><b>Bemerkung</b></td>
        <td style="text-align: left; width: 75px;"><b>Priorität</b></td>
        <td style="text-align: left; width: 75px;"><b>Status</b></td>
    </tr>';

$gesamtpreis = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Formatierung des Datums
    $formattedDate = (new DateTime($row['CreatedDate']))->format('d.m.Y');
        
    // Erstelle die Zeile mit Tabelleninhalten
    $html .= "
    <tr>
        <td style='vertical-align: top; width: 100px;'>{$formattedDate}</td>
        <td style='vertical-align: top; width: 250px;'>{$row['Description']}</td>
        <td style='vertical-align: top; width: 125px;'>{$row['Notes']}</td>        
        <td style='vertical-align: top; width: 75px;'>{$row['PriorityText']}</td>
        <td style='vertical-align: top; width: 75px;'>{$row['StatusText']}</td>
    </tr>";
}

$html .= "</table>";
 
$html .= '
<hr>
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0"><br><br><br><br>';

$html .= nl2br($HelpDesk_footer); 
 
//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
// TCPDF Library laden
if (!file_exists('tcpdf/tcpdf.php')) {
    die('Fehler: TCPDF-Bibliothek nicht gefunden.');
}
require_once('tcpdf/tcpdf.php');
 
// Erstellung des PDF Dokuments
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 
// Dokumenteninformationen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($pdfAuthor);
$pdf->SetTitle('HelpDesk '.$kassen_nummer);
$pdf->SetSubject('HelpDesk '.$kassen_nummer); 

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", "HelpDesk", array(0,0,0), array(0,0,0));
$pdf->setFooterData(array(0,0,0), array(0,0,0));
// Header und Footer Informationen
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
 
// Auswahl des Font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
// Auswahl der MArgins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
 
// Automatisches Autobreak der Seiten
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
 
// Image Scale 
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
 
// Schriftart
$pdf->SetFont('helvetica', '', 10);  // Legt die Schriftart für den Text fest
 
// Neue Seite
$pdf->AddPage();


// Fügt den HTML Code in das PDF Dokument ein
try {
    $pdf->writeHTML($html, true, true, true, false, align: '');
} catch (Exception $e) {
    die('Fehler bei der PDF-Erstellung: ' . $e->getMessage());
}
 
//Ausgabe der PDF
 
//Variante 1: PDF direkt an den Benutzer senden:
$pdf->Output($pdfName, 'I');
 
//PDF-Dokument per E-Mail versenden
$pdfPfad = dirname(__FILE__).'/'.$pdfName;
$pdf->Output($pdfPfad, 'F');
 
$dateien = array($pdfPfad);
//mail_att("g.wisst@web.de", "Betreff", "Euer Nachrichtentext", "Absendername", "g.wisst@web.de", "g.wisst@web.de", $dateien);

//Variante 2: PDF im Verzeichnis abspeichern:
// $pdf->Output(dirname(__FILE__).'\\PDF\\'.$pdfName, 'F');
// echo 'PDF herunterladen: <a href="\\PDF\\'.$pdfName.'">'.$pdfName.'</a>';
 
?>



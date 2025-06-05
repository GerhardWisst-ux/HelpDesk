<?php
require 'db.php';
session_start();

$userid = $_SESSION['userid'];

// TCPDF Library laden
if (!file_exists('tcpdf/tcpdf.php')) {
    die('Fehler: TCPDF-Bibliothek nicht gefunden.');
}
require_once('tcpdf/tcpdf.php');
if (!file_exists('Images/Ticket.png')) {
    echo 'Fehler: Logo-Datei nicht gefunden.';
}

$HelpDesk_header = '
<img style="max-height:40px;" src="Images/Ticket.png">
';
$Rechnungsdatum = date("d.m.Y");


$MST_ID = $_GET['MST_ID'];

$sql = "SELECT T1.MST_ID, T1.INV_NO, T1.CUSTOMER_NAME, T1.STREET, T1.ADDRESS FROM INVOICE_MST T1 WHERE T1.MST_ID='" . $MST_ID . "' ";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$inv_mst_data_row = $stmt->fetch(PDO::FETCH_ASSOC);

$HelpDesk_empfaenger = "Firma" . "\n" . $inv_mst_data_row['CUSTOMER_NAME'] . "\n"
    . $inv_mst_data_row['STREET'] . "\n\n"
    . $inv_mst_data_row['ADDRESS'];

$HelpDesk_empfaenger = "<b>" . htmlspecialchars($HelpDesk_empfaenger, ENT_QUOTES, 'UTF-8') . "</b>";

// $HelpDesk_footer = "Dieses PDF Dokument wurde mit dem HelpDesk erstellt:
$HelpDesk_footer = " 
";

$inv_det_query = "SELECT T2.PRODUCT_NAME, T2.NUMBER, T2.HOURS, T2.AMOUNT FROM INVOICE_DET T2 WHERE T2.MST_ID='" . $MST_ID . "' ";
$stmt = $pdo->prepare($inv_det_query);
$stmt->execute();

$html .= '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
	<tr>
	   <td>' . nl2br(trim($HelpDesk_header)) . '</td>
	   <td style="text-align: right">			
			Rechnungsdatum: ' . $Rechnungsdatum . '<br>			
		</td>
	</tr>
	<tr>
		<td colspan="2">' . nl2br(trim($HelpDesk_empfaenger)) . '</td>
	</tr>
 
	<tr>
		 <td style="font-size:1.3em; font-weight: bold;">
<br><br><b>RECHNUNG - ' . $inv_mst_data_row['INV_NO'] . '
</b><br>
		 </td>
	</tr>
	
</table>
<br><br><br>
 
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
    <tr style="background-color: #cccccc; padding:5px;">        
        <td style="text-align: left; width: 250px;"><b>Position</b></td>
        <td style="text-align: left; width: 150px;"><b>Stunden</b></td>
        <td style="text-align: left; width: 150px;"><b>pro Stunde</b></td>
        <td style="text-align: left; width: 125px;"><b>Betrag</b></td>        
    </tr>';

$gesamtpreis = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Formatierung des Datums

    $gesamtpreis = $gesamtpreis + $row['AMOUNT'];

    // Erstelle die Zeile mit Tabelleninhalten
    $html .= "
    <tr>        
        <td style='vertical-align: top; width: 250px;'>{$row['PRODUCT_NAME']}</td>
        <td style='vertical-align: top; width: 150px; text-align:right;'>" . number_format($row['HOURS'], 0, '.', ',') . "</td>
        <td style='vertical-align: top; width: 150px; text-align:right;'>" . number_format($row['NUMBER'], 2, '.', ',') . "</td>
        <td style='vertical-align: top; width: 125px; text-align:right;'>" . number_format($row['AMOUNT'], 2, '.', ',') . "</td>        
		
    </tr>";
    }

    $html .= "<tr>
        <td style='vertical-align: top; width: 250px;'>SUMME</td>
        <td style='vertical-align: top; width: 150px; text-align:right;'></td>
        <td style='vertical-align: top; width: 150px; text-align:right;'></td>
        <td style='vertical-align: top; width: 125px; text-align:right;'>" . number_format($gesamtpreis, 2, '.', ',') . "</td>
    </tr>
    <tr>
        <td style='vertical-align: top; width: 250px;'>MWST</td>
        <td style='vertical-align: top; width: 150px; text-align:right;'></td>
        <td style='vertical-align: top; width: 150px; text-align:right;'></td>
        <td style='vertical-align: top; width: 125px; text-align:right;'>" . number_format($gesamtpreis * 0.19, 2, '.', ',') . "</td>
    </tr>
    <tr>
        <td style='vertical-align: top; width: 250px;'><b>RECHNUNGSBETRAG</b></td>
        <td style='vertical-align: top; width: 150px; text-align:right;'></td>
        <td style='vertical-align: top; width: 150px; text-align:right;'></td>
        <td style='vertical-align: top; width: 125px; text-align:right;'><b>" . number_format($gesamtpreis * 1.19, 2, '.', ',') . "</b></td>
    </tr>
<tr><td colspan='3'>&nbsp;</td></tr>
<tr><td colspan='3'>&nbsp;</td></tr>
<tr><td colspan='3'><b>DER RECHNUNGSBETRAG IST SOFORT UND OHNE ABZUG FÄLLIG</b></td></tr>

<tr><td colspan='3' align='left'><b>DANKE FÜR DEN AUFTRAG. BIS ZUM NÄCHSTEN MAL!</b></td></tr>
";

$html .= "</table>";


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
$pdf->SetTitle('Rechnung ' . $inv_mst_data_row['INV_NO']);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", "EDV Beratung Wißt, Augsburger Str. 717, 70329 Stuttgart, Mobil:01520-8750327, Mail: g.wisst@web.de", array(0, 0, 0), array(0, 0, 0));
$pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));
// Header und Footer Informationen
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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

// $pdf->writeHTML($content);

$file_location = "/home/fbi1glfa0j7p/public_html/examples/generate_pdf/uploads/"; //add your full path of your server
//$file_location = "/opt/lampp/htdocs/examples/generate_pdf/uploads/"; //for local xampp server

$datetime = date('dmY_hms');
$file_name = $inv_mst_data_row['INV_NO'] . ".pdf";
ob_end_clean();

if ($_GET['ACTION'] == 'VIEW') {
    $pdf->Output($file_name, 'I'); // I means Inline view
} else if ($_GET['ACTION'] == 'DOWNLOAD') {
    $pdf->Output($file_name, 'D'); // D means download
} else if ($_GET['ACTION'] == 'UPLOAD') {
    $pdf->Output($file_location . $file_name, 'F'); // F means upload PDF file on some folder
    echo "Upload erfolgreich!!";
}

//----- End Code for generate pdf

?>
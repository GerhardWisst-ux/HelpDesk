<?php
require 'db.php';
session_start();

// TCPDF Library laden
if (!file_exists('tcpdf/tcpdf.php')) {
    die('Fehler: TCPDF-Bibliothek nicht gefunden.');
}
require_once('tcpdf/tcpdf.php');

if (!file_exists('Images/Ticket.png')) {
    echo 'Fehler: Logo-Datei nicht gefunden.';
}

$HelpDesk_header = '
<img src="Images/Ticket.png">
';

$MST_ID = $_GET['MST_ID'];

$sql = "SELECT T1.MST_ID, T1.INV_NO, T1.CUSTOMER_NAME, T1.STREET, T1.ADDRESS FROM INVOICE_MST T1 WHERE T1.MST_ID='" . $MST_ID . "' ";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$inv_mst_data_row = $stmt->fetch(PDO::FETCH_ASSOC);

//----- Code for generate pdf
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
//$pdf->SetTitle("Export HTML Table data to PDF using TCPDF in PHP");  
$pdf->SetHeaderData('', 1, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont('helvetica');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage(); //default A4
//$pdf->AddPage('P','A5'); //when you require custome page size 

$content = '';

$content .= '
	<style type="text/css">
	body{
	font-size:12px;
	line-height:24px;
	font-family:"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
	color:#000;
	}
	</style>    
	<table cellpadding="0" cellspacing="0" style="border:0px solid #ddc;width:100%;">
	<table style="width:100%;" >
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="center"><b>EDV Beratung Wißt</b></td></tr>
	<tr><td colspan="2" align="center"><b>MOBIL: +711 1612-2317</b></td></tr>
	<tr><td colspan="2" align="center"><b>WEBSITE: WWW.EDV-BERATUNG-WISST.DE</b></td></tr>
	<tr><td colspan="2"><b>KUNDE: ' . $inv_mst_data_row['CUSTOMER_NAME'] . ' </b></td></tr>
	<tr><td><b>ADRESSE: ' . $inv_mst_data_row['STREET'] . ' </b></td><td align="right"><b>Rechnungsdatum: ' . date("d.m.Y") . '</b> </td></tr>
	<tr><td>&nbsp;</td><td align="right"><b>Rechnungsnummer: ' . $inv_mst_data_row['INV_NO'] . '</b></td></tr>
	<tr><td colspan="2" align="center"><b>RECHNUNG</b></td></tr>
	<tr class="heading" style="background:#eee;border-bottom:0px solid #ddd;font-weight:bold;">
		<td>
			RECHNUNGSPOSITION
		</td>
		<td align="right">
			BETRAG
		</td>
	</tr>';
$total = 0;
$inv_det_query = "SELECT T2.PRODUCT_NAME, T2.AMOUNT FROM INVOICE_DET T2 WHERE T2.MST_ID='" . $MST_ID . "' ";
$stmt = $pdo->prepare($inv_det_query);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $content .= '
		  <tr class="itemrows">
			  <td>
				  <b>' . $row['PRODUCT_NAME'] . '</b>
				  <br>
				  <i>12 Stunden</i>
			  </td>
			  <td align="right"><b>
				  ' . number_format($row['AMOUNT'], 2, ',', '.') . " €" . '
			  </b></td>
		  </tr>';
    $total = $total + $row['AMOUNT'];
}
$content .= '<tr class="total"><td colspan="2" align="right">-------------------------------------------</td></tr>
		<tr><td colspan="2" align="right"><b>GESAMTBETRAG:&nbsp;' . number_format($total, 2, ',', '.') . " €" . '</b></td></tr>
		<tr><td colspan="2" align="right">-------------------------------------------</td></tr>
        <tr><td colspan="2" align="right">&nbsp;</td></tr>
	<tr><td colspan="2" align="left"><b>DER RECHNUNGSBETRAG IST SOFORT UND OHNE ABZUG FÄLLIG</b></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="left"><b>DANKE FÜR DEN AUFTRAG. BIS ZUM NÄCHSTEN MAL!</b></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	</table>
</table>';
$pdf->writeHTML($content);

$file_location = "/home/fbi1glfa0j7p/public_html/examples/generate_pdf/uploads/"; //add your full path of your server
//$file_location = "/opt/lampp/htdocs/examples/generate_pdf/uploads/"; //for local xampp server

$datetime = date('dmY_hms');
$file_name = "RE_" . $datetime . ".pdf";
ob_end_clean();

if ($_GET['ACTION'] == 'VIEW') {
    $pdf->Output($file_name, 'I'); // I means Inline view
} else if ($_GET['ACTION'] == 'DOWNLOAD') {
    $pdf->Output($file_name, 'D'); // D means download
} else if ($_GET['ACTION'] == 'UPLOAD') {
    $pdf->Output($file_location . $file_name, 'F'); // F means upload PDF file on some folder
    echo "Upload erfolgriech!!";
}

//----- End Code for generate pdf




?>
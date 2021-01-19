<?php
require_once('../../vendor/autoload.php');
// require_once ('/usr/share/php/Com/Tecnick/Pdf/Parser/autoload.php');

$pdfmodel = $_POST['pdfmodel'];
$filename = 'Export.pdf';

// echo(json_encode($pdfmodel));
$pdf = new \Com\Tecnick\Pdf\Tcpdf('mm', 'L', 'A4');
$pdf->SetCreator('xzdwq');
$pdf->SetAuthor('xzdwq');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->page->add();
$pdf->SetTitle('123');

// $pdf->page->SetBox([40, 40]);

// $pdf->Output('t.pdf','I');
$doc = $pdf->getOutPDFString();
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
header("Content-Length: ".filesize($filename)); 

var_export($doc);
// file_put_contents('example.pdf', $doc);
?>
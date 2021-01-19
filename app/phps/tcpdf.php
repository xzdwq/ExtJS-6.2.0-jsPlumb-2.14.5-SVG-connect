<?php
require_once('../../vendor/autoload.php');
// require_once ('/usr/share/php/Com/Tecnick/Pdf/Parser/autoload.php');

$pdfmodel = $_POST['pdfmodel'];

// echo(json_encode($pdfmodel));
$pdf = new \Com\Tecnick\Pdf\Tcpdf();
$pdf->page->add();
$doc = $pdf->getOutPDFString();

// var_export($doc);
file_put_contents('example.pdf', $doc);
?>
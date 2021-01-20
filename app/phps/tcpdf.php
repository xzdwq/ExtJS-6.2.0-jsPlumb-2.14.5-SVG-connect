<?php
require_once('../../vendor/tecnick.com/tcpdf/tcpdf.php');
require('svg.php');
// require_once ('/usr/share/php/Com/Tecnick/Pdf/Parser/autoload.php');

$pdfmodel = $_POST['pdfmodel'];
$filename = 'Export.pdf';
$svg = getSvg(); //Если в SVG есть русский текст и он отображается как ???, то в стиле тэга <text изменить font-family на тот который есть в TCPDF (т.е. font-family:sans-serif на font-family:arial)

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false, false);
// $fontname = TCPDF_FONTS::addTTFfont('../../fonts/sansserif.ttf', 'TrueTypeUnicode', '', 96);
// $pdf->SetFont($fontname, '', 14, '', false);
$pdf->SetTitle('Специфицированная ведомость № '.$cd45.' на '.$god);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setFontSubsetting(true);
$pdf->AddPage();
$pdf->SetFont('arial','',8);
$pdf->SetY(110);
$pdf->Cell(190,7,'Специфицированная ведомость № '.$cd45.' на '.$god.' год',0,0,'C');
$pdf->Ln();
$pdf->ImageSVG('@'.$svg, $x=15, $y=30, $w='80', $h='80', $link='', $align='', $palign='', $border=1, $fitonpage=false);
$pdf->Output($filename.'.pdf','I');

?>
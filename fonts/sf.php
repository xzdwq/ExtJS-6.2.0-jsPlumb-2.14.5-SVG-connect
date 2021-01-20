<?php

// подключаем библиотеку tcpdf
require( 'tcpdf/tcpdf.php' );
// подключаем файл с описанием функций получения данных и т.п.
require 'include/init.php';

function FillString($pdf, $c, $laststring=false){ // формирует строку табличной части
    // ширина столбцов
    $w = array(68,7,22,15,21,29,13,11,16,28,15,19,22);
    $align = ($c[0] == "1") ? // если в параметрах номера колонок, используем альтернативное выравнивание
    array("C","C","C","C","C","C","C","C","C","C","C","C","C") :
    array("L","C","C","R","R","R","L","C","R","R","L","L","L");
	// режим "перевода каретки" - все, кроме последнего - вправо
    $lnmode=array(0,0,0,0,0,0,0,0,0,0,0,0,1);
    $lastch = 0;
	// вычисляем высоту строки по самой высокой ячейке
	foreach ($c as $key=>$value) {
        $lastch= ($lastch > $pdf->GetStringHeight($w[$key],$c[$key])) ? $lastch : $pdf->GetStringHeight($w[$key],$c[$key]);
    }
	// если текущая строка не поместится на лист, создаём новый лист и рисуем шапку табличной части
    if (($lastch + $pdf->getY()) > ($pdf->getPageHeight() - $pdf->getBreakMargin())) {
        $pdf->startPage();
		//FillTableHead($pdf); // я не вывожу названия столбцов ТЧ на втором и следующих листах, но можно
        FillString($pdf,array(1,2,'2a',3,4,5,6,7,8,9,10,'10a',11),false);
    }
	// если строка последняя и она не поместится на листе с "подвалом", создаём новый лист...
    if (($laststring) and (($pdf->getY()+$lastch+55) > ($pdf->getPageHeight() - $pdf->getBreakMargin()))) {
        $pdf->startPage();
		//FillTableHead($pdf);
        FillString($pdf,array(1,2,'2a',3,4,5,6,7,8,9,10,'10a',11),false);
    }
    foreach ($c as $key=>$value) {
        $pdf->MultiCell($w[$key], $lastch,
            $value,
            'LRTB', $align[$key], false,  $lnmode[$key],
             $pdf->getX(), $pdf->getY(), true, 0,
             false, true, 0,  'T',
             true);
        $lastch= ($lastch>$pdf->getLastH()) ? $lastch : $pdf->getLastH();
    }
}
function FillTotal($pdf, $c){
    $w = array(68+7+22+15+21,29,13+11,16,28);
    $align = array("L","R","C","R","R");
    foreach ($c as $key=>$value) {
        $pdf->MultiCell($w[$key], 0,
            $value,
            'LRTB', $align[$key], false,  0,
             $pdf->getX(), $pdf->getY(), true, 0,
             false, true, 0,  'T',
             true);
    }
    $pdf->Ln();
}
function FillTableHead($pdf){
    $pdf->MultiCell(68, 18,
         "Наименование товара (описание выполненных работ, оказанных услуг), имущественного права",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $nextY=$pdf->getY();
    $thisX=$pdf->getX();
    $pdf->MultiCell(29, 7,
         "Единица измерения",
         'LRTB', 'C', false,  2,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $nextX=$pdf->getX();
    $pdf->MultiCell(7, 11,
         "код",
         'LRTB', 'C', false,  0,
          $thisX, $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(22, 11,
         "условное обозначение (национальное)",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(15, 18,
         "Коли-\nчество (объём)",
         'LRTB', 'C', false,  0,
          $nextX, $nextY, true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(21, 18,
         "Цена (тариф) за единицу измерения",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(29, 18,
         "Стоимость товаров (работ, услуг), имущественных прав, всего без налога",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(13, 18,
         "В том  числе  сумма  акциза",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(11, 18,
         "Нало-\nговая ставка",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(16, 18,
         "Сумма налога",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(28, 18,
         "Стоимость товаров (работ, услуг), имущественных прав, всего с учетом налога",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $nextY=$pdf->getY();
    $thisX=$pdf->getX();
    $pdf->MultiCell(34, 7,
         "Страна происхождения товара",
         'LRTB', 'C', false,  2,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $nextX=$pdf->getX();
    $pdf->MultiCell(15, 11,
         "цифро-\nвой код",
         'LRTB', 'C', false,  0,
          $thisX, $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(19, 11,
         "краткое наи-\nменование",
         'LRTB', 'C', false,  0,
          $pdf->getX(), $pdf->getY(), true, 0,
          false, true, 0,  'M',
          true);
    $pdf->MultiCell(22, 18,
         "Номер таможенной декларации",
         'LRTB', 'C', false,  1,
          $nextX, $nextY, true, 0,
          false, true, 0,  'M',
          true);
    FillString($pdf,array(1,2,'2a',3,4,5,6,7,8,9,10,'10a',11),false);    
}

if (filter_has_var(INPUT_GET,'advice')) {
	// получить реквизиты счёта-фактуры из внешней базы
    $rekv=GetSFParams(filter_input(INPUT_GET,'advice',FILTER_SANITIZE_STRING));
	// разбор массива полученных реквизитов по переменным; не обязательно, но так проще читать/править форму
    $sfn = $rekv[0];
    $sfd = $rekv[1];
    $dealname = $rekv[2];
    $dealaddr = $rekv[3];
    $dealinn = $rekv[4];
    $dealkpp = $rekv[5];
    $senderaddr = $rekv[6];
    $recieveraddr = $rekv[7];
    $advnum = $rekv[8];
    $buyername = $rekv[9];
    $buyeraddr = $rekv[10];
    $buyerinn = $rekv[11];
    $buyerkpp = $rekv[12];
    $currency = $rekv[13];
    $tch = array();
	// в 14-м элементе массива параметров ожидается массив, содержащий номенклатуру
    if (is_array($rekv[14])) {
        foreach ($rekv[14] as $index => $value) {
            $tch[$index] = $value;
        }
     } else {
        die("Указанная реализация не содержит номенклатуры.");
    }
    $totalprice=$rekv[15];
    $totalnds=$rekv[16];
    $bigtotalprice=$rekv[17];
    $director=$rekv[18];
    $glavbuh=$rekv[19];
    // не используемые у нас реквизиты
    $corrnum = "--";
    $corrdate = "--";
    $pboul=" ";
    $pboulrekv=" ";
} else {
    die('Не указан код документа!');
}
// Portrain/Landscape, mm, A4, unicode, UTF-8, diskcache, pdfa
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false, false);
// отключаем вывод стандартных заголовков
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// поля документа (левое, верхнее, правое)
$pdf->SetMargins(6, 10, 10);
// русские шрифты в стандартную поставку не входят - используем сгенерированные самостоятельно
$pdf->SetFont('arial','',8);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,10);
//начинаем вывод данных
$pdf->MultiCell(50, 15,
 "Приложение №1\n"
."к постановлению Правительства\n"
."Российской Федерации\n"
."от 26.12.2011 № 1137", 0, 'L', 0, 1, $pdf->getPageWidth() - 50);
$pdf->SetFont('arialbd','',14);
$pdf->Write(7,"Счет-фактура № {$sfn} от {$sfd} г.");
$pdf->Ln();
$pdf->Write(7,"Исправление № {$corrnum} от {$corrdate}");
$pdf->SetFont('arial','',8);
$pdf->Ln();
$pdf->Write(3.89,"Продавец: {$dealname}");
$pdf->Ln();
$pdf->Write(3.89,"Адрес: {$dealaddr}");
$pdf->Ln();
$pdf->Write(3.89,"ИНН/КПП продавца: {$dealinn}/{$dealkpp}");
$pdf->Ln();
$pdf->Write(3.89,"Грузоотправитель и его адрес: {$senderaddr}");
$pdf->Ln();
$pdf->Write(3.89,"Грузополучатель и его адрес: {$recieveraddr}");
$pdf->Ln();
$pdf->Write(3.89,"К платежно-расчетному документу № {$advnum}");
$pdf->Ln();
$pdf->Write(3.89,"Покупатель: {$buyername}");
$pdf->Ln();
$pdf->Write(3.89,"Адрес: {$buyeraddr}");
$pdf->Ln();
$pdf->Write(3.89,"ИНН/КПП покупателя: {$buyerinn}/{$buyerkpp}");
$pdf->Ln();
$pdf->Write(3.89,"Валюта (наименование, код): {$currency}");
//$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
FillTableHead($pdf);
$lines=count($tch) - 1;
foreach ($tch as $key=>$cstring) {
    FillString($pdf,$cstring,$lines==$key);
//    $totalprice.=$cstring[9];
}
FillTotal($pdf, array(
    "Всего к оплате",
    $totalprice,
    "X",
    $totalnds,
    $bigtotalprice),false);
$pdf->Ln();
$pdf->MultiCell(54, 8,
     "Руководитель организации\n"
	 ."или иное уполномоченное лицо",
     /*borders*/'', /*align*/'L', /*fill*/false, /*ln(0-R,1-CRLF,2-D*/ 0,
     /*X*/ $pdf->getX(), /*Y*/ $pdf->getY(), /*resetLastHeight*/true, /*stratch*/0,
     /*isHTML*/ false, /*autopadding*/ true, /*maxh*/0, /*valign*/ 'B',
     /*fitcell*/ true);
$pdf->MultiCell(30, 8,
     " ",
     'B', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(2, 8,
     " ",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(43, 8,
     "{$director}",
     'B', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(7, 8,
     " ",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(54, 8,
     "Главный бухгалтер\n"
	 ."или иное уполномоченное лицо",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(30, 8,
     " ",
     'B', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(2, 8,
     " ",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(43, 8,
     "{$glavbuh}",
     'B', 'L', false,  1,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->SetFont('arial','',6);
$pdf->MultiCell(54, 8,
     " ",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(30, 8,
     "(подпись)",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(2, 8,
     " ",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(43, 8,
     "(ф.и.о.)",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(7, 8,
     " ",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(54, 8,
     " ",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(30, 8,
     "(подпись)",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(2, 8,
     " ",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(43, 8,
     "(ф.и.о.)",
     '', 'C', false,  1,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->SetFont('arial','',8);
//$pdf->Ln();
$pdf->MultiCell(54, 8,
     "Индивидуальный предприниматель",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(30, 8,
     " ",
     'B', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(2, 8,
     " ",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(43, 8,
     "{$pboul}",
     'B', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(15, 8,
     " ",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->MultiCell(105, 8,
     "{$pboulrekv}",
     'B', 'L', false,  1,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'B',
      true);
$pdf->SetFont('arial','',6);
$pdf->MultiCell(54, 8,
     " ",
     '', 'L', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(30, 8,
     "(подпись)",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(2, 8,
     " ",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(43, 8,
     "(ф.и.о.)",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(15, 8,
     " ",
     '', 'C', false,  0,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->MultiCell(105, 8,
     "(реквизиты свидетельства о государственной\nрегистрации индивидуального предпринимателя)",
     '', 'C', false,  1,
      $pdf->getX(), $pdf->getY(), true, 0,
      false, true, 0,  'T',
      true);
$pdf->Write(3.89,"Примечание: Первый экземпляр - покупателю, второй экземпляр - продавцу.");
$pagestotal=$pdf->getNumPages();
$headerX=10;
$headerY=6;
$footerX=$pdf->getPageWidth() - 45;
$footerY=$pdf->getPageHeight() - 10;
$pdf->SetFont('arial','',8);
for ($index = 1; $index <= $pagestotal; $index++) {
    $pdf->setPage($index);
    $pdf->SetAutoPageBreak(false);
    if ($index<>1) {$pdf->MultiCell(/*W*/30, /*H*/0,
         "Лист {$index}",
         '', 'L', false,  0,
          $headerX, $headerY, true, 0,
          false, false, 0,  'T',
          true);}
    $pdf->MultiCell(30, 0,
         "Страница {$index} из {$pagestotal}",
         '', 'L', false,  0,
          $footerX, $footerY, true, 0,
          false, false, 0,  'T',
          true);
}
$pdf->Output( "sf.pdf", "I");
#$pdf->render();
?>
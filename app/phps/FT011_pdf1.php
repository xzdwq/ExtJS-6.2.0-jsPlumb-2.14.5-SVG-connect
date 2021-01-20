<?php
		#	Файл формирует печать в режиме "Формирование спецификаций на стандартные изделия"
		#	Переменные: $god, $zakazchik (X451), $dogovor (MM56), $specifikaciya (CR03)
		#$nds (nds_sort), $dol (dolzhnist_sort)
		#	переданы сюда методом GET в адресной строке из init.js
require_once	('elah2.php');
require				('f_is_db.php');
require_once	('/usr/share/php/phppdf/tcpdf/tcpdf.php');
$dt				=		date('d').".".date('m').".".date('Y');
$tm				=		date("H:i:s",time());
$bd				=		is_db();
$c				=		Connect($bd);
		#	Запрос получения ФИО исполнителя (для последней страницы)
$sql_p704 = "SELECT FT011.get_fio(FT011.get_user()) AS P710 FROM dual";

	$sql_p704_res = OCIParse($c, $sql_p704);
	OCIExecute($sql_p704_res);

	while(OCIFetch($sql_p704_res))
		{
			$p710		=		OCIResult($sql_p704_res,"P710");						//ФИО исполнителя
			$p710		=		iconv('KOI8-R', 'UTF-8', $p710);						//Меняем кодировку для TCPDF
		}

		#	Запрос формирования номера спецификации по его идентификатору
$sql_nom_spec = "SELECT CD45 FROM FT011.OMC60N02 WHERE CR03 = $specifikaciya";

	$sql_nom_spec_res = ociparse($c, $sql_nom_spec);
	ociexecute($sql_nom_spec_res);

	while(OCIFetch($sql_nom_spec_res))
		{
			$cd45		=		OCIResult($sql_nom_spec_res,"CD45");				//Номер спецификации
			$cd45		=		iconv('KOI8-R', 'UTF-8', $cd45);						//Меняем кодировку для TCPDF
		}

		#	Запрос формирования номера договора по его идентификатору
$sql_naim_dogovor = "SELECT CD44 FROM FT011.OMC60N01 WHERE MM56=$dogovor";

	$sql_naim_dogovor_res = OCIParse($c, $sql_naim_dogovor);
	OCIExecute($sql_naim_dogovor_res);

	while(OCIFetch($sql_naim_dogovor_res))
		{
			$cd44		=		OCIResult($sql_naim_dogovor_res,"CD44");		//Номер договора
			$cd44		=		iconv('KOI8-R', 'UTF-8', $cd44);						//Меняем кодировку для TCPDF
		}

		#	Запрос формирования наименования заказчика, индекса и юр.адреса
$sql_naim_zakazchik = "SELECT X481, X48001, X47001 FROM FT011.AK101N01 WHERE X451=$zakazchik";

	$sql_naim_zakazchik_res = OCIParse($c, $sql_naim_zakazchik);
	OCIExecute($sql_naim_zakazchik_res);

	while(OCIFetch($sql_naim_zakazchik_res))
		{
			$x481		=		OCIResult($sql_naim_zakazchik_res,"X481");		//Наименование заказчика
			$x481		=		iconv('KOI8-R', 'UTF-8', $x481);							//Меняем кодировку для TCPDF
			$x48001	=		OCIResult($sql_naim_zakazchik_res,"X48001");	//Индекс
			$x47001	=		OCIResult($sql_naim_zakazchik_res,"X47001");	//Юр. адрес
			$x47001	=		iconv('KOI8-R', 'UTF-8', $x47001);						//Меняем кодировку для TCPDF
		}

class MYPDF extends TCPDF {
  public function Header() {
  	global $dt;
  	global $tm;
  	global $cd45;
  	global $god;
  	global $cd44;
  	global $zakazchik;
  	global $x481;
  	$left_col='Специфицированная ведомость № '.$cd45.' на '.$god.' год '.'к договору № '.$cd44."\n".'Заказчик: '.$zakazchik.' '.$x481."\n";
  	if($this->PageNo() == 1){
		$this->SetFont('freeserif','',9);
		$this->Cell(140,'','',0,0);
		$this->Cell(50,3,'Экземпляр № 1',0,0,'R');}
		if($this->PageNo() == 2){
		$this->SetFont('freeserif','',9);
		$this->Cell(140,'','',0,0);
		$this->Cell(50,3,'Экземпляр № 1',0,0,'R');
		$this->Ln();
		$this->Cell(140,'','',0,0);
		$this->Cell(50,3,'FT011C04',0,0,'R');
		$this->Ln();
		$this->Cell(160,'','',0,0);
		$this->Cell(50,3,'Лист '.$this->getAliasNumPage().' / листов '.$this->getAliasNbPages(),0,0,'R');
		$this->Ln();
		$this->Cell(140,'','',0,0);
		$this->Cell(50,3,'Дата: '.$dt.' Время: '.$tm,0,0,'R');
		$this->Ln(5);}

		if($this->PageNo() > 2){
			$this->SetFont('freeserif','',9);
			$this->MultiCell(140,0,$left_col,0,'L',0,0,'','',true,0,false,true,0);
			$this->SetFont('freeserif','',9);
			$this->Cell(50,3,'Экземпляр № 1',0,0,'R');
			$this->Ln();
			$this->Cell(140,'','',0,0);
			$this->Cell(50,3,'FT011C04',0,0,'R');
			$this->Ln();
			$this->Cell(160,'','',0,0);
			$this->Cell(50,3,'Лист '.$this->getAliasNumPage().' / листов '.$this->getAliasNbPages(),0,0,'R');
			$this->Ln();
			$this->Cell(140,'','',0,0);
			$this->Cell(50,3,'Дата: '.$dt.' Время: '.$tm,0,0,'R');
			$this->Ln(5);

    $this->SetFont('freeserif','B',9);
    $this->SetY(26);
    $this->Cell(45,8,'Обозначение ДСЕ',1,0,'C');
		$this->Cell(45,8,'Код ДСЕ',1,0,'C');
		$this->Cell(80,8,'Потребность в штуках и сроки поставки',1,0,'C');
		$this->MultiCell(20,4,'Оптовая цена (шт.)',1,'C');
		$this->Ln(0);
		$this->Cell(90,8,'Наименование ДСЕ',1,0,'C');
		$this->Cell(16,8,'1 квартал',1,0,'C');
		$this->Cell(16,8,'2 квартал',1,0,'C');
		$this->Cell(16,8,'3 квартал',1,0,'C');
		$this->Cell(16,8,'4 квартал',1,0,'C');
		$this->Cell(16,8,'Год',1,0,'C');
		$this->Cell(20,8,'Сумма (руб.)',1,0,'C');
		$this->Ln();}
  }
}

$pdf = new MYPDF('P','mm','A4',true,'UTF-8',false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($p710);
$pdf->SetTitle('Специфицированная ведомость № '.$cd45.' на '.$god);
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setFontSubsetting(true);

$pdf->AddPage();
$pdf->SetFont('freeserif','B',14);
$pdf->SetY(110);
$pdf->Cell(190,7,'Специфицированная ведомость № '.$cd45.' на '.$god.' год',0,0,'C');
$pdf->Ln();
$pdf->Cell(190,7,'к договору № '.$cd44,0,0,'C');
$pdf->Ln();
$pdf->Cell(190,7,'Заказчик '.$zakazchik,0,0,'C');
$pdf->Ln();
$pdf->MultiCell(190,7,$x481,0,'C',0,0,'','',true);
$pdf->Ln();
$pdf->MultiCell(190,5,$x48001.' '.$x47001,0,'C',0,0,'','',true);

$pdf->AddPage();
$pdf->SetFont('freeserif','B',14);
$pdf->SetY(40);
$pdf->MultiCell(190,7,'Общие указания к номенклатуре стандартизованных изделий (СИ)'."\n".'изготовляемые на АО "ФНПЦ "ПО "СТАРТ" им. М.В. Проценко"',0,'C');
$pdf->Ln();
$pdf->SetFont('freeserif','',14);
$pdf->Cell(5,5,'1. ',0,0,'L');
$pdf->MultiCell(185,7,'Оптовые цены на изделия установлены франко-вагон станция отправления без стоимости тары'."\n",0,'J');
$pdf->Ln(5);
$pdf->Cell(5,5,'2. ',0,0,'L');
$pdf->MultiCell(185,7,'Минимальная партия заказа - 1000 штук'."\n",0,'J');
$pdf->Ln(5);
$pdf->Cell(5,5,'3. ',0,0,'L');
$pdf->MultiCell(185,7,'В случае заявления партий менее 1000 штук (до 500 штук) к оптовой цене применяется коэффициент 1.4; при заказе менее 500 штук применяется коэффициент 1.5'."\n",0,'J');
$pdf->Ln(5);
$pdf->Cell(5,5,'4. ',0,0,'L');
$pdf->MultiCell(185,7,'Детали из латуни и меди поставляются без покрытия'."\n",0,'J');
$pdf->Ln(5);
$pdf->Cell(5,5,'5. ',0,0,'L');
$pdf->MultiCell(185,7,'Детали по ОСТ 95 1443-73 поставляются без покрытия'."\n",0,'J');
$pdf->Ln(5);
$pdf->Cell(5,5,'6. ',0,0,'L');
$pdf->MultiCell(185,7,'При изготовлении деталей, предусмотренных номенклатурой на стандартные изделия, применяется выборочный контроль.'."\n".'При требовании заказчиком 100% контроля качества к оптовой цене применяется коэффициент 1.3'."\n",0,'J');
$pdf->Ln(5);
$pdf->Cell(5,5,'7. ',0,0,'L');
$pdf->MultiCell(185,7,'При изменении цен на сырье, материалы, энергоресурсы и индексации минимальной заработной платы к оптовой цене будет применяться коэффициент удорожания цен'."\n",0,'J');

$pdf->AddPage();
$pdf->SetY(42);
$pdf->SetMargins(PDF_MARGIN_LEFT, 42, PDF_MARGIN_RIGHT);
$sql_tabl = "SELECT P003, FT011.FF_CD70(P003) AS CD70, FT011.OB_NAIM_P003(P003) AS OB_NAIM_P003,  sum(k1) AS F_K1_CR03, sum(k2) AS F_K2_CR03, sum(k3) AS F_K3_CR03, sum(k4) AS F_K4_CR03, H700 FROM
	(
	SELECT P003, FT011.F_K1_CR03(a.cr03,b.P003) AS k1,0 AS k2,0 AS k3,0 AS k4, H700 FROM FT011.FF320E03 a, FT011.OMC60N03 b WHERE a.cr03=b.cr03 and  b.cr03=$specifikaciya
	union
	SELECT P003, 0 AS k1, FT011.F_K2_CR03(a.cr03,b.P003) AS k2,0 AS k3,0 AS k4, H700 FROM FT011.FF320E03 a,FT011.OMC60N03 b WHERE  a.cr03=b.cr03 and b.cr03=$specifikaciya
	union
	SELECT P003, 0 AS k1, 0 AS k2, FT011.F_K3_CR03(a.cr03,b.P003) AS k3,0 AS k4, H700 FROM FT011.FF320E03 a,FT011.OMC60N03 b WHERE  a.cr03=b.cr03 and b.cr03=$specifikaciya
	union
	SELECT P003, 0 AS k1, 0 AS k2,0 AS k3, FT011.F_K4_CR03(a.cr03,b.P003) AS k4, H700 FROM FT011.FF320E03 a, FT011.OMC60N03 b WHERE  a.cr03=b.cr03 and b.cr03=$specifikaciya
	)group by P003, H700 order by OB_NAIM_P003 ASC";

	$sql_tabl_res = OCIParse($c, $sql_tabl);
	OCIExecute($sql_tabl_res);

	$summa_array = array();
	$k=0;
	$str_tmp='';
	while(OCIFetch($sql_tabl_res))
		{
			$k++;
		  $p003						=			OCIResult($sql_tabl_res,"P003");					//Код ДСЕ
		  $cd70						=			OCIResult($sql_tabl_res,"CD70");
		  $ob_naim				=			OCIResult($sql_tabl_res,"OB_NAIM_P003");	//Обозначение и наименование ДСЕ
		  $ob_naim				=			iconv('KOI8-R', 'UTF-8', $ob_naim);//Меняем кодировку для TCPDF
		  $f_k1_cr03			=			OCIResult($sql_tabl_res,"F_K1_CR03");			//1 квартал
		  $f_k2_cr03			=			OCIResult($sql_tabl_res,"F_K2_CR03");			//2 квартал
		  $f_k3_cr03			=			OCIResult($sql_tabl_res,"F_K3_CR03");			//3 квартал
		  $f_k4_cr03			=			OCIResult($sql_tabl_res,"F_K4_CR03");			//4 квартал
		  $itogo					=			$f_k1_cr03 + $f_k2_cr03 + $f_k3_cr03 + $f_k4_cr03;		//Итого
		  $h700						=			OCIResult($sql_tabl_res,"H700");					//Цена
		  $summa					=			$h700 * $itogo;														//Сумма
		  $h700_float			= 		floatval($h700);													//Для отображения нуля перед запятой

		  #Разбиение результата функции OB_NAIM_P003 на два элемента:
		  #Обозначение ДСЕ и Наименование ДСЕ
		  $ob_naim_0			=			split('!',$ob_naim);
		  $ob_naim_1			=			$ob_naim_0[0];		//Обозначение ДСЕ
		  $ob_naim_2			=			$ob_naim_0[1];		//Наименование ДСЕ
			$ob_naim_1			=			trim($ob_naim_1);	//Избавляемся от лишних пробелов
			$ob_naim_2			=			trim($ob_naim_2);	//Избавляемся от лишних пробелов
		 array_push($summa_array, $summa);

		 if($str_tmp!=$ob_naim_1){
		 $pdf->Bookmark($ob_naim_1,0,0,'','B',array(0,0,0));
		 $str_tmp=$ob_naim_1;}
		 $pdf->SetFont('freeserif','',10);

		 if($pdf->GetStringWidth($ob_naim_1) > 38) {
				$pdf->SetFont('freeserif','',7.9);
				$pdf->Cell(45,5,$ob_naim_1,1,0,'C');
				$pdf->SetFont('freeserif','',10);

				if($cd70 == ''){
					$pdf->Cell(45,5,$p003,1,0,'C');
				}
				else {
				$pdf->Cell(45,5,$p003.' / '.$cd70,1,0,'C');
				}

				$pdf->Cell(16,5,$f_k1_cr03,'TR',0,'C');
			  $pdf->Cell(16,5,$f_k2_cr03,'TR',0,'C');
			  $pdf->Cell(16,5,$f_k3_cr03,'TR',0,'C');
			  $pdf->Cell(16,5,$f_k4_cr03,'TR',0,'C');
			  $pdf->Cell(16,5,$itogo,'TR',0,'C');
			  $pdf->Cell(20,5,$h700_float,'TBR',0,'C');
				$pdf->Ln();

					if($pdf->GetStringWidth($ob_naim_2) > 60){
					$pdf->SetFont('freeserif','',9);
					$pdf->Cell(90,5,'    '.$ob_naim_2,1,0,'L');}
					else {
					$pdf->SetFont('freeserif','',10);
					$pdf->Cell(90,5,'    '.$ob_naim_2,1,0,'L');
					}

				$pdf->SetFont('freeserif','',10);
			  $pdf->Cell(16,5,'','BR',0,'C');
				$pdf->Cell(16,5,'','BR',0,'C');
				$pdf->Cell(16,5,'','BR',0,'C');
				$pdf->Cell(16,5,'','BR',0,'C');
				$pdf->Cell(16,5,'','BR',0,'C');
				$pdf->Cell(20,5,$summa,'BR',0,'C');
			  $pdf->Ln();
			} else {
			$pdf->Cell(45,5,'    '.$ob_naim_1,1,0,'L');
			$pdf->SetFont('freeserif','',10);

			if($cd70 == ''){
					$pdf->Cell(45,5,$p003,1,0,'C');
				}
				else {
				$pdf->Cell(45,5,$p003.' / '.$cd70,1,0,'C');
				}

			$pdf->Cell(16,5,$f_k1_cr03,'TR',0,'C');
		  $pdf->Cell(16,5,$f_k2_cr03,'TR',0,'C');
		  $pdf->Cell(16,5,$f_k3_cr03,'TR',0,'C');
		  $pdf->Cell(16,5,$f_k4_cr03,'TR',0,'C');
		  $pdf->Cell(16,5,$itogo,'TR',0,'C');
		  $pdf->Cell(20,5,$h700_float,'TBR',0,'C');
			$pdf->Ln();

				if($pdf->GetStringWidth($ob_naim_2) > 60){
				$pdf->SetFont('freeserif','',9);
				$pdf->Cell(90,5,'    '.$ob_naim_2,1,0,'L');}
				else {
				$pdf->SetFont('freeserif','',10);
				$pdf->Cell(90,5,'    '.$ob_naim_2,1,0,'L');
				}

			$pdf->SetFont('freeserif','',10);
		  $pdf->Cell(16,5,'','BR',0,'C');
			$pdf->Cell(16,5,'','BR',0,'C');
			$pdf->Cell(16,5,'','BR',0,'C');
			$pdf->Cell(16,5,'','BR',0,'C');
			$pdf->Cell(16,5,'','BR',0,'C');
			$pdf->Cell(20,5,$summa,'BR',0,'C');
		  $pdf->Ln();}

		}
$summa_array_1	=		array_sum($summa_array);
$nds_2					=		$nds/100;
$summa_nds_sost	=		round($summa_array_1*$nds_2,2);
$itogo_s_nds		=		$summa_nds_sost + $summa_array_1;
$dol						=		str_replace('quot','"',$dol);
$fio						=		iconv('KOI8-R', 'UTF-8', $fio);
$dol						=		iconv('KOI8-R', 'UTF-8', $dol);
$dol						=		stripslashes($dol);
$pdf->SetFont('freeserif','B',10);
$pdf->Cell(170,8,'Итого:  ',0,0,'R');
$pdf->Cell(20,8,$summa_array_1,0,0,'C');
$pdf->Ln();
$pdf->Cell(170,8,'Итого с учётом НДС:  ',0,0,'R');
$pdf->Cell(20,8,$itogo_s_nds,0,0,'C');
$pdf->Ln();
$pdf->Cell(170,8,'НДС составляет:  ',0,0,'R');
$pdf->Cell(20,8,$summa_nds_sost,0,0,'C');
$pdf->Ln(10);

$pdf->SetFont('freeserif','',13);
$pdf->Cell(105,6,'Утверждаю:',0,0,'L');
$pdf->Cell(85,6,'Утверждаю:',0,0,'L');
$pdf->Ln();
$pdf->Cell(105,5,'Директор по производству',0,0,'L');
if($dol == ''){
	$pdf->Cell(85,5,'','B',0,'L');
	$pdf->Ln();
	$pdf->Cell(105,5,'АО "ФНПЦ "ПО "Старт" им. М.В.Проценко"',0,0,'L');
	$pdf->Cell(85,5,'','B',0,'L');
} else {if($pdf->GetStringWidth($dol) > 85) {
	$pdf->MultiCell(85,5,$dol,0,'L');
	$pdf->Ln(0);
	$yPos=$pdf->GetY();
	$pdf->SetY($yPos - 5);
	$pdf->Cell(105,5,'АО "ФНПЦ "ПО "Старт" им. М.В.Проценко"',0,0,'L');
	$pdf->Cell(85,5,'',0,0,'L');
}else {
$pdf->Cell(85,5,$dol,0,0,'L');
$pdf->Ln();
$pdf->Cell(105,5,'АО "ФНПЦ "ПО "Старт" им. М.В.Проценко"',0,0,'L');
$pdf->Cell(85,5,'',0,0,'L');
}}
$pdf->Ln();
if($fio == ''){
$pdf->Cell(85,6,'                                                   ','B',0,'L');
$pdf->Cell(20,6,'',0,0,'R');
$pdf->Cell(85,6,'                                               ','B',0,'R');
} else {
$pdf->Cell(85,6,'                                                   ','B',0,'L');
$pdf->Cell(20,6,'',0,0,'R');
$pdf->Cell(85,6,'       '.$fio.'     ','B',0,'R');
}
$pdf->SetFont('freeserif','',12);

$pdf->SetY(250);
$pdf->Cell(70,4,'',0,0,'L');
$pdf->Ln();
$pdf->Cell(70,4,'Исполнено в 1 экземпляре на '.$pdf->getAliasNbPages().' листах каждый',0,0,'L');
$pdf->Ln();
$pdf->Cell(70,4,'Исполнитель: '.$p710.'_______________',0,0,'L');
$pdf->Ln();
$pdf->Cell(70,4,'Дата: '.$dt.' Время: '.$tm,0,0,'L');

$pdf->setPrintHeader(false);
$pdf->addTOCPage();
$pdf->SetY(5);
$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);

$pdf->SetFont('freeserif','',9);
$pdf->Cell(140,'','',0,0);
$pdf->Cell(50,3,'Экземпляр № 1',0,0,'R');
$pdf->Ln();
$pdf->Cell(140,'','',0,0);
$pdf->Cell(50,3,'FT011C04',0,0,'R');
$pdf->Ln();
$pdf->Cell(160,'','',0,0);
$pdf->Cell(50,3,'Лист '.$pdf->getAliasNumPage().' / листов '.$pdf->getAliasNbPages(),0,0,'R');
$pdf->Ln();
$pdf->Cell(140,'','',0,0);
$pdf->Cell(50,3,'Дата: '.$dt.' Время: '.$tm,0,0,'R');
$pdf->Ln(5);
$pdf->SetFont('freeserif','B',14);
$pdf->SetY(25);
$pdf->MultiCell(0,0,'Оглавление',0,'C',0,1,'','',true,0);
$pdf->Ln();
$pdf->SetFont('freeserif','',12);
$pdf->addTOC(3,'courier','.','Оглавление','B',array(128,0,0));
$pdf->endTOCPage();
$pdf->Output($cd45.'-'.$god.'-'.$zakazchik.'.pdf','I');
?>
<?php
		#	���� ��������� ������ � ������ "����������� � ��������� ��������� �� ����������������� �������"
define				('FPDF_FONTPATH', '/usr/share/php/phppdf/font/');
require_once	('/usr/share/php/phppdf/fpdf/fpdf.php');
require_once	('elah2.php');
require				('f_is_db.php');

$dt				=		date('d').".".date('m').".".date('Y');
$tm				=		date("H:i:s",time());

$bd				=		is_db();
$c				=		Connect($bd);
$k=0;
		#	������ ��������� ��� ����������� (��� ��������� ��������)
$sql_p704_2 = "SELECT FT011.GET_FIO(FT011.GET_USER()) AS P710 FROM dual";

	$sql_p704_res_2 = OCIParse($c, $sql_p704_2);
	OCIExecute($sql_p704_res_2);

	while(OCIFetch($sql_p704_res_2))
	{
		$p710_2		=		OCIResult($sql_p704_res_2,"P710");						//��� �����������
		$p710_2		=		iconv('KOI8-R', 'WINDOWS-1251', $p710_2);			//������ ��������� ��� FPDF
	}
		# ������ ������������ ���� ������
$sql1_2vz = "SELECT CR03_Z, X368 FROM FT011.FT011E05";

	$sqlres1_2vz = OCIParse($c, $sql1_2vz);
	OCIExecute($sqlres1_2vz);

	while(OCIFetch($sqlres1_2vz))
	{
	  $cr03_z					=			OCIResult($sqlres1_2vz,"CR03_Z");			//��� ������
	  $x368						=			OCIResult($sqlres1_2vz,"X368");				//���
	}

if($cr03_z == 1){
	$cr03_z = '�������� ������';
}else{
	$cr03_z = '�������������� ������';
}

class PDF extends FPDF {
	function Header(){
		global $dt;
		global $tm;
		$kolvo		=		$_GET['kolvo'];
		global $cr03_z;
		global $x368;

		$this->SetFont('TimesNewRomanPSMT','',8);
		$this->AliasNbPages('{pages}');
		$this->Cell(140);
		$this->Cell(50,3,'FT012C02',0,0,'R');
		$this->Ln();
		$this->Cell(140);
		$this->SetX(157.6);
		$this->Cell(50,3,'���� '.$this->PageNo().' / ������ {pages}',0,0,'R');
		$this->Ln();
		$this->Cell(140);
		$this->Cell(50,3,'����: '.$dt.' �����: '.$tm,0,0,'R');
		$this->Ln();
		$this->Cell(140);
		$this->Cell(50,3,'����� �������: '.$kolvo,0,0,'R');
		$this->Ln(5);
		$this->SetFont('TimesNewRomanPSMT','',14);
		$this->Cell(190,7,'����������� � ��������� ��������� �� ����������������� �������',0,0,'C');
		$this->Ln();
		$this->Cell(190,7,$cr03_z.' �� '.$x368.' ���',0,0,'C');
		$this->Ln(10);
		$this->SetFont('TimesNewRomanPSMT','',8);
		$this->Cell(20,9,'��� ���',1,0,'C');
		$this->Cell(80,9,'��������������',1,0,'C');
		$this->Cell(10,9,'��',1,0,'C');
		$this->Cell(10,9,'����',1,0,'C');
		$this->MultiCell(18,3,'��������� ����������� ����',1,'C');
		$yPos=$this->GetY();
		$this->SetY($yPos - 9);
		$this->Cell(138);
		$this->MultiCell(18,3,'��������� ����������� ����',1,'C');
		$yPos=$this->GetY();
		$this->SetY($yPos - 9);
		$this->Cell(156);
		$this->MultiCell(18,4.5,'����������� ���������',1,'C');
		$yPos=$this->GetY();
		$this->SetY($yPos - 9);
		$this->Cell(174);
		$this->Cell(16,9,'���������',1,0,'C');
		$this->Ln();
	}
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddFont('TimesNewRomanPSMT','','Times.php');							//�������
$pdf->AddFont('TimesNewRomanPS-BoldMT','','timesbd.php');				//������
$pdf->AddFont('TimesNewRomanPS-ItalicMT','','timesi.php');			//������
$pdf->AddFont('TimesNewRomanPS-BoldItalicMT','','timesbi.php');	//������ ������
//1-� ��������
$pdf->AddPage();
$pdf->SetFont('TimesNewRomanPSMT','',8);
	#	��������� ������ �� ������������ ������� ���������
	$ed_izm = "SELECT DISTINCT FT011.KE_NAME(b.X625) AS F_AK300 FROM FT011.FT011E05 a, FT011.AB121N05 b WHERE a.P450=b.P450";

	$ed_izm_21 = OCIParse($c, $ed_izm);
	OCIExecute($ed_izm_21);

	$items = array();
	while(OCIFetch($ed_izm_21))
	{
	  $f_ak300_ei			=			OCIResult($ed_izm_21,"F_AK300");
	  $f_ak300_ei			=			iconv('KOI8-R', 'WINDOWS-1251',$f_ak300_ei);
	}
	# ������ ������������ �������� �������
$sql1_2 = "SELECT P450, FT011.MTR_KD_2(P450) AS MTR_TXC, X368, H700, P710, X368_C, CR03_Z, H701, H7011, SUM(OB_POTR) AS OB , SUM(STOI) AS STOI FROM (
   SELECT P450, X368, P003, H250, H700, P710, TO_CHAR(X368_C,'DD.MM.YY') AS X368_C, CR03_Z, H701, H7011, C050, (H250 * C050) AS OB_POTR, (H250 * C050) * H700 AS STOI FROM FT011.FT011E05)
    GROUP BY P450, X368, P710, X368_C, CR03_Z, H700, H701, H7011
    ORDER BY MTR_TXC ASC";

/*"SELECT a.P450, TRIM(REGEXP_REPLACE(FT011.MTR_KD_2(a.P450),'<[^>]*>')) AS MTR_TXC, a.X368, TRIM(FT011.F_AK300('C075', b.X625)) AS F_AK300, a.H700, a.H701, a.H7011, SUM(a.H250) AS H250
	FROM FT011.FT011E05 a, FT011.AB121N05 b
	WHERE a.P450=b.P450
	GROUP BY a.P450,  a.X368, a.H7011, a.H701, a.H700, b.X625
	ORDER BY MTR_TXC ASC";*/

	$sqlres1_2 = OCIParse($c, $sql1_2);
	OCIExecute($sqlres1_2);
	$summa_array = array();
	while(OCIFetch($sqlres1_2))
	{
	  $p450						=			OCIResult($sqlres1_2,"P450");						//��� ���
	  $mtr_txc				=			OCIResult($sqlres1_2,"MTR_TXC");				//��������������
	  $mtr_txc				=			iconv('KOI8-R', 'WINDOWS-1251',$mtr_txc);//������ ��������� ��� FPDF
	  $x368_2					=			OCIResult($sqlres1_2,"X368");						//���
	  $h700_2					=			OCIResult($sqlres1_2,"H700");						//����
	  $h700_2					=			floatval($h700_2);											//��� ����������� ���� ����� �������
	  $h701						=			OCIResult($sqlres1_2,"H701");						//��������� ����������� ����
	  $h701						=			floatval($h701);												//��� ����������� ���� ����� �������
	  $h7011					=			OCIResult($sqlres1_2,"H7011");					//��������� ����������� ����
	  $h7011					=			floatval($h7011);												//��� ����������� ���� ����� �������
	  $h250						=			OCIResult($sqlres1_2,"OB");							//����������� ���������
	  $h250						=			floatval($h250);												//��� ����������� ���� ����� �������
	  $stoimost_2			=			OCIResult($sqlres1_2,"STOI");						//���������
	  $stoimost_2			=			round($stoimost_2,2);										//��� ����� ����� �������
	  array_push($summa_array, $stoimost_2);

	  $mtr_txc_0	=			split('!',$mtr_txc);
	  $mtr_txc_1	=			$mtr_txc_0[0];
	  $mtr_txc_2	=			$mtr_txc_0[1];


	if($pdf->GetStringWidth($mtr_txc) > 80) {
		$pdf->Cell(20,10,$p450,1,0,'C');
		$pdf->MultiCell(80,5,$mtr_txc_1.' '.$mtr_txc_2,1,'C');
		$yPos=$pdf->GetY();
		$pdf->SetY($yPos - 10);
		$pdf->Cell(100);
		$pdf->Cell(10,10,$f_ak300_ei,1,0,'C');
		$pdf->Cell(10,10,$h700_2,1,0,'C');
		$pdf->Cell(18,10,$h701,1,0,'C');
		$pdf->Cell(18,10,$h7011,1,0,'C');
		$pdf->Cell(18,10,$h250,1,0,'C');
		$pdf->Cell(16,10,$stoimost_2,1,0,'C');
	}else{
	$pdf->Cell(20,5,$p450,1,0,'C');
	$pdf->Cell(80,5,$mtr_txc_1.' '.$mtr_txc_2,1,0,'C');
	$pdf->Cell(10,5,$f_ak300_ei,1,0,'C');
	$pdf->Cell(10,5,$h700_2,1,0,'C');
	$pdf->Cell(18,5,$h701,1,0,'C');
	$pdf->Cell(18,5,$h7011,1,0,'C');
	$pdf->Cell(18,5,$h250,1,0,'C');
	$pdf->Cell(16,5,$stoimost_2,1,0,'C');
	}
	$pdf->Ln();
	}
$summa_array_1	=		array_sum($summa_array);
$pdf->SetFont('TimesNewRomanPS-BoldMT','',9);
$pdf->Cell(174,8,'�����:  ',0,0,'R');
$pdf->Cell(16,8,$summa_array_1,0,0,'C');
$pdf->Ln();
$pdf->SetFont('TimesNewRomanPSMT','',8);
$pdf->SetY(263);
$pdf->Cell(70,4,'',0,0,'L');
$pdf->Ln();
$pdf->Cell(70,4,'�����������: '.$p710_2.'�������: _______________',0,0,'L');
$pdf->Ln();
$pdf->Cell(70,4,'����: '.$dt.' �����: '.$tm,0,0,'L');

$pdf->Output();
?>
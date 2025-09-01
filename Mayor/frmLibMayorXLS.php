<?php
	session_start();
	//$Periodo=$_SESSION['PERIODOPC'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];


	if (isset($_POST['messelect']) || isset($_POST['anoselect'])) {
		if ($_POST['messelect']<=9) {
			$_SESSION['PERIODOPC']="0".$_POST['messelect']."-".$_POST['anoselect'];
		}else{
			$_SESSION['PERIODOPC']=$_POST['messelect']."-".$_POST['anoselect'];     
		}
	}else{
		$_SESSION['PERIODOPC']=$_SESSION['PERIODO'];
	}

	$PeriodoX=$_SESSION['PERIODOPC'];

	if (isset($_POST['anual']) && $_POST['anual']==1) {
		$PeriodoX=substr($_SESSION['PERIODOPC'],3,4);
	}

	$NomArch="LibroMayor_".$RutEmpresa."_".$PeriodoX.".xls";

	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$NomArch.""); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	include 'Grilla.php';
?>
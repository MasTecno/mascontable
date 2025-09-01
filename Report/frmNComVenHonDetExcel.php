<?php

	session_start();
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$NomArch="Analitico-Emp".$RutEmpresa.".xls";

	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$NomArch.""); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	if ($_POST['pendiente']=="N") {
		include 'frmNComVenHonDet.php';
	}else{	
		include 'frmNComVenHonDetPen.php';
	}

?>
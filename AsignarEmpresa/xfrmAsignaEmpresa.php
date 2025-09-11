<?php 

	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


	$SQL="SELECT * FROM CTEmpresas WHERE id='".$_POST['idmov']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$RutEmp=$registro['rut'];
	}

	$SQL1="SELECT * FROM CTContadoresAsignado WHERE rutempresa='$RutEmp' AND idcontador='".$_POST['ListCont']."'";
	$resultados1 = $mysqli->query($SQL1);
	$row_cnt = $resultados1->num_rows;
	if ($row_cnt==0) {
		$mysqli->query("INSERT INTO CTContadoresAsignado VALUES('','".$_POST['ListCont']."','".$RutEmp."','A')");
	}else{
		$mysqli->query("DELETE FROM CTContadoresAsignado WHERE rutempresa='$RutEmp' AND idcontador='".$_POST['ListCont']."'");
	}

	$mysqli->close();

	header("location:frmAsignaEmpresa.php?Cont=".$_POST['ListCont']);
?>
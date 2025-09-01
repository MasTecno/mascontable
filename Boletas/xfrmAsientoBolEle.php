<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if (isset($_POST['DefeAsie']) && $_POST['DefeAsie']!="") {
		$mysqli->query("DELETE FROM CTAsientoBolEle WHERE tipo='V' AND rut_empresa=''");
		$mysqli->query("DELETE FROM CTAsientoBolEle WHERE tipo='V' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");

		$mysqli->query("INSERT INTO CTAsientoBolEle VALUES('','','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','".$_POST['Comp4']."','V');");
		$mysqli->query("INSERT INTO CTAsientoBolEle VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','".$_POST['Comp4']."','V');");
	}else{
		$mysqli->query("DELETE FROM CTAsientoBolEle WHERE tipo='V' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		$mysqli->query("INSERT INTO CTAsientoBolEle VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','".$_POST['Comp4']."','V');");
	}

	$mysqli->close();

	header("location:frmAsientoBolEle.php");
?>
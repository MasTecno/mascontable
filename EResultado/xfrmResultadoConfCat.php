<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if ($_POST['idmodcat']!="") {
		$mysqli->query("UPDATE CTEstResultadoCab SET Nombre='".$_POST['NombreCat']."', Tipo='".$_POST['Tipo']."' WHERE id='".$_POST['idmodcat']."'");            
	}else{
		$mysqli->query("INSERT INTO CTEstResultadoCab VALUES('','".$_POST['NombreCat']."','".$_POST['Tipo']."','A')");
	}

	$mysqli->close();
	header("location:frmResultadoConfCat.php");
?>
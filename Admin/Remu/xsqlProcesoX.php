<?php

	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../index.php?Msj=95");
		exit;
	}

	include 'conexionserver.php';
	include 'conexion.php';
	$mysqli=conectarServer();

	$sql = "SELECT * FROM UnionServer WHERE id='".$_POST['sel1']."'";
	$resultado = $mysqli->query($sql);

	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		$xcla=$registro["Clave"];
		$xbas=$registro["Base"];
	}
	$mysqli->close();

	$mysqliX=xconectar($xusu,$xcla,$xbas);

	$StrSql=utf8_decode($_POST['SqlScript']);

	// mysqli_query("SET NAMES 'utf8'");

	if (mysqli_multi_query($mysqliX, $StrSql)) {
		echo "Proceso Exitoso";
	} else {
		echo "Error: " . $_POST['SqlScript'] . "<br><br>" . mysqli_error($mysqliX);
	}

	$mysqliX->close();

?>
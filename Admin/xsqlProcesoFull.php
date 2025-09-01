<?php
	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../index.php?Msj=95");
		exit;
	}

	include 'conexionserver.php';
	include 'conexion.php';
	$mysqli=conectarServer();

	$sql = "SELECT * FROM UnionServer WHERE Estado='A'";//AND Numero<=10
	$resultado = $mysqli->query($sql);

	// $StrSql="SELECT * CTAsiento WHERE tipo='C'";

	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		$xcla=$registro["Clave"];
		$xbas=$registro["Base"];

		$mysqliX=xconectar($xusu,$xcla,$xbas);

		$StrSql=$_POST['SqlScript'];

		if (mysqli_multi_query($mysqliX, utf8_decode($StrSql))) {
			echo $registro["Server"].", Proceso Exitoso <br><br>";
		} else {
			echo "Error: ". $registro["Server"]. ", ". $_POST['SqlScript'] . "<br>" . mysqli_error($mysqliX). "<br><br>";
		}
	}
	
	$mysqliX->close();
	$mysqli->close();
?>
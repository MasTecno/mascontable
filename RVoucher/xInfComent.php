<?php

	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTRegLibroDiarioCome WHERE keyas='".$_POST['KeyCom']."' AND rutempresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$mens=$registro['comentario'];
	}
	$mysqli->close();
	
	echo $mens;
?>
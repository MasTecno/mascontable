<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();

	$mysqli=conectarUnion();

	$SQL="SELECT * FROM FacturasMastecno WHERE Codigo='".$_GET['Key']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Arch=$registro['NombreArchivo'];
	}

	$Ruta="Archivos/".$Arch;
	$mysqli->close();

	header ("Content-Disposition: attachment; filename=".$Arch." ");
	header ("Content-Type: application/octet-stream");
	header ("Content-Length: ".filesize($Ruta));
	readfile($Ruta);

?>
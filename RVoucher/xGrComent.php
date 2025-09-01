<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();

	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTRegLibroDiarioCome WHERE keyas='".$_POST['KeyCom']."' AND rutempresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt==0) {
		$mysqli->query("INSERT INTO CTRegLibroDiarioCome VALUES('','$RutEmpresa','".$_POST['KeyCom']."','".$_POST['commentcomp']."','A')");

	}else{
		$mysqli->query("UPDATE CTRegLibroDiarioCome SET comentario='".$_POST['commentcomp']."' WHERE rutempresa='$RutEmpresa' AND keyas='".$_POST['KeyCom']."'");
	}

	$mysqli->close();

?>
<?php 
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
		
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTCategoria WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$L1='N1j'.$registro["id"];
		$L2='N2j'.$registro["id"];

		$scr="UPDATE CTCategoria SET N1='".$_POST[$L1]."', N2='".$_POST[$L2]."' WHERE id='".$registro["id"]."';";
		$mysqli->query($scr);
	}
	
	$mysqli->close();

	header("location:frmCuentas.php");
?>
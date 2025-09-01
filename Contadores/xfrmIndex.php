<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if ($_POST['idmod']!="") {
		$mysqli->query("UPDATE CTContadoresFirma SET Rut='".$_POST['rut']."', Nombre='".$_POST['nombre']."', Cargo='".$_POST['cargo']."' WHERE Id='".$_POST['idmod']."'");
	}else{
		$SQL="SELECT * FROM CTContadoresFirma WHERE Rut='".$_POST['rut']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("INSERT INTO CTContadoresFirma VALUES('','".$_POST['rut']."','".$_POST['nombre']."','".$_POST['cargo']."','A')");
		}else{
			$mysqli->close();
			header("location:./?ex=yes");
			exit;
		}
	}

	$mysqli->close();
	header("location:./");
?>
<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if($_POST['idccosto']!=""){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTCCosto SET nombre='".$_POST['nombre']."' WHERE id='".$_POST['idccosto']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'");
		$mysqli->close();
	}else{
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQL="SELECT * FROM CTCCosto WHERE codigo='".$_POST['codigo']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$mysqli->close();
			header("location:index.php?Err=1");
			exit;
		}

		$mysqli->query("INSERT INTO CTCCosto (id,rutempresa,codigo,nombre,estado) VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['codigo']."','".$_POST['nombre']."','A')");
		$mysqli->close();
	}

	header("location:../CCostos");
?>
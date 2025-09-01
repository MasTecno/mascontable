<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTAsientoPlantilla WHERE nombre='".$_POST['NomPlan']."'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt==0) {

		$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['TGuaPlan']."' AND rutempresa='$RutEmpresa' AND glosa<>'' ORDER BY id ASC";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$TipMov=$registro1['tipo'];
		}

		$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['TGuaPlan']."' AND rutempresa='$RutEmpresa' ORDER BY id ASC";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			if ($registro1['glosa']=="") {
				$mysqli->query("INSERT INTO CTAsientoPlantilla VALUES('','$TipMov','".$_POST['NomPlan']."','".$registro1['cuenta']."','A')");
			}
		}
	}else{
		echo "El nombre que esta Asignado ya existe";
	}

	$mysqli->close();

?>
<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';


	$resultado='<option value="0">Seleccione</option>';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if($_POST['id_tipodocumento']=="C" || $_POST['id_tipodocumento']=="P"){
		if ($_POST['id_tipodocumento']=="C") {
			$tidoc="V";
			$tCliPro="C";
		}

		if ($_POST['id_tipodocumento']=="P") {
			$tidoc="C";
			$tCliPro="P";
		}

		// echo $SQLCliPro="SELECT CTRegDocumentos.rutempresa, CTRegDocumentos.rut, CTCliPro.razonsocial, CTCliPro.tipo, CTRegDocumentos.tipo
		// FROM CTRegDocumentos LEFT JOIN CTCliPro ON CTRegDocumentos.rut = CTCliPro.rut
		// GROUP BY CTRegDocumentos.rutempresa, CTRegDocumentos.rut, CTCliPro.razonsocial, CTCliPro.tipo, CTRegDocumentos.tipo
		// HAVING (((CTRegDocumentos.rutempresa)='".$_SESSION['RUTEMPRESA']."') AND ((CTCliPro.tipo)='$tCliPro') AND ((CTRegDocumentos.tipo)='$tidoc'))
		// ORDER BY CTCliPro.razonsocial;";


		$SQLCliPro="SELECT cli.rut, cli.razonsocial
		FROM CTRegDocumentos doc
		JOIN CTCliPro cli ON doc.rut = cli.rut
		WHERE doc.rutempresa = '".$_SESSION['RUTEMPRESA']."'
		AND doc.tipo = '$tidoc'
		GROUP BY cli.rut, cli.razonsocial;";

		$resultados = $mysqli->query($SQLCliPro);
		while ($registro = $resultados->fetch_assoc()){
				$resultado=$resultado.'<option value="'.$registro['rut'].'">'.$registro['rut'].' - '.$registro['razonsocial'].'</option>'."\n";
		}
	}

	if($_POST['id_tipodocumento']=="H"){
		$SQL1="SELECT rut FROM CTHonorarios WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' GROUP BY rut";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()){
			$SQL="SELECT * FROM CTCliPro WHERE rut='".$registro1['rut']."' GROUP BY rut ORDER BY razonsocial";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$resultado=$resultado.'<option value="'.$registro['rut'].'">'.$registro['rut'].' - '.$registro['razonsocial'].'</option>'."\n";
			}
		}
	}

	$mysqli->close();

	echo $resultado;
?>
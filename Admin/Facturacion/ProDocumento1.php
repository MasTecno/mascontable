<?php

	include '../conexionserver.php';

	$mysqli=conectarServer();

	$SQL="SELECT * FROM FacturasMastecno WHERE id_Server='".$_POST['NIdServer']."' AND Periodo='".$_POST['PServer']."'";

	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		if ($_POST['OpPago']!="S") {
			$mysqli->query("UPDATE FacturasMastecno SET Estado='".$_POST['OpPago']."' WHERE id_Server='".$_POST['NIdServer']."' AND Periodo='".$_POST['PServer']."'");
		}
	}

    $dia = substr($_POST['NFPago'],0,2);
    $mes = substr($_POST['NFPago'],3,2);
    $ano = substr($_POST['NFPago'],6,4);

    $XFecha=$ano."/".$mes."/".$dia;

	$SQL="SELECT * FROM DatosPersonales WHERE idServer='".$_POST['NIdServer']."' AND Estado='A'";

	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		$mysqli->query("UPDATE DatosPersonales SET FPago='".$XFecha."' WHERE idServer='".$_POST['NIdServer']."' AND Estado='A'");
	}


	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$xDato1=utf8_encode($registro['Contacto']);	
	// 	$xDato2=date('d-m-Y',strtotime($registro['FPago']));
	// }


	$mysqli->close();


	header("location:index.php#".$_POST['NServer']);

	

?>
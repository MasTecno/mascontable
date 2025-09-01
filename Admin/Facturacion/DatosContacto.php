<?php

	include '../conexionserver.php';

	session_start();

	$xDato1="Sin Datos";
	$xDato2="Sin Datos";
	$xDato3="Sin Datos";
	$xDato4="Sin Datos";

	$mysqli=conectarServer();
	$SQL="SELECT * FROM DatosPersonales WHERE idServer='".$_POST['NIdServer']."' AND estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xDato1=utf8_encode($registro['Contacto']);	
		$xDato2=date('d-m-Y',strtotime($registro['FPago']));
	}

	// $SQL="SELECT * FROM DatosPersonales WHERE idServer='".$_POST['NIdServer']."' AND estado='A'";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$xDato1=utf8_encode($registro['Contacto']);	
	// 	$xDato2=$registro['FPago'];
	// }

	$mysqli->close();


	$xDato5='';

	echo json_encode(
      array("dato1" => "$xDato1", 
      "dato2" => "$xDato2",
      "dato3" => "$xDato3", 
      "dato4" => "$xDato4", 
      "dato5" => "$xDato5")
      );


?>
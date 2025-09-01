<?php

	include 'conexionserver.php';

	session_start();
	

	$xDato1="Sin Datos";
	$xDato2="Sin Datos";
	$xDato3="Sin Datos";
	$xDato4="Sin Datos";

	$xDato6=date("m");

	if ($xDato6=="01") {
		$xDato6="Enero";
	}
	if ($xDato6=="02") {
		$xDato6="Febrero";
	}
	if ($xDato6=="03") {
		$xDato6="Marzo";
	}
	if ($xDato6=="04") {
		$xDato6="Abril";
	}
	if ($xDato6=="05") {
		$xDato6="Mayo";
	}
	if ($xDato6=="06") {
		$xDato6="Junio";
	}
	if ($xDato6=="07") {
		$xDato6="Julio";
	}
	if ($xDato6=="08") {
		$xDato6="Agosto";
	}
	if ($xDato6=="09") {
		$xDato6="Septiembre";
	}
	if ($xDato6=="10") {
		$xDato6="Octubre";
	}
	if ($xDato6=="11") {
		$xDato6="Noviembre";
	}
	if ($xDato6=="12") {
		$xDato6="Diciembre";
	}

	$xDato7=date("m-Y");

	$mysqli=conectarServer();
	$SQL="SELECT * FROM DatosPersonales WHERE idServer='".$_POST['ListServ']."' AND estado='A'";
	// exit;
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xDato1=$registro['Correo'];	
		$xDato2=utf8_encode($registro['Contacto']);	
		$xDato3=utf8_encode($registro['Corto']);
		$xDato4=$registro['Monto'];
	}
	// $xDato1=$SQL;
	$mysqli->close();


	$xDato5='Hola '.$xDato3.', espero que est&eacute;s bien:

Adjunto Factura de servicios mes de '.$xDato6.'.

	Los datos son;
	Banco Estado
	Cta Vista o Chequera Electr&oacute;nica
	391-7-053956-1
	76917161-4
	samuel@mastecno.cl

Favor confirmar dentro de los primero 15 d&iacute;as con comprobante la transferencia, la idea de esto es para que no le acumulen muchos documentos.

Estoy atento a cualquier duda o consulta.
Gracias de ante mano.  

Saludos.

<strong>Samuel Santander Vallejos</strong>
MasTecno Spa.
';

	echo json_encode(
		array("dato1" => "$xDato1", 
		"dato2" => "$xDato2",
		"dato3" => "$xDato3", 
		"dato4" => "$xDato4", 
		"dato5" => "$xDato5",
		"dato6" => "$xDato6",
		"dato7" => "$xDato7")
	);


?>
<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		echo json_encode(
			array("dato1" => "Exit")
		);

		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTRegDocumentos WHERE id='".descriptSV($_POST['KeyMov'])."' AND estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xDato1=$registro['exento'];	
		$xDato2=$registro['neto'];	
		$xDato3=$registro['iva'];	
		$xDato4=$registro['retencion'];	
		$xDato5=$registro["rut"];
		$xDato7=date('d-m-Y',strtotime($registro['fecha']));
		$xDato9=$registro["numero"];
		$xDato10=$registro["total"];
		$xDato12=$registro["cuenta"];
		$xDato13=$registro["ccosto"];

		$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$xDato8=$registro1["sigla"];
		}
	}

	if($_POST['frm']=="H"){

		$SQL="SELECT * FROM CTHonorarios WHERE id='".descriptSV($_POST['KeyMov'])."' AND estado='A'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {

			$xDato1=0;	
			$xDato2=$registro['liquido'];	
			$xDato3=0;	
			$xDato4=$registro['retencion'];	
			$xDato5=$registro["rut"];
			$xDato7=date('d-m-Y',strtotime($registro['fecha']));
			$xDato9=$registro["numero"];
			$xDato10=$registro["bruto"];
			$xDato12=$registro["cuenta"];
			$xDato13=$registro["ccosto"];
	
			// $SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
			// $resultados1 = $mysqli->query($SQL1);
			// while ($registro1 = $resultados1->fetch_assoc()) {
			// 	$xDato8=$registro1["sigla"];
			// }
			$xDato8="Honorarios";
		}
	}


	$SQL="SELECT * FROM CTCliPro WHERE rut='".$xDato5."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xDato6=utf8_encode($registro["razonsocial"]);
	}

	$mysqli->close();
	unset($_SESSION['CARRITO']);

	echo json_encode(
      array("dato1" => "$xDato1", 
      "dato2" => "$xDato2",
      "dato3" => "$xDato3", 
      "dato4" => "$xDato4", 
      "dato5" => "$xDato5", 
      "dato6" => "$xDato6", 
      "dato7" => "$xDato7", 
      "dato8" => "$xDato8", 
      "dato9" => "$xDato9", 
      "dato10" => "$xDato10", 
      "dato11" => "$xDato11", 
      "dato12" => "$xDato12", 
      "dato13" => "$xDato13")
    );
?>
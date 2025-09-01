<?php

    include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if($registro['tipo']=="IVA"){
			$DIVA=$registro['valor']; 
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];  
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}
	
	$SDebe=0;
	$SHaber=0;

	$SQL="SELECT sum(Debe) as SDebe, sum(Haber) as SHaber FROM CTAperturaAsiento WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$SDebe=number_format($registro['SDebe'], $NDECI, $DDECI, $DMILE);	
		$SHaber=number_format($registro['SHaber'], $NDECI, $DDECI, $DMILE);	
	}

	$mysqli->close();

	echo json_encode(
      array("dato1" => "$SDebe", 
      "dato2" => "$SHaber")
      );

?>
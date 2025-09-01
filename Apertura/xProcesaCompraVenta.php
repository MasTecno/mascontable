<?php

    include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	$FECHA=date("Y/m/d");

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$mysqli->query("DELETE FROM CTRegDocumentos WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND origen='Z';");

	$SQL="SELECT * FROM CTParametros WHERE estado='A' AND tipo='SEPA_LIST'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$DLIST=$registro['valor'];	
	}

	$mysqli->close();

    $archivo = $_FILES['CsvCompraVenta']['tmp_name'];	

	//leo el archivo que contiene los datos del producto
	$DatosArc = fopen ($archivo , "r" );

	//Leo linea por linea del archivo hasta un maximo de 1000 caracteres por linea leida usando coma(,) como delimitador
	while (($datos =fgetcsv($DatosArc,0,$DLIST)) !== FALSE ){
		//Arreglo Bidimensional para guardar los datos de cada linea leida del archivo
		$linea[]=array('TDocumento'=>$datos[0],'CodSII'=>$datos[1],'Rut'=>$datos[2],'RSocial'=>$datos[3],'Numero'=>$datos[4],'Fecha'=>$datos[5],'Exento'=>$datos[6],'Neto'=>$datos[7],'IVA'=>$datos[8],'Retencion'=>$datos[9],'Total'=>$datos[10]);
	}

	$DatosArc = fopen ($archivo , "r" );
	$LArchivo=-1;
	while ($data = fgetcsv ($DatosArc, 0, $DLIST)){
		$LArchivo++;
	}

	//Cierra el archivo
	fclose ($DatosArc);

	$Periodo=$_SESSION['PERIODO'];
	$PerInsert = substr($Periodo,3,4);
	// $PerInsert = "12-".($PerInsert-0);
	$PerInsert = $_POST['PApertura1'];
	
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$STRSQL="INSERT INTO CTRegDocumentos VALUES";
	//Iteracion el array para extraer cada uno de los valores almacenados en cada items
	$indice=0;
	foreach($linea as $indice=>$value) {

		$XTDocumento=$value["TDocumento"];
		$XCodSII=$value["CodSII"];
		$XRut=$value["Rut"];
		$XRSocial=$value["RSocial"];
		$XNumero=$value["Numero"];
		$XExento=$value["Exento"];
		$XNeto=$value["Neto"];
		$XIVA=$value["IVA"];
		$XRetencion=$value["Retencion"];
		$XTotal=$value["Total"];

		$dia = substr($value["Fecha"],0,2);
		$mes = substr($value["Fecha"],3,2);
		$ano = substr($value["Fecha"],6,4);

		$XFecha=$ano."/".$mes."/".$dia;


		$SQL="SELECT * FROM CTTipoDocumento WHERE tiposii='$XCodSII'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XCodSII=$registro['id'];	
		}

		if ($XRut!="" && $XNumero!="" && $XTotal>0 && $XNumero!="") {

			if ($XTDocumento=="C") {
				$TCliPro="P";
			}else{
				$TCliPro="C";
			}

			$SQL="SELECT * FROM CTCliPro WHERE rut='$XRut' AND tipo='$TCliPro'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$mysqli->query("INSERT INTO CTCliPro VALUES('','$XRut','".strtoupper($XRSocial)."','','','','','','$TCliPro','A')");
			}

			if ($STRSQL!="INSERT INTO CTRegDocumentos VALUES") {
				$STRSQL = $STRSQL.",";
			}
        	$STRSQL = $STRSQL." ('','$PerInsert','".$_SESSION['RUTEMPRESA']."','$XRut','','','$XCodSII','$XNumero','$XFecha','$XExento','$XNeto','$XIVA','$XRetencion','$XTotal','','','$XTDocumento','$FECHA','A','Z','','')";
		}
	}
	$mysqli->close();
// 	echo $STRSQL;

// exit;
	$STRSQL = $STRSQL.";";

	// if ($SumDebe==0 && $SumHaber==0) {
	// 	echo "No hay datos que procesar";
	// 	exit;
	// }

	// if ($SumDebe!=$SumHaber) {
	// 	echo "Las sumatorias de Debe y Haber no conciden, Proceso Cancelado";
	// 	exit;
	// }

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$mysqli->query($STRSQL);
	$mysqli->close();

	header("location:../Apertura");

	// echo "<font color=green>".number_format($ingresado,2)." Productos Almacenados con exito<br/>";
	// echo "<font color=red>".number_format($duplicado,2)." Productos Duplicados<br/>";
	// echo "<font color=red>".number_format($error,2)." Errores de almacenamiento<br/>";
?>
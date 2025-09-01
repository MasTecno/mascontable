<?php
    include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$FECHA=date("Y/m/d");

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$mysqli->query("DELETE FROM CTHonorarios WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND origen='Z';");


	$SQL="SELECT * FROM CTParametros WHERE estado='A' AND tipo='SEPA_LIST'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$DLIST=$registro['valor'];	
	}

	$cuta="";
	$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa=''";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$cuta=$registro['L1'];	
	}

	$mysqli->close();

    $archivo = $_FILES['CsvHonorario']['tmp_name'];	

	//leo el archivo que contiene los datos del producto
	$DatosArc = fopen ($archivo , "r" );

	//Leo linea por linea del archivo hasta un maximo de 1000 caracteres por linea leida usando coma(,) como delimitador
	while (($datos =fgetcsv($DatosArc,0,$DLIST)) !== FALSE ){
		//Arreglo Bidimensional para guardar los datos de cada linea leida del archivo
		$linea[]=array('Numero'=>$datos[0],'Rut'=>$datos[1],'RSocial'=>$datos[2],'Fecha'=>$datos[3],'Bruto'=>$datos[4],'Retencion'=>$datos[5],'Liquido'=>$datos[6]);
	}

	$DatosArc = fopen ($archivo , "r" );
	$LArchivo=-1;
	while ($data = fgetcsv ($DatosArc, 0, $DLIST)){
		$LArchivo++;
	}

	//Cierra el archivo
	fclose ($DatosArc);

	// $SumDebe=0;
	// $SumHaber=0;

	$Periodo=$_SESSION['PERIODO'];
	$PerInsert = substr($Periodo,3,4);
	// $PerInsert = "12-".($PerInsert-0);
	$PerInsert = $_POST['PApertura1'];


	$STRSQL="INSERT INTO CTHonorarios VALUES";
	//Iteracion el array para extraer cada uno de los valores almacenados en cada items
	$indice=0;
	foreach($linea as $indice=>$value) {

		//$XFecha=;
		$XRut=$value["Rut"];
		$XRSocial=$value["RSocial"];
		$XNumero=$value["Numero"];
		$XBruto=$value["Bruto"];
		$XRetencion=$value["Retencion"];
		$XLiquido=$value["Liquido"];

		$dia = substr($value["Fecha"],0,2);
		$mes = substr($value["Fecha"],3,2);
		$ano = substr($value["Fecha"],6,4);

		$XFecha=$ano."/".$mes."/".$dia;


		if ($XRut!="" && $XNumero!="" && $XBruto>0 && $XRetencion>0 && $XLiquido>0) {

			if ($STRSQL!="INSERT INTO CTHonorarios VALUES") {
				$STRSQL = $STRSQL.",";
			}
        	$STRSQL = $STRSQL." ('','$PerInsert','".$_SESSION['RUTEMPRESA']."','$XFecha','$XRut','$XNumero','$cuta','$XBruto','$XRetencion','$XLiquido','R','$FECHA','','A','Z')";
		}


		$SQL="SELECT * FROM CTCliPro WHERE rut='$XRut' AND tipo='P'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("INSERT INTO CTCliPro VALUES('','$XRut','".strtoupper($XRSocial)."','','','','','','P','A')");
		}


	}

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
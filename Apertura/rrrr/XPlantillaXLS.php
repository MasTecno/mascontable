<?php
    include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';


	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$mysqli->query("DELETE FROM CTAperturaAsiento WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."';");


	$SQL="SELECT * FROM CTParametros WHERE estado='A' AND tipo='SEPA_LIST'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$DLIST=$registro['valor'];	
	}

	$mysqli->close();

    $archivo = $_FILES['CsvCuentas']['tmp_name'];

	//leo el archivo que contiene los datos del producto
	$DatosArc = fopen ($archivo , "r" );

	//Leo linea por linea del archivo hasta un maximo de 1000 caracteres por linea leida usando coma(,) como delimitador
	while (($datos =fgetcsv($DatosArc,0,$DLIST)) !== FALSE ){
		//Arreglo Bidimensional para guardar los datos de cada linea leida del archivo
		$linea[]=array('Codigo'=>$datos[0],'Cuenta'=>$datos[1],'Tipo'=>$datos[2],'Categoria'=>$datos[3],'Debe'=>$datos[4],'Haber'=>$datos[5]);
	}

	$DatosArc = fopen ($archivo , "r" );
	$LArchivo=-1;
	while ($data = fgetcsv ($DatosArc, 0, $DLIST)){
		$LArchivo++;
	}

	//Cierra el archivo
	fclose ($DatosArc);

	$SumDebe=0;
	$SumHaber=0;

	$STRSQL="INSERT INTO CTAperturaAsiento VALUES";
	//Iteracion el array para extraer cada uno de los valores almacenados en cada items
	$indice=0;
	foreach($linea as $indice=>$value) {

		$XCodigo=$value["Codigo"];
		$XCuenta=$value["Cuenta"];
		$XTipo=$value["Tipo"];
		$XCategoria=$value["Categoria"];
		$XDebe=$value["Debe"];
		$XHaber=$value["Haber"];

		if ($XCodigo!="" && $XDebe!="" && $XHaber!="" && $indice>0 && ($XDebe>0 || $XHaber>0)) {

			if ($STRSQL!="INSERT INTO CTAperturaAsiento VALUES") {
				$STRSQL = $STRSQL.",";
			}

        	$STRSQL = $STRSQL." ('','".$_SESSION['RUTEMPRESA']."','$XCodigo','$XDebe','$XHaber','A')";

        	$SumDebe=$SumDebe+$XDebe;
        	$SumHaber=$SumHaber+$XHaber;
		}
	}

	$STRSQL = $STRSQL.";";

	if ($SumDebe==0 && $SumHaber==0) {
		echo "No hay datos que procesar";
		exit;
	}

	if ($SumDebe!=$SumHaber) {
		echo "Las sumatorias de Debe y Haber no conciden, Proceso Cancelado";
		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$mysqli->query($STRSQL);
	$mysqli->close();

	header("location:../Apertura");

	// echo "<font color=green>".number_format($ingresado,2)." Productos Almacenados con exito<br/>";
	// echo "<font color=red>".number_format($duplicado,2)." Productos Duplicados<br/>";
	// echo "<font color=red>".number_format($error,2)." Errores de almacenamiento<br/>";
?>
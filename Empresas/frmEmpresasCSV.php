<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();
	//include 'conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_LIST"){
			$SL=$registro['valor'];  
		}
	}
	$cont=1;
	$variable="N".$SL."Rut".$SL."Razon Social".$SL."Rut Representante".$SL."Representante".$SL."Direccion".$SL."Cuidad".$SL."Correo".$SL."Giro".$SL."Periodo".$SL."Estado";
		// if ($_SESSION["PLAN"]=="S"){
	$SQL="SELECT * FROM CTEmpresas ORDER BY razonsocial ASC";
		// }else{
		// 	$SQL="SELECT * FROM CTCuentas WHERE estado<>'X' ORDER BY numero ASC";
		// }
		// $SQL="SELECT * FROM CTCuentas WHERE estado<>'X' ORDER BY numero ASC";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if ($variable!="") {
			$variable=$variable."\r\n";
		}

		// $tipcat="";

		// $SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
		// $resultados1 = $mysqli->query($SQL1);
		// while ($registro1 = $resultados1->fetch_assoc()) {
		// 	$tipcat=$registro1["nombre"];
		// 	$tiptip=$registro1["tipo"];
		// }

		$variable=$variable.$cont.$SL.$registro["rut"].$SL.$registro["razonsocial"].$SL.$registro["rut_representante"].$SL.$registro["representante"].$SL.$registro["direccion"].$SL.$registro["ciudad"].$SL.$registro["correo"].$SL.$registro["giro"].$SL.$registro["periodo"].$SL.$registro["estado"];
		$cont++;
	}       

	$mysqli->close();

	// header("Content-Type: text/plain");
	// header('Content-Disposition: attachment; filename="Apertura-'.$RutEmpresa.'.csv"');


	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header('Content-Disposition: attachment; filename="MasContable-Listado de Empresas.csv"');
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	echo $variable;

?>
<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);

	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_LIST"){
			$SL=$registro['valor'];  
		}
	}

	$variable="Codigo".$SL."Cuenta".$SL."Tipo".$SL."Categoria".$SL."Debe".$SL."Haber";
		if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado<>'X' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero ASC";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE estado<>'X' ORDER BY numero ASC";
		}
		// $SQL="SELECT * FROM CTCuentas WHERE estado<>'X' ORDER BY numero ASC";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if ($variable!="") {
			$variable=$variable."\r\n";
		}

		$tipcat="";

		$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$tipcat=$registro1["nombre"];
			$tiptip=$registro1["tipo"];
		}

		$variable=$variable.$registro["numero"].$SL.strtoupper($registro["detalle"]).$SL.$tiptip.$SL.$tipcat.$SL."0".$SL."0";
	}       

	$mysqli->close();

	// header("Content-Type: text/plain");
	// header('Content-Disposition: attachment; filename="Apertura-'.$RutEmpresa.'.csv"');


	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header('Content-Disposition: attachment; filename="Apertura-'.$RutEmpresa.'.csv"');
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	echo $variable;

?>
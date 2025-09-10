<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTCategoria WHERE id='".$_POST['SelCat']."' AND estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$D1=$registro["N1"];
		$D2=$registro["N2"];
	}


	if ($_SESSION["PLAN"]=="S"){
		// $SQL="SELECT * FROM CTCuentasEmpresa WHERE estado<>'X' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero ASC";
		$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero LIKE '".$D1.$D2."%' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero DESC LIMIT 1";
	}else{
		$SQL="SELECT * FROM CTCuentas WHERE numero LIKE '".$D1.$D2."%' ORDER BY numero DESC LIMIT 1";
	}

	
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$UltCta=$registro["numero"];
	}

	echo $UltCta+1;

?>
<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$FECHA=date("Y/m/d");
	$KeyAs=$_POST['KeyAs'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='$KeyAs' AND rutempresa='$RutEmpresa' AND glosa<>''";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xGlosa=$registro['glosa'];
		$xfecha=$registro['fecha'];
	}

	if ($_POST['frm1']=="R") {

		$SQL="SELECT * FROM CTCliPro WHERE tipo='P' AND estado='A' AND rut='".$_POST['RutUno']."' ORDER BY razonsocial";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xSelAsignar=$registro['id'];
		}
		$mysqli->query("INSERT INTO CTFondo VALUES('','$xSelAsignar','".$_POST['RutUno']."','$RutEmpresa','$xGlosa','$xfecha','".$_POST['Monto']."','$KeyAs','$FECHA','I','A');");
	}

	$mysqli->close();

	header("location:Voucher.php");
?>
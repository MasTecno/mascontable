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
	$SQS="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQS);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];	
		}
		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];	
		}
		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];	
		}	
		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];	
		}
	}


	// $SQS="SELECT * FROM CTFondo WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND TIpo='I' AND Estado='A'";
	// $resultados = $mysqli->query($SQS);
	// while ($registro = $resultados->fetch_assoc()) {

	// 	echo $SQL1="SELECT sum(Monto) as smonto FROM `CTFondo` WHERE IdPersonal='".$registro['Id']."' AND Estado='A' AND Tipo='E' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' GROUP by IdPersonal";
	// 	exit;
	// 	$resultados1 = $mysqli->query($SQL1);
	// 	while ($registro1 = $resultados1->fetch_assoc()) {
	// 		$smonto=$registro1['smonto'];
	// 	}

	// 	if ($smonto>=$registro['Monto']) {
	// 		$mysqli->query("UPDATE CTFondo SET Estado='C' WHERE Id='".$registro['Id']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Tipo='I' AND Estado='A'");
	// 	}

	// }

	// $smonto=0;
	$SQL="SELECT * FROM CTFondo WHERE Tipo='I' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";

	if ($_POST['SelAsignar']!="") {
		$SQL=$SQL." AND IdPersonal='".$_POST['SelAsignar']."'";
	}

	if (isset($_POST['Todos'])!="") {
		//$SQL=$SQL." AND IdPersonal='".$_POST['SelAsignar']."'";
	}else{
		$SQL=$SQL." AND Estado='A'";
	}

	$SQL=$SQL." ORDER BY Id";
	// echo $SQL; 

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$smonto=0;
		$SQL1="SELECT sum(Monto) as smonto FROM `CTFondo` WHERE IdPersonal='".$registro['Id']."' AND Estado='A' AND Tipo='E' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' GROUP by IdPersonal";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$smonto=$registro1['smonto'];
		}

		$mcierre=$registro['Monto']-$smonto;
		if ($mcierre<0) {
			$opera="R";
		}else{
			$opera="S";
		}
		echo '
			<tr>
				<td>'.date('d-m-Y',strtotime($registro['Fecha'])).'</td>
				<td>'.$registro['Titulo'].'</td>
				<td>'.number_format($registro['Monto'], $NDECI, $DDECI, $DMILE).'</td>
				<td>'.number_format($smonto, $NDECI, $DDECI, $DMILE).'</td>
				<td>
					<button type="button" class="btn btn-success btn-sm" onclick="Ver('.$registro['Id'].')">
						<span class="glyphicon glyphicon-book"></span>
					</button>
				</td>
		';
		if ($registro['Estado']=="A") {
			echo '	
					<td>
						<button type="button" class="btn btn-warning btn-sm" onclick="Cerrar('.$registro['Id'].','.$mcierre.',\''.$opera.'\')">
							<span class="glyphicon glyphicon-ban-circle"></span>
						</button>
					</td>
				</tr>
			';
		}else{
			echo '	
					<td>
					</td>
				</tr>
			';
		}
	}
	$mysqli->close();
?>

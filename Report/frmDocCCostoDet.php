<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

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

	$SQL1="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='".$_POST['SelCCosto']."'";

	$resultados1 = $mysqli->query($SQL1);
	while ($registro1 = $resultados1->fetch_assoc()) {
		$LNCosto=$registro1["nombre"];
	}


	$mysqli->close();

	echo '
		<div class="col-md-12 text-center">
			<br>
			<input type="button" class="btn btn-default btn-sm" onclick="printDiv(\'DivImp\')" value="Imprimir">
		</div>

		<div class="col-md-12" id="DivImp">
			<div class="clearfix"></div>
			<br>
				<div class="col-md-12 text-center"><strong>Informe Centro de Costo: '.$LNCosto.'</strong></div>
			<br>

			<div class="col-md-12 text-center"></div>
			<div class="clearfix"></div>
			<hr>

			<table class="table table-hover table-striped">

				<thead>
				<tr>
					<th>F. Doc</th>
					<th>N&deg; Doc</th>
					<th>Rut</th>
					<th>R. Social</th>
					<th>F. Pago</th>
					<th>Periodo</th>
					<th class="text-right">Exento</th>
					<th class="text-right">Neto</th>
					<th class="text-right">IVA</th>
					<th class="text-right">Retenci&oacute;n</th>
					<th class="text-right">Total</th>
				</tr>
				</thead>
				<tbody>
	';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTControRegDocPago WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND keyas<>''";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Lkeyas=$registro["keyas"];
		//$LRut=$registro["rut"];
		$LCCosto=0;

		$SQL1="SELECT * FROM CTRegLibroDiario WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND keyas='$Lkeyas' AND glosa<>'' AND ccosto='".$_POST['SelCCosto']."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$LCCosto=$registro1["ccosto"];
			$LPeriodoP=$registro1["periodo"];
			$LFechaP=$registro1["fecha"];
		}

		$SQL1="SELECT * FROM CTRegDocumentos WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='".$registro['rut']."' AND numero='".$registro['ndoc']."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			//$LCCosto=$registro1["ccosto"];
			$LFecha=$registro1["fecha"];
			$MExento=$registro1["exento"];
			$MNeto=$registro1["neto"];
			$MIva=$registro1["iva"];
			$MRet=$registro1["retencion"];
			$MTotal=$registro1["total"];

		}

		$SQL1="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='$LCCosto'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$LNCosto=$registro1["nombre"];
		}

		$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$LRSocial=$registro1["razonsocial"];
		}

		if ($LCCosto>0) {
			echo '
				<tr>
					<td>'.date('d-m-Y',strtotime($LFecha)).'</td>
					<td>'.$registro["ndoc"].'</td>
					<td>'.$registro["rut"].'</td>
					<td>'.$LRSocial.'</td>
					<td>'.date('d-m-Y',strtotime($LFechaP)).'</td>
					<td>'.$LPeriodoP.'</td>
					<td align="right"><strong>'.number_format($MExento, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($MNeto, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($MIva, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($MRet, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($MTotal, $NDECI, $DDECI, $DMILE).'</strong></td>
				</tr>
			';
		}


	}

	echo '
				</tbody>
			</table>
	';


	$mysqli->close();
?>
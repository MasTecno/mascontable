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

	// $SQL="SELECT * FROM CTCuentas WHERE id='".$_POST['cacuenta']."'";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$ncuenta=$registro["numero"];
	// 	$nomcuenta=$registro["detalle"];
	// }


	$mysqli->close();

	echo '

		<style type="text/css">
			@media print {
				.well {
					background-color: #e4e4e4 !important;
				}
				.well-sm {
					background-color: #e4e4e4 !important;
				}
			}
		</style>


	
		<div class="col-md-12 text-center">
			<br>
			<input type="button" class="btn btn-default btn-sm" onclick="printDiv(\'DivImp\')" value="Imprimir">
		</div>
        <div class="col-md-12" id="DivImp">
		<div class="clearfix"></div>
		<br>
        <div class="well well-sm">         
        	Detalle
        </div>

		<table class="table table-hover table-striped">

		<thead>
		<tr>
			<th width="10%">Fecha</th>
			<th width="10%">Vencimiento</th>
			<th width="5%">N&deg;</th>
			<th width="5%">Folio</th>
			<th width="5%">Tipo</th>
			<th>Rut</th>
			<th>R. Social</th>
			<th width="10%">Monto</th>
		</tr>
		</thead>
		<tbody id="myTable">  	
	';
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);



if ($_POST['sfecha']=="") {
	
}



	$SBalance=0;
	$xglosa="";
	$TanoD = substr($Periodo,3,4);
	// $SQL="SELECT * FROM CTControlDocumento WHERE rutempresa='$RutEmpresa' AND cuenta='$ncuenta' AND periodo like '%-$TanoD'";


	$SQL="SELECT CTControlDocumento.fecha, CTControlDocumento.vencimiento, CTControlDocumento.folio, CTControlDocumento.ndoc, CTControlDocumento.monto, CTControlDocumento.tdocumento, CTControRegDocPago.origen, CTCliPro.rut, CTControlDocumento.tipo, CTControlDocumento.estado,CTControRegDocPago.rutempresa
	FROM (CTControlDocumento RIGHT JOIN CTControRegDocPago ON CTControlDocumento.ndoc = CTControRegDocPago.ndoc) LEFT JOIN CTCliPro ON CTControRegDocPago.rut = CTCliPro.rut
	GROUP BY CTControlDocumento.fecha, CTControlDocumento.vencimiento, CTControlDocumento.ndoc, CTControlDocumento.monto, CTControlDocumento.tdocumento, CTControRegDocPago.origen, CTCliPro.rut, CTControlDocumento.tipo, CTControlDocumento.estado
	HAVING (((CTControRegDocPago.origen)='B')";


	if ($_POST['tdocumentos']!="") {
		$SQL=$SQL." AND ((CTControlDocumento.tipo)='".$_POST['tdocumentos']."')";
	}

	if ($_POST['sdocumentos']!="") {
		$SQL=$SQL." AND ((CTControlDocumento.tdocumento)='".$_POST['sdocumentos']."')";
	}

		$SQL=$SQL." AND ((CTControRegDocPago.rutempresa)='".$_SESSION['RUTEMPRESA']."')";


	if($_POST['fdesde']!="" && $_POST['fhasta']!=""){

		$dia = substr($_POST['fdesde'],0,2);
		$mes = substr($_POST['fdesde'],3,2);
		$ano = substr($_POST['fdesde'],6,4);

		$Lfdesde=$ano."/".$mes."/".$dia;

		$dia = substr($_POST['fhasta'],0,2);
		$mes = substr($_POST['fhasta'],3,2);
		$ano = substr($_POST['fhasta'],6,4);

		$Lfhasta=$ano."/".$mes."/".$dia;

		if ($_POST['sfecha']=="V") {
			$SQL=$SQL." AND (CTControlDocumento.vencimiento BETWEEN '$Lfdesde' AND '$Lfhasta')";
		}else{
			$SQL=$SQL." AND (CTControlDocumento.fecha BETWEEN '$Lfdesde' AND '$Lfhasta')";
		}

		// if ($_POST['sfecha']=="E") {
		// 	$SQL=$SQL." AND CTControlDocumento.fecha BETWEEN '$Lfdesde' AND '$Lfhasta'";
		// }else{

		// }
	}

	$SQL=$SQL.") ORDER BY CTControlDocumento.vencimiento;";

	// $SQL=$SQL." ORDER BY fecha";
 // echo $SQL;
 // exit;

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

			// $SQL1="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$registro["keyas"]."' AND glosa<>''";
			// $resultados1 = $mysqli->query($SQL1);
			// while ($registro1 = $resultados1->fetch_assoc()) {
			// 	$xglosa=$registro1["glosa"];
			// 	$xncomprobante=$registro1["ncomprobante"];

				if ($registro["tipo"]=="C") {
					$xMen="Cheque";
				}
				if ($registro["tipo"]=="T") {
					$xMen="Transferencia";	
				}
				if ($registro["tipo"]=="O") {
					$xMen="Otros";
				}
			// }



			$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro['rut']."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$xnombra=$registro1["razonsocial"];
			}

			// $SBalance=$SBalance+$registro["debe"];
			// $SBalance=$SBalance-$registro["haber"];

			// $SQL1="SELECT * FROM CTCCosto WHERE rutempresa='$RutEmpresa' AND id='".$registro["ccosto"]."'";
			// $resultados1 = $mysqli->query($SQL1);
			// while ($registro1 = $resultados1->fetch_assoc()) {
			// 	$xnombra=$registro1["nombre"];
			// }
			// if ($registro["ccosto"]=="0") {
			// 	$xnombra="DEFECTO";
			// }

			echo '
				<tr>
				<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
				<td>'.date('d-m-Y',strtotime($registro["vencimiento"])).'</td>
				<td>'.$registro['ndoc'].'</td>
				<td>'.$registro['folio'].'</td>
				<td>'.$xMen.'</td>
				<td>'.$registro['rut'].'</td>
				<td>'.$xnombra.'</td>
				<td align="right">'.number_format($registro["monto"], $NDECI, $DDECI, $DMILE).'</td>
				</tr>
			';
	}
	echo "
			</tbody>
			</table>
			</div>";
	$mysqli->close();
?>
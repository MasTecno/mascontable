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
	if ($_SESSION["PLAN"]=="S"){
		$SQL="SELECT * FROM CTCuentasEmpresa WHERE id='".$_POST['cacuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	}else{
		$SQL="SELECT * FROM CTCuentas WHERE id='".$_POST['cacuenta']."'";
	}
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$ncuenta=$registro["numero"];
		$nomcuenta=$registro["detalle"];
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
        <div class="well well-sm">         
          Auxiliar cuenta '.$ncuenta.' - '.$nomcuenta.'
        </div>

		<table class="table table-hover table-striped">

		<thead>
		<tr>
			<th width="10%">Fecha</th>
			<th width="5%">N&deg; Comp</th>
			<th width="5%">Tipo</th>
			<th>Glosa</th>
			<th width="10%">C.Costo</th>
			<th width="10%">Cargo</th>
			<th width="10%">Abono</th>
			<th width="10%">Saldo</th>
		</tr>
		</thead>
		<tbody id="myTable">  	
	';
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


	$SBalance=0;
	$xglosa="";
	$TanoD = substr($Periodo,3,4);
	$SQL="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND cuenta='$ncuenta' AND periodo like '%-$TanoD'";


	if($_POST['fdesde']!="" && $_POST['fhasta']!=""){

		$dia = substr($_POST['fdesde'],0,2);
		$mes = substr($_POST['fdesde'],3,2);
		$ano = substr($_POST['fdesde'],6,4);

		$Lfdesde=$ano."/".$mes."/".$dia;

		$dia = substr($_POST['fhasta'],0,2);
		$mes = substr($_POST['fhasta'],3,2);
		$ano = substr($_POST['fhasta'],6,4);

		$Lfhasta=$ano."/".$mes."/".$dia;
		
		$SQL=$SQL." AND fecha BETWEEN '$Lfdesde' AND '$Lfhasta'";
	}

	$SQL=$SQL." ORDER BY fecha";


	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

			$SQL1="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$registro["keyas"]."' AND glosa<>''";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$xglosa=$registro1["glosa"];
				$xncomprobante=$registro1["ncomprobante"];

				if ($registro1["tipo"]=="E") {
					$xMen="Egreso";
				}
				if ($registro1["tipo"]=="I") {
					$xMen="Ingreso";	
				}
				if ($registro1["tipo"]=="T") {
					$xMen="Traspaso";
				}
			}
			$SBalance=$SBalance+$registro["debe"];
			$SBalance=$SBalance-$registro["haber"];

			$SQL1="SELECT * FROM CTCCosto WHERE rutempresa='$RutEmpresa' AND id='".$registro["ccosto"]."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$xnombra=$registro1["nombre"];
			}
			if ($registro["ccosto"]=="0") {
				$xnombra="DEFECTO";
			}

			echo '
				<tr>
				<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
				<td>'.$xncomprobante.'</td>
				<td>'.$xMen.'</td>
				<td>'.utf8_encode($xglosa).'</td>
				<td>'.utf8_encode($xnombra).'</td>
				<td align="right">'.number_format($registro["debe"], $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format($registro["haber"], $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format($SBalance, $NDECI, $DDECI, $DMILE).'</td>
				</tr>
			';
	}
	echo "
			</tbody>
			</table>
			</div>";
	$mysqli->close();
?>
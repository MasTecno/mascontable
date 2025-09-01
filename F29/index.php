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
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}
		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}

	function NombreCta($Cta){
		global $RutEmpresa;
		global $mysqli;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S"){
			$SQLCta="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='$RutEmpresa' AND numero='$Cta'";
		}else{
			$SQLCta="SELECT * FROM CTCuentas WHERE estado='A' AND numero='$Cta'";
		}
		$resultados = $mysqli->query($SQLCta);
		while ($registro = $resultados->fetch_assoc()) {
			$CtaImpuestoUnico=$registro['detalle'];
		}
		return $CtaImpuestoUnico;
	}

    $SQL="SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo=''";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {

		if($registro['Tipo']=="IVACredito"){
			$CtaIvaCredito=$registro['Valor'];
			$CtaIvaCreditoNombre=$CtaIvaCredito." - ".NombreCta($CtaIvaCredito);
		}
		if($registro['Tipo']=="IVADebito"){
			$CtaIvaDebito=$registro['Valor'];
			$CtaIvaDebitoNombre=$CtaIvaDebito." - ".NombreCta($CtaIvaDebito);
		}
		
        if($registro['Tipo']=="ImpUnico"){
            $CtaImpuestoUnico=$registro['Valor'];
			$CtaImpuestoUnicoNombre=$CtaImpuestoUnico." - ".NombreCta($CtaImpuestoUnico);
        }
        if($registro['Tipo']=="RetHonorarios"){
            $CtaRetencionHonorarios=$registro['Valor'];
			$CtaRetencionHonorariosNombre=$CtaRetencionHonorarios." - ".NombreCta($CtaRetencionHonorarios);
        }
        if($registro['Tipo']=="Prestamo3Sueldo"){   
            $CtaRetencion3Remuneraciones=$registro['Valor'];
			$CtaRetencion3RemuneracionesNombre=$CtaRetencion3Remuneraciones." - ".NombreCta($CtaRetencion3Remuneraciones);
        }

        if($registro['Tipo']=="Prestamo3Honorarios"){
            $CtaRetencion3Honorarios=$registro['Valor'];
			$CtaRetencion3HonorariosNombre=$CtaRetencion3Honorarios." - ".NombreCta($CtaRetencion3Honorarios);
        }
		if($registro['Tipo']=="Remanente"){
			$CtaRemanente=$registro['Valor'];
			$CtaRemanenteNombre=$CtaRemanente." - ".NombreCta($CtaRemanente);
		}
		if($registro['Tipo']=="PPM"){
			$PPMx=$registro['Valor'];
			$PPMNombre=$PPMx." - ".NombreCta($PPMx);
		}
    }

	if($_POST['SwAccion']=="P"){

		$PPM=$_POST['PPM'];

		$SQL="SELECT count(*) as T FROM CTParametrosF29 WHERE Tipo='PPM' AND RutEmpresa='$RutEmpresa' AND Periodo='$Periodo'";
		$resultados = $mysqli->query($SQL);
		$registro = $resultados->fetch_assoc();
		if($registro['T']==0) {
			$SQL="INSERT INTO CTParametrosF29 (Tipo, Valor, RutEmpresa, Periodo) VALUES ('PPM', '$PPM', '$RutEmpresa', '$Periodo')";
		} else {
			$SQL="UPDATE CTParametrosF29 SET Valor='$PPM' WHERE Tipo='PPM' AND RutEmpresa='$RutEmpresa' AND Periodo='$Periodo'";
		}
		$mysqli->query($SQL);

		header("location:index.php");
		exit;
	}

	$SQL="SELECT * FROM CTParametrosF29 WHERE Tipo='PPM' AND RutEmpresa='$RutEmpresa' AND Periodo='$Periodo'";
	$resultados = $mysqli->query($SQL);
	$registro = $resultados->fetch_assoc();
	$PPM=$registro['Valor'];

	if($PPM=="") {
		$PPM=20.5;
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">
		<script>
			function procesar() {
				document.getElementById("SwAccion").value = "P";
				document.form1.submit();
			}
			function configurar() {
				window.location.href = "configF29.php";
			}
		</script>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<input type="hidden" id="SwAccion" name="SwAccion" value="">
			<br>

			<div class="col-sm-10 col-sm-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Propuesta de Impuestos F29</h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-2">
										<div class="input-group">
											<span class="input-group-addon">Periodo</span>
											<input type="text" class="form-control text-right" id="Periodo" name="Periodo" value="<?php echo $Periodo; ?>" disabled>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="input-group">
											<span class="input-group-addon">% PPM</span>	
											<input type="text" class="form-control text-right" id="PPM" name="PPM" value="<?php echo $PPM; ?>">
										</div>
									</div>
									<div class="col-sm-2">
										<button type="button" class="btn btn-success" id="btnProcesar" onclick="procesar()"> <i class="fa fa-play"></i> Procesar</button>
									</div>
									<div class="col-sm-2">
										<button type="button" class="btn btn-warning" id="btnConfigurar" onclick="configurar()"> <i class="fa fa-cog"></i> Configurar</button>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<br>


							<div class="col-sm-12">
								<table class="table table-hover table-condensed table-bordered">
									<thead>
										<tr style="background-color:#d9edf7;">
											<th width="30%">Concepto</th>
											<th width="20%" style="text-align: right;">Cuenta Contable</th>
											<th width="5%" style="text-align: center;">Código</th>
											<th width="5%" style="text-align: right;">C. de Doc.</th>
											<th width="5%" style="text-align: center;">Código</th>
											<th width="10%" style="text-align: right;">Valores</th>
										</tr>
									</thead>
									<tbody>

										<?php 
											$RutEmpresa=$_SESSION['RUTEMPRESA'];
											$PeriodoX=$_SESSION['PERIODO'];
											$SumNet=0;
											$SumIva=0;

											$Cont=0;
											$Siva=0;
											$Sneto=0;
											$Sexento=0;

											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											////Ventas
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii = 33) and estado='A' ORDER BY id";   ////33 Facturas Electronicas Ventas
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='V' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
													$Sneto=$Sneto+$registro1["Sneto"];
												}
											}

											echo '
												<tr>
													<td>Facturas de emitidas por ventas y servicios del giro</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">503</td>
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>
													<td style="text-align: center;">502</td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';            

											$SumIva=$SumIva+$Siva;
											$Siva=0;
											$Cont=0;
											
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii = 39) and estado='A' ORDER BY id";   ////39 Boletas Electronicas Ventas
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont, numero FROM CTRegDocumentos WHERE estado='A' AND tipo='V' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$registro1["numero"];
													$Sneto=$Sneto+$registro1["Sneto"];
												}
											}

											$ano=substr($PeriodoX, 3, 4);
											$mes=substr($PeriodoX, 0, 2);

											$R=$ano.$mes."-";
											$Cont=str_replace($R, '', $Cont);

											echo '
												<tr>
													<td>Boletas</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">101</td>
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>
													<td style="text-align: center;">111</td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';            

											$SumIva=$SumIva+$Siva;
											$Siva=0;
											$Cont=0;
											
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii=48) and estado='A' ORDER BY id";   ////48 Comprobantes Transbanks Ventas
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='V' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
												}
											}

											echo '
												<tr>
													<td>Comprobantes o recibos de pago generados por medios electrónicos</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">758</td>	
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>	
													<td style="text-align: center;">759</td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';            

											$SumIva=$SumIva+$Siva;
											$Siva=0;
											$Cont=0;
											
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii=61) and estado='A' ORDER BY id";   ////61 Notas de credito Ventas
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='V' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' AND (TipoDocRef=33) ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
													$Sneto=$Sneto-$registro1["Sneto"];
												}
											}

											echo '
												<tr>
													<td>Nota de Crédito Electrónica</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">509</td>	
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>	
													<td style="text-align: center;">510</td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';  

											$SumIva=$SumIva-$Siva;
											$Siva=0;
											$Cont=0;
											
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii=61) and estado='A' ORDER BY id";   ////61 Notas de credito Ventas
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='V' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' AND (TipoDocRef=39 OR TipoDocRef=48) ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
													$Sneto=$Sneto-$registro1["Sneto"];
												}
											}

											echo '
												<tr>
													<td>Nota de Crédito Electrónica (Vales de máquinas autorizadas por el Servicio)</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">708</td>	
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>	
													<td style="text-align: center;">709</td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';  



											$SumIva=$SumIva-$Siva;

											echo '
												<tr style="background-color: #f5f5f5;">
													<td><strong>SubTotal Débitos</strong></td>
													<td style="text-align: right;">'.$CtaIvaDebitoNombre.'</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">538</td>
													<td style="text-align: right;"><strong>'.number_format($SumIva, $NDECI, $DMILE, $DMILE).'</strong></td>
												</tr>
											';    

											$TotalDebitos=$SumIva;
										?>

										<?php 
											$RutEmpresa=$_SESSION['RUTEMPRESA'];
											$PeriodoX=$_SESSION['PERIODO'];
											$SumNet=0;
											$SumIva=0;

											$Cont=0;
											$Siva=0;

											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											////Ventas
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii = 33) and estado='A' ORDER BY id";   ////33 Facturas Electronicas Compras
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='C' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
												}
											}

											echo '
												<tr>
													<td>Facturas recibidas del giro y facturas de compras emitidas</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">519</td>
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>
													<td style="text-align: center;">520</td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';            

											$SumIva=$SumIva+$Siva;
											$Siva=0;
											$Cont=0;
											
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii=61) and estado='A' ORDER BY id";   ////61 Notas de credito Compras
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='C' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
												}
											}

											echo '
												<tr>
													<td>Nota de Crédito Electrónica</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">527</td>	
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>	
													<td style="text-align: center;">528</td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';  

											// $SumIva=$SumIva-$Siva;
											// $Siva=0;
											// $Cont=0;
											
											// $SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii=34) and estado='A' ORDER BY id";   ////34 Facturas exentas Compras
											// $resultados = $mysqli->query($SQL);
											// while ($registro = $resultados->fetch_assoc()) {
											// 	$IDDOC=$registro["id"];

											// 	$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='C' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
											// 	$resultados1 = $mysqli->query($SQL1);
											// 	while ($registro1 = $resultados1->fetch_assoc()) {
											// 		$Siva=$Siva+$registro1["Siva"];
											// 		$Cont=$Cont+$registro1["Cont"];
											// 	}
											// }

											// echo '
											// 	<tr>
											// 		<td>Factura no Afecta o Exenta Electrónica</td>
											// 		<td style="text-align: right;"></td>
											// 		<td style="text-align: center;"></td>	
											// 		<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>	
											// 		<td style="text-align: center;"></td>
											// 		<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
											// 	</tr>
											// ';  

											$SumIva=$SumIva-$Siva;
											$Siva=0;
											$Cont=0;
											
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii=56) and estado='A' ORDER BY id";   ////56 Nota de Debito Compras
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='C' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
												}
											}

											echo '
												<tr>
													<td>Nota de Débito Electrónica</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;"></td>	
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>	
													<td style="text-align: center;"></td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';  

											$SumIva=$SumIva-$Siva;
											$Siva=0;
											$Cont=0;
											
											$SQL="SELECT * FROM CTTipoDocumento WHERE (tiposii=46) and estado='A' ORDER BY id";   ////46 Factura de Compras
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];

												$SQL1="SELECT sum(iva) as Siva, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='C' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$Siva=$Siva+$registro1["Siva"];
													$Cont=$Cont+$registro1["Cont"];
												}
											}

											echo '
												<tr>
													<td>Factura de Compra Electrónica</td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;"></td>	
													<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>	
													<td style="text-align: center;"></td>
													<td style="text-align: right;">'.number_format($Siva, $NDECI, $DMILE, $DMILE).'</td>
												</tr>
											';  

											echo '
												<tr style="background-color: #f5f5f5;">
													<td><strong>SubTotal Créditos</strong></td>
													<td style="text-align: right;">'.$CtaIvaCreditoNombre.'</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">511</td>
													<td style="text-align: right;"><strong>'.number_format($SumIva, $NDECI, $DMILE, $DMILE).'</strong></td>	
												</tr>
											';    

											$TotalCreditos=$SumIva;


											echo '
											<tr style="background-color:rgb(180, 180, 180);">
												<td><strong>Total Débitos - Créditos</strong></td>
												<td style="text-align: right;"></td>
												<td style="text-align: right;"></td>
												<td style="text-align: right;"></td>
												<td style="text-align: right;"></td>
												<td style="text-align: right;"><strong>'.number_format($TotalDebitos-$TotalCreditos, $NDECI, $DMILE, $DMILE).'</strong></td>	
											</tr>
										';
										$DebCred=$TotalDebitos-$TotalCreditos;
//////////////////////##############################################


											if($TotalDebitos-$TotalCreditos<0){
												echo '
													<tr>
														<td><strong>Remanente de IVA</strong></td>
														<td style="text-align: right;">'.$CtaRemanenteNombre.'</td>
														<td style="text-align: right;"></td>
														<td style="text-align: right;"></td>
														<td style="text-align: right;"></td>
														<td style="text-align: right;"><strong>'.number_format((($TotalDebitos-$TotalCreditos)*-1), $NDECI, $DMILE, $DMILE).'</strong></td>	
													</tr>
												';
												$DebCred=0;
											}

											

											$shaber=0;
											$SQL="SELECT sum(haber) as shaber, sum(debe) as sdebe FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND tipo='T' AND cuenta='$CtaImpuestoUnico'";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$shaber=$registro["shaber"];
												$sdebe=$registro["sdebe"];
											}

											echo '
												<tr>
													<td>Retención Impuesto Únicos a los Trabajadores, Según Art. 74 N1 LIR</td>
													<td style="text-align: right;">'.$CtaImpuestoUnicoNombre.'</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">48</td>	
													<td style="text-align: right;">'.number_format(($shaber-$sdebe), $NDECI, $DMILE, $DMILE).'</td>	
												</tr>
											';

											$DebCred=$DebCred+($shaber-$sdebe);

											$sretencion=0;
											$SQL="SELECT sum(retencion) as sretencion FROM CTHonorarios WHERE rutempresa='$RutEmpresa' AND periodo='$PeriodoX'";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$sretencion=$registro["sretencion"];
											}
											
											echo '
												<tr>
													<td>Retención de impuesto sobre las rentas del art. 42 N2, según art. 74 N2 LIR (Honorarios)</td>
													<td style="text-align: right;">'.$CtaRetencionHonorariosNombre.'</td> 
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">151</td>
													<td style="text-align: right;">'.number_format($sretencion, $NDECI, $DMILE, $DMILE).'</td>		
												</tr>
											';

											$DebCred=$DebCred+($sretencion);

											$shaber=0;
											$SQL="SELECT sum(haber) as shaber, sum(debe) as sdebe FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND tipo='T' AND cuenta='$CtaRetencion3Remuneraciones'";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$shaber=$registro["shaber"];
												$sdebe=$registro["sdebe"];
											}

											echo '
												<tr>
													<td>Retención sobre rentas del Art. 42 Nº1 LIR con tasa del 3% (Remuneraciones)</td>
													<td style="text-align: right;">'.$CtaRetencion3RemuneracionesNombre.'</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">49</td>
													<td style="text-align: right;">'.number_format(($shaber-$sdebe), $NDECI, $DMILE, $DMILE).'</td>	
												</tr>
											';

											$DebCred=$DebCred+($shaber-$sdebe);

											$shaber=0;
											$SQL="SELECT sum(haber) as shaber, sum(debe) as sdebe FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND tipo='T' AND cuenta='$CtaRetencion3Honorarios'";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$shaber=$registro["shaber"];
												$sdebe=$registro["sdebe"];
											}

											echo '
												<tr>
													<td>Retención sobre rentas del Art. 42 Nº1 LIR con tasa del 3% (Honorarios)</td>
													<td style="text-align: right;">'.$CtaRetencion3HonorariosNombre.'</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">155</td>
													<td style="text-align: right;">'.number_format(($shaber-$sdebe), $NDECI, $DMILE, $DMILE).'</td>	
												</tr>
											';
											
											////INICIO PPM


											$DebCred=$DebCred+($shaber-$sdebe);	
											$CalPPM=round(($Sneto*$PPM)/100);

											echo '
												<tr style="background-color: #f5f5f5;">
													<td>Base Imponible</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">563</td>
													<td style="text-align: right;">'.number_format($Sneto, $NDECI, $DMILE, $DMILE).'</td>	
												</tr>
											';

											echo '
												<tr style="background-color: #f5f5f5;">
													<td>P.P.M.</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">115</td>
													<td style="text-align: right;">'.number_format($PPM, 2, ",", ".").'</td>	
												</tr>
											';



											echo '
												<tr>
													<td>Base Imponible para cálculo P.P.M.</td>
													<td style="text-align: right;">'.$PPMNombre.'</td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: center;">26</td>
													<td style="text-align: right;">'.number_format($CalPPM, $NDECI, $DMILE, $DMILE).'</td>	
												</tr>
											';

											////FIN PPM
											$DebCred=$DebCred+$CalPPM;
											
											echo '
												<tr style="background-color:rgb(180, 180, 180);">
													<td><strong>Total impuestos a pagar</strong></td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"><strong>'.number_format($DebCred, $NDECI, $DMILE, $DMILE).'</strong></td>	
												</tr>
											';

										?>
									</tbody>
								</table>


								<h3>SIN DERECHO A CRÉDITO FISCAL</h3>
								<table class="table table-hover table-condensed table-bordered">
									<thead>
										<tr style="background-color:#d9edf7;">
											<th width="30%">Concepto</th>
											<th width="20%" style="text-align: right;">Cuenta Contable</th>
											<th width="5%" style="text-align: center;">Código</th>
											<th width="5%" style="text-align: right;">C. de Doc.</th>
											<th width="5%" style="text-align: center;">Código</th>
											<th width="10%" style="text-align: right;">Valores</th>
										</tr>
									</thead>

									<tbody>

									<?php
										echo '
										<tr>
											<td>Internas Afectas</td>
											<td style="text-align: right;"></td>
											<td style="text-align: center;">564</td>
											<td style="text-align: right;">0</td>
											<td style="text-align: right;">521</td>
											<td style="text-align: right;">0</td>
										</tr>
										<tr>
											<td>Importaciones</td>
											<td style="text-align: right;"></td>
											<td style="text-align: center;">566</td>
											<td style="text-align: right;">0</td>
											<td style="text-align: right;">560</td>
											<td style="text-align: right;">0</td>
										</tr>
										';

										$SQL1="SELECT sum(exento) as Sexento, sum(neto) as Sneto, count(*) as Cont FROM CTRegDocumentos WHERE estado='A' AND tipo='C' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND exento>0 ORDER BY rut, fecha";
										$resultados1 = $mysqli->query($SQL1);
										while ($registro1 = $resultados1->fetch_assoc()) {
											$Sexento=$Sexento+$registro1["Sexento"];
											$Cont=$Cont+$registro1["Cont"];
										}
										echo '
										<tr>
											<td>Internas exentas, o no gravadas</td>
											<td style="text-align: right;"></td>
											<td style="text-align: center;">584</td>
											<td style="text-align: right;">'.number_format($Cont, $NDECI, $DMILE, $DMILE).'</td>
											<td style="text-align: right;">562</td>
											<td style="text-align: right;">'.number_format($Sexento, $NDECI, $DMILE, $DMILE).'</td>
										</tr>
										';
									?>

									</tbody>
								</table>




							</div>
						</div>
					</div>
				</div>
			</div>


		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>

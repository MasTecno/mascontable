<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
	include '../conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    if($Periodo==""){
      header("location:frmMain.php");
      exit;
    }

    $keyasComp=$_POST['Keyimp'];
	
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='$keyasComp' AND rutempresa='$RutEmpresa' AND glosa<>''";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xfecha=date('d-m-Y',strtotime($registro["fecha"]));
		$xglosa=$registro["glosa"];

		if ($registro["tipo"]=="E") {
			$xMen=$registro["ncomprobante"]." / Egreso";
		}
		if ($registro["tipo"]=="I") {
			$xMen=$registro["ncomprobante"]." / Ingreso";  
		}
		if ($registro["tipo"]=="T") {
			$xMen=$registro["ncomprobante"]." / Traspaso";
		}
		if ($registro["tipo"]=="") {
			$xMen="Comprobante";
		}
	}

	$SQL="SELECT * FROM CTRegLibroDiarioCome WHERE keyas='$keyasComp' AND rutempresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xcomnet=$registro["comentario"];
	}

	$SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xNOM=$registro['razonsocial']; 
		$xRUT=$registro['rut']; 
		$xDIR=$registro['direccion'];   
		$xCUI=$registro['ciudad'];  
		$xGIR=$registro['giro'];    
		$xRrep=$registro['rut_representante'];    
		$xRep=$registro['representante'];    
	}

	$mysqli->close();

// $SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo LIKE '%$PeriodoX' GROUP BY id,keyas ORDER BY fecha, id, debe ASC";

?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">
			function printDiv(nombreDiv) {
				var contenido= document.getElementById(nombreDiv).innerHTML;
				var contenidoOriginal= document.body.innerHTML;

				document.body.innerHTML = contenido;

				window.print();

				document.body.innerHTML = contenidoOriginal;

				window.close();
			}			
		</script>
	</head>

	<body onload="">
	<!-- <body onload=""> -->

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">

			<div class="col-md-12 text-center">
					<br>
					<input type="button" class="btn btn-default btn-sm" onclick="printDiv('DivImp')" value="Imprimir">
					<br>
					<br>
			</div>

		<div class="col-md-12" id="DivImp">

			<style type="text/css">
				@media print {
					.panel-heading {
						background-color: #f5f5f5 !important;
					}
				}
			</style>

			<div class="col-md-2">
			</div>
			<div class="col-md-8">
				<br>
				<div class="col-md-12" style="font-size: 12px;">
					<div class="col-sm-2">
						Contribuyente:
					</div>
					<div class="col-sm-10">
						<?php echo $xNOM; ?>
					</div>

					<div class="col-sm-2">
						Rut:
					</div>
					<div class="col-sm-10">
						<?php 
							$RutPunto1=substr($xRUT,-10,2);
							$RutPunto2=substr($xRUT,-5);
							$RutPunto3=substr($xRUT,-8,3);
							echo $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;
						 ?>
					</div>

					<div class="col-sm-2">
						Domicilio:
					</div>
					<div class="col-sm-10">
						<?php echo $xDIR; ?>
					</div>

					<div class="col-sm-2">
						Cuidad:
					</div>
					<div class="col-sm-10">
						<?php echo $xCUI; ?>
					</div>

					<div class="col-sm-2">
						Rep. Legal:
					</div>
					<div class="col-sm-10">
						<?php echo $xRep; ?>
					</div>

					<div class="col-sm-2">
						Rep. Rut:
					</div>
					<div class="col-sm-10">
						<?php 
							$RutPunto1=substr($xRrep,-10,2);
							$RutPunto2=substr($xRrep,-5);
							$RutPunto3=substr($xRrep,-8,3);
							echo $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;
						?>
					</div>
					<div class="clearfix"></div>
					<br>
				</div>

				<br>
				<?php

					// $this->Cell(20,3,"Contribuyente: ",0,0,'R');
					// $this->Cell(30,3,$xNOM,0,1,'');

					// $this->Cell(20,3,"Rut: ",0,0,'R');

					// $RutPunto1=substr($xRUT,-10,2);
					// $RutPunto2=substr($xRUT,-5);
					// $RutPunto3=substr($xRUT,-8,3);
					// $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

					// $this->Cell(30,3,$srtRut,0,1,'');
					
					// $this->Cell(20,3,"Domicilio: ",0,0,'R');
					// $this->Cell(30,3,$xDIR,0,1,'');

					// $this->Cell(20,3,"Cuidad: ",0,0,'R');
					// $this->Cell(30,3,$xCUI,0,1,'');

					// $this->Cell(20,3,"Giro: ",0,0,'R');
					// $this->Cell(30,3,$xGIR,0,1,'');

					// $this->Cell(20,3,"Rep. Legal: ",0,0,'R');
					// $this->Cell(30,3,$xRep,0,1,'');

					// $RutPunto1=substr($xRrep,-10,2);
					// $RutPunto2=substr($xRrep,-5);
					// $RutPunto3=substr($xRrep,-8,3);
					// $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

					// $this->Cell(20,3,"Rep. Rut: ",0,0,'R');
					// $this->Cell(30,3,$srtRut,0,1,'');

				?>            
				<div class="clearfix"></div>

				<div class="panel panel-default">
					<div class="panel-heading text-center"><strong>Comprobante</strong></div>
					<div class="panel-body">

						<table width="100%" border="0">
							<tr>
								<td width="25%">&nbsp;</td>
								<td width="25%">&nbsp;</td>
								<td width="25%"><strong>Fecha Impresi&oacute;n</strong></td>
								<td width="25%"><?php echo date("d-m-Y"); ?></td>
							</tr>
							<tr>
								<td><strong>Tipo</strong></td>
								<td><?php echo $xMen; ?></td>
								<td><strong>Fecha</strong></td>
								<td><?php echo $xfecha; ?></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
						
					</div>
				</div>
				<div class="clearfix"></div>
				
				<div class="panel panel-default">
					<div class="panel-heading text-center"><strong>Detalle</strong></div>
					<div class="panel-body">

						<table class="table table-hover" id="grilla">
							<thead>
								<tr>
									<th width="10%">Codigo</th>
									<th>Cuenta</th>
									<th width="10%" style="text-align: right;">Debe</th>
									<th width="10%" style="text-align: right;">Haber</th>
									<th width="1%"> </th>
								</tr>
							</thead>
							<tbody>
							<?php

								$NomCont=$_SESSION['NOMBRE'];
								$RazonSocial=$_SESSION['RAZONSOCIAL'];
								$RutEmpresa=$_SESSION['RUTEMPRESA'];

								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']); 

								$SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND keyas ='$keyasComp' GROUP BY id,keyas ORDER BY fecha, id, debe ASC";

								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									if ($_SESSION["PLAN"]=="S"){
										$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}else{
										$SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
									}
									$resultados1 = $mysqli->query($SQL1);
									while ($registro1 = $resultados1->fetch_assoc()) {
										$ncuenta=strtoupper($registro1["detalle"]);
									}

									$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$registro["keyas"]."' AND glosa <>''";
									$resultados1 = $mysqli->query($SQL1);
									while ($registro1 = $resultados1->fetch_assoc()) {
										$DetalleDoc=$registro1["tipo"];
										if ($registro1["tipo"]=="E") {
											$xMen="Egreso";
										}
										if ($registro1["tipo"]=="I") {
											$xMen="Ingreso";  
										}
										if ($registro1["tipo"]=="T") {
											$xMen="Traspaso";
										}
										$ncomprobante=number_format($registro1["ncomprobante"], $NDECI, $DDECI, $DMILE);
									}

									$swCC='';
									$SqlSimple="SELECT * FROM CTCCosto WHERE id='".$registro["ccosto"]."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
									$Resul1 = $mysqli->query($SqlSimple);
									while ($Regi1 = $Resul1->fetch_assoc()) { 
										$swCC='<d style="font-size: 10px;">('.$Regi1['nombre'].')</d>';
									}
						
						
									$SQL3="SELECT * FROM CTFondo WHERE keyas='".$registro["keyas"]."' AND Cuenta='".$registro["cuenta"]."'";
									$resultados3 = $mysqli->query($SQL3);
									while ($registro3 = $resultados3->fetch_assoc()) {
										$NRazSoc="";
										$RRazSoc="";
										if($registro3["Rut"]!=""){
											$RRazSoc=$registro3["Rut"];
											$SQL4="SELECT * FROM CTCliPro WHERE rut='".$registro3["Rut"]."'";
											$resultados4 = $mysqli->query($SQL4);
											while ($registro4 = $resultados4->fetch_assoc()) { 
												$NRazSoc=$registro4["razonsocial"];
											}
										}else{
						
											$SQL4="SELECT * FROM CTFondo WHERE keyas='".$registro["keyas"]."' ORDER BY Id LIMIT 1";
											$resultados4 = $mysqli->query($SQL4);
											while ($registro4 = $resultados4->fetch_assoc()) { 
												$RRazSoc=$registro4["Rut"];
											}
						
											$SQL4="SELECT * FROM CTCliPro WHERE rut='$RRazSoc'";
											$resultados4 = $mysqli->query($SQL4);
											while ($registro4 = $resultados4->fetch_assoc()) { 
												$NRazSoc=$registro4["razonsocial"];
											}
											break;
										}
						
										$TextRef=" (".$RRazSoc." - ".$NRazSoc.")";
									}


									if($registro["glosa"]==""){
										echo '<tr>';
										echo '<td>'.$registro["cuenta"].'</td>
										<td>'.$ncuenta.'<l style="font-size: 10px;"> '.$TextRef.' '.$swCC.'</l></td>
										<td align="right"> '.number_format($registro["debe"], $NDECI, $DDECI, $DMILE).'</td>
										<td align="right"> '.number_format($registro["haber"], $NDECI, $DDECI, $DMILE).'</td>
										</tr>';

										$tgdebe=$tgdebe+$registro["debe"];
										$tghaber=$tghaber+$registro["haber"];
									}

									if($registro["glosa"]!=""){
										$Totdebe=$Totdebe+$tgdebe;
										$Tothabe=$Tothabe+$tghaber;
										$tgdebe=0;
										$tghaber=0;
										$BtsEli=0;
									}
								} 
		  
								echo '
								<tr>';
								echo '
									<td></td>
									<td><strong>Totales</strong></td>
									<td align="right"> '.number_format($Totdebe, $NDECI, $DDECI, $DMILE).'</td>
									<td align="right"> '.number_format($Tothabe, $NDECI, $DDECI, $DMILE).'</td>
								</tr>';

								

							?>
						</tbody>
						</table>


<?php
							$Tabla='
								<table class="table table-condensed" style="font-size: 11px;">
									<thead>
										<tr>
											<th>Tipo</th>
											<th>N&uacute;mero</th>
											<th>Rut</th>
											<th>R.Social</th>
											<th style="text-align: right;">Monto</th>
										</tr>
									</thead>
									<tbody>
								';
							if($DetalleDoc=="I" || $DetalleDoc=="E"){
								$SqlSimple="SELECT * FROM CTControRegDocPago WHERE keyas='$keyasComp' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
							}else{
								$SqlSimple="SELECT * FROM `CTRegDocumentos` WHERE keyas='$keyasComp' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
							}
							$ResSimple = $mysqli->query($SqlSimple);
							while ($LinSimple = $ResSimple->fetch_assoc()) {
								$XRSocial="";
								$xSigla="";

								$SqlTipDoc="SELECT * FROM `CTCliPro` WHERE rut='".$LinSimple["rut"]."' AND estado='A'";
								$xResultado = $mysqli->query($SqlTipDoc);
								while ($Linea = $xResultado->fetch_assoc()) {
									$XRSocial=$Linea["razonsocial"];
								}

								$SqlTipDoc="SELECT * FROM `CTTipoDocumento` WHERE id='".$LinSimple["id_tipodocumento"]."' AND estado='A'";
								$xResultado = $mysqli->query($SqlTipDoc);
								while ($Linea = $xResultado->fetch_assoc()) {
									$xSigla=$Linea["sigla"];
								}

								if($DetalleDoc=="I" || $DetalleDoc=="E"){
									$Monto=$LinSimple["monto"];
									$Numero=$LinSimple["ndoc"];
								}else{
									$Monto=$LinSimple["total"];
									$Numero=$LinSimple["numero"];
								}

								$Tabla=$Tabla.'
										<tr>
											<td>'.$xSigla.'</td>
											<td>'.$Numero.'</td>
											<td>'.$LinSimple["rut"].'</td>
											<td>'.$XRSocial.'</td>
											<td align="right">'.number_format($Monto, $NDECI, $DDECI, $DMILE).'</td>
										</tr>
								';
							}

							$Tabla=$Tabla.'
									</tbody>
								</table>
							';
							echo $Tabla; 
							$mysqli->close();
?>


					</div>
				</div>

				<div class="clearfix"> </div>

				<div class="col-sm-6">
					<div class="panel panel-default">
						<div class="panel-heading text-center"><strong>Comentario</strong></div>
						<div class="panel-body">
							<?php echo $xcomnet; ?>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="panel panel-default">
						<div class="panel-heading text-center"><strong>Glosa</strong></div>
						<div class="panel-body">
							<?php echo $xglosa; ?>
						</div>
					</div>
				</div>

				<div class="clearfix"> </div>
				<br> <br>
				<br> <br>
				<div class="col-sm-6 text-center">
					_____________________________________________________<br>
										Firma y Rut

				</div>
				<div class="col-sm-6 text-center">
					_____________________________________________________<br>
										Firma y Rut
				</div>


			</div>
			<div class="col-md-2">
			</div>
		</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>
	<br>
	<br>
	
	</body>
</html>
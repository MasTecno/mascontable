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

	if ($_POST['SelPeriodo']=="C") {
		$PeriodoC=$_POST['SelPeriodo'];

		$dmes = "01";
		$dano = substr($Periodo,3,4);
	}else{
		$Periodo=$_POST['SelPeriodo'];
		
		if ($Periodo=="") {
			$Periodo=$_SESSION['PERIODO'];
		}
		$dmes = substr($Periodo,0,2);
		$dano = substr($Periodo,3,4);
	}

?>
<!DOCTYPE html>
<html>
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script type="text/javascript">
		function printDiv(nombreDiv) {
			var contenido= document.getElementById(nombreDiv).innerHTML;
			var contenidoOriginal= document.body.innerHTML;
			document.body.innerHTML = contenido;
			window.print();
			document.body.innerHTML = contenidoOriginal;
		} 		
		function Upfrom(){
			form1.submit();
		}
		function RefMen(){
			form1.submit();
		}

		function GenLibro(){
			if (form1.SelPeriodo.value=="") {
				alert("No a selecionado un Periodo");
			}else{
				form1.method="POST";
				form1.target="_blank";
				form1.action="LCaja14TerExcel.php";
				form1.submit();
				form1.target="";
				form1.action="#";
			}
		}

	</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-md-12">       
			<!--  -->
			<form id="form1" name="form1" method="POST" action="#">
				<br>
				<div class="col-md-4 text-center">
					<div class="panel panel-default">
						<div class="panel-heading">Visualizar</div>
						<div class="panel-body">
							<div class="col-md-12">
								<div class="input-group">
								<span class="input-group-addon">Periodo</span>
									<select class="form-control" id="SelPeriodo" name="SelPeriodo" required onchange="Upfrom()">
										<option value="">Seleccionar</option>
										<?php
										
											if($_POST['SelPeriodo']=="C"){
												echo '<option value="C" selected>A&ntilde;o Completo</option>';
											}else{
												echo '<option value="C">A&ntilde;o Completo</option>';
											}

											$dmes = substr($Periodo,0,2);
											$dano = substr($Periodo,3,4);
											$dmes = 1;

											while ($dmes <= 12) {
												if ($dmes<=9) {
													$Xper="0".$dmes."-".$dano;
													if ($_POST['SelPeriodo']==$Xper) {
														echo '<option value="'.$Xper.'" selected>'.$Xper.'</option>';
													}else{
														echo '<option value="'.$Xper.'">'.$Xper.'</option>';
													}
													
												}else{
													$Xper=$dmes."-".$dano;
													if ($_POST['SelPeriodo']==$Xper) {
														echo '<option value="'.$Xper.'" selected>'.$Xper.'</option>';
													}else{
														echo '<option value="'.$Xper.'">'.$Xper.'</option>';
													}
												}
												$dmes++;
											}
										?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 text-center">
					<div class="panel panel-default">
						<div class="panel-heading">Opciones</div>
						<div class="panel-body">
							<div class="col-md-12">

							<div class="col-md-6 text-center">
								<div class="checkbox" style="font-size: 12px;">
									<label class="checkbox-inline"><input type="checkbox" value="MMenbrete" name="MMenbrete" onclick="RefMen()" <?php if (isset($_POST['MMenbrete']) && $_POST['MMenbrete']!="") { echo "checked"; } ?> >Visualizar Membrete</label>
								</div>
								</div>

								<div class="col-md-6 text-center">
								<div class="checkbox" style="font-size: 12px;">
									<label class="checkbox-inline"><input type="checkbox" value="Neto" name="Neto" onclick="RefMen()" <?php if (isset($_POST['Neto']) && $_POST['Neto']!="") { echo "checked"; } ?> >Monto Neto</label>
								</div>
								</div>

								<button type="button" onclick="GenLibro()"><i class='far fa-file-excel'></i> Descargar</button>
								<button type="button" onclick="printDiv('DivImp')" ><i class='fas fa-print'></i> Imprimir</button>
							</div>
						</div>
					</div>
				</div>





				<div class="clearfix"></div>
				<br>


				<br>
				<!-- <input class="form-control" id="myInput" type="text" placeholder="Buscar..."> -->
				<div class="col-md-12 text-center">
					<br>
					<!-- <input type="button" class="btn btn-default btn-sm" value=""> -->
				</div>
			</div>

			<br>
			<div class="col-sm-12 text-left" id="DivImp">
				
				<div class="col-md-6">
					<?php if (isset($_POST['MMenbrete']) && $_POST['MMenbrete']!="") { ?>
						
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


									if (strlen($xRUT)==9) {
										$RutPunto1=substr($xRUT,-10,1);
									}else{
										$RutPunto1=substr($xRUT,-10,2);
									}
									
									$RutPunto2=substr($xRUT,-5);
									$RutPunto3=substr($xRUT,-8,3);
									// $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;





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
									// $RutPunto1=substr($xRrep,-10,2);
									// $RutPunto2=substr($xRrep,-5);
									// $RutPunto3=substr($xRrep,-8,3);

									$RutPunto1=substr($xRrep,-10,2);
									$RutPunto2=substr($xRrep,-5);
									$RutPunto3=substr($xRrep,-8,3);


									if (strlen($xRrep)==9) {
										$RutPunto1=substr($xRrep,-10,1);
									}else{
										$RutPunto1=substr($xRrep,-10,2);
									}
									
									$RutPunto2=substr($xRrep,-5);
									$RutPunto3=substr($xRrep,-8,3);

									echo $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;
								?>
							</div>
							<div class="clearfix"></div>
							<br>
						</div>
					<?php } ?>
					<br>					
				</div>
				<div class="clearfix"></div>
				<br>

				<div class="col-md-12 text-center">
					<h3>LIBRO DE CAJA REGIMEN 14TER</h3>
					<h3><?php 
						if ($_POST['SelPeriodo']=="C") {
							echo "A&ntilde;o ".$dano;
						}else{
							echo $_POST['SelPeriodo'];
						}
						?></h3>
				</div>

				<div class="clearfix"></div>
				<br>
				<table class="table table-bordered" style="font-size: 12px;">
					<thead>
					<tr>
						<td rowspan="2"><strong>#</strong></td>
						<td rowspan="2"><strong>Folio</strong></td>
						<td rowspan="2" width="8%"><strong>RUT Emisor</strong></td>
						<td rowspan="2"><strong>Transacci&oacute;n /Tipo Documento</strong></td>
						<td>&nbsp;</td>
						<td colspan="2" style="text-align: center;"><strong>Operaciones afectas a IVA</strong></td>
						<td rowspan="2"><strong>Monto Operaciones Exentas</strong></td>
						<td rowspan="2"><strong>Fecha Operaci&oacute;n</strong></td>
						<td rowspan="2"><strong>Monto de Registro</strong></td>
						<td rowspan="2"><strong>Importe Recibido o pagado</strong></td>
						<td rowspan="2"><strong>Glosa</strong></td>
						<td colspan="3" style="text-align: center;"><strong>SALDO Y MOVIMIENTOS</strong></td>
						<td colspan="3" style="text-align: center;"><strong>BASE IMPONIBLE</strong></td>
					</tr>
					<tr>
						<td><strong>Libro Auxiliar</strong></td>
						<td><strong>Monto Neto</strong></td>
						<td><strong>IVA</strong></td>
						<td><strong>INGRESO</strong></td>
						<td><strong>EGRESO</strong></td>
						<td><strong>SALDO</strong></td>
						<td><strong>INGRESO</strong></td>
						<td><strong>EGRESO</strong></td>
						<td><strong>SALDO</strong></td>
					</tr>
					<thead>
					<tbody>
					<?php

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

						$l=0;
						$TIngreso=0;
						$TEgreso=0;
						$Cont=1;
						$Apert=0;

						/////busca marxcados como apertura
						if ($_SESSION["PLAN"]=="S"){
							$SQL = "SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa' AND ingreso='S'";
						}else{
							$SQL = "SELECT * FROM CTCuentas WHERE ingreso='S'";
						}

						$SqlCta="";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							if ($SqlCta!="") {
								$SqlCta=$SqlCta." OR ";
							}
							$SqlCta=$SqlCta."cuenta='".$registro['numero']."'";
						}

						$Xsql2 = "SELECT * FROM CTAsientoApertura WHERE RutEmpresa='$RutEmpresa' AND Periodo LIKE '%$XA%'";
						$Resul2 = $mysqli->query($Xsql2);
						while ($Reg2 = $Resul2->fetch_assoc()) {

							$SQL="SELECT fecha, periodo, rutempresa, keyas, tipo, debe, haber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo LIKE '%$XA%'";
							$SQL=$SQL."AND (".$SqlCta.")";
							$SQL=$SQL." AND keyas='".$Reg2['KeyAs']."' AND glosa='' ORDER BY fecha";
							$resultados = $mysqli->query($SQL);

							while ($registro = $resultados->fetch_assoc()) {
								$FecOpe=date('d-m-Y',strtotime($registro['fecha']));
								$D1=$registro['debe'];
								$D2=$registro['haber'];
	
								$MontAsient=$D1+$D2;
								$Apert=$Apert+$MontAsient;

								$TotSal=$TotSal+$Apert;
								echo '
								<tr>
									<td style="text-align: right;">'.$Cont.'</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>Apertura</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>'.$FecOpe.'</td>
									<td style="text-align: right;">'.number_format($MontAsient, $NDECI, $DDECI, $DMILE).'</td>
									<td>&nbsp;</td>
									<td>Apertura</td>
									<td style="text-align: right;">'.number_format($MontAsient, $NDECI, $DDECI, $DMILE).'</td>
									<td style="text-align: right;"></td>
									<td style="text-align: right;">'.number_format($MontAsient, $NDECI, $DDECI, $DMILE).'</td>
								</tr>
								';

								$TIngreso=$TIngreso+$MontAsient;
								$TEgreso=$TEgreso+$D2;

								$Cont++;
							}
						}

						if ($_POST['SelPeriodo']=="C") {
							$StrSql="SELECT * FROM CTRegLibroDiario WHERE tipo<>'T' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo like'%-$dano'";
						}else{
							$StrSql="SELECT * FROM CTRegLibroDiario WHERE tipo<>'T' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo = '".$_POST['SelPeriodo']."'";
						}

						if ($_SESSION["PLAN"]=="S"){
							$StrCuentas="SELECT * FROM CTCuentasEmpresa WHERE ingreso='S' AND estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
						}else{
							$StrCuentas="SELECT * FROM CTCuentas WHERE ingreso='S' AND estado='A'";
						}

						$ResulCta = $mysqli->query($StrCuentas);
						while ($RegCta = $ResulCta->fetch_assoc()) {
							if ($l==0) {
								$StrSql= $StrSql." AND (cuenta='".$RegCta['numero']."'";
								$l=1;
							}else{
								$StrSql= $StrSql." OR cuenta='".$RegCta['numero']."'";
							}
						}

						$StrSql= $StrSql.")";
						$StrSql= $StrSql." ORDER BY fecha";

						$Resul = $mysqli->query($StrSql);
						while ($Reg = $Resul->fetch_assoc()) {

							$Sw1=0;
							$Auxiliar="";
							$NumDoc="";
							$NomDoc="";

							$D1=$Reg['debe'];
							$D2=$Reg['haber'];

							$Di1=$Reg['debe'];
							$Di2=$Reg['haber'];


							$MonNet="";
							$MonIva="";
							$MonRet="";
							$Glosa2="";

							$MontAsient=$D1+$D2;
							$FecOpe=date('d-m-Y',strtotime($Reg['fecha']));

							$Sql="SELECT * FROM CTRegLibroDiario WHERE keyas='".$Reg['keyas']."' AND glosa<>''";
							$Sqldd=$Sql;
							$Resultado = $mysqli->query($Sql);
							while ($Registros = $Resultado->fetch_assoc()) {
								$Glosa=$Registros['glosa'];
								$TipMov=$Registros['tipo'];
							}

							$Sql="SELECT * FROM CTRegLibroDiarioCome WHERE keyas='".$Reg['keyas']."'";
							$Resultado = $mysqli->query($Sql);
							while ($Registros = $Resultado->fetch_assoc()) {
								$Glosa2=$Registros['comentario'];
							}

							$BasTri="S";
							/////Base Imponible
							$Sql = "SELECT * FROM CTAsientoNoBase WHERE KeyAs='".$Reg['keyas']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
							$Resultado = $mysqli->query($Sql);
							while ($Registros = $Resultado->fetch_assoc()) {
								$BasTri="N";
							}

							$Strhtml="";

							$Sql="SELECT tipo, ndoc, rut, sum(monto) as lmonto FROM CTControRegDocPago WHERE keyas='".$Reg['keyas']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' GROUP BY tipo, ndoc, rut, monto";

							$Resultado = $mysqli->query($Sql);
							while ($Registros = $Resultado->fetch_assoc()) {
								$TipDoc=$Registros['tipo'];
								$NumDoc=$Registros['ndoc'];
								$RutDoc=$Registros['rut'];
								$MonDoc=$Registros['lmonto'];

								if ($TipDoc=="V" || $TipDoc=="C") {

									$Sql1="SELECT * FROM CTRegDocumentos WHERE tipo='$TipDoc' AND numero='$NumDoc' AND rut='$RutDoc' AND total='$MonDoc' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$TipDocX=$Registros1['id_tipodocumento'];
										$MonNet=$Registros1['neto']+$Registros1['exento'];
										$MonIva=$Registros1['iva'];
										$MonRet=$Registros1['retencion'];
										$MonTot=$Registros1['total'];
										$Sw1=1;
									}

									if ($Sw1==0) {
										$Sql1="SELECT * FROM CTRegDocumentos WHERE tipo='$TipDoc' AND numero='$NumDoc' AND rut='$RutDoc' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
										$Resultado1 = $mysqli->query($Sql1);
										while ($Registros1 = $Resultado1->fetch_assoc()) {
											$TipDocX=$Registros1['id_tipodocumento'];
											$MonNet=round(($MonDoc/1.19));
											$MonIva=round($MonDoc-($MonDoc/1.19));
											$MonRet=0;
											$MonTot=$MonDoc;
											$Sw1=1;
										}
									}

									$Sql1="SELECT * FROM CTTipoDocumento WHERE id='$TipDocX'";
									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$NomDoc=$Registros1['nombre'];
										$operador=$Registros1['operador'];
									}	

									$Resta=0;
									if (isset($_POST['Neto'])){
										$Resta=$MonIva;
									}

									$pf1="";
									$sf1="";
									$pf2="";
									$sf2="";
												
									if ($operador=="R") {
										// echo $NumDoc;
										if($D1>0){
											$pf1="(";
											$sf1=")";
										}
										if($D2>0){
											$pf2="(";
											$sf2=")";
										}

										if ($TipDoc=="V") {

											$D2=($MonTot);
											$D1=0;
										}else{
											$D1=($MonTot);
											$D2=0;

										}

									}else{
										if ($TipDoc=="V") {
											$D1=($MonTot);
											$D2=0;
										}else{
											$D2=($MonTot);
											$D1=0;
										}
									}

									if ($TipDoc=="V") {
										$Auxiliar="Ventas";
									}else{
										$Auxiliar="Compras";
									}


								}


								if ($TipDoc=="H"){
									$Sql1="SELECT * FROM CTHonorarios WHERE numero='$NumDoc' AND rut='$RutDoc' AND liquido='$MonDoc' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$TipDoc=$Registros1['id_tipodocumento'];
										$MonLiq=$Registros1['liquido'];
										$MonRet=$Registros1['retencion'];
										$MonBru=$Registros1['bruto'];
										//$Sw1=1;
									}

									$Sql1="SELECT * FROM CTTipoDocumento WHERE id='$TipDoc'";
									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$NomDoc=$Registros1['nombre'];
									}

									$Auxiliar="Honorario";
									$NomDoc="HONORARIOS";
									$Sw1=2;
									$D2=$MonLiq;

									$Sql1="SELECT sum(monto) as HMonto FROM CTControRegDocPago WHERE ndoc='$NumDoc' AND rut='$RutDoc' AND tipo='H' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$MontAsient=$Registros1['HMonto'];
									}
									$D2=$MontAsient;
								}

								if ($operador=="R") {
									$TotSal=$TotSal+$D1+$D2;
								}else{
									$TotSal=$TotSal+$D1-$D2;
								}

								if($BasTri=="N"){
									$Di1=0;
									$Di2=0;
								}else{
									$Di1=$D1;
									$Di2=$D2;
								}

								// $TotSal=$TotSal+$D1-$D2;
								$TotImpo=$TotImpo+$Di1-$Di2;

								if ($Sw1==1) { //////facturas
									$Strhtml=$Strhtml. '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$NumDoc.'</td>
											<td>'.$RutDoc.'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$Auxiliar.'</td>
											<td style="text-align: right;">'.number_format($MonNet, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($MonIva, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($MonRet, $NDECI, $DDECI, $DMILE).'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">'.number_format(($D1+$D2), $NDECI, $DDECI, $DMILE).'</td>
											<td>&nbsp;</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td style="text-align: right;">'.$pf1.number_format($D1, $NDECI, $DDECI, $DMILE).$sf1.'</td>
											<td style="text-align: right;">'.$pf2.number_format($D2, $NDECI, $DDECI, $DMILE).$sf2.'</td>
											<td style="text-align: right;">'.number_format($TotSal, $NDECI, $DDECI, $DMILE).'</td>

											<td style="text-align: right;">'.$pf1.number_format($Di1, $NDECI, $DDECI, $DMILE).$sf1.'</td>
											<td style="text-align: right;">'.$pf2.number_format($Di2, $NDECI, $DDECI, $DMILE).$sf2.'</td>
											<td style="text-align: right;">'.number_format($TotImpo, $NDECI, $DDECI, $DMILE).'</td>
										</tr>
									';

									$TIngreso=$TIngreso+$D1;
									$TEgreso=$TEgreso+$D2;

									$TiIngreso=$TiIngreso+$Di1;
									$TiEgreso=$TiEgreso+$Di2;
								}

								if ($Sw1==2) { ////Honorarios
									$Strhtml=$Strhtml. '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$NumDoc.'</td>
											<td>'.$RutDoc.'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$Auxiliar.'</td>
											<td style="text-align: right;">'.number_format($MonNet, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($MonIva, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($MonRet, $NDECI, $DDECI, $DMILE).'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">'.number_format($MontAsient, $NDECI, $DDECI, $DMILE).'</td>
											<td>&nbsp;</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td style="text-align: right;">'.number_format($D1, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($D2, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($TotSal, $NDECI, $DDECI, $DMILE).'</td>

											<td style="text-align: right;">'.number_format($Di1, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($Di2, $NDECI, $DDECI, $DMILE).'</td>
											<td style="text-align: right;">'.number_format($TotImpo, $NDECI, $DDECI, $DMILE).'</td>

										</tr>
									';
									$TIngreso=$TIngreso+$D1;
									$TEgreso=$TEgreso+$D2;

									$TiIngreso=$TiIngreso+$Di1;
									$TiEgreso=$TiEgreso+$Di2;
	
								}

								$Cont++;
							}

							if ($Sw1==0) {

								if($BasTri=="N"){
									$Di1=0;
									$Di2=0;
								}else{
									$Di1=$D1;
									$Di2=$D2;
								}								

								$TotSal=$TotSal+$D1-$D2;
								$TotImpo=$TotImpo+$Di1-$Di2;
								if ($TipMov=="E") {
									$Auxiliar="Egreso";
								}
								if ($TipMov=="I") {
									$Auxiliar="Ingreso";
								}
								if ($TipMov=="T") {
									$Auxiliar="Traspaso";
								}
								if ($TipMov=="") {
									$Auxiliar="I/E";
								}

								echo '
									<tr>
										<td style="text-align: right;">'.$Cont.'</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>'.strtoupper($Glosa).'</td>
										<td>'.$Auxiliar.'</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>'.$FecOpe.'</td>
										<td style="text-align: right;">'.number_format($MontAsient, $NDECI, $DDECI, $DMILE).'</td>
										<td>&nbsp;</td>
										<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
										<td style="text-align: right;">'.number_format($D1, $NDECI, $DDECI, $DMILE).'</td>
										<td style="text-align: right;">'.number_format($D2, $NDECI, $DDECI, $DMILE).'</td>
										<td style="text-align: right;">'.number_format($TotSal, $NDECI, $DDECI, $DMILE).'</td>

										<td style="text-align: right;">'.number_format($Di1, $NDECI, $DDECI, $DMILE).'</td>
										<td style="text-align: right;">'.number_format($Di2, $NDECI, $DDECI, $DMILE).'</td>
										<td style="text-align: right;">'.number_format($TotImpo, $NDECI, $DDECI, $DMILE).'</td>
										</tr>
								';
								$TIngreso=$TIngreso+$D1;
								$TEgreso=$TEgreso+$D2;

								$TiIngreso=$TiIngreso+$Di1;
								$TiEgreso=$TiEgreso+$Di2;

							}

							echo $Strhtml;

							$Cont++;
						}

						echo '
							<tr style="font-weight: 900;">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>Totales</td>
								<td style="text-align: right;">'.number_format($TIngreso, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format($TEgreso, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format(($TIngreso-$TEgreso), $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format($TiIngreso, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format($TiEgreso, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format(($TiIngreso-$TiEgreso), $NDECI, $DDECI, $DMILE).'</td>
							</tr>
						';



						$mysqli->close();
					?>
					</tbody>
				</table >
				<br>
				<br>
				<div class="col-md-8"></div>
				<div class="col-md-4">
					<?php
					// echo '

					// <table class="table table-bordered" width="50%" style="font-size: 12px;">
					// 	<tr style="font-weight: 900;">
					// 		<td style="text-align: right;">Saldo</td>
					// 		<td style="text-align: right;">'.number_format($TIngreso, $NDECI, $DDECI, $DMILE).'</td>
					// 		<td style="text-align: right;">'.number_format($TEgreso, $NDECI, $DDECI, $DMILE).'</td>
					// 	</tr>
					// 	<tr style="font-weight: 900;">
					// 		<td style="text-align: right;">Apertura</td>
					// 		<td style="text-align: right;"></td>
					// 		<td style="text-align: right;">'.number_format($Apert, $NDECI, $DDECI, $DMILE).'</td>
					// 	</tr>
					// 	<tr style="font-weight: 900;">
					// 		<td style="text-align: right;">Saldo Final</td>
					// 		<td style="text-align: right;"></td>
					// 		<td style="text-align: right;">'.number_format(($TIngreso-$TEgreso-$Apert), $NDECI, $DDECI, $DMILE).'</td>
					// 	</tr>
					// </table>
					// ';
				?>
				</div>



			</form>
			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>
	</body>

</html>
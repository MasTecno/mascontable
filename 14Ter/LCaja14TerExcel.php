<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$Periodo=$_SESSION['PERIODO'];

	$NomArch="LibroCaja14Ter-Emp".$RutEmpresa.".xls";

	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$NomArch.""); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

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
		
		$dmes = substr($Periodo,0,2);
		$dano = substr($Periodo,3,4);
	}


?>

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
					</tr>
					<tr>
						<td><strong>Libro Auxiliar</strong></td>
						<td><strong>Monto Neto</strong></td>
						<td><strong>IVA</strong></td>
						<td><strong>INGRESO</strong></td>
						<td><strong>EGRESO</strong></td>
						<td><strong>SALDO</strong></td>
					</tr>
					<thead>
					<tbody>
					<?php

						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
						if ($_POST['SelPeriodo']=="C") {
							$StrSql="SELECT * FROM CTRegLibroDiario WHERE tipo<>'T' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo like'%-$dano'";
						}else{
							$StrSql="SELECT * FROM CTRegLibroDiario WHERE tipo<>'T' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo = '".$_POST['SelPeriodo']."'";
						}

						$l=0;
						$TIngreso=0;
						$TEgreso=0;
						$Cont=1;

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
										$MonNet=$Registros1['neto'];
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

									if ($operador=="R") {
										$MonTot=$MonTot*-1;
									}

									if ($TipDoc=="V") {
										$Auxiliar="Ventas";
										$D1=$MonTot;
										$D2=0;
									}else{
										$Auxiliar="Compras";
										$D2=$MonTot;
										$D1=0;
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
								}


								$TotSal=$TotSal+$D1-$D2;

								if ($Sw1==1) {
									$Strhtml=$Strhtml. '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$NumDoc.'</td>
											<td>'.$RutDoc.'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$Auxiliar.'</td>
											<td style="text-align: right;">'.$MonNet.'</td>
											<td style="text-align: right;">'.$MonIva.'</td>
											<td style="text-align: right;">'.$MonRet.'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">'.($D1+$D2).'</td>
											<td>&nbsp;</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td style="text-align: right;">'.$D1.'</td>
											<td style="text-align: right;">'.$D2.'</td>
											<td style="text-align: right;">'.$TotSal.'</td>
										</tr>
									';
									$TIngreso=$TIngreso+$D1;
									$TEgreso=$TEgreso+$D2;
								}

								if ($Sw1==2) {
									$Strhtml=$Strhtml. '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$NumDoc.'</td>
											<td>'.$RutDoc.'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$Auxiliar.'</td>
											<td style="text-align: right;">'.$MonNet.'</td>
											<td style="text-align: right;">'.$MonIva.'</td>
											<td style="text-align: right;">'.$MonRet.'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">'.$MontAsient.'</td>
											<td>&nbsp;</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td style="text-align: right;">'.$D1.'</td>
											<td style="text-align: right;">'.$D2.'</td>
											<td style="text-align: right;">'.$TotSal.'</td>
										</tr>
									';
									$TIngreso=$TIngreso+$D1;
									$TEgreso=$TEgreso+$D2;
								}

								$Cont++;
							}

							if ($Sw1==0) {
								$TotSal=$TotSal+$D1-$D2;
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
										<td style="text-align: right;">'.$MontAsient.'</td>
										<td>&nbsp;</td>
										<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
										<td style="text-align: right;">'.$D1.'</td>
										<td style="text-align: right;">'.$D2.'</td>
										<td style="text-align: right;">'.$TotSal.'</td>
									</tr>
								';
								$TIngreso=$TIngreso+$D1;
								$TEgreso=$TEgreso+$D2;

							}

							echo $Strhtml;

							$Cont++;
						}
					//}

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
										<td style="text-align: right;">'.$TIngreso.'</td>
										<td style="text-align: right;">'.$TEgreso.'</td>
										<td style="text-align: right;">'.($TIngreso-$TEgreso).'</td>
									</tr>
								';



						$mysqli->close();
					?>
					</tbody>
				</table>
				</table>
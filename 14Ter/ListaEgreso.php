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

	if ($_POST['SelPeriodo']=="C") {
		$PeriodoC=$_POST['SelPeriodo'];

		$dmes = "01";
		$dano = substr($Periodo,3,4);
	}else{
		$Periodo=$_POST['SelPeriodo'];
		
		$dmes = substr($Periodo,0,2);
		$dano = substr($Periodo,3,4);
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// $SQL="SELECT * FROM CTParametros WHERE estado='A'";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	if($registro['tipo']=="SEPA_MILE"){
	// 		$DMILE=$registro['valor'];  
	// 	}
	// 	if($registro['tipo']=="SEPA_DECI"){
	// 		$DDECI=$registro['valor'];  
	// 	}
	// 	if($registro['tipo']=="NUME_DECI"){
	// 		$NDECI=$registro['valor'];  
	// 	} 
	// 	if($registro['tipo']=="IVA"){
	// 		$IVA=$registro['valor'];  
	// 	} 
	// }
	// $IVA="1.".$IVA;

	// $SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEmpresa'";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$xNOM=$registro['razonsocial']; 
	// 	$xRUT=$registro['rut']; 
	// 	$xDIR=$registro['direccion'];   
	// 	$xCUI=$registro['ciudad'];  
	// 	$xGIR=$registro['giro'];    
	// 	$xRrep=$registro['rut_representante'];    
	// 	$xRep=$registro['representante'];    
	// }
	// $mysqli->close();



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

?>
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
			</div>
			<div class="clearfix"></div>
			<br>



			<div class="col-md-12">

				<div class="col-md-12 text-center">
					<h3>Libro de Ingresos y Egresos 14Ter</h3>
					<h3><?php 
						if ($_POST['SelPeriodo']=="C") {
							echo "Registros de Egresos, A&ntilde;o ".$dano;
						}else{
							echo "Registros de Egresos, ".$_POST['SelPeriodo'];
						}
						?></h3>
				</div>

				<table class="table table-striped table-bordered" id="indextable">
				<thead>
					<tr>
						<th>#</th>
						<th>N. Doc</th>
						<th>T. Doc</th>
						<th>Rut</th>
						<th>Fecha</th>
						<th>Pagados</th>
						<th>Adeudados</th>
						<th>Glosa</th>
						<th>Op. Entidades Rel.</th>
						<th>Anteriores al 31/12/2014</th>
					</tr>
				</thead>
				<tbody>
					<?php

						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
						if ($_POST['SelPeriodo']=="C") {
							$StrSql="SELECT * FROM CTRegLibroDiario WHERE tipo='E' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo like'%-$dano'";
						}else{
							$StrSql="SELECT * FROM CTRegLibroDiario WHERE tipo='E' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo = '".$_POST['SelPeriodo']."'";
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

							$D1=$Reg['haber'];
							$D1x=$Reg['haber'];
							//$D2=$Reg['haber'];

							$MonNet="";
							$MonIva="";
							$MonRet="";
							$Glosa2="";

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

							$Sql="SELECT tipo, ndoc, rut, sum(monto) as lmonto FROM CTControRegDocPago WHERE keyas='".$Reg['keyas']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' GROUP BY tipo, ndoc, rut, monto";
							// echo "<br>";
							$Resultado = $mysqli->query($Sql);
							while ($Registros = $Resultado->fetch_assoc()) {
								$TipDoc=$Registros['tipo'];
								$NumDoc=$Registros['ndoc'];
								$RutDoc=$Registros['rut'];
								$MonDoc=$Registros['lmonto'];

								if ($TipDoc=="C") {
									$Sql1="SELECT * FROM CTRegDocumentos WHERE tipo='C' AND numero='$NumDoc' AND rut='$RutDoc' AND total='$MonDoc' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
									//$Sql1z=$Sql1;
									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$TipDocX=$Registros1['id_tipodocumento'];
										$MonExt=$Registros1['exento'];
										$MonNet=$Registros1['neto'];
										$MonIva=$Registros1['iva'];
										$MonRet=$Registros1['retencion'];
										$MonTotX=$Registros1['total'];

										if (isset($_POST['ApliNeto']) && $_POST['ApliNeto']!="") {
											$MonTot=$MonExt+$MonNet+$MonRet;
										}else{
											$MonTot=$MonExt+$MonNet+$MonIva+$MonRet;
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

									$Sw1=1;
									$D1=$MonTot;//revisar
									$D2=0;
								}

								if ($TipDoc=="H"){
									$SwHONO="N";
									$Sql1="SELECT * FROM CTHonorarios WHERE numero='$NumDoc' AND rut='$RutDoc' AND liquido='$MonDoc' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$TipDoc=$Registros1['id_tipodocumento'];
										$MonLiq=$Registros1['liquido'];
										$MonRet=$Registros1['retencion'];
										$MonBru=$Registros1['bruto'];
										$SwHONO="S";
									}

									$NomDoc="HONORARIOS";

									$Sw1=2;
									$D1=$MonLiq;
									$D2=0;
								}

								if ($SwHONO=="N") {
									$D1=$MonDoc;
								}

								if ($Sw1==1) {

									if ($D1x<$D1) {
										$D1=$D1x;
									}

									if ($MonDoc==$MonTot) {
										$D1=$MonTot;
									}

									echo '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$NumDoc.'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$RutDoc.'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">'.$D1.'</td>
											<td style="text-align: right;">0</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									';

									$TD1=$TD1+$D1;
									$TD2=$TD2+$D2;
								}

								if ($Sw1==2) {
									echo '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$NumDoc.'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$RutDoc.'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">'.$D1.'</td>
											<td style="text-align: right;">0</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									';

									$TD1=$TD1+$D1;
									$TD2=$TD2+$D2;
								}
								$Cont++;
							}
							if ($Sw1==0) {
								if ($TipMov=="E") {
									$Auxiliar="EGRESO";
								}
								if ($TipMov=="I") {
									$Auxiliar="INGRESO";
								}
								if ($TipMov=="T") {
									$Auxiliar="TRASPASO";
								}
								if ($TipMov=="") {
									$Auxiliar="I/E";
								}
								$D2=$MontAsient;
								echo '
									<tr>
										<td style="text-align: right;">'.$Cont.'</td>
										<td>&nbsp;</td>
										<td>'.$Auxiliar.'</td>
										<td>&nbsp;</td>
										<td>'.$FecOpe.'</td>
										<td style="text-align: right;">'.$D1.'</td>
										<td style="text-align: right;">0</td>
										<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								';
								$TD1=$TD1+$D1;
								$TD2=$TD2+$D2;
								$Cont++;
							}
						}

						////////////Facturas sin procesar
						$D1=0;
						$D2=0;
						if ($_POST['SelPeriodo']=="C") {
							$StrSql="SELECT * FROM CTRegDocumentos WHERE tipo='C' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo like'%-$dano'";
						}else{
							$StrSql="SELECT * FROM CTRegDocumentos WHERE tipo='C' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo = '".$_POST['SelPeriodo']."'";
						}

						$StrSql= $StrSql."";
						$StrSql= $StrSql." ORDER BY fecha";

						$Resul = $mysqli->query($StrSql);
						while ($Reg = $Resul->fetch_assoc()) {

							$TipDocX=$Reg['id_tipodocumento'];
							$MonExt=$Reg['exento'];
							$MonNet=$Reg['neto'];
							$MonIva=$Reg['iva'];
							$MonRet=$Reg['retencion'];
							$MonTotX=$Reg['total'];

							// $MonTot=$Registros1['neto'];

							// if ($MonTot==0) {
							// 	$MonTot=$Registros1['total'];
							// }

							if (isset($_POST['ApliNeto']) && $_POST['ApliNeto']!="") {
								$MonTot=$MonExt+$MonNet+$MonRet;
							}else{
								$MonTot=$MonExt+$MonNet+$MonIva+$MonRet;
							}

							$D2=$MonTot;

							// $TipDocX=$Reg['id_tipodocumento'];

							$Sql1="SELECT * FROM CTTipoDocumento WHERE id='$TipDocX'";
							$Resultado1 = $mysqli->query($Sql1);
							while ($Registros1 = $Resultado1->fetch_assoc()) {
								$NomDoc=$Registros1['nombre'];
								$operador=$Registros1['operador'];
							}

							if ($operador=="R") {
								$D2=$D2*-1;
							}

							$Glosa="DOCUMENTO SIN CENTRALIZAR";
							$Glosa2="";
							$FecOpe=date('d-m-Y',strtotime($Reg['fecha']));

							if ($Reg['keyas']=="") {
								echo '
									<tr>
										<td style="text-align: right;">'.$Cont.'</td>
										<td style="text-align: right;">'.$Reg['numero'].'</td>
										<td>'.$NomDoc.'</td>
										<td>'.$Reg['rut'].'</td>
										<td>'.$FecOpe.'</td>
										<td style="text-align: right;">0</td>
										<td style="text-align: right;">'.$D2.'</td>
										<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								';
								$TD1=$TD1+$D1;
								$TD2=$TD2+$D2;
								$Cont++;
							}else{

								$Sql1="SELECT * FROM CTControRegDocPago WHERE ndoc='".$Reg['numero']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND id_tipodocumento='$TipDocX' AND rut='".$Reg['rut']."'";

								$resultados = $mysqli->query($Sql1);
								$row_cnt = $resultados->num_rows;
								if ($row_cnt==0) {

									$Sql1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$Reg['keyas']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND glosa<>''";

									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$Glosa=$Registros1['glosa'];
										$FecOpe=date('d-m-Y',strtotime($Registros1['fecha']));
									}

									echo '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$Reg['numero'].'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$Reg['rut'].'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">0</td>
											<td style="text-align: right;">'.$D2.'</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									';
									$TD1=$TD1+$D1;
									$TD2=$TD2+$D2;
									$Cont++;

								}
							}
						}

						////////////HONORARIOS sin procesar
						$D1=0;
						$D2=0;
						if ($_POST['SelPeriodo']=="C") {
							$StrSql="SELECT * FROM CTHonorarios WHERE tdocumento='R' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo like'%-$dano'";
						}else{
							$StrSql="SELECT * FROM CTHonorarios WHERE tdocumento='R' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND periodo = '".$_POST['SelPeriodo']."'";
						}

						$StrSql= $StrSql."";
						$StrSql= $StrSql." ORDER BY fecha";

						$Resul = $mysqli->query($StrSql);
						while ($Reg = $Resul->fetch_assoc()) {

							$D2=$Reg['liquido'];

							$TipDocX=$Reg['id_tipodocumento'];
							$NomDoc="HONORARIOS";

							$Glosa="DOCUMENTO SIN CENTRALIZAR";
							$Glosa2="";
							$FecOpe=date('d-m-Y',strtotime($Reg['fecha']));

							if ($Reg['movimiento']=="") {
								// echo "hhh";
								echo '
									<tr>
										<td style="text-align: right;">'.$Cont.'</td>
										<td style="text-align: right;">'.$Reg['numero'].'</td>
										<td>'.$NomDoc.'</td>
										<td>'.$Reg['rut'].'</td>
										<td>'.$FecOpe.'</td>
										<td style="text-align: right;">0</td>
										<td style="text-align: right;">'.$D2.'</td>
										<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								';
								$TD1=$TD1+$D1;
								$TD2=$TD2+$D2;
								$Cont++;
							}else{

								$Sql1="SELECT * FROM CTControRegDocPago WHERE ndoc='".$Reg['numero']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND id_tipodocumento='H' AND rut='".$Reg['rut']."'";

								$resultados = $mysqli->query($Sql1);
								$row_cnt = $resultados->num_rows;
								if ($row_cnt==0) {

									$Sql1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$Reg['movimiento']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND glosa<>''";

									$Resultado1 = $mysqli->query($Sql1);
									while ($Registros1 = $Resultado1->fetch_assoc()) {
										$Glosa=$Registros1['glosa'];
										$FecOpe=date('d-m-Y',strtotime($Registros1['fecha']));
									}

									echo '
										<tr>
											<td style="text-align: right;">'.$Cont.'</td>
											<td style="text-align: right;">'.$Reg['numero'].'</td>
											<td>'.$NomDoc.'</td>
											<td>'.$Reg['rut'].'</td>
											<td>'.$FecOpe.'</td>
											<td style="text-align: right;">0</td>
											<td style="text-align: right;">'.$D2.'</td>
											<td>* '.strtoupper($Glosa).'<br>** '.strtoupper($Glosa2).'</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									';
									$TD1=$TD1+$D1;
									$TD2=$TD2+$D2;
									$Cont++;

								}
							}
						}

						echo '
							<tr style="font-weight: 900;">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>Totales</td>
								<td style="text-align: right;">'.$TD1.'</td>
								<td style="text-align: right;">'.$TD2.'</td>
								<td style="text-align: right;"></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						';



						$mysqli->close();
					?>


				</tbody>
				</table>
			</div>

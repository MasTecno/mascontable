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

	$PeriodoX=$_POST['anoselect'];

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

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}

	$NCosot="";

	if($_POST['SelCCosto']>0){

		$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' AND id='".$_POST['SelCCosto']."' ORDER BY nombre";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$NCosot="Centro de Costos: ".$registro['nombre'];
		}
	}

	$Str=$Str.'
		<table width="100%">
			<tr style="text-align:center; font-size: 18px;">
				<td><strong>ESTADO DE RESULTADO</strong></td>	
			</tr>
            <tr style="text-align:center; font-size: 18px;">
                <td>Periodo comprendido entre Enero a Diciembre del '.$PeriodoX.'</td>
            </tr>  
			<tr style="text-align:center; font-size: 18px;">
				<td>'.$NCosot.'</td>
			</tr>
		</table>
		<br>
	';	

$Str=$Str.'
				<table class="table table-condensed table-bordered table-hover" width="100%">
						<tr style="background-color: #ccc;">
							<td width="22%"><strong>INGRESOS</strong></td>
							<td width="6%" style="text-align: right;"><strong>Enero</strong></td>
							<td width="6%" style="text-align: right;"><strong>Febrero</strong></td>
							<td width="6%" style="text-align: right;"><strong>Marzo</strong></td>
							<td width="6%" style="text-align: right;"><strong>Abril</strong></td>
							<td width="6%" style="text-align: right;"><strong>Mayo</strong></td>
							<td width="6%" style="text-align: right;"><strong>Junio</strong></td>
							<td width="6%" style="text-align: right;"><strong>Julio</strong></td>
							<td width="6%" style="text-align: right;"><strong>Agosto</strong></td>
							<td width="6%" style="text-align: right;"><strong>Septiembre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Octubre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Noviembre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Diciembre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Total</strong></td>
						</tr>

						';
						
							$ArrayIng = array();

							if ($_SESSION["PLAN"]=="S"){
								$SQLint2="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa'";
							}else{
								$SQLint2="SELECT * FROM CTCuentas WHERE 1=1";
							}

							$SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='I' ORDER BY Id";
							$resultados = $mysqli->query($SQL);
							$cont=1;
							while ($registro = $resultados->fetch_assoc()) {

								$Str=$Str.'
									<tr>
										<th style="text-align: left;">'.$registro['Nombre'].'</th>
										<th colspan="13"></th>
									</tr>
								';

								$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='$RutEmpresa'";
								$resultados1 = $mysqli->query($SQLint);
								$row_cnt = $resultados1->num_rows;
								if ($row_cnt==0) {
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='' ORDER BY Cuenta";
								}else{
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='$RutEmpresa' ORDER BY Cuenta";
								}

								$resultadosint = $mysqli->query($SQLint);
								while ($registroint = $resultadosint->fetch_assoc()) {

									$resultados2 = $mysqli->query($SQLint2." AND numero='".$registroint['Cuenta']."'");
									while ($registroint2 = $resultados2->fetch_assoc()) {
										$Xoper=$registroint2['detalle'];
									}

									$i=1;
									$toto=0;
									$linea="";
									$Str=$Str.'<tr>';
									while ($i<=13) {
										if ($i==1) {
											$linea = $linea.'<td style="text-align: right;">'.$Xoper.'</td>';
										}
										
										if ($i<10) {
											$LMes="0".$i;
										}else{
											$LMes=$i;
										}

										$valorlinea=0;
										if ($i>=1 && $i<=12) {
											$SQL3="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%".$LMes."-".$PeriodoX."%' AND estado='A' AND cuenta='".$registroint['Cuenta']."'";
											if ($_POST['SelCCosto']>0) {
												$SQL3=$SQL3." AND ccosto='".$_POST['SelCCosto']."'";
											}

											$resultados3 = $mysqli->query($SQL3);
											while ($registro3 = $resultados3->fetch_assoc()) {
												$toto=$toto+$registro3['shaber']-$registro3['sdebe'];
												$valorlinea=$valorlinea+$registro3['shaber']-$registro3['sdebe'];
											}
											$linea = $linea.'<td style="text-align: right;">'.number_format($valorlinea, $NDECI, $DDECI, $DMILE).'</td>';

											$ArrayIng[$i-1]=$ArrayIng[$i-1]+$valorlinea;
										}

										if (isset($_POST['check0'])) {
											if ($i==13 && $toto<>0) {
												$Str=$Str.$linea;
												$Str=$Str.'<td style="text-align: right;"><strong>'.number_format($toto, $NDECI, $DDECI, $DMILE).'</strong></td>';
											}
										}else{
											if ($i==13) {
												$Str=$Str.$linea;
												$Str=$Str.'<td style="text-align: right;"><strong>'.number_format($toto, $NDECI, $DDECI, $DMILE).'</strong></td>';
											}
										}

										$i++;

									}
									$Str=$Str.'</tr>';
								}
							}

						$Str=$Str.'					
					</tbody>
					<tr>
						<th>TOTALES INGRESOS</th>

					';
						$SumaIng=0;
						foreach ($ArrayIng as $i => $value) {
							$Str=$Str.'<th style="text-align: right; background-color: #ededed;"><strong>'.number_format($ArrayIng[$i], $NDECI, $DDECI, $DMILE).'</strong></th>';
							$SumaIng=$SumaIng+$ArrayIng[$i];
						}				
						$Str=$Str.'<th style="text-align: right; background-color: #ededed;"><strong>'.number_format($SumaIng, $NDECI, $DDECI, $DMILE).'</strong></th>';
					$Str=$Str.'
					</tr>
				</table>
				<br><br>';

$Str=$Str.'

				<table class="table table-condensed table-bordered table-hover" width="100%">

						<tr style="background-color: #ccc;">
							<td width="22%"><strong>EGRESOS</strong></td>
							<td width="6%" style="text-align: right;"><strong>Enero</strong></td>
							<td width="6%" style="text-align: right;"><strong>Febrero</strong></td>
							<td width="6%" style="text-align: right;"><strong>Marzo</strong></td>
							<td width="6%" style="text-align: right;"><strong>Abril</strong></td>
							<td width="6%" style="text-align: right;"><strong>Mayo</strong></td>
							<td width="6%" style="text-align: right;"><strong>Junio</strong></td>
							<td width="6%" style="text-align: right;"><strong>Julio</strong></td>
							<td width="6%" style="text-align: right;"><strong>Agosto</strong></td>
							<td width="6%" style="text-align: right;"><strong>Septiembre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Octubre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Noviembre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Diciembre</strong></td>
							<td width="6%" style="text-align: right;"><strong>Total</strong></td>
						</tr>

						';
						
							$ArrayEgr = array();

							if ($_SESSION["PLAN"]=="S"){
								$SQLint2="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa'";
							}else{
								$SQLint2="SELECT * FROM CTCuentas WHERE 1=1";
							}

							$SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='E' ORDER BY Id";
							$resultados = $mysqli->query($SQL);
							$cont=1;
							while ($registro = $resultados->fetch_assoc()) {

								$Str=$Str.'
									<tr>
										<th>'.$registro['Nombre'].'</th>
										<th colspan="13"></th>
									</tr>
								';

								$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='$RutEmpresa'";
								$resultados1 = $mysqli->query($SQLint);
								$row_cnt = $resultados1->num_rows;
								if ($row_cnt==0) {
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='' ORDER BY Cuenta";
								}else{
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='$RutEmpresa' ORDER BY Cuenta";
								}

								$resultadosint = $mysqli->query($SQLint);
								while ($registroint = $resultadosint->fetch_assoc()) {

									$resultados2 = $mysqli->query($SQLint2." AND numero='".$registroint['Cuenta']."'");
									while ($registroint2 = $resultados2->fetch_assoc()) {
										$Xoper=$registroint2['detalle'];
									}

									$i=1;
									$toto=0;
									$linea="";
									$Str=$Str.'<tr>';
									while ($i<=13) {
										if ($i==1) {
											$linea = $linea.'<td style="text-align: right;">'.$Xoper.'</td>';
										}
										
										if ($i<10) {
											$LMes="0".$i;
										}else{
											$LMes=$i;
										}

										$valorlinea=0;
										if ($i>=1 && $i<=12) {
											$SQL3="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%".$LMes."-".$PeriodoX."%' AND estado='A' AND cuenta='".$registroint['Cuenta']."'";
											if ($_POST['SelCCosto']>0) {
												$SQL3=$SQL3." AND ccosto='".$_POST['SelCCosto']."'";
											}

											$resultados3 = $mysqli->query($SQL3);
											while ($registro3 = $resultados3->fetch_assoc()) {
												$toto=$toto+$registro3['sdebe']-$registro3['shaber'];
												$valorlinea=$valorlinea+$registro3['sdebe']-$registro3['shaber'];
											}
												$valorlinea=$valorlinea*-1;
											$linea = $linea.'<td style="text-align: right;">'.number_format(($valorlinea*-1), $NDECI, $DDECI, $DMILE).'</td>';

											$ArrayEgr[$i-1]=$ArrayEgr[$i-1]+$valorlinea;
										}

										if (isset($_POST['check0'])) {
											if ($i==13 && $toto<>0) {
												$Str=$Str.$linea;
												$Str=$Str.'<td style="text-align: right;"><strong>'.number_format(($toto*-1), $NDECI, $DDECI, $DMILE).'</strong></td>';
											}
										}else{
											if ($i==13) {
												$Str=$Str.$linea;
												$Str=$Str.'<td style="text-align: right;"><strong>'.number_format($toto, $NDECI, $DDECI, $DMILE).'</strong></td>';
											}
										}

										$i++;
									}
									$Str=$Str.'</tr>';
								}
							}

						$Str=$Str.'					
					</tbody>
					<tr>
						<th>TOTALES EGRESOS</th>

					';
						$SumaEng=0;
						foreach ($ArrayEgr as $i => $value) {
							$Str=$Str.'<td style="text-align: right; background-color: #ededed;"><strong>'.number_format(($ArrayEgr[$i]*-1), $NDECI, $DDECI, $DMILE).'</strong></td>';
							$SumaEng=$SumaEng+($ArrayEgr[$i]*-1);
						}	
						$Str=$Str.'<td style="text-align: right; background-color: #ededed;"><strong>'.number_format($SumaEng, $NDECI, $DDECI, $DMILE).'</strong></td>';			

					$Str=$Str.'
					</tr>
				</table>
				
				<br><br>';

				$Str=$Str.'

					<table class="table table-condensed" width="50%" align="center">
						<tr style="background-color: #ededed;">
							<td width="33%" style="text-align: center;">Total Ingresos</td>
							<td width="33%" style="text-align: center;">Total Egresos</td>
							<td width="34%" style="text-align: center;">Resultado</td>
						</tr>
						<tr>
							<td style="text-align: center;">'.number_format($SumaIng, $NDECI, $DDECI, $DMILE).'</td>
							<td style="text-align: center;">'.number_format($SumaEng, $NDECI, $DDECI, $DMILE).'</td>
							<td style="text-align: center;">'.number_format($SumaIng-$SumaEng, $NDECI, $DDECI, $DMILE).'</td>
						</tr>
					</table>

				<br><br>
					';


// $mysqli->close();


	if ($_SERVER["REQUEST_URI"]=="/EResultado/ReportPDF.php") {
		if (isset($_POST['check_list']) && is_array($_POST['check_list']) && count($_POST['check_list'])>0) { 
			$Str=$Str.'
				<br><br>
				<table width="100%" align="center">
					<tr>
						<td>Certificamos que el presente balance ha sido confeccionado con datos proporcionados por la empresa, conjuntamente con la documentaci&oacute;n que se</td>
					</tr>
					<tr>
						<td>encuentra en los libros de contabilidad (Art. 100 del C. Tributario)</td>
					</tr>
				</table>
			';

			foreach($_POST['check_list'] as $selected) {
				$SQL="SELECT * FROM CTContadoresFirma WHERE Id='".$selected."'";
				$resultados = $mysqli->query($SQL);
				$row_cnt = $resultados->num_rows;

				while ($registro = $resultados->fetch_assoc()) {
					$NomContador=$NomContador.'<td align="center">'.$registro['Nombre'].'</td>';
					$RutContador=$RutContador.'<td align="center">'.$registro['Rut'].'<br>'.$registro['Cargo'].'</td>';
				}
			}

			$SQL="SELECT * FROM CTEmpresas WHERE rut='".$RutEmpresa."'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$representante=$registro['representante'];
				$xRrep=$registro['rut_representante'];    
			}

			$i=1;
			while ($i<=count($_POST['check_list'])){
				$TutContador=$TutContador.'<td align="center">Firma Contador(a)</td>';
				$i++;
			}

			$Str=$Str.'
				<br><br><br>
				<table width="100%" align="center">
					<tr>
						<td align="center">'.$representante.'</td>
						'.$NomContador.'
					</tr>
					<tr>
						<td align="center">'.$xRrep.'<br>'.$RazonSocial.'</td>
						'.$RutContador.'
					</tr>
					<tr>
						<td align="center">Firma Representante Legal</td>
						'.$TutContador.'
					</tr>
				</table>
			';
		}    

		$HTML=$Str;
	}else{
		if ($_SERVER["REQUEST_URI"]=="/EResultado/ReportXLS.php") {
			echo utf8_decode($Str);
		}else{
			echo $Str;
		}
	}
?>
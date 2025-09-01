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

	if ($_POST['Id14D']!="") {
		$Updtab="UPDATE CT14D SET BasTri='".$_POST['AfeImp']."' WHERE Id='".$_POST['Id14D']."'";
		$mysqli->query($Updtab);
	}

	$mysqli->close();

	// if ($_GET['Per']!="" && isset($_GET['Per'])) {
	// 	$Periodo=$_GET['Per'];
	// }

	if ($_POST['messelect']<=9 && isset($_POST['messelect'])) {
		$LMes="0".$_POST['messelect'];
		$Periodo=$LMes."-".$_POST['anoselect'];
	}else{
		$LMes=$_POST['messelect'];
		$Periodo=$LMes."-".$_POST['anoselect'];
	}

	$Ldmes = substr($Periodo,0,2);
	$Ldano = substr($Periodo,3,4);


	if ($_POST['mescon']=="S") {
		$TitReporte = $Periodo;
	}else{
		$TitReporte = substr($Periodo,3,4);
	}


	$Str=$Str.'
		<table width="100%" border="0">
			<tr>
				<td style="text-align: center;"><h4><strong>ANEXO 3. LIBRO DE CAJA CONTRIBUYENTES ACOGIDOS AL R&Eacute;GIMEN DEL ART&Iacute;CULO 14 LETRA D) DEL N&#176;3 Y N&#176;8 LETRA (a) DE LA LEY SOBRE IMPUESTO A LA RENTA</strong></h4></td>
			</tr>
		</table>
		<br>

		<table width="100%" border="0">
			<tr>
				<td width="10%">&nbsp;</td>
				<td width="90%"><strong>PERIODO</strong> '.$TitReporte.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><strong>RUT</strong> '.$RutEmpresa.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><strong>NOMBRE/RAZ&Oacute;N SOCIAL</strong> '.$RazonSocial.'</td>
			</tr>
		</table>

		<br>	
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="1">
			<thead>
				<tr>
					<th style="text-align:center;">N</th>
					<th style="text-align:center;">TIPO OPERACI&Oacute;N <br>(INGRESO = 1; EGRESO = 2)</th>
					<th style="text-align:center;">N DE DOCUMENTO</th>
					<th style="text-align:center;">TIPO DOCUMENTO</th>
					<th style="text-align:center;">RUT EMISOR</th>
					<th style="text-align:center;">FECHA OPERACI&Oacute;N</th>
					<th style="text-align:center;">GLOSA DE OPERACI&Oacute;N</th>
					<th style="text-align:right;">MONTO TOTAL FLUJO DE INGRESO O EGRESO</th>
					<th style="text-align:right;">MONTO QUE AFECTA LA BASE IMPONIBLE</th>
				</tr>
			</thead>
			<tbody>';
					function Saldo($per){
						$Col1=0;
						$Col2=0;

						$Ing1=0;
						$Ing2=0;
						$Egr1=0;
						$Egr2=0;

						$Mper = substr($per,0,2);
						$Aper = substr($per,3,4);

						$mes=(($Mper*1)-1);

						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
						if ($Mper>1) {
							while($mes>0){
								if ($mes<10) {
									$Fper="0".$mes."-".$Aper;
								}else{
									$Fper=$mes."-".$Aper;	
								}

								$SQL = "SELECT * FROM CT14D WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Periodo LIKE '%$Fper%' AND Estado='A' ORDER BY FecOpe, Id ASC";

								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									// $Col1=$registro['Total'];
									// $Col2=$registro['Total'];

									// if ($registro['Total']>$registro['Pagado']) {
									// 	$Col1=$registro['Pagado'];
									// 	$Col2=$registro['Pagado'];
									// }

									// if(isset($_POST['tos']) && $_POST['tos']=='accepted'){
									// 	if ($registro['NumDoc']!="") {
									// 		$Col2=$Col2-$registro['IVA'];

									// 		if ($registro['Total']>$registro['Pagado']) {
									// 			$Col2=round($registro['Pagado']/1.19);//-$registro['IVA'];
									// 		}
									// 	}
									// }

									// if ($registro['BasTri']=="N") {
									// 	$Col2=0;
									// }



									// if ($registro['IngEgr']=="1") {
									// 	if ($registro['TipDoc']=="61-NoCrEl") {
									// 		$Ing1=$Ing1-$Col1;
									// 		$Ing2=$Ing2-$Col2;	
									// 	}else{
									// 		$Ing1=$Ing1+$Col1;
									// 		$Ing2=$Ing2+$Col2;	
									// 	}
									// }else{
									// 	if ($registro['TipDoc']=="61-NoCrEl") {
									// 		$Egr1=$Egr1-$Col1;
									// 		$Egr2=$Egr2-$Col2;	
									// 	}else{
									// 		$Egr1=$Egr1+$Col1;
									// 		$Egr2=$Egr2+$Col2;
									// 	}
			
									// }


									// // if ($registro['IngEgr']=="1") {
									// // 	$Ing1=$Ing1+$Col1;
									// // 	$Ing2=$Ing2+$Col2;
									// // }else{
									// // 	$Egr1=$Egr1+$Col1;
									// // 	$Egr2=$Egr2+$Col2;
									// // }


									$Col1=$registro['Total'];
									$Col2=$registro['Total'];
			
									if ($registro['Total']>$registro['Pagado']) {
										$Col1=$registro['Pagado'];
										$Col2=$registro['Pagado'];
									}
			
									if(isset($_POST['tos']) && $_POST['tos']==='accepted'){
										if ($registro['NumDoc']!="") {
											$Col2=$Col2-$registro['IVA'];
			
											if ($registro['Total']>$registro['Pagado']) {
												$Col2=round($registro['Pagado']/1.19);
											}
										}
									}
			
									$pf1="";
									$sf1="";
									$pf2="";
									$sf2="";
			
									if ($registro['TipDoc']=="APERTURA" && $registro['IngEgr']=="2") {
										$pf1="(";
										$sf1=")";
										$pf2="(";
										$sf2=")";
									}
			
									if ($registro['TipDoc']=="61-NoCrEl") {
										$pf1="(";
										$sf1=")";
										$pf2="(";
										$sf2=")";
									}
			
									if ($registro['BasTri']=="N") {
										$Col2=0;
										$pf2="";
										$sf2="";
									}
			
								
			
									if ($registro['IngEgr']=="1") {
										$Ing1=$Ing1+$Col1;
										$Ing2=$Ing2+$Col2;	
									}
			
									if ($registro['IngEgr']=="2") {
										$Egr1=$Egr1+$Col1;
										$Egr2=$Egr2+$Col2;	
									}


								}
								$mes--;
							}
						}

						if ($Ing1>0 || $Egr2>0) {
							return [$Ing1,$Ing2,$Egr1,$Egr2];
						}
					}

					if ($_POST['mescon']=="S") {
						
						list($FIng1,$FIng2,$FEgr1,$FEgr2)=Saldo($Periodo);

						$Ing1=$FIng1;
						$Ing2=$FIng2;
						$Egr1=$FEgr1;
						$Egr2=$FEgr2;
						if ($Ing1!="" || $Ing2!="" || $Egr1!="" || $Egr2!="") {
							$Str=$Str.'
								<tr style="border-collapse: collapse;">
									<td align="center"></td>
									<td align="center"></td>
									<td align="center"></td>
									<td align="center"></td>
									<td align="center"></td>
									<td align="center"></td>
									<td align="center">SALDO DE ARRASTRE</td>
									<td style="text-align: right;">'.number_format(($FIng1-$FEgr1), $NDECI, $DDECI, $DMILE).'</td>
									<td style="text-align: right;">'.number_format(($FIng2-$FEgr2), $NDECI, $DDECI, $DMILE).'</td>
								</tr>
							';
						}
					}

					if ($_POST['mescon']=="S") {
						$XA=$_POST['messelect']."-".$_POST['anoselect'];

						if ($_POST['messelect']<=9 && isset($_POST['messelect'])) {
							$LMes="0".$_POST['messelect'];
							$XA=$LMes."-".$_POST['anoselect'];
						}
					}else{
						$XM = substr($Periodo,0,2);
						$XA = substr($Periodo,3,4);
						$XP =$XA."-".$XM;
					}

					$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
					$Cont=1;
					$SQL = "SELECT * FROM CT14D WHERE RutEmpresa='$RutEmpresa' AND Periodo LIKE '%$XA%' AND Estado='A' ORDER BY FecOpe, Id ASC";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {

						$Col1=$registro['Total'];
						$Col2=$registro['Total'];

						if ($registro['Total']>$registro['Pagado']) {
							$Col1=$registro['Pagado'];
							$Col2=$registro['Pagado'];
						}

						if(isset($_POST['tos']) && $_POST['tos']==='accepted'){
							if ($registro['NumDoc']!="") {
								$Col2=$Col2-$registro['IVA'];

								if ($registro['Total']>$registro['Pagado']) {
									$Col2=round($registro['Pagado']/1.19);
								}
							}
						}

						$pf1="";
						$sf1="";
						$pf2="";
						$sf2="";

						if ($registro['TipDoc']=="APERTURA" && $registro['IngEgr']=="2") {
							$pf1="(";
							$sf1=")";
							$pf2="(";
							$sf2=")";
						}

						if ($registro['TipDoc']=="61-NoCrEl") {
							$pf1="(";
							$sf1=")";
							$pf2="(";
							$sf2=")";
						}

						if ($registro['BasTri']=="N") {
							$Col2=0;
							$pf2="";
							$sf2="";
						}

						$Str=$Str.'
							<tr>
								<td align="center">'.$Cont.'</td>
								<td align="center">'.$registro['IngEgr'].'</td>
								<td align="right">'.$registro['NumDoc'].'</td>
								<td align="center">'.$registro['TipDoc'].'</td>
								<td align="right">'.$registro['Rut'].'</td>
								<td align="center">'.date('d-m-Y',strtotime($registro['FecOpe'])).'</td>
								<td align="">'.$registro['Glosa'].'</td>
								<td style="text-align: right;">'.$pf1.number_format($Col1, $NDECI, $DDECI, $DMILE).$sf1.'</td>
								<td style="text-align: right;" data-toggle="modal" data-target="#Autoriza" onclick="Conf('.$registro['Id'].')">'.$pf2.number_format($Col2, $NDECI, $DDECI, $DMILE).$sf2.'</td>
							</tr>
						';

						$Cont++;

						if ($registro['IngEgr']=="1") {
							$Ing1=$Ing1+$Col1;
							$Ing2=$Ing2+$Col2;	
						}

						if ($registro['IngEgr']=="2") {
							$Egr1=$Egr1+$Col1;
							$Egr2=$Egr2+$Col2;	
						}

					}

					if($Ing1<0){
						$Ing1=$Ing1*-1;
					}

					if($Ing2<0){
						$Ing2=$Ing2*-1;
					}


		$Str=$Str.'		
			</tbody>
		</table>
		<br>

		<table class="table table-condensed table-bordered" border="1">
			<thead>
				<tr>
					<th colspan="6" style="text-align: center;">SALDOS Y TOTALES LIBRO DE CAJA</th>
				</tr> 

				<tr>
					<th colspan="3" style="text-align: center;">FLUJO DE INGRESOS Y EGRESOS</th>
					<th colspan="3" style="text-align: center;">MONTOS QUE AFECTAN LA BASE IMPONIBLE</th>
				</tr>

				<tr>
					<th style="text-align: center;" width="16.6%">TOTAL MONTO RESUMEN FLUJO DE INGRESOS DEL PER&Iacute;ODO</th>
					<th style="text-align: center;" width="16.6%">TOTAL MONTO RESUMEN FLUJO DE EGRESOS DEL PER&Iacute;ODO</th>
					<th style="text-align: center;" width="16.6%">SALDO FLUJO DE CAJA</th>
					<th style="text-align: center;" width="16.6%">TOTAL RESUMEN DE INGRESOS</th>
					<th style="text-align: center;" width="16.6%">TOTAL RESUMEN DE EGRESOS</th>
					<th style="text-align: center;" width="16.7%">RESULTADO NETO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="text-align: right;">'.number_format($Ing1, $NDECI, $DDECI, $DMILE).'</td>
					<td style="text-align: right;">'.number_format($Egr1, $NDECI, $DDECI, $DMILE).'</td>
					<td style="text-align: right;">'.number_format(($Ing1-$Egr1), $NDECI, $DDECI, $DMILE).'</td>
					<td style="text-align: right;">'.number_format($Ing2, $NDECI, $DDECI, $DMILE).'</td>
					<td style="text-align: right;">'.number_format($Egr2, $NDECI, $DDECI, $DMILE).'</td>
					<td style="text-align: right;">'.number_format(($Ing2-$Egr2), $NDECI, $DDECI, $DMILE).'</td>
				</tr>
			</tbody>
		</table>
	';

	$mysqli->close();
	if ($_SERVER["REQUEST_URI"]=="/14D/ReportPDF.php") {
		$HTML=$Str;
	}else{
		echo $Str;
	}
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
	$swb=0;
	$SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY id  DESC";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro["glosa"]=="" && $swb==0) {
			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE id='".$registro["id"]."'");
		}else{
			$swb=1;
		}
	} 

	$StrCCosto="";

	$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$StrCCosto=$StrCCosto.'<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
	}

	function NombreCCosto($ccosto) {
		global $RutEmpresa;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$sql = "SELECT * FROM CTCCosto WHERE id='$ccosto' AND rutempresa='$RutEmpresa'";
		$resultado = $mysqli->query($sql);
		while ($registro = $resultado->fetch_assoc()) {
			return $registro['nombre'];
		}
	}

	function BuscaTipoComprobante($keyas) {
		global $RutEmpresa;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$sql = "SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas ='$keyas'";
		$resultado = $mysqli->query($sql);

		while ($registro = $resultado->fetch_assoc()) {
			if ($registro["tipo"]=="E") {
				$xMen="Egr-".$registro["ncomprobante"];
			}
			if ($registro["tipo"]=="I") {	
				$xMen="Ing-".$registro["ncomprobante"];	
			}
			if ($registro["tipo"]=="T") {
				$xMen="Tra-".$registro["ncomprobante"];
			}
		}
		return $xMen;
	}

	function BuscaGlosa($keyas) {
		global $RutEmpresa;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$sql = "SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas ='$keyas'";
		$resultado = $mysqli->query($sql);
		$registro = $resultado->fetch_assoc();
		return $registro['glosa'];
	}

	$PeriodoX=$Periodo; 
	$sdebe=0;
	$shaber=0;

	if (isset($_POST['messelect'])){
		if ($_POST['messelect']!=""){
			$dmes = $_POST['messelect'];
			$dano = $_POST['anoselect'];
			$PeriodoX=$_POST['messelect'].'-'.$_POST['anoselect'];
		}else{
			$dmes = substr($PeriodoX,0,2);
			$dano = substr($PeriodoX,3,4);
		} 
	}else{
		$dmes = substr($PeriodoX,0,2);
		$dano = substr($PeriodoX,3,4);
	}

	$sqlin = "SELECT * FROM CTParametros WHERE estado='A'";
	$resultadoin = $mysqli->query($sqlin);

	while ($registro = $resultadoin->fetch_assoc()) {
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

	$mysqli->close();
	
	$SwPDF="N";
	$NumCol=11;
	if ($_SERVER["REQUEST_URI"]=="/Mayor/frmLibMayorPDF.php") {
		$SwPDF="S";
		$NumCol=10;
	}

	if (isset($_POST['anual']) && $_POST['anual']=="1") {
		$Tut='A&Ntilde;O '.$dano; 
	}else{
		$Tut=$dmes.' - '.$dano;
	}

	$Str=$Str.'
			<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="0">
				<tr>
					<td align="center" style="font-size: 18px;"><strong>LIBRO MAYOR '.$Tut.'</strong></td>
				</tr>
			</table>
			<br>
	';


	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	if ($_SESSION["PLAN"]=="S"){
		$sql = "SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	}else{
		$sql = "SELECT * FROM CTCuentas WHERE 1=1";
	}

	if(isset($_POST['seleccue']) && $_POST['seleccue']!="0") {
		$sql=$sql." AND numero='".$_POST['seleccue']."'";
	}

	if(isset($_POST['CtaMayor']) && $_POST['CtaMayor']!="") {
		$sql=$sql." AND numero='".$_POST['CtaMayor']."'";
	}

	$sql=$sql." ORDER BY numero";

	if(isset($_POST['messelect']) && $_POST['messelect']!="") {
		$PerConsulta=$_POST['messelect'].'-'.$_POST['anoselect'];
	}else{
		$PerConsulta=$PeriodoX;
	}
	

	$Cuentas = array();
	$resultado = $mysqli->query($sql);
	while ($registro = $resultado->fetch_assoc()) {
		$AnoConsul=substr($PerConsulta,3,4);
		$SqlX="SELECT cuenta FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%$AnoConsul' AND estado='A' AND glosa='' AND cuenta='".$registro['numero']."' AND (debe<>0 OR haber<>0) GROUP BY cuenta";
		$resultadoX = $mysqli->query($SqlX);
		while ($registroX = $resultadoX->fetch_assoc()) {
			$Cuentas[] = $registroX['cuenta'];
		}
	}	

	function NombreCuenta($cuenta) {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S"){
			$sql = "SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND numero='$cuenta'";
		}else{
			$sql = "SELECT * FROM CTCuentas WHERE 1=1 AND numero='$cuenta'";
		}
		$resultado = $mysqli->query($sql);
		while ($registro = $resultado->fetch_assoc()) {
			$NombreCuenta = strtoupper($registro['detalle']);
		}
		return $NombreCuenta;
	}

	foreach ($Cuentas as $cuenta) {

		///verifico que ha registros para mostrar el codigo
		if (isset($_POST['anual']) && $_POST['anual']=="1") { 
			$AnoConsul=substr($PerConsulta,3,4);
			$sqlverifica="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%-$AnoConsul' AND estado='A' AND glosa='' AND cuenta='$cuenta'";
		}else{
			if(isset($_POST['CtaMayor']) && $_POST['CtaMayor']!="") {
				$AnoConsul=substr($PerConsulta,3,4);
				$sqlverifica="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%-$AnoConsul' AND estado='A' AND glosa='' AND cuenta='$cuenta'";
			}else{
				$sqlverifica="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo ='".$PerConsulta."' AND estado='A' AND glosa='' AND cuenta='$cuenta'";
			}
		}



		if ($_POST['SelCCosto']!="") {
			$sqlverifica=$sqlverifica." AND ccosto='".$_POST['SelCCosto']."'";
		}

		if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
			$dia = substr($_POST['fdesde'],0,2);
			$mes = substr($_POST['fdesde'],3,2);
			$ano = substr($_POST['fdesde'],6,4);

			$LFdesde=$ano."/".$mes."/".$dia;

			$dia = substr($_POST['fhasta'],0,2);
			$mes = substr($_POST['fhasta'],3,2);
			$ano = substr($_POST['fhasta'],6,4);

			$LFhasta=$ano."/".$mes."/".$dia;

			$sqlverifica=$sqlverifica." AND fecha BETWEEN '".$LFdesde."' AND '".$LFhasta."'";
		}

		$sqlverifica=$sqlverifica." AND (debe<>0 or haber<>0) ORDER BY fecha ASC, id ASC;";
		// echo $sqlverifica;
		$resul = $mysqli->query($sqlverifica);
		$row_cnt = $resul->num_rows;
		if ($row_cnt>0) {
			$Str=$Str.'
					<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="1">
					
						<tr style="background-color: #d9d9d9;">
							<td class="text-center" widtd="100%" colspan="'.$NumCol.'">'.$cuenta.' - '.NombreCuenta($cuenta).'</td>
						</tr>
						<tr style="background-color: #d9d9d9;">
							<td width="4%">D&iacute;a</td>
							<td width="4%">Mes</td>';
							if($SwPDF=='N'){
								$Str=$Str.'
									<td width="1%"></td>
									<td width="5%">Asiento</td>
									';
							}else{
								$Str=$Str.'
									<td width="6%">Asiento</td>
								';
							}
			
			$Str=$Str.'
							<td width="6%">Tipo</td>
							<td width="9%">Nro Doc</td>
							<td width="9%">C. Costo</td>
							<td width="32%">Glosa</td>
							<td width="10%" style="text-align: right;">Debe</td>
							<td width="10%" style="text-align: right;">Haber</td>
							<td width="10%" style="text-align: right;">Saldo</td>
						</tr>
			';

			$sdebe=0;
			$shaber=0;
			$xMON=0;

			$resultado1 = $mysqli->query($sqlverifica);
			while ($registro1 = $resultado1->fetch_assoc()) {

				$sqlverifica2="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas ='".$registro1["keyas"]."' AND id>'".$registro1["id"]."' LIMIT 1";
				$resultado2 = $mysqli->query($sqlverifica2);  ///TIPO DE VOUCHER
				while ($registro2 = $resultado2->fetch_assoc()) {
					$xglosa=strtoupper($registro2['glosa']);
					if ($registro2["tipo"]=="E") {
						$xMen="Egr-".$registro2["ncomprobante"];
					}
					if ($registro2["tipo"]=="I") {
						$xMen="Ing-".$registro2["ncomprobante"];	
					}
					if ($registro2["tipo"]=="T") {
						$xMen="Tra-".$registro2["ncomprobante"];
					}
				}


				if($registro1["ccosto"]!="") {
					$Nomccosto=NombreCCosto($registro1["ccosto"]);
				}
					// $Nomccosto="";
					// $sqlverifica2="SELECT * FROM CTCCosto WHERE rutempresa='$RutEmpresa' AND id ='".$registro1["ccosto"]."'";
					// $resultado2 = $mysqli->query($sqlverifica2);  ///CENTRO DE COSTP
					// while ($registro2 = $resultado2->fetch_assoc()) {
					// 	$Nomccosto=$registro2["nombre"];
					// }

					// if ($Nomccosto=="") {
					// 	$Nomccosto="";
					// }
				$ndocu="";
				$TipDocDet="";
				$LDebe=0;
				$LHaber=0;

				$sqlverifica2="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND keyas ='".$registro1["keyas"]."'";
				$resul2 = $mysqli->query($sqlverifica2);
				$row_cnt2 = $resul2->num_rows;
				if ($row_cnt2>0) {
					if ($row_cnt2==1) {
						$resultado2 = $mysqli->query($sqlverifica2);
						while ($registro2 = $resultado2->fetch_assoc()) {
							$ndocu=$registro2['numero'];
							$TipDoc=$registro2['id_tipodocumento'];
							$MontoDoc=$registro2['monto'];

							$TipDocDet="";
							$operador="S";
							$sqlverifica3="SELECT * FROM CTTipoDocumento WHERE id ='".$TipDoc."'";
							$resultado3 = $mysqli->query($sqlverifica3);
							while ($registro3 = $resultado3->fetch_assoc()) {
								$TipDocDet=strtoupper($registro3['sigla']);
								$operador=$registro3['operador'];
							}

							if ($TipDocDet=="") {
								$TipDocDet="";
							}

							if ($operador=="R") {
								$MontoDoc=$MontoDoc*-1;
							}

							$Xdia=date('d',strtotime($registro1["fecha"]));
							$Xmes=date('m',strtotime($registro1["fecha"]));

							$LDebe=$registro1["debe"];
							$LHaber=$registro1["haber"];

							$sdebe=$sdebe+$LDebe;
							$shaber=$shaber+$LHaber;

							$xMON=$xMON+$LDebe-$LHaber;

							$Str=$Str.'
								<tr>
									<td>'.$Xdia.'</td>
									<td>'.$Xmes.'</td>';
									if($SwPDF=='N'){
										$Str=$Str.'
											<td>
												<button type="button" class="btn btn-mastecno btn-xs" onclick="ModAsiento(\''.$registro1["keyas"].'\')">
													<span class="glyphicon glyphicon-pencil"></span> Editar
												</button>
											</td>										
										';
									}
							$Str=$Str.'
									<td>'.$xMen.'</td>
									<td>'.$TipDocDet.'</td>
									<td>'.$ndocu.'</td>
									<td>'.$Nomccosto.'</td>
									<td>'.$xglosa.'</td>
									<td style="text-align: right;">'.number_format($LDebe, $NDECI, $DDECI, $DMILE).'</td>
									<td style="text-align: right;">'.number_format($LHaber, $NDECI, $DDECI, $DMILE).'</td>
									<td style="text-align: right;">'.number_format(($xMON), $NDECI, $DDECI, $DMILE).'</td>
								</tr>
							';
						}
					}else{
						$sqlverifica2=$sqlverifica2. " LIMIT 1";
						$resultado2 = $mysqli->query($sqlverifica2);
						while ($registro2 = $resultado2->fetch_assoc()) {
							$ndocu=$registro2['numero'];
							$TipDoc=$registro2['id_tipodocumento'];
							$MontoDoc=$registro2['monto'];

							$TipDocDet="";
							$operador="S";
							$sqlverifica3="SELECT * FROM CTTipoDocumento WHERE id ='".$TipDoc."'";
							$resultado3 = $mysqli->query($sqlverifica3);
							while ($registro3 = $resultado3->fetch_assoc()) {
								$TipDocDet=strtoupper($registro3['sigla']);
								$operador=$registro3['operador'];
							}

							if ($TipDocDet=="") {
								$TipDocDet="";
							}

							if ($operador=="R") {
								$MontoDoc=$MontoDoc*-1;
							}

							$Xdia=date('d',strtotime($registro1["fecha"]));
							$Xmes=date('m',strtotime($registro1["fecha"]));

							$LDebe=$registro1["debe"];
							$LHaber=$registro1["haber"];

							$sdebe=$sdebe+$LDebe;
							$shaber=$shaber+$LHaber;

							$xMON=$xMON+$LDebe-$LHaber;

							// $i=1;
							// $Reg=$registro1["id"];
							// while ($i==1) {

							$xglosa=BuscaGlosa($registro1["keyas"]);
							$xMen=BuscaTipoComprobante($registro1["keyas"]);
								
								// $sqlverifica3="SELECT * FROM CTRegLibroDiario  WHERE id ='".$Reg++."' AND glosa<>''";
								// $resultado3 = $mysqli->query($sqlverifica3);
								// while ($registro3 = $resultado3->fetch_assoc()) {
								// 	$xglosa=strtoupper($registro3['glosa']);
								// 	if ($registro3["tipo"]=="E") {
								// 		$xMen="Egr-".$registro3["ncomprobante"];
								// 	}
								// 	if ($registro3["tipo"]=="I") {
								// 		$xMen="Ing-".$registro3["ncomprobante"];	
								// 	}
								// 	if ($registro3["tipo"]=="T") {
								// 		$xMen="Tra-".$registro3["ncomprobante"];
								// 	}
								// 	$i=2;
								// }
							// }

							$Str=$Str.'
								<tr>
								<td>'.$Xdia.'</td>
								<td>'.$Xmes.'</td>';
								if($SwPDF=='N'){
									$Str=$Str.'
										<td>
											<button type="button" class="btn btn-mastecno btn-xs" onclick="ModAsiento(\''.$registro1["keyas"].'\')">
												<span class="glyphicon glyphicon-pencil"></span> Editar
											</button>
										</td>										
									';
								}
							$Str=$Str.'
								<td>'.$xMen.'</td>
								<td colspan="3">MULTIPLES DOCUMENTOS</td>
								<td>'.$xglosa.'</td>
								<td style="text-align: right;">'.number_format($LDebe, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format($LHaber, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format(($xMON), $NDECI, $DDECI, $DMILE).'</td>
								</tr>
							';
						}
					}
				}else{

					$xMen=BuscaTipoComprobante($registro1["keyas"]);
					$xglosa=BuscaGlosa($registro1["keyas"]);

					// $sqlverifica2="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas ='".$registro1["keyas"]."' AND id>'".$registro1["id"]."' LIMIT 1";
					// $resultado2 = $mysqli->query($sqlverifica2);
					// while ($registro2 = $resultado2->fetch_assoc()) {
					// 	$xglosa=strtoupper($registro2['glosa']);
					// 	if ($registro2["tipo"]=="E") {
					// 		$xMen="Egr-".$registro2["ncomprobante"];
					// 	}
					// 	if ($registro2["tipo"]=="I") {
					// 		$xMen="Ing-".$registro2["ncomprobante"];	
					// 	}
					// 	if ($registro2["tipo"]=="T") {
					// 		$xMen="Tra-".$registro2["ncomprobante"];
					// 	}
					// }

					$ndocu="";
					$TipDocDet="";

					$Xdia=date('d',strtotime($registro1["fecha"]));
					$Xmes=date('m',strtotime($registro1["fecha"]));

					$LDebe=$registro1["debe"];
					$LHaber=$registro1["haber"];

					$sdebe=$sdebe+$LDebe;
					$shaber=$shaber+$LHaber;

					$xMON=$xMON+$LDebe-$LHaber;

					$Str=$Str.'
							<tr>
								<td>'.$Xdia.'</td>
								<td>'.$Xmes.'</td>';
								if($SwPDF=='N'){
									$Str=$Str.'
										<td>
											<button type="button" class="btn btn-mastecno btn-xs" onclick="ModAsiento(\''.$registro1["keyas"].'\')">
												<span class="glyphicon glyphicon-pencil"></span> Editar
											</button>
										</td>										
									';
								}	
						$Str=$Str.'
								<td>'.$xMen.'</td>
								<td>'.$TipDocDet.'</td>
								<td>'.$ndocu.'</td>
								<td>'.$Nomccosto.'</td>
								<td>'.$xglosa.'</td>
								<td style="text-align: right;">'.number_format($LDebe, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format($LHaber, $NDECI, $DDECI, $DMILE).'</td>
								<td style="text-align: right;">'.number_format(($xMON), $NDECI, $DDECI, $DMILE).'</td>
							</tr>
						';	
				}						
			}

			$xMON=$sdebe-$shaber;

			$Str=$Str.'
				<tr style="background-color: #ebebeb;">
					<td></td>
					<td></td>
					<td></td>';
					if($SwPDF=='N'){
						$Str=$Str.'<td></td>';
					}
			$Str=$Str.'
					<td></td>
					<td></td>
					<td></td>
					<td style="text-align: right;"><strong>TOTALES</strong></td>
					<td style="text-align: right;"><strong>'.number_format($sdebe, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td style="text-align: right;"><strong>'.number_format($shaber, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td style="text-align: right;">'.number_format(($xMON), $NDECI, $DDECI, $DMILE).'</td>
				</tr>
			</tbody>
			</table>
			<br><br>
			';
			$XD1=$XD1+$sdebe;
			$XD2=$XD2+$shaber;
			// $XD3=$XD3+$xMON;
		}
		
	}

	$Str=$Str.'
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="">
			<tr style="background-color: #d9d9d9;">
				<td class="text-right" colspan="7"><strong>TOTALIZADO</strong></td>
				<td width="10%" style="text-align: right;"><strong>'.number_format($XD1, $NDECI, $DDECI, $DMILE).'</strong></td>
				<td width="10%" style="text-align: right;"><strong>'.number_format($XD2, $NDECI, $DDECI, $DMILE).'</strong></td>
				<td width="10%" style="text-align: right;"><strong>'.number_format(($XD1-$XD2), $NDECI, $DDECI, $DMILE).'</strong></td>
			</tr>

		</table>';


	$mysqli->close();

	if ($_SERVER["REQUEST_URI"]=="/Mayor/frmLibMayorPDF.php") {
		$HTML=$Str;
	}else{
		if ($_SERVER["REQUEST_URI"]=="/Mayor/frmLibMayorXLS.php") {
			echo utf8_decode($Str);
		}else{
			echo $Str;
		}
	}
?>
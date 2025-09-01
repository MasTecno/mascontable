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
	// $swb=0;
	// $SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY id DESC";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	if ($registro["glosa"]=="" && $swb==0) {
	// 		$mysqli->query("DELETE FROM CTRegLibroDiario WHERE id='".$registro["id"]."'");
	// 	}else{
	// 		$swb=1;
	// 	}
	// } 

	// $StrCCosto="";

	// $SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$StrCCosto=$StrCCosto.'<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
	// }

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

	$meses = array(
		"01" => "Enero",
		"02" => "Febrero", 
		"03" => "Marzo",
		"04" => "Abril",
		"05" => "Mayo",
		"06" => "Junio",
		"07" => "Julio",
		"08" => "Agosto",
		"09" => "Septiembre",
		"10" => "Octubre",
		"11" => "Noviembre",
		"12" => "Diciembre"
	);
	
	$Tdmes = isset($meses[$dmes]) ? $meses[$dmes] : "";
	
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
			<table style="width: 100%;" border="0">
				<tr>
					<td align="center" style="font-size: 18px;"><strong>LIBRO MAYOR '.$Tut.'</strong></td>
				</tr>
			</table>
			<br>
	';


	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	if ($_SESSION["PLAN"]=="S"){
		$SqlCuentas = "SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	}else{
		$SqlCuentas = "SELECT * FROM CTCuentas WHERE 1=1";
	}

	// if(isset($_POST['seleccue']) && $_POST['seleccue']!="0") {
	// 	$sql=$sql." AND numero='".$_POST['seleccue']."'";
	// }

	// if(isset($_POST['CtaMayor']) && $_POST['CtaMayor']!="") {
	// 	$sql=$sql." AND numero='".$_POST['CtaMayor']."'";
	// }

	// $sql=$sql." ORDER BY numero";

	if(isset($_POST['messelect']) && $_POST['messelect']!="") {
		$PerConsulta=$_POST['messelect'].'-'.$_POST['anoselect'];
	}else{
		$PerConsulta=$PeriodoX;
	}


	if (isset($_POST['anual']) && $_POST['anual']=="1") { 
		$AnoConsul=substr($PerConsulta,3,4);
		$sqlverifica="SELECT rutempresa,periodo,cuenta,glosa,keyas,id,fecha,debe,haber,ccosto FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%-$AnoConsul' AND estado='A' AND glosa=''";
		$sql = "SELECT cuenta FROM CTRegLibroDiario WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND glosa='' AND periodo like '%-$AnoConsul' GROUP BY cuenta ORDER BY cuenta";
	}else{
		$sqlverifica="SELECT rutempresa,periodo,cuenta,glosa,keyas,id,fecha,debe,haber,ccosto FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo ='".$PerConsulta."' AND estado='A' AND glosa=''";
		$sql = "SELECT cuenta FROM CTRegLibroDiario WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND glosa='' AND periodo like '$PerConsulta' GROUP BY cuenta ORDER BY cuenta";
	}

	function fn_Cuenta($RutEmpresa,$idcc){
		global $mysqli;
		$Nomccosto="";
		$sqlverifica2="SELECT * FROM CTCCosto WHERE rutempresa='$RutEmpresa' AND id ='".$idcc."'";
		$resultado2 = $mysqli->query($sqlverifica2);  ///CENTRO DE COSTP
		while ($registro2 = $resultado2->fetch_assoc()) {
			$Nomccosto=$registro2["nombre"];
		}

		return $Nomccosto;
	}

	// $AnoConsul=substr($PerConsulta,3,4);

	//echo $sql; // = "SELECT cuenta FROM CTRegLibroDiario WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND glosa='' AND periodo like '%-$AnoConsul' GROUP BY cuenta ORDER BY cuenta";
	$resultado = $mysqli->query($sql);
	$ArraCuentas = array();
	while ($registro = $resultado->fetch_assoc()) {
		$ArraCuentas[] = $registro['cuenta'];
	}

	// echo "<pre>";
	// print_r($ArraCuentas);
	// echo "</pre>";

	foreach ($ArraCuentas as $cuenta) {

		$sql1 = $SqlCuentas." AND numero='".$cuenta."'";
		$resultado = $mysqli->query($sql1);

		while ($registro = $resultado->fetch_assoc()) {

			$Str=$Str.'
				<table style="width: 100%;" border="1">
					<tr>
						<td class="text-center" widtd="100%" colspan="'.$NumCol.'">'.$registro["numero"].' - '.$registro["detalle"].'</td>
					</tr>

						<tr>
							<td width="4%">D&iacute;a</td>
							<td width="4%">Mes</td>';
							if($SwPDF=='N'){
								$Str=$Str.'
									<td width="5%">Asiento</td>
									';
							}else{
								$Str=$Str.'
									<td width="6%">Asiento</td>
								';
							}
			$Str=$Str.'
							

							<td width="32%">Glosa</td>
							<td width="10%" style="text-align: right;">Debe</td>
							<td width="10%" style="text-align: right;">Haber</td>
							<td width="10%" style="text-align: right;">Saldo</td>
						</tr>';

						$sqlverificaX=$sqlverifica." AND cuenta='".$registro['numero']."' AND (debe>0 or haber>0) ORDER BY fecha ASC, id ASC;";
						$resul = $mysqli->query($sqlverificaX);
						while ($registro = $resul->fetch_assoc()) {

							$Xdia=date('d',strtotime($registro["fecha"]));
							$Xmes=date('m',strtotime($registro["fecha"]));
							$LDebe=$registro["debe"];
							$LHaber=$registro["haber"];
					
							$sdebe=$sdebe+$LDebe;
							$shaber=$shaber+$LHaber;
		
							$xMON=$xMON+$LDebe-$LHaber;

							$xglosa="";
							$xMen="";
							$Nomccosto="";

							$sqlverifica2="SELECT tipo,ncomprobante,glosa FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas ='".$registro["keyas"]."' AND id>'".$registro["id"]."' LIMIT 1";
							$resultado2 = $mysqli->query($sqlverifica2);  ///TIPO DE VOUCHER
							while ($registro2 = $resultado2->fetch_assoc()) {
								$xglosa = strtoupper($registro2['glosa']);
								$xMen = $registro2['ncomprobante'];
								$xTipo = $registro2['tipo'];

								$tiposVoucher = array(
									"E" => "Egr",
									"I" => "Ing",
									"T" => "Tra"
								);
								
								$xMen = isset($tiposVoucher[$xTipo]) ? $tiposVoucher[$xTipo]."-".$xMen : $xMen;
							}
							if($registro["ccosto"]!=0){
								$Nomccosto=fn_Cuenta($RutEmpresa,$registro["ccosto"]);
							}
							
							$Str=$Str.'
								<tr>
									<td>'.$Xdia.'</td>
									<td>'.$Xmes.'</td>
									<td>'.$xMen.'</td>
									<td>'.$xglosa.'</td>
									<td style="text-align: right;">'.number_format($LDebe, $NDECI, $DDECI, $DMILE).'</td>
									<td style="text-align: right;">'.number_format($LHaber, $NDECI, $DDECI, $DMILE).'</td>
									<td style="text-align: right;">'.number_format(($xMON), $NDECI, $DDECI, $DMILE).'</td>
								</tr>
							';
						}

						$xMON=$sdebe-$shaber;

						$Str=$Str.'
							<tr style="background-color: #ebebeb;">
								<td></td>
								<td></td>';
								if($SwPDF=='N'){
									$Str=$Str.'<td></td>';
								}
						$Str=$Str.'
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

			
			$Str=$Str.'
				</table>
			';
		}
	}


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
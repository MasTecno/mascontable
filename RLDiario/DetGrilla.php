<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	// $Periodo=$_SESSION['PERIODOPC'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

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
	$mysqli->close();

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


	if (isset($_POST['messelect']) || isset($_POST['anoselect'])) {
		if ($_POST['messelect']<=9) {
			$_SESSION['PERIODOPC']="".$_POST['messelect']."-".$_POST['anoselect'];
		}else{
			$_SESSION['PERIODOPC']=$_POST['messelect']."-".$_POST['anoselect'];     
		}
	}else{
		$_SESSION['PERIODOPC']=$_SESSION['PERIODO'];
	}

	$PeriodoX=$_SESSION['PERIODOPC'];


	if (isset($_POST['anual']) && $_POST['anual']==1) {
		$PeriodoX=substr($_SESSION['PERIODOPC'],3,4);
		$Tut='A&Ntilde;O '.$dano;
	}

	$Str=$Str.'
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="0">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>LIBRO DIARIO/VOUCHER '.$Tut.'</strong></td>
			</tr>
		</table>
		<br>
	';

	$Str=$Str.'
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="1">

			<tr style="background-color: #d9d9d9;">
				<td width="10%"><strong>Fecha</strong></td>
				<td width="10%" style="text-align: center;"><strong>Comprobante</strong></td>
				<td width="10%" style="text-align: center;"><strong>Tipo</strong></td>
				<td width="10%"><strong>CC</strong></td>
				<td width="10%"><strong>Codigo</strong></td>
				<td width="30%"><strong>Cuenta</strong></td>
				<td width="10%"><strong>Debe</strong></td>
				<td width="10%"><strong>Haber</strong></td>
			</tr>';
		
				if ($_SESSION["PLAN"]=="S"){
					$SqlCtaO="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND ";
				}else{
					$SqlCtaO="SELECT * FROM CTCuentas WHERE ";
				}
				$idActua=0;

				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
				$SqlStr="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo LIKE '%$PeriodoX' GROUP BY id,keyas ORDER BY fecha, id, debe ASC";
				$Resultado = $mysqli->query($SqlStr);
				while ($Registro = $Resultado->fetch_assoc()) {

					if($Registro["glosa"]==""){
						$ncuenta="<strong>Cta NO Existe</strong>";
						$SqlCta=$SqlCtaO."numero='".$Registro["cuenta"]."'";
						$SqlCons = $mysqli->query($SqlCta);
						while ($RegCons = $SqlCons->fetch_assoc()) {
							$ncuenta=strtoupper($RegCons["detalle"]);
						}

						$xCC="";
						$SqlCC="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='".$Registro["ccosto"]."'";
						$SqlCCr = $mysqli->query($SqlCC);
						while ($RegCC = $SqlCCr->fetch_assoc()) {
							$xCC=strtoupper($RegCC["codigo"]);
						}

						$Str=$Str.'
							<tr>
								<td>'.date('d-m-Y',strtotime($Registro["fecha"])).'</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>'.$xCC.'</td>
								<td>'.$Registro["cuenta"].'</td>
								<td>'.$ncuenta.'</td>
								<td align="right"> '.number_format($Registro["debe"], $NDECI, $DDECI, $DMILE).'</td>
								<td align="right"> '.number_format($Registro["haber"], $NDECI, $DDECI, $DMILE).'</td>
							</tr>
						';
						$tgdebe=$tgdebe+$Registro["debe"];
						$tghaber=$tghaber+$Registro["haber"];
					}

					if($Registro["glosa"]!=""){
						
						$SqlCons="SELECT * FROM CTRegLibroDiario WHERE keyas='".$Registro["keyas"]."' AND glosa <>'' AND rutempresa='$RutEmpresa' AND id='".$Registro["id"]."'";
						$ResuCons = $mysqli->query($SqlCons);
							while ($RegCons = $ResuCons->fetch_assoc()) {
							if ($RegCons["tipo"]=="E") {
								$xMen="Egreso";
							}
							if ($RegCons["tipo"]=="I") {
								$xMen="Ingreso";  
							}
							if ($RegCons["tipo"]=="T") {
								$xMen="Traspaso";
							}
							$ncomprobante=number_format($RegCons["ncomprobante"], $NDECI, $DDECI, $DMILE);
						}

						$Str=$Str.'
							<tr style="background-color: #ebebeb;"> 
								<td></td>
								<td align="center">'.$ncomprobante.'</td>
								<td align="center">'.$xMen.'</td>  
								<td colspan="3"><strong>'.strtoupper($Registro["glosa"]).'</strong></td>
								<td align="right"> '.number_format($tgdebe, $NDECI, $DDECI, $DMILE).'</td>
								<td align="right"> '.number_format($tghaber, $NDECI, $DDECI, $DMILE).'</td>
							</tr>
						';
						$Totdebe=$Totdebe+$tgdebe;
						$Tothabe=$Tothabe+$tghaber;
						$tgdebe=0;
						$tghaber=0;
					}
				}

				$Str=$Str.'
					<tr style="background-color: #d9d9d9;">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><strong>TOTALES</strong></td>
						<td align="right"> '.number_format($Totdebe, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right"> '.number_format($Tothabe, $NDECI, $DDECI, $DMILE).'</td>
					</tr>

		</table>					
				';

	$mysqli->close();

	if ($_SERVER["REQUEST_URI"]=="/RLDiario/frmLibDiarioPDF.php") {
		$HTML=$Str;
	}else{
		if ($_SERVER["REQUEST_URI"]=="/RLDiario/frmLibDiarioXLS.php") {
			echo utf8_decode($Str);
		}else{		
			echo $Str;
		}
	}
?>
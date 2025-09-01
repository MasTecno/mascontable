<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$PDFStr=0;
	if ($_SERVER["REQUEST_URI"]=="/RLComprasVentas/frmLibComVenPDF.php") {
		$PDFStr=1;
	}

	$NomCont=$_SESSION['NOMBRE'];
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

	if (isset($_POST['messelect']) || isset($_POST['anoselect'])) {
		if ($_POST['messelect']<=9) {
			$_SESSION['PERIODOPC']="0".$_POST['messelect']."-".$_POST['anoselect'];
		}else{
			$_SESSION['PERIODOPC']=$_POST['messelect']."-".$_POST['anoselect'];     
		}
	}else{
		$_SESSION['PERIODOPC']=$_SESSION['PERIODO'];
	}

	$PeriodoX=$_SESSION['PERIODOPC'];


	if (isset($_POST['anual']) && $_POST['anual']==1) {
		$PeriodoX=substr($_SESSION['PERIODOPC'],3,4);
	}
	$frm=$_POST['frm'];
	$Str="";

	if ($frm=="C") {
		$Tut='
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="0">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>LIBRO DE COMPRAS '.$PeriodoX.'</strong></td>
			</tr>
		</table>
		';
	}else{
		$Tut='
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="0">
			<tr>
				<td align="center" style="font-size: 18px;"><strong>LIBRO DE VENTAS '.$PeriodoX.'</strong></td>
			</tr>
		</table>
		';
	}

	$Str=$Str.$Tut;


$Str=$Str.'
		<table class="table-condensed table-bordered table-hover" style="width: 100%;" border="1">
			<tr style="background-color: #d9d9d9;">
				<td width="5%">Periodo</td>
				<td width="7%">Rut</td>
				<td width="15%">Razon Social</td>
				<td width="10%">Documento</td>
				<td width="7%" style="text-align: right;">N&deg; Doc</td>
				<td width="7%" style="text-align: right;">Cuenta</td>
				<td width="7%" style="text-align: center;">Procesa</td>
				<td width="7%" style="text-align: center;">Fecha</td>
				<td width="7%" style="text-align: right;">Exento</td>
				<td width="7%" style="text-align: right;">Neto</td>
				<td width="7%" style="text-align: right;">Iva</td>
				<td width="7%" style="text-align: right;">Reten/Imp.Esp.</td>
				<td width="7%" style="text-align: right;">Total</td>
			</tr>
';
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$rsocial="";
	$SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo like '%$PeriodoX' ORDER BY periodo, fecha";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$cl=1;
		
		if ($rrut!=$registro["rut"]) {
			$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
			$resultados1 = $mysqli->query($SQL1);
			$row_cnt = $resultados1->num_rows;
			if ($row_cnt==0) {
				$rsocial="";
			}else{
				$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
					$rsocial=$registro1["razonsocial"];
				}				
			}
			$rrut=$registro["rut"];
		}

		$operador=1;
		$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$nomdoc=$registro1["nombre"];
			if($registro1["operador"]=="R"){
				$operador=-1;
			}
		}

		$simbol="";
		if ($registro["lote"]!="") {
			$simbol="X";
		}

		$Str=$Str.'
			<tr>
				<td>'.$registro["periodo"].'</td>
				<td>'.$registro["rut"].'</td>
				<td>'.strtoupper($rsocial).'</td>
				<td>'.strtoupper($nomdoc).'</td>
				<td align="right">'.$registro["numero"].'</td>
				<td align="right">'.$registro["cuenta"].'</td>
				<td align="center"><strong>'.$simbol.'</strong></td>
				<td align="center">'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
				<td align="right">'.number_format(($registro["exento"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format(($registro["neto"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format(($registro["iva"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format(($registro["retencion"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format(($registro["total"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
			</tr>
		';            

		$texento=$texento+($registro["exento"]*$operador);
		$tneto=$tneto+($registro["neto"]*$operador);
		$tiva=$tiva+($registro["iva"]*$operador);
		$trete=$trete+($registro["retencion"]*$operador);
		$ttotal=$ttotal+($registro["total"]*$operador);
	}

	if ($frm=="V") {

		$SQL="SELECT sum(Neto) as Snet, sum(IVA) as Siva, sum(Total) as Stot, Periodo, DTE, keyas FROM CTBoletasDTE WHERE RutEmpresa='$RutEmpresa' AND periodo='$PeriodoX' GROUP BY Periodo, DTE ";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {

			$cl=1;

			$SQL1="SELECT * FROM CTTipoDocumento WHERE tiposii='".$registro["DTE"]."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$nomdoc=$registro1["nombre"];
			}

			$CantLote =0;
			$SQL1="SELECT count(*) as T FROM CTBoletasDTE WHERE RutEmpresa='$RutEmpresa' AND DTE='".$registro["DTE"]."' AND periodo='$PeriodoX' GROUP BY Periodo, DTE ";

			$resultados1 = $mysqli->query($SQL1);
			while ($registro1= $resultados1->fetch_assoc()) {
				$CantLote = $registro1['T'];
			}


			$SQL1="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$registro["keyas"]."' ORDER BY id LIMIT 1 OFFSET 1";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$FReg=date('d-m-Y',strtotime($registro1["fecha"]));
				$Fcta=$registro1["cuenta"];
			}

			$LExento=0;
			$LNeto=$registro["Snet"];
			$LIva=$registro["Siva"];
			$LRete=0;
			$LTotal=$registro["Stot"];

			if ($registro["DTE"]==38 || $registro["DTE"]==41) {
				$LExento=$registro["Snet"];
				$LNeto=0;
			}

			$Str=$Str.'
				<tr>
					<td>'.$registro["Periodo"].'</td>
					<td>'.$_SESSION['RUTEMPRESA'].'</td>
					<td>'.$_SESSION['RAZONSOCIAL'].'</td>
					<td>'.$nomdoc.'</td>
					<td align="right">Resumen('.$CantLote.')</td>
					<td align="center"><strong></strong></td>
					<td align="center"><strong>&alpha;</strong></td>
					<td>'.$FReg.'</td>
					<td align="right">'.number_format($LExento, $NDECI, $DDECI, $DMILE).'</td>
					<td align="right">'.number_format($LNeto, $NDECI, $DDECI, $DMILE).'</td>
					<td align="right">'.number_format($LIva, $NDECI, $DDECI, $DMILE).'</td>
					<td align="right">'.number_format($LRete, $NDECI, $DDECI, $DMILE).'</td>
					<td align="right">'.number_format($LTotal, $NDECI, $DDECI, $DMILE).'</td>
				</tr>
			';            

			$texento=$texento+$LExento;
			$tneto=$tneto+$LNeto;
			$tiva=$tiva+$LIva;
			$trete=$trete+$LRete;
			$ttotal=$ttotal+$LTotal;
		}
	}

	if ($cl==0) {
	$Str=$Str.'
		<tr>
			<td align="center" colspan="13"><strong>No hay documentos para Visualizar</strong></td>
		</tr>
	';
	}

	$mysqli->close();
	if ($cl!=0) {
	$Str=$Str.'
		<tr style="background-color: #d9d9d9;">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td align="right"><strong>Totales</strong></td>
			<td align="right"><strong>'.number_format($texento, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>'.number_format($tneto, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>'.number_format($tiva, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>'.number_format($trete, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>'.number_format($ttotal, $NDECI, $DDECI, $DMILE).'</strong></td>
		</tr>
	';
	}
$Str=$Str.'
	</table>
	<br><br>

			<h4 class="text-center">RESUMEN</h4>
			<table class="table-condensed table-bordered table-hover" style="width: 80%;" border="1">
				<tr style="background-color: #d9d9d9;">
					<td width="40%">Documento</td>
					<td width="10%">Cant. Doc.</td>
					<td style="text-align: right;" width="10%">Exento</td>
					<td style="text-align: right;" width="10%">Neto</td>
					<td style="text-align: right;" width="10%">Iva</td>
					<td style="text-align: right;" width="10%">Reten/Imp.Esp</td>
					<td style="text-align: right;" width="10%">Total</td>
				</tr>
		';

			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
			$SQL="SELECT * FROM CTTipoDocumento WHERE estado='A' ORDER BY id";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$IDDOC=$registro["id"];
				$Cont=0;
				$Sexento=0;
				$Sneto=0;
				$Siva=0;
				$Srete=0;
				$Stotal=0;

				$SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo like '%$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {

					$operador=1;
					$SQL2="SELECT * FROM CTTipoDocumento WHERE id='".$registro1["id_tipodocumento"]."'";
					$resultados2 = $mysqli->query($SQL2);
					while ($registro2 = $resultados2->fetch_assoc()) {
						$operador=1;
						if($registro2["operador"]=="R"){
							$operador=-1;
						}
					}

					$Cont=$Cont+1;
					$Sexento=$Sexento+($registro1["exento"]*$operador);
					$Sneto=$Sneto+($registro1["neto"]*$operador);
					$Siva=$Siva+($registro1["iva"]*$operador);
					$Srete=$Srete+($registro1["retencion"]*$operador);
					$Stotal=$Stotal+($registro1["total"]*$operador);
				}

				if ($Cont>0) {
					$Str=$Str. '
						<tr>
							<td>'.strtoupper($registro["nombre"]).'</td>
							<td>'.$Cont.'</td>
							<td align="right">'.number_format(($Sexento), $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format(($Sneto), $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format(($Siva), $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format(($Srete), $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format(($Stotal), $NDECI, $DDECI, $DMILE).'</td>
						</tr>
					';            
				}
				$X1=$X1+$Sexento;
				$X2=$X2+$Sneto;
				$X3=$X3+$Siva;
				$X4=$X4+$Srete;
				$X5=$X5+$Stotal;
			}

			if ($frm=="V") {
				$SQL="SELECT sum(Neto) as Snet, sum(IVA) as Siva, sum(Total) as Stot, Periodo, DTE, keyas FROM CTBoletasDTE WHERE RutEmpresa='$RutEmpresa' AND periodo like '%$PeriodoX' GROUP BY Periodo, DTE ";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {

					$SQL1="SELECT * FROM CTTipoDocumento WHERE tiposii='".$registro["DTE"]."'";
					$resultados1 = $mysqli->query($SQL1);
					while ($registro1 = $resultados1->fetch_assoc()) {
						$nomdoc=$registro1["nombre"];
					}

					$CantLote=0;
					$SQL1="SELECT count(*) as T FROM CTBoletasDTE WHERE RutEmpresa='$RutEmpresa' AND DTE='".$registro["DTE"]."' AND Periodo like '%$dano' GROUP BY Periodo, DTE ";
					$resultados1 = $mysqli->query($SQL1);
					while ($registro1= $resultados1->fetch_assoc()) {
						$CantLote = $registro1['T'];
					}

					$LExento=0;
					$LNeto=$registro["Snet"];
					$LIva=$registro["Siva"];
					$LRete=0;
					$LTotal=$registro["Stot"];

					if ($registro["DTE"]==38 || $registro["DTE"]==41) {
						$LExento=$registro["Snet"];
						$LNeto=0;
					}

					$Str=$Str. '
						<tr>
							<td>'.$nomdoc.'</td>
							<td>'.$CantLote.'</td>
							<td align="right">'.number_format($LExento, $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format($LNeto, $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format($LIva, $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format($LRete, $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format($LTotal, $NDECI, $DDECI, $DMILE).'</td>
						</tr>
					';

					$X1=$X1+$LExento;
					$X2=$X2+$LNeto;
					$X3=$X3+$LIva;
					$X4=$X4+$LRete;
					$X5=$X5+$LTotal;
				}
			}


			$Str=$Str. '
				<tr style="background-color: #d9d9d9;">
					<td></td>
					<td></td>
					<td align="right"><strong>'.number_format($X1, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($X2, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($X3, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($X4, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($X5, $NDECI, $DDECI, $DMILE).'</strong></td>
				</tr>
			';

$Str=$Str.'
    </table>
    <br><br>
     ';

	if ($_SERVER["REQUEST_URI"]=="/RLComprasVentas/frmLibComVenPDF.php") {
		$HTML=$Str;
	}else{
		echo $Str;
	}
?>
<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
?>

			<table class="table table-hover table-striped">

				<thead>
				<tr>
					<th>Fecha Doc</th>
					<th>N&deg; Doc</th>
					<th>T. Doc</th>
					<th>T. Comprobante</th>
					<th>F. Movimiento</th>
					<th>Observaci&oacute;n</th>
					<th style="text-align: right;">Adeudado</th>
					<th style="text-align: right;">Pagado</th>
					<th style="text-align: right;">Saldo</th>
				</tr>
				</thead>
				<tbody>

<?php
	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];


	if ($_POST['id_tipodocumento']=="C") {
		$tidoc="V";
		$tCliPro="C";
	}

	if ($_POST['id_tipodocumento']=="P") {
		$tidoc="C";
		$tCliPro="P";
	}

	if ($_POST['id_tipodocumento']=="H") {
		$tidoc="H";
		$tCliPro="P";
	}

	$dia = substr($_POST['fdesde'],0,2);
	$mes = substr($_POST['fdesde'],3,2);
	$ano = substr($_POST['fdesde'],6,4);

	$Lfdesde=$ano."/".$mes."/".$dia;

	$dia = substr($_POST['fhasta'],0,2);
	$mes = substr($_POST['fhasta'],3,2);
	$ano = substr($_POST['fhasta'],6,4);

	$Lfhasta=$ano."/".$mes."/".$dia;

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT tipo, valor FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if($registro['tipo']=="IVA"){
			$DIVA=$registro['valor']; 
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];  
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}

	unset($_SESSION['ListRut']);
	unset($_SESSION['ArrayRut']);
	$Indece=0;

	if ($_POST['id_tipodocumento']=="H") {
		// $SQL="SELECT rut FROM CTHonorarios WHERE rutempresa='$RutEmpresa' AND tdocumento='R' OR tdocumento='T' GROUP BY rut";
		$SQL="SELECT DISTINCT rut FROM CTHonorarios WHERE rutempresa='$RutEmpresa' AND tdocumento IN ('R', 'T');";

		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$RegRut=array(
				'Rut'=>$registro['rut']
			);

			$_SESSION['ListRut'][$Indece]=$RegRut;
			$Indece++;
		}
	}else{
		// $SQL="SELECT rut FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND tipo='$tidoc' GROUP BY rut";
		// $SQL="SELECT DISTINCT rut FROM CTRegDocumentos WHERE rutempresa = '83984200-7' AND tipo = 'V';";
		$SQL="SELECT DISTINCT rut FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND tipo='$tidoc';";

		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$RegRut=array(
				'Rut'=>$registro['rut']
			);

			$_SESSION['ListRut'][$Indece]=$RegRut;
			$Indece++;
		}
	}

	$Indece=0;
	$Str = "SELECT rut, razonsocial, tipo FROM CTCliPro WHERE tipo='$tCliPro'";

	if ($_POST['trazon']!="0") {
		$Str=$Str." AND rut='".$_POST['trazon']."'";
	}

	if ($_POST['parrut']!="") {
		$Str=$Str." AND rut LIKE '%".$_POST['parrut']."%'";
	}

	$Str=$Str." GROUP BY rut ORDER BY razonsocial";

	$resul = $mysqli->query($Str);
	while ($reg = $resul->fetch_assoc()) {
		foreach($_SESSION['ListRut'] as $indice=>$RegRut){
			if ($reg['rut']==$RegRut['Rut']) {
				$ARut=array(
					'Rut'=>$RegRut['Rut'],
					'RSocial'=>$reg['razonsocial']
				);
				$_SESSION['ArrayRut'][$Indece]=$ARut;
				$Indece++;
			}
		}
	}

		$Indece=0;
		$sTotal1=0;
		$sTotal2=0;


		foreach($_SESSION['ArrayRut'] as $indice=>$ArrayRegRut){
			$Linea="";
			$CabGrupo="";
			$DetGrupo="";
			$DetGrupoSub="";
			$Lncompro="";
			$LFConta="";
			$LPConta="";
			$xMen="";
			$LPGlosa="";
			$LCCosto="";

			$SubMonto=0;

			$sw=0;
			$Col1=0;
			$Col2=0;
			
			$CabGrupo= '
				<tr>
					<th colspan="9" style="background-color: #e8e8e8;">'.$ArrayRegRut['Rut'].' / '.$ArrayRegRut['RSocial'].'</th>
				</tr>
			';

			if ($_POST['id_tipodocumento']=="H") {

				$SQL="SELECT rutempresa, rut, fecha, numero, movimiento, liquido  FROM CTHonorarios WHERE rutempresa='$RutEmpresa'";
				$SQL=$SQL." AND rut='".$ArrayRegRut['Rut']."'";

				if ($_POST['fdesde']!="" || $_POST['fhasta']!="") {
					$SQL=$SQL." AND fecha BETWEEN '$Lfdesde' AND '$Lfhasta'";
				}

				if ($_POST['ndocu']!="" ) {
					$SQL=$SQL." AND numero LIKE '%".$_POST['ndocu']."%'";
				}

				$SQL=$SQL." ORDER BY fecha";
			}else{
				$SQL="SELECT rutempresa, rut, fecha, tipo, numero, keyas, total, id_tipodocumento FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND tipo='$tidoc'";
				$SQL=$SQL." AND rut='".$ArrayRegRut['Rut']."'";

				if ($_POST['fdesde']!="" || $_POST['fhasta']!="") {
					$SQL=$SQL." AND fecha BETWEEN '$Lfdesde' AND '$Lfhasta'";
				}

				if ($_POST['ndocu']!="" ) {
					$SQL=$SQL." AND numero LIKE '%".$_POST['ndocu']."%'";
				}

				$SQL=$SQL." ORDER BY fecha";		
			}

			$resultados = $mysqli->query($SQL);

			while ($registro = $resultados->fetch_assoc()) {

				if ($_POST['id_tipodocumento']=="H") {
					$ValKeyas=$registro["movimiento"];
					$MontoReg=$registro["liquido"];
					$XTipDocumento="H";
					$NDocu=$registro["numero"];
					$Xsigla="HONORARIOS";
				}else{
					$ValKeyas=$registro["keyas"];
					$MontoReg=$registro["total"];
					$XTipDocumento=$registro["id_tipodocumento"];
					$NDocu=$registro["numero"];
				}

				$SQLTipDoc="SELECT * FROM CTTipoDocumento WHERE id='$XTipDocumento'";
				$ResTipDoc = $mysqli->query($SQLTipDoc);
				while ($RegTipDoc = $ResTipDoc->fetch_assoc()) {
					$Xsigla=$RegTipDoc['sigla'];
					$Operador=$RegTipDoc['operador'];
				}

				$NC="";
				$LncomproRef="";
				$MontoNC=0;
				$MontoFC=0;
				///Verifico que esta centralizado o no
				if ($ValKeyas!="") {
					$NC=substr($ValKeyas,0,2);

					if ($NC=="NC"){
						$nCN="";

						/////Datos de la Factura -> desde la Nota de Credito
						$SQL1="SELECT numero, total FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND keyas='$ValKeyas' AND FolioDocRef='$NDocu'";
						$resultados1 = $mysqli->query($SQL1);
						while ($registro1 = $resultados1->fetch_assoc()) {
							$nCN="(N. Credito: ".$nCN=$registro1['numero'].")";
							$MontoFC=$registro1['total'];

							$SQL2="SELECT total FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND keyas='$ValKeyas' AND numero='$NDocu'";
							$resultados2 = $mysqli->query($SQL2);
							while ($registro2 = $resultados2->fetch_assoc()) {
								$MontoNC=$MontoNC+$registro2['total'];
							}
						}

						if($nCN==""){
							$SQL1="SELECT FolioDocRef, total FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND keyas='$ValKeyas' AND numero='$NDocu'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$nCN="(Factura: ".$registro1['FolioDocRef'].")";
								$MontoNC=$MontoNC+$registro1['total'];
							}
						}

						$SQL1="SELECT ncomprobante FROM CTRegLibroDiario WHERE keyas='$ValKeyas' AND glosa<>'' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
						$resultados1 = $mysqli->query($SQL1);
						while ($registro1 = $resultados1->fetch_assoc()) {
							$Lncompro=$registro1["ncomprobante"];
						}

						$LncomproRef=" Referencia ".$nCN;
						$LFConta="";
						$LPConta="";
						$xMen="";
						$LCCosto="";
					}

					if ($NC=="NC"){
						$SQL1="SELECT ncomprobante, fecha, periodo, glosa, tipo FROM CTRegLibroDiario WHERE keyas='$ValKeyas' AND glosa<>'' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND nfactura='$NDocu' AND rut='".$ArrayRegRut['Rut']."'";
					}else{
						$SQL1="SELECT ncomprobante, fecha, periodo, glosa, tipo FROM CTRegLibroDiario WHERE keyas='$ValKeyas' AND glosa<>'' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
					}
					
					$resultados1 = $mysqli->query($SQL1);
					while ($registro1 = $resultados1->fetch_assoc()) {
						$Lncompro=$registro1["ncomprobante"];
						$LFConta=date('d-m-Y',strtotime($registro1["fecha"]));
						$LPConta=$registro1["periodo"];
						$LPGlosa=$registro1["glosa"];

						if ($registro1["tipo"]=="E") {
							$xMen="Egreso";
						}
						if ($registro1["tipo"]=="I") {
							$xMen="Ingreso";	
						}
						if ($registro1["tipo"]=="T") {
							$xMen="Traspaso";
						}		
					}

				}else{
					$Lncompro="Sin Contabilizar".$LncomproRef;
					$LFConta="";
					$LPConta="";
					$xMen="";
					$LPGlosa="";
					$LCCosto="";
				}

				/////Cabcerera del documento
				if ($Operador=="R") {
					$SubMonto=$SubMonto-$MontoReg;
					$Col1=$Col1+0;
					$Col2=$Col2+$MontoReg;

					$DetGrupo=$DetGrupo.'
						<tr>
							<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
							<td>'.$NDocu.'</td>
							<td>'.$Xsigla.'</td>
							<td>'.$xMen.' / '.$Lncompro.$LncomproRef.'</td>
							<td>'.$LFConta.'</td>
							<td>'.$LPGlosa.'</td>
							<td align="right">0</td>
							<td align="right">'.number_format($MontoReg, $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">'.number_format($SubMonto, $NDECI, $DDECI, $DMILE).'</td>
						</tr> 
					';
				}else{

					$SubMonto=$SubMonto+$MontoReg;
					$Col1=$Col1+$MontoReg;
					$Col2=$Col2+0;
					$CopySubMonto=$SubMonto;

					$DetGrupo=$DetGrupo.'
						<tr>
							<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
							<td>'.$NDocu.'</td>
							<td>'.$Xsigla.'</td>
							<td>'.$xMen.' / '.$Lncompro.$LncomproRef.'</td>
							<td>'.$LFConta.'</td>
							<td>'.$LPGlosa.'</td>
							<td align="right">'.number_format($MontoReg, $NDECI, $DDECI, $DMILE).'</td>
							<td align="right">0</td>
							<td align="right">'.number_format($SubMonto, $NDECI, $DDECI, $DMILE).'</td>
						</tr> 
					';
				}

				$SQL1="SELECT monto, keyas FROM CTControRegDocPago WHERE rutempresa='$RutEmpresa' AND id_tipodocumento='$XTipDocumento' AND rut='".$ArrayRegRut['Rut']."' AND tipo='$tidoc' AND ndoc='$NDocu'";
				if ($_POST['fdesde']!="" || $_POST['fhasta']!="") {
					$SQL1=$SQL1." AND fecha BETWEEN '$Lfdesde' AND '$Lfhasta'";
				}
				$SQL1=$SQL1." ORDER BY fecha";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
					$xMpAGADO=$registro1['monto'];

					$SQL2="SELECT ncomprobante, fecha, periodo, glosa, tipo FROM CTRegLibroDiario WHERE keyas='".$registro1["keyas"]."' AND glosa<>'' AND ncomprobante<>0 AND rutempresa='$RutEmpresa'";
					$resultados2 = $mysqli->query($SQL2);
					while ($registro2 = $resultados2->fetch_assoc()) {
						$Tncompro=$registro2["ncomprobante"];
						$TFConta=date('d-m-Y',strtotime($registro2["fecha"]));
						$TPConta=$registro2["periodo"];
						$TPGlosa=$registro2["glosa"];

						if ($registro2["tipo"]=="E") {
							$TMen="Egreso";
						}
						if ($registro2["tipo"]=="I") {
							$TMen="Ingreso";	
						}
					}

					if ($Operador=="R") {
						$SubMonto=$SubMonto+$xMpAGADO;
						$Col1=$Col1+$xMpAGADO;
						$Col2=$Col2+0;
						$DetGrupo=$DetGrupo.'
							<tr>
								<td> </td>
								<td> </td>
								<td> </td>
								<td>'.$TMen.' / '.$Tncompro.'</td>
								<td>'.$TFConta.'</td>
								<td>'.$TPGlosa.'</td>
								<td align="right">'.number_format($xMpAGADO, $NDECI, $DDECI, $DMILE).'</td>
								<td align="right">0</td>
								<td align="right">'.number_format($SubMonto, $NDECI, $DDECI, $DMILE).'</td>
							</tr>
						';
					}else{
						$SubMonto=$SubMonto-$xMpAGADO;
						$Col1=$Col1+0;
						$Col2=$Col2+$xMpAGADO;

						$DetGrupo=$DetGrupo.'
							<tr>
								<td> </td>
								<td> </td>
								<td> </td>
								<td>'.$TMen.' / '.$Tncompro.'</td>
								<td>'.$TFConta.'</td>
								<td>'.$TPGlosa.'</td>
								<td align="right">0</td>
								<td align="right">'.number_format($xMpAGADO, $NDECI, $DDECI, $DMILE).'</td>
								<td align="right">'.number_format($SubMonto, $NDECI, $DDECI, $DMILE).'</td>
							</tr>
						';
					}

				}
			}

			if ($Lncompro!="" && $DetGrupo!="") {
				echo $CabGrupo.$DetGrupo;

				echo '
					<tr style="background-color: #faebd7;">
						<td> </td>
						<td> </td>
						<td> </td>
						<td></td>
						<td></td>
						<td>Totales</td>
						<td align="right">'.number_format($Col1, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format($Col2, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format(($Col1-$Col2), $NDECI, $DDECI, $DMILE).'</td>
					</tr>
				';

				$sTotal1=$sTotal1+$Col1;
				$sTotal2=$sTotal2+$Col2;
			}

		}



		echo '
			<tr style="background-color: #e31616; color: #fff;">
				<td> </td>
				<td> </td>
				<td> </td>
				<td></td>
				<td></td>
				<td>Totales</td>
				<td align="right">'.number_format($sTotal1, $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format($sTotal2, $NDECI, $DDECI, $DMILE).'</td>
				<td align="right">'.number_format(($sTotal1-$sTotal2), $NDECI, $DDECI, $DMILE).'</td>
			</tr>
		';
	$mysqli->close();

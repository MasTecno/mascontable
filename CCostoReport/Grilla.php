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

	// $PeriodoX=$_POST['anoselect'];
	
	if (isset($_POST['anoselect']) && $_POST['anoselect']>0) {
		$PeriLike = $_POST['anoselect'];
	}else{
		$PeriLike = substr($Periodo,3,4);
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

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}

	// $Str=$Str.'
	// 	<table width="100%">
	// 		<tr style="text-align:center; font-size: 18px;">
	// 			<td><strong>ESTADO RESULTADO</strong></td>	
	// 		</tr>
	// 		<tr style="text-align:center; font-size: 18px;">
	// 			<td><strong>'.$NCosot.'</strong></td>	
	// 		</tr>
	// 	</table>
	// 	<br>
	// ';

	function DetTable($idcosto,$numcuenta,$perdetalle){
		// $RutEmpresa=$_SESSION['RUTEMPRESA'];
		// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		// $Det="";
		// $SQLint1="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND ccosto='$idcosto' AND cuenta='$numcuenta' AND periodo LIKE'%$perdetalle%' ORDER BY fecha";
		// $Resul = $mysqli->query($SQLint1);
		// while ($RegE = $Resul->fetch_assoc()) {
		// 	$Cont=0;
		// 	$Rut="";
		// 	$Sdet=0;
		// 	$Shab=0;
		// 	$SQLint="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND keyas='".$RegE['keyas']."'";
		// 	$Reus = $mysqli->query($SQLint);
		// 	while ($Fila = $Reus->fetch_assoc()) {
		// 		$Nume=$Fila['numero'];
		// 		$Rut=$Fila['rut'];
		// 		$Cont++;
		// 	}

		// 	if($Cont>1){
		// 		$Razon="MULTIPLES DOCUMENTOS";
		// 		$Nume="";
		// 	}else{
		// 		$SQLint="SELECT * FROM CTCliPro WHERE rut='$Rut'";
		// 		$Reus = $mysqli->query($SQLint);
		// 		while ($Fila = $Reus->fetch_assoc()) {
		// 			$Razon=$Fila['razonsocial'];	
		// 		}					
		// 	}
		// 	$mondeb=$RegE['debe'];
		// 	$monhab=$RegE['haber'];
		// 	$Det=$Det.'
		// 	<tr>
		// 		<td>'.$RegE['fecha'].'</td>
		// 		<td align="right">'.$Nume.'</td>
		// 		<td>'.$Razon.'</td>
		// 		<td>'.$RegE['debe'].'</td>
		// 		<td align="right">'.$mondeb.'</td>
		// 		<td align="right">'.$monhab.'</td>
		// 		<td align="right">'.($mondeb-$monhab).'</td>
		// 	</tr>
		// 	';
		// 	$Sdet=$Sdet+$mondeb;
		// 	$Shab=$Shab+$monhab;
		// }
		// if($Det!=""){
		// 	$Det=$Det.'
		// 	<tr>
		// 		<td></td>
		// 		<td></td>
		// 		<td></td>
		// 		<td align="right">Total Cuenta</td>
		// 		<td align="right">'.$Sdet.'</td>
		// 		<td align="right">'.$Shab.'</td>
		// 		<td align="right">'.($Sdet-$Shab).'</td>
		// 	</tr>
		// 	';
		// }
		// return $Det;
	}


$Tabla='

<table width="100%" border="0">
	<tr style="background-color: #ededed;">
		<td><strong>Fecha</strong></td>
		<td align="center"><strong>Documento</strong></td>
		<td><strong>Proveedor/Cliente</strong></td>
		<td><strong>Concepto</strong></td>
		<td><strong>Debe</strong></td>
		<td><strong>Haber</strong></td>
		<td><strong>Acumulado</strong></td>
	</tr>
';


	if ($_SESSION["PLAN"]=="S"){
		$SQLint2="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa' ORDER BY numero, detalle";
	}else{
		$SQLint2="SELECT * FROM CTCuentas WHERE 1=1 ORDER BY numero, detalle";
	}


	$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa'";
	if($_POST['SelCCosto']>0){
		$SQL=$SQL." AND id='".$_POST['SelCCosto']."'";
	}
	$SQL=$SQL." ORDER BY nombre";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$idcosto=$registro['id'];
		$CCosto='
			<tr style="background-color: #cfcece;">
				<td colspan="7"><strong>'.$registro['nombre'].'</strong></td>
			</tr>
		';
		$swcc=0;
		$Res = $mysqli->query($SQLint2);
		while ($Reg = $Res->fetch_assoc()) {
			$numcuenta=$Reg['numero'];
			$Cuenta='
				<tr>
					<td colspan="1"><strong>'.$Reg['numero'].' - '.$Reg['detalle'].'</strong></td>
				</tr>
			';

			// $Data=DetTable($idcosto,$numcuenta,$PeriLike);

			$Data="";
			$mondeb=0;
			$monhab=0;
			$Sdet=0;
			$Shab=0;			
			$SQLint1="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND ccosto='$idcosto' AND cuenta='$numcuenta' AND periodo LIKE'%$PeriLike%' ORDER BY fecha";
			$Resul = $mysqli->query($SQLint1);
			while ($RegE = $Resul->fetch_assoc()) {
				$Cont=0;
				$Rut="";
				$Razon="";
				$Nume="";
				// $Sdet=0;
				// $Shab=0;
				$SQLint="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$RegE['keyas']."' AND glosa<>''";
				$Reus = $mysqli->query($SQLint);
				while ($Fila = $Reus->fetch_assoc()) {
					$Glosa=$Fila['glosa'];
				}


				$SQLint="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND keyas='".$RegE['keyas']."'";
				$Reus = $mysqli->query($SQLint);
				while ($Fila = $Reus->fetch_assoc()) {
					$Nume=$Fila['numero'];
					$Rut=$Fila['rut'];
					$Cont++;
				}
	
				if($Cont>1){
					$Razon="MULTIPLES DOCUMENTOS";
					$Nume="";
				}else{
					$SQLint="SELECT * FROM CTCliPro WHERE rut='$Rut'";
					$Reus = $mysqli->query($SQLint);
					while ($Fila = $Reus->fetch_assoc()) {
						$Razon=$Fila['razonsocial'];	
					}					
				}
				$mondeb=$RegE['debe'];
				$monhab=$RegE['haber'];
				$acumu=$monhab-$mondeb;

				$Data=$Data.'
				<tr>
					<td>'.date('d-m-Y',strtotime($RegE['fecha'])).'</td>
					<td align="center">'.$Nume.'</td>
					<td>'.$Razon.'</td>
					<td>'.$Glosa.'</td>
					<td align="right">'.number_format($mondeb, $NDECI, $DDECI, $DMILE).'</td>
					<td align="right">'.number_format($monhab, $NDECI, $DDECI, $DMILE).'</td>
					<td align="right">'.number_format($acumu, $NDECI, $DDECI, $DMILE).'</td>
				</tr>
				';

				$Sdet=$Sdet+$mondeb;
				$Shab=$Shab+$monhab;

				$Sdetx=$Sdetx+$mondeb;
				$Shabx=$Shabx+$monhab;

			}

			if($Data!=""){
				$Data=$Data.'
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td style="background-color: #cfcece;" align="right">Total Cuenta '.$Reg['detalle'].'</td>
					<td style="background-color: #cfcece;" align="right">'.number_format($Sdet, $NDECI, $DDECI, $DMILE).'</td>
					<td style="background-color: #cfcece;" align="right">'.number_format($Shab, $NDECI, $DDECI, $DMILE).'</td>
					<td style="background-color: #cfcece;" align="right">'.number_format(($Shabx-$Sdetx), $NDECI, $DDECI, $DMILE).'</td>
				</tr>
				';
			}
	
			if($Data!=""){

				if($swcc==0){
					$Str=$Str.$CCosto;
					$swcc=1;
				}

				$Str=$Str.$Cuenta.$Data;
			}
		}

		$Str=$Str.'
		<tr style="background-color: #cfcece;">
			<td></td>
			<td></td>
			<td></td>
			<td align="right"><strong>Total C. Costo '.$registro['nombre'].'</strong></td>
			<td align="right"><strong>'.number_format($Sdetx, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>'.number_format($Shabx, $NDECI, $DDECI, $DMILE).'</strong></td>
			<td align="right"><strong>'.number_format(($Shabx-$Sdetx), $NDECI, $DDECI, $DMILE).'</strong></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

		';
	}
	
	$Str=$Tabla.$Str;

	$Str=$Str.'
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	';

	$mysqli->close();

	if ($_SERVER["REQUEST_URI"]=="/CCostoReport/ReportPDF.php") {
		$HTML=$Str;
	}else{
		echo $Str;
	}
?>
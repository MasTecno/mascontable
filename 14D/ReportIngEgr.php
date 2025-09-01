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
	// $Factor=($DIVA/100)+1;

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
	}


	if ($_POST['Movi']=="Ing") {
		$t="1";
		$Movi="INGRESO";
		$D1="PERCIBIDOS";
		$D2="DEVENGADOS";
	}
	if ($_POST['Movi']=="Egr") {
		$t="2";
		$Movi="EGRESO";
		$D1="PAGADOS";
		$D2="ADEUDADOS";
	}

	$Ldmes = substr($Periodo,0,2);
	$Ldano = substr($Periodo,3,4);


	$Str=$Str.'
		<table width="100%" border="0">
			<tr>
				<td style="text-align: center;"><h4><strong>ANEXO 1. LIBRO DE INGRESOS Y EGRESOS PARA CONTRIBUYENTES ACOGIDOS AL R&Eacute;GIMEN DEL ART&Iacute;CULO 14 LETRA D) DEL N&#176;3 Y N&#176;8 LETRA (a) DE LA LEY SOBRE IMPUESTO A LA RENTA</strong></h4></td>
			</tr>
		</table>			


		<br>
	
		<table width="100%" border="0">
			<tr>
				<td style="text-align: center;"><h5><strong>CONTRIBUYENTES ACOGIDOS AL R&Eacute;GIMEN DEL ART&Iacute;CULO 14 LETRA D) DEL N&#176;3 Y N&#176;8 LETRA (a) Y NO SE ENCUENTREN OBLIGADOS A LLEVAR LIBRO DE COMPRAS Y VENTAS</strong></h5></td>
			</tr>
		</table>			
		<br>

		<table width="100%" border="0">
			<tr>
				<td width="10%">&nbsp;</td>
				<td width="90%"><strong>PERIODO</strong> '.$Periodo.'</td>
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
		<br>

		<table width="100%" border="0">
			<tr>
				<td style="text-align: center; font-size: 12px !important;"><strong>SECCI&Oacute;N A '.$Movi.'</strong></td>
			</tr>
		</table>
		<br>
		<br>

		<table width="100%" border="0">
			<tr>
				<td width="4%" rowspan="3" style="text-align: center;"><strong>N</strong></td>
				<td width="85%" colspan="7" style="text-align: center;"><strong>REGISTRO DE OPERACIONES</strong></td>
				<td width="11%"><strong>&nbsp;</strong></td>
			</tr>

			<tr>
				<td width="7%" rowspan="2" style="text-align: center;"><strong>N DE DOCUMENTO</strong></td>
				<td width="7%" rowspan="2" style="text-align: center;"><strong>TIPO DOCUMENTO</strong></td>
				<td width="7%" rowspan="2" style="text-align: center;"><strong>RUT RECEPTOR</strong></td>
				<td width="9%" rowspan="2" style="text-align: center;"><strong>FECHA OPERACIÓN</strong></td>
				<td width="16%" colspan="2" style="text-align: center;"><strong>'.$Movi.' $</strong></td>
				<td width="39%" rowspan="2" style="text-align: center;"><strong>GLOSA DE OPERACIÓN</strong></td>
				<td width="11%" rowspan="2" style="text-align: center;"><strong>Operaciónes con Entidades Relacionadas</strong></td>
			</tr>
			<tr>
				<td width="8%" style="text-align: right;"><strong>'.$D1.'</strong></td>
				<td width="8%" style="text-align: right;"><strong>'.$D2.'</strong></td>
			</tr>
			';


			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
			$Cont=1;
			//$t="1"; //Ingreso
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

			
			// $SQL = "SELECT * FROM CT14D WHERE RutEmpresa='$RutEmpresa' AND Periodo like '%$XA%' AND IngEgr='$t' AND NumDoc<>'' ORDER BY FecOpe, Id ASC";
			$SQL = "SELECT * FROM CT14D WHERE RutEmpresa='$RutEmpresa' AND Periodo like '%$XA%' AND IngEgr='$t' ORDER BY FecOpe, Id ASC";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$LMonto=$registro['Pagado'];
				// $Col2=$registro['Pagado'];
				$SumPag=0;
				$Dev=0;
				// $SQL1 = "SELECT * FROM CT14D WHERE RutEmpresa='$RutEmpresa' AND Rut='".$registro['Rut']."' AND Periodo<'$Periodo' AND IngEgr='$t' AND NumDoc='".$registro['NumDoc']."' ORDER BY FecOpe, Id ASC";
				$SQL1 = "SELECT * FROM CT14D WHERE RutEmpresa='$RutEmpresa' AND Id<'".$registro['Id']."' AND Rut='".$registro['Rut']."' AND FecOpe<='".$registro['FecOpe']."' AND IngEgr='$t' AND NumDoc='".$registro['NumDoc']."' ORDER BY FecOpe, Id ASC";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
					$SumPag=$SumPag+$registro1['Pagado'];
				}

				$Dev=$registro['Total']-($registro['Pagado']+$SumPag);//-$SumPag;

				if ($Dev<0) {
					$Dev=0;
				}

				if(isset($_POST['tos']) && $_POST['tos']==='accepted'){
					if ($LMonto>0) {
						if ($registro['NumDoc']!="") {
							$LMonto=$LMonto-$registro['IVA'];
							if ($registro['Total']>$registro['Pagado']) {
								$LMonto=round($LMonto/1.19);//-$registro['IVA'];
							}
						}
					}

					if ($Dev>0) {
						if ($registro['NumDoc']!="") {
							// $Dev=$Dev-$registro['IVA'];
							if ($registro['Total']>$registro['Pagado']) {
								$Dev=round($Dev/1.19);//-$registro['IVA'];
							}
						}
					}
				}


				if ($registro['TipDoc']=="61-NoCrEl") {
					// $LMonto=$LMonto*-1;
					// $Dev=$Dev*-1;
				}
				if ($registro['NumDoc']=="") {
					// $Dev=$LMonto;
					// $LMonto=0;
					$Dev=0;//$LMonto;
					// $LMonto=0;

				}


				$NC = substr($registro['keyas'], 0, 2);

				if($NC=="NC" && $Dev>0 && $registro['TipDoc']<>"61-NoCrEl" && $LMonto>$Dev){
					$Dev=0;
				}	


				$LGlosa=str_replace(" DE DOCUMENTO, FACTURA ELECTRONICA", " DE DOCUMENTO, FAC. ELEC.",$registro['Glosa']);
				$LGlosa=str_replace(" DE DOCUMENTO, NOTA DE CREDITO ELECTRONICA", " DE DOCUMENTO, NC ELEC.",$LGlosa);

				$Str=$Str.'
				<tr>
					<td align="center" height="10" valign="middle">'.$Cont.'</td>
					<td align="center" valign="middle">'.$registro['NumDoc'].'</td>
					<td align="center" valign="middle">'.$registro['TipDoc'].'</td>
					<td align="right" valign="middle">'.$registro['Rut'].'</td>
					<td align="center" valign="middle">'.date('d-m-Y',strtotime($registro['FecOpe'])).'</td>
					<td align="right" valign="middle">'.number_format($LMonto, $NDECI, $DDECI, $DMILE).'</td>
					<td align="right" valign="middle">'.number_format($Dev, $NDECI, $DDECI, $DMILE).'</td>
					<td>&nbsp;&nbsp;'.$LGlosa.'</td>
					<td valign="middle"></td>
				</tr>
				';

				$Cont++;
				$Sum1=$Sum1+$LMonto;
				$Sum2=$Sum2+$Dev;

			}

			$Str=$Str.'
				<tr>
					<td colspan="5"><strong>Total Ingresos del Mes</strong></td>
					<td align="right"><strong>'.number_format($Sum1, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td align="right"><strong>'.number_format($Sum2, $NDECI, $DDECI, $DMILE).'</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
		</table>';

		$mysqli->close();
		if ($_SERVER["REQUEST_URI"]=="/14D/ReportPDF.php") {
			$HTML=$Str;
		}else{
			echo $Str;
		}
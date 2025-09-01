<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$XM = substr($Periodo,0,2);
	$XA = substr($Periodo,3,4);
	$XP =$XA."-".$XM;

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	if ($_POST['messelect']<=9 && isset($_POST['messelect'])) {
		$LMes="0".$_POST['messelect'];
		$Periodo=$LMes."-".$_POST['anoselect'];
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// $Xsql = "DELETE FROM CT14D WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo'";
	$Xsql = "DELETE FROM CT14D WHERE RutEmpresa='$RutEmpresa' AND Periodo LIKE '%$XA%'";
	$mysqli->query($Xsql);

	$Xsql = "SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND periodo LIKE '%$XA%' ORDER BY id ASC";
	$UtId=0;
	$Resul = $mysqli->query($Xsql);
	while ($Reg = $Resul->fetch_assoc()){
		$Rsql="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$Reg['keyas']."' AND id>$UtId AND id<".$Reg['id']." AND periodo LIKE '%$XA%' order BY id ASC";
		
		$RRes = $mysqli->query($Rsql);
		while ($Rreg = $RRes->fetch_assoc()){
			if ($Rreg['glosa']=="") {
				$Sql="UPDATE CTRegLibroDiario SET tipo='".$Reg['tipo']."', ncomprobante='".$Reg['ncomprobante']."' WHERE keyas='".$Reg['keyas']."' AND id=".$Rreg['id']." AND rutempresa='$RutEmpresa' AND glosa=''";
				$mysqli->query($Sql);
			}
		}
		$UtId=$Reg['id'];
	}

	function BuscaKey($l){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$foliINI=$l;
		$swFolio="N";

		while ($swFolio=="N") {
			$sqlNF = "SELECT * FROM CTRegLibroDiario WHERE keyas='$l'";
			$ResulNF = $mysqli->query($sqlNF);										
			$row_cntNF = $ResulNF->num_rows;

			if ($l!=$foliINI && $row_cntNF==0) {
				$swFolio="S";
			}else{
				$l=$l+1;
			}
		}
		return $l;
	}

	$UtId=0;
	$Xsql = "SELECT keyas, count(keyas) as co FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND periodo LIKE '%$XA%' GROUP BY keyas ORDER BY id ASC";
	$Resul = $mysqli->query($Xsql);
	while ($Reg = $Resul->fetch_assoc()){

		if ($Reg['co']>1) {
			$Xsql1 = "SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$Reg['keyas']."' AND glosa<>'' AND periodo LIKE '%$XA%' ORDER BY id ASC";									
			$Resul1 = $mysqli->query($Xsql1);
			while ($Reg1 = $Resul1->fetch_assoc()){
				$swInr="N";

				if($Reg1['tipo']=="E") {
					$Rsql="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$Reg['keyas']."' AND id>$UtId AND id<".$Reg1['id']." AND periodo LIKE '%$XA%' order BY id ASC";
					$RRes = $mysqli->query($Rsql);
					while ($Rreg = $RRes->fetch_assoc()){
						if ($Rreg['glosa']=="" && $Rreg['tipo']=="E" && $swInr=="N") {
							$key=$Reg1['keyas'];
							$NuevoKey= BuscaKey($key);

							$Sql="UPDATE CTControRegDocPago SET keyas='$NuevoKey' WHERE rutempresa='$RutEmpresa' AND keyas='".$Reg1['keyas']."' AND fecha='".$Reg1['fecha']."'";
							$mysqli->query($Sql);

							$Sql="UPDATE CTRegLibroDiario SET keyas='$NuevoKey' WHERE rutempresa='$RutEmpresa' AND id>$UtId AND id<=".$Reg1['id']."";
							$mysqli->query($Sql);
							$swInr="S";
						}
					}
				}

				if($Reg1['tipo']=="I") {
					$Rsql="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND keyas='".$Reg['keyas']."' AND id>$UtId AND id<".$Reg1['id']." AND periodo LIKE '%$XA%' order BY id ASC";
					$RRes = $mysqli->query($Rsql);
					while ($Rreg = $RRes->fetch_assoc()){
						if ($Rreg['glosa']=="" && $Rreg['tipo']=="I" && $swInr=="N") {
							$key=$Reg1['keyas'];
							$NuevoKey= BuscaKey($key);

							$Sql="UPDATE CTControRegDocPago SET keyas='$NuevoKey' WHERE rutempresa='$RutEmpresa' AND keyas='".$Reg1['keyas']."' AND fecha='".$Reg1['fecha']."'";
							$mysqli->query($Sql);

							$Sql="UPDATE CTRegLibroDiario SET keyas='$NuevoKey' WHERE rutempresa='$RutEmpresa' AND id>$UtId AND id<=".$Reg1['id']."";
							$mysqli->query($Sql);
							$swInr="S";
						}
					}
				}

				$UtId=$Reg1['id'];
			}

		}
	}

// exit;
			// $mysqli->query($Sql);


	/////busca marxcados como apertura
	if ($_SESSION["PLAN"]=="S"){
		$SQL = "SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa' AND ingreso='S'";
	}else{
		$SQL = "SELECT * FROM CTCuentas WHERE ingreso='S'";
	}

	$SqlCta="";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($SqlCta!="") {
			$SqlCta=$SqlCta." OR ";
		}
		$SqlCta=$SqlCta."cuenta='".$registro['numero']."'";
	}

	

	$Xsql2 = "SELECT * FROM CTAsientoApertura WHERE RutEmpresa='$RutEmpresa' AND Periodo LIKE '%$XA%'";
	$Resul2 = $mysqli->query($Xsql2);
	while ($Reg2 = $Resul2->fetch_assoc()) {

		$SQL="SELECT fecha, periodo, rutempresa, keyas, tipo, debe, haber FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo LIKE '%$XA%'";
		$SQL=$SQL."AND (".$SqlCta.")";
		$SQL=$SQL." AND keyas='".$Reg2['KeyAs']."' AND glosa='' ORDER BY fecha";
		$resultados = $mysqli->query($SQL);

		while ($registro = $resultados->fetch_assoc()) {

			$BasTri="S";
			/////Base Imponible
			$Xsql = "SELECT * FROM CTAsientoNoBase WHERE KeyAs='".$registro['keyas']."' AND RutEmpresa='$RutEmpresa'";
			$Resul = $mysqli->query($Xsql);
			while ($Reg = $Resul->fetch_assoc()) {
				$BasTri="N";
			}
			
			$sum=$registro['debe']+$registro['haber'];
			// $InsSqlX="INSERT INTO CT14D VALUES('','$RutEmpresa','$Periodo','1','','APERTURA','','".$registro['fecha']."','$sum','0','$sum','$sum','APERTURA','','N','$BasTri','A');";
			$InsSqlX="INSERT INTO CT14D VALUES('','$RutEmpresa','".$registro['periodo']."','1','','APERTURA','','".$registro['fecha']."','$sum','0','$sum','$sum','APERTURA','','N','$BasTri','A');";
			$mysqli->query($InsSqlX);
		}
	}

	$SqlCta="";
	if ($_SESSION["PLAN"]=="S"){
		$SQL = "SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa' AND auxiliar='E' OR auxiliar='B'";
	}else{
		$SQL = "SELECT * FROM CTCuentas WHERE auxiliar='E' OR auxiliar='B'";
	}
	// echo $SQL;

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($SqlCta!="") {
			$SqlCta=$SqlCta." OR ";
		}
		$SqlCta=$SqlCta."cuenta='".$registro['numero']."'";
	}

	// echo $SqlCt;
	// exit;
	// $SQL="SELECT fecha, periodo, rutempresa, keyas, tipo, debe, haber, nfactura FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo LIKE '%$Periodo'";
	$SQL="SELECT fecha, periodo, rutempresa, keyas, tipo, debe, haber, nfactura FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo LIKE '%$XA%'";
	$SQL=$SQL."AND (".$SqlCta.")";
	$SQL=$SQL.' AND (tipo="I" OR tipo="E") ORDER BY fecha';
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$Xsql = "SELECT * FROM CTRegLibroDiario WHERE keyas='".$registro['keyas']."' AND rutempresa='$RutEmpresa' AND glosa<>''";
		$Resul = $mysqli->query($Xsql);
		while ($Reg = $Resul->fetch_assoc()) {
			$Glasa=$Reg['glosa'];
		}
		$BasTri="S";
		/////Base Imponible
		$Xsql = "SELECT * FROM CTAsientoNoBase WHERE KeyAs='".$registro['keyas']."' AND RutEmpresa='$RutEmpresa'";
		$Resul = $mysqli->query($Xsql);
		while ($Reg = $Resul->fetch_assoc()) {
			$BasTri="N";
		}

		if ($registro['tipo']=="I") {
			$Tipo="1";
		}

		if ($registro['tipo']=="E") {
			$Tipo="2";
		}

		$Xsql = "SELECT * FROM CTControRegDocPago WHERE keyas='".$registro['keyas']."' AND rutempresa='$RutEmpresa'";
		$Resul = $mysqli->query($Xsql);										
		$row_cnt = $Resul->num_rows;
		if ($row_cnt>0) {
			$Xsql1 = "SELECT * FROM CTControRegDocPago WHERE keyas='".$registro['keyas']."' AND rutempresa='$RutEmpresa'";
			$Resul1 = $mysqli->query($Xsql1);
			while ($Reg = $Resul1->fetch_assoc()) {
				$D1=$Reg['rut'];
				$D2=$Reg['id_tipodocumento'];
				$D3=$Reg['ndoc'];
				$D4=$Reg['monto'];
				$D5=$Reg['tipo'];

				$BajaIva=0;

				$LNeto=0;
				$LIva=0;
				$LTotal=0;
				$LTipo="";

				$LNeto="";
				$LIva="";
				$LTotal="";
				$LNume="";
				$LTipo="";
				$LRut="";

				$Xsql2 = "SELECT * FROM CTRegDocumentos WHERE rutempresa LIKE '$RutEmpresa' AND rut LIKE '$D1' AND id_tipodocumento = '$D2' AND numero LIKE '$D3' AND tipo LIKE '$D5'";
				$Resul2 = $mysqli->query($Xsql2);
				while ($Reg2 = $Resul2->fetch_assoc()) {
					$LNeto=$Reg2['exento']+$Reg2['neto']+$Reg2['retencion'];
					$LIva=$Reg2['iva'];
					$LTotal=$Reg2['total'];
					$LNume=$Reg2['numero'];
					$LTipo=$Reg2['id_tipodocumento'];
					$LRut=$Reg2['rut'];
				}

				if ($Reg['tipo']=="H") {
					$Xsql2 = "SELECT * FROM CTHonorarios WHERE rutempresa LIKE '$RutEmpresa' AND rut LIKE '$D1' AND numero LIKE '$D3'";
					$Resul2 = $mysqli->query($Xsql2);
					while ($Reg2 = $Resul2->fetch_assoc()) {
						$LTotal=$Reg2['liquido'];
						$LRut=$Reg2['rut'];
						$LNume=$Reg2['numero'];
						$LTipo="HONORARIOS";
					}
				}

				$Xsql2 = "SELECT * FROM CTTipoDocumento WHERE id = '$LTipo'";
				$Resul2 = $mysqli->query($Xsql2);
				while ($Reg2 = $Resul2->fetch_assoc()) {
					$LTipo=$Reg2['tiposii']."-".$Reg2['sigla'];
				}

				// $InsSql="INSERT INTO CT14D VALUES('','$RutEmpresa','$Periodo','$Tipo','$LNume','$LTipo','$LRut','".$registro['fecha']."','$LNeto','$LIva','$LTotal','$D4','$Glasa','','N','$BasTri','A');";
				$InsSql="INSERT INTO CT14D VALUES('','$RutEmpresa','".$registro['periodo']."','$Tipo','$LNume','$LTipo','$LRut','".$registro['fecha']."','$LNeto','$LIva','$LTotal','$D4','".$Glasa."','','N','$BasTri','A');";
				$mysqli->query($InsSql);
			}
		}else{
			$Monto=$registro['debe']+$registro['haber'];

			if ($registro['nfactura']=="PagBolEle") {
				$Xsql2 = "SELECT DTE, count(keyas) as ConBol FROM CTBoletasDTE WHERE keyas='".$registro['keyas']."' GROUP BY keyas";
				// exit;
				$Resul2 = $mysqli->query($Xsql2);
				while ($Reg2 = $Resul2->fetch_assoc()) {
					$TipoDTE=$Reg2['DTE'];
					$LNume=$Reg2['DTE'];
				}				

				if($TipoDTE=='39'){
					$LNeto=round($Monto/1.19);
					$LIva=round($Monto-$LNeto);
					$LTotal=$Monto;
				}

				// $InsSql="INSERT INTO CT14D VALUES('','".$_SESSION['RUTEMPRESA']."','$Periodo','$Tipo','($LNume)','$TipoDTE-".$registro['nfactura']."','".$_SESSION['RUTEMPRESA']."','".$registro['fecha']."','$LNeto','$LIva','$LTotal','$LTotal','$Glasa','','N','$BasTri','A');";
				$InsSql="INSERT INTO CT14D VALUES('','".$_SESSION['RUTEMPRESA']."','".$registro['periodo']."','$Tipo','($LNume)','$TipoDTE-".$registro['nfactura']."','".$_SESSION['RUTEMPRESA']."','".$registro['fecha']."','$LNeto','$LIva','$LTotal','$LTotal','$Glasa','','N','$BasTri','A');";
			}else{
				// $InsSql="INSERT INTO CT14D VALUES('','$RutEmpresa','$Periodo','$Tipo','','','','".$registro['fecha']."','$Monto','0','$Monto','$Monto','$Glasa','','N','$BasTri','A');";
				$InsSql="INSERT INTO CT14D VALUES('','$RutEmpresa','".$registro['periodo']."','$Tipo','','','','".$registro['fecha']."','$Monto','0','$Monto','$Monto','$Glasa','','N','$BasTri','A');";
			}
			$mysqli->query($InsSql);
		}
	}


	$LNeto="";
	$LIva="";
	$LTotal="";
	$LNume="";
	$LTipo="";
	$LRut="";


	//// DOCUMENTOS SIN CENTRALIZAR
	// $Xsql2 = "SELECT * FROM CTRegDocumentos WHERE rutempresa LIKE '$RutEmpresa' AND fecha LIKE '".$XP."%' AND keyas<>''";
	$Xsql2 = "SELECT * FROM CTRegDocumentos WHERE rutempresa LIKE '$RutEmpresa' AND periodo LIKE '%$XA%' AND keyas<>''";
	$Resul2 = $mysqli->query($Xsql2);
	while ($Reg2 = $Resul2->fetch_assoc()) {
		$LNeto=$Reg2['exento']+$Reg2['neto']+$Reg2['retencion'];
		$LIva=$Reg2['iva'];
		$LTotal=$Reg2['total'];
		$LNume=$Reg2['numero'];
		$LTipo=$Reg2['id_tipodocumento'];
		$LRut=$Reg2['rut'];

		if ($Reg2['tipo']=="V") {
			$t="1";
		}else{
			$t="2";
		}

		$Xsql1 = "SELECT * FROM CTTipoDocumento WHERE id = '$LTipo'";
		$Resul1 = $mysqli->query($Xsql1);
		while ($Reg1 = $Resul1->fetch_assoc()) {
			$LTipo=$Reg1['tiposii']."-".$Reg1['sigla'];
		}

		$Xsql = "SELECT * FROM CT14D WHERE rutempresa='$RutEmpresa' AND Rut='$LRut' AND NumDoc='$LNume' AND TipDoc='$LTipo'";
		$Resul = $mysqli->query($Xsql);										
		$row_cnt = $Resul->num_rows;
		if ($row_cnt==0) {

			// $InsSql="INSERT INTO CT14D VALUES('','$RutEmpresa','$Periodo','$t','$LNume','$LTipo','$LRut','".$Reg2['fecha']."','$LNeto','$LIva','$LTotal','0','SIN MOVIMIENTOS','','N','S','X');";
			$InsSql="INSERT INTO CT14D VALUES('','$RutEmpresa','".$Reg2['periodo']."','$t','$LNume','$LTipo','$LRut','".$Reg2['fecha']."','$LNeto','$LIva','$LTotal','0','SIN MOVIMIENTOS','','N','S','X');";
			$mysqli->query($InsSql);
		}
	}

	$mysqli->close();
// exit;
	header("location:../14D");
?>
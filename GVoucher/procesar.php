<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$rt=$_POST['tfecha']*1;

	if ($rt=="" || $rt<1 || $rt>31) {
		//header("location:index.php?Msj=5");
		echo "Error de fecha";
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if ($Periodo=="") {
		echo "Error de Periodo";
		exit;
	}

    $KeyAs=date("YmdHis");

	$FECHA=date("Y/m/d");

	if ($rt<=9) {
		$fecdig="0".$rt."-".$Periodo;
	}else{
		$fecdig=$rt."-".$Periodo;
	}

	$dia = substr($fecdig,0,2);
	$mes = substr($fecdig,3,2);
	$ano = substr($fecdig,6,4);

	$xfecha=$ano."/".$mes."/".$dia;

	$Periodo=$mes."-".$ano;

	$dia = substr($_POST['fdoc'],0,2);
	$mes = substr($_POST['fdoc'],3,2);
	$ano = substr($_POST['fdoc'],6,4);

	$Xfdoc=$ano."/".$mes."/".$dia;

	$dia = substr($_POST['fdocven'],0,2);
	$mes = substr($_POST['fdocven'],3,2);
	$ano = substr($_POST['fdocven'],6,4);

	$xfdocven=$ano."/".$mes."/".$dia;

	if (strlen($Periodo)<7 || strlen($Periodo)>7){
		echo "Error de Periodo";
		exit;
	}

	$TanoD = substr($Periodo,3,4);

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$FolioComp=0;
	$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='".$_POST['ttmovimiento']."' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
	$resultados = $mysqli->query($SQL1);
	while ($registro = $resultados->fetch_assoc()) {
		$FolioComp=$registro['valor'];
	}

	if ($FolioComp==0) {
		$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','".$_POST['ttmovimiento']."','2','A');");
		$FolioComp=1;
	}else{
		$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='".$_POST['ttmovimiento']."' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
	}
	$sw=0;
	if ($_POST['tdocumentos']=="C" || $_POST['tdocumentos']=="H") {

		if (!isset($_POST['check_list']) || !is_array($_POST['check_list'])) {
			echo "Error: No se han seleccionado documentos para procesar";
			exit;
		}

		$checked_count = count($_POST['check_list']);
		foreach($_POST['check_list'] as $selected) {

			if ($_POST["tdocumentos"]=="H") {
				$SQL="SELECT * FROM CTHonorarios WHERE id='".$selected."' AND rutempresa='$RutEmpresa'";
				$xidtipdoc="999";
			}else{
				$SQL="SELECT * FROM CTRegDocumentos WHERE id='".$selected."' AND rutempresa='$RutEmpresa'";				
			}
			$idtipdoc="";
			$Xoperador="S";
			$TipoSII="";
			$xrutX="";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {	
				$xndoc=$registro['numero'];
				if ($_POST["tdocumentos"]=="H") {
					$tmont=$registro['liquido'];
					// $idtipdoc=2;
				}else{
					$tmont=$registro['total'];
					$idtipdoc=$registro['id_tipodocumento'];			
				}
				$xrut=$registro['rut'];
			}

			if ($checked_count==1) {
				$SQL="SELECT * FROM CTTipoDocumento WHERE id='".$idtipdoc."'";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {	
					$Xoperador=$registro['operador'];
					$TipoSII=$registro['tiposii'];
				}
			}else{
				$Xoperador="S";
			}

			$CorTotal=$_POST['monto'];

			if ($CorTotal<0) {
				$CorTotal=$CorTotal*-1;
			}

			$xcmonto=0;
			if ($_POST["tdocumentos"]=="H") {
				$SQL="SELECT sum(monto) as cmonto FROM CTControRegDocPago WHERE ndoc='$xndoc' AND tipo='".$_POST['tdocumentos']."' AND id_tipodocumento='0' AND rutempresa='$RutEmpresa' AND rut='$xrut'";
			}else{
				$SQL="SELECT sum(monto) as cmonto FROM CTControRegDocPago WHERE ndoc='$xndoc' AND tipo='".$_POST['tdocumentos']."' AND id_tipodocumento='$idtipdoc' AND rutempresa='$RutEmpresa' AND rut='$xrut'";
			}

			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {	
				$xcmonto=$registro['cmonto'];
			}

			$tmont=$tmont-$xcmonto;

			if ($checked_count==1) {
				$tmont=$_POST['monto'];
				if ($tmont<0) {
					$tmont=$tmont*-1;
				}
			}

			$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','$xrut','$Periodo','$idtipdoc','$xndoc','$KeyAs','$tmont','$xfecha','$FECHA','".$_POST['tdocumentos']."','".$_POST['fpago']."','A')");

			if ($_POST['fpago']=="A") {

				$SQL1="SELECT * FROM CTFondo WHERE Estado='A' AND RutEmpresa='$RutEmpresa' AND Id='".$_POST['cuentaasi']."' AND Tipo='I' ORDER BY Fecha";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
					$IdPer=$registro1['IdPersonal'];
					$xrutX=$registro1['Rut'];
				}

				$L1="";
			
				$SQL1="SELECT * FROM CTAsientoFondo WHERE rut_empresa='$RutEmpresa' AND tipo='A'";
				$resultados = $mysqli->query($SQL1);
				while ($registro = $resultados->fetch_assoc()) {
					$L1=$registro['L1'];
				}
				if ($L1=="") {
					$SQL1="SELECT * FROM CTAsientoFondo WHERE rut_empresa='' AND tipo='A'";
					$resultados = $mysqli->query($SQL1);
					while ($registro = $resultados->fetch_assoc()) {
						$L1=$registro['L1'];
					}
				}

		        $mysqli->query("INSERT INTO CTFondo VALUES('','".$_POST['cuentaasi']."','$xrutX','$RutEmpresa','".$_POST['glosa']."','$xfecha','$L1','$tmont','$KeyAs','$FECHA','E','A');");
			}

			if ($_POST['fpago']=="B") {
				$mysqli->query("INSERT INTO CTControlDocumento (rutempresa,folio,ndoc,entidad,fecha,vencimiento,monto,tdocumento,keyas,tipo,estado) VALUES ('$RutEmpresa','".$_POST['ndocpago']."','$xndoc','".$_POST['tentidad']."','$Xfdoc','$xfdocven','".$CorTotal."','".$_POST['tdocumentos']."','$KeyAs','".$_POST['opt1']."','A')");
			}

			$sw++;
			if ($sw==500) {
				break;
			}
		}


		if ($Xoperador=="R") {
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cuentaasi']."','".$CorTotal."','0','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");

			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cacuenta']."','0','".$CorTotal."','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");
		}else{

			if ($_POST['fpago']=="A") {

				$L1="";
				$L2="";

				$SQL1="SELECT * FROM CTAsientoFondo WHERE rut_empresa='$RutEmpresa' AND tipo='A'";
				$resultados = $mysqli->query($SQL1);
				while ($registro = $resultados->fetch_assoc()) {
					$L1=$registro['L1'];
					$L2=$registro['L2'];
				}

				if ($L1=="") {
					$SQL1="SELECT * FROM CTAsientoFondo WHERE rut_empresa='' AND tipo='A'";
					$resultados = $mysqli->query($SQL1);
					while ($registro = $resultados->fetch_assoc()) {
						$L1=$registro['L1'];
						$L2=$registro['L2'];
					}
				}

				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,rutreferencia,tiporeferencia,docreferencia) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cacuenta']."','".$CorTotal."','0','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."','$xrut','$TipoSII','$xndoc')");

				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,rutreferencia,tiporeferencia,docreferencia) VALUES ('$Periodo','$RutEmpresa','$xfecha','','$L1','0','".$CorTotal."','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."','$xrutX','".$_POST['cuentaasi']."','0')");			

			}else{
				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cacuenta']."','".$CorTotal."','0','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");

				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cuentaasi']."','0','".$CorTotal."','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");			
			}
		}
		
		$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','".$_POST['glosa']."','0','0','0','$FECHA','A','$KeyAs','$FolioComp','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");

	}

	$sw=0;
	if ($_POST['tdocumentos']=="V") {

		$checked_count = count($_POST['check_list']);
		foreach($_POST['check_list'] as $selected) {

			$Xoperador="S";
			$SQL="SELECT * FROM CTRegDocumentos WHERE id='".$selected."' AND rutempresa='$RutEmpresa'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {	
				$xndoc=$registro['numero'];
				$tmont=$registro['total'];
				$tmont1=$registro['total'];
				$xrut=$registro['rut'];
				$idtipdoc=$registro['id_tipodocumento'];
			}

			if ($checked_count==1) {
				$SQL="SELECT * FROM CTTipoDocumento WHERE id='".$idtipdoc."'";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {	
					$Xoperador=$registro['operador'];
				}

				$tmont=$_POST['monto'];
				if ($tmont<0) {
					$tmont=$tmont*-1;
				}
			}else{
				$Xoperador="S";
			}

			if ($checked_count>1){
				$CorTotal=$_POST['monto'];

				if ($CorTotal<0) {
					$CorTotal=$CorTotal*-1;
				}

				$xcmonto=0;
				// $SQL="SELECT sum(monto) as cmonto FROM CTControRegDocPago WHERE ndoc='$xndoc' AND id_tipodocumento='$idtipdoc' AND rutempresa='$RutEmpresa' AND rut='$xrut'";
				$SQL="SELECT sum(monto) as cmonto FROM CTControRegDocPago WHERE ndoc='$xndoc' AND id_tipodocumento='$idtipdoc' AND tipo='".$_POST['tdocumentos']."' AND rutempresa='$RutEmpresa' AND rut='$xrut'";
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {	
					$xcmonto=$registro['cmonto'];
				}

				$tmont=$tmont-$xcmonto;
			}

			if ($checked_count==1 && $tmont1>$_POST['monto']) {

				$CorTotal=$_POST['monto'];

				if ($CorTotal<0) {
					$CorTotal=$CorTotal*-1;
				}

				$tmont=$CorTotal;

			}
			
			if ($CorTotal==0) {
				$CorTotal=$_POST['monto'];
				if ($CorTotal<0) {
					$CorTotal=$CorTotal*-1;
				}
			}

			// echo $tmont;

			$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','$xrut','$Periodo','$idtipdoc','$xndoc','$KeyAs','$tmont','$xfecha','$FECHA','".$_POST['tdocumentos']."','".$_POST['fpago']."','A')");

			if ($_POST['fpago']=="B") {
				$mysqli->query("INSERT INTO CTControlDocumento (rutempresa,folio,ndoc,entidad,fecha,vencimiento,monto,tdocumento,keyas,tipo,estado) VALUES ('$RutEmpresa''".$_POST['ndocpago']."','$xndoc','".$_POST['tentidad']."','$Xfdoc','$xfdocven','".$CorTotal."','".$_POST['tdocumentos']."','$KeyAs','".$_POST['opt1']."','A')");
			}

			$sw++;
			if ($sw==500) {
				break;
			}

		}

		if ($Xoperador=="R") {
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cacuenta']."','".$CorTotal."','0','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cuentaasi']."','0','".$CorTotal."','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");
		}else{
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cuentaasi']."','".$CorTotal."','0','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");

			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$_POST['cacuenta']."','0','".$CorTotal."','$FECHA','A','$KeyAs','0','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");
		}


		$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto) VALUES ('$Periodo','$RutEmpresa','$xfecha','".$_POST['glosa']."','0','0','0','$FECHA','A','$KeyAs','$FolioComp','".$_POST['ttmovimiento']."','".$_POST['tccosto']."')");

	}

	$mysqli->close();
	echo "";
?>
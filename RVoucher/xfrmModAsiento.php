<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

	$Clineas=$_POST['Clineas'];
	$keyas=$_POST['keyas'];
	$StError="";

	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($_POST['SwEli']!="" && isset($_POST['SwEli'])){
		
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$lotefac=0;
		$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['keyas']."' AND rutempresa='$RutEmpresa'";

		$resultados = $mysqli->query($SQL1);
		while ($registro1 = $resultados->fetch_assoc()) {
			if ($registro1["glosa"]!="" && $registro1["nfactura"]>0 && $registro1["rut"]=="") {
				$lotefac=$registro1["nfactura"];
			}else{
				$xrut=$registro1["rut"];
				$xdoc=$registro1["nfactura"];
			}
			$strHono=$registro1["nfactura"];
		}      

		$strHono=substr($strHono,0,4);
		if ($strHono=="Hono") {
			$mysqli->query("UPDATE CTHonorarios SET movimiento='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND movimiento='".$_POST['keyas']."'");
			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['keyas']."' AND rutempresa='$RutEmpresa'");
			$d=date("Y-m-d");
			$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','".$_POST['keyas']."','".date("H:i:s")."','".$_SESSION['NOMBRE']."');");
			$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['keyas']."'");
			
			$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['keyas']."' AND rutempresa='$RutEmpresa'");
			$mysqli->query("DELETE FROM CTAsientoApertura WHERE KeyAs='".$_POST['keyas']."' AND RutEmpresa='$RutEmpresa'");
			$EKeyasH=$_POST['keyas']."H";
			$d=date("Y-m-d");
			$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','$EKeyasH','".date("H:i:s")."','".$_SESSION['NOMBRE']."');");
		}else{
			$mysqli->query("UPDATE CTRegDocumentos SET lote='', keyas='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND keyas='".$_POST['keyas']."'");
			$mysqli->query("DELETE FROM CTFondo WHERE keyas='".$_POST['keyas']."' AND rutempresa='$RutEmpresa'");
			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['keyas']."' AND rutempresa='$RutEmpresa'");
			$d=date("Y-m-d");
			$mysqli->query("INSERT INTO  CTRegLibroDiarioLog VALUES('','$RutEmpresa','$d','".$_POST['keyas']."','".date("H:i:s")."','".$_SESSION['NOMBRE']."');");
			$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['keyas']."'");
			$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['keyas']."' AND rutempresa='$RutEmpresa'");
			$mysqli->query("DELETE FROM CTAnticipos WHERE KeyAs='".$_POST['keyas']."' AND RutEmpresa='$RutEmpresa'");
			$mysqli->query("DELETE FROM CTAnticipos WHERE KeyasDestino='".$_POST['keyas']."' AND RutEmpresa='$RutEmpresa'");
			$mysqli->query("DELETE FROM CTBoletasDTE WHERE keyas='".$_POST['keyas']."' AND RutEmpresa='$RutEmpresa'");
			$mysqli->query("DELETE FROM CTAsientoApertura WHERE KeyAs='".$_POST['keyas']."' AND RutEmpresa='$RutEmpresa'");
			$mysqli->query("DELETE FROM CTRendicion WHERE KeyAs='".$_POST['keyas']."' AND RutEmpresa='$RutEmpresa'");
		}


		// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		// $lotefac=0;
		// $SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'";

		// $resultados = $mysqli->query($SQL1);
		// while ($registro1 = $resultados->fetch_assoc()) {

		// 	if ($registro1["glosa"]!="" && $registro1["nfactura"]>0 && $registro1["rut"]=="") {
		// 		$lotefac=$registro1["nfactura"];
		// 	}else{
		// 		$xrut=$registro1["rut"];
		// 		$xdoc=$registro1["nfactura"];
		// 	}

		// 	$strHono=$registro1["nfactura"];
		// }      

		// $strHono=substr($strHono,0,4);
		// if ($strHono=="Hono") {

		// 	$mysqli->query("UPDATE CTHonorarios SET movimiento='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND movimiento='".$_POST['dat2']."'");
		// 	$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");
		// 	$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['dat2']."'");
		// 	$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");
		// }else{

		// 	$mysqli->query("UPDATE CTRegDocumentos SET lote='', keyas='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND keyas='".$_POST['dat2']."'");
		// 	$mysqli->query("DELETE FROM CTFondo WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");
		// 	$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");
		// 	$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['dat2']."'");
		// 	$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");
		// 	$mysqli->query("DELETE FROM CTAnticipos WHERE KeyAs='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");
		// 	$mysqli->query("DELETE FROM CTAnticipos WHERE KeyasDestino='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");
		// }

		// $mysqli->close();
		// header("location:../Mayor");

		// exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$i=1;
	while ($i <= $Clineas) {

		$Cuenta="Comp".$i;
		$Cuenta=$_POST[$Cuenta];
		
		if ($Cuenta!="") {
			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$Cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$Cuenta'";
			}
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$StError="La cuenta no existe";
				break;
			}
		}

		$MDebe="Debe".$i;
		$MDebe=str_replace(".","",$_POST[$MDebe]);

		$MHaber="Haber".$i;
		$MHaber=str_replace(".","",$_POST[$MHaber]);
// exit;
		// if ($MDebe>0 && $MHaber>0) {
		// 	$StError="Por favor no trate de propocar errores poniedo monto en Debe y Haber para la Misma Cuenta";
		// 	break;
		// }

		$i++;
	}

	if ($StError!="") {
		echo $StError;
		$mysqli->close();
		exit;
	}

	$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='$keyas' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND glosa<>'' ORDER BY id ASC";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Cnfactura=$registro['nfactura'];
		$Crut=$registro['rut'];
		$Cncomprobante=$registro['ncomprobante'];
		$Ctipo=$registro['tipo'];
		$Ciddocref=$registro['iddocref'];
		$Ctipodocref=$registro['tipodocref'];
		$Cccosto=$registro['ccosto'];
	}

	$dia = substr($_POST['TFecha'],0,2);
    $mes = substr($_POST['TFecha'],3,2);
    $ano = substr($_POST['TFecha'],6,4);

    $xfecha=$ano."/".$mes."/".$dia;

    $Periodo=$mes."-".$ano;

	$FECHA=date("Y/m/d");


	if ($_POST['tmovi']!=$Ctipo) {
		$Cncomprobante=1;

		$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='".$_POST['tmovi']."' AND rutempresa='$RutEmpresa' AND ano='$ano'";
		$resultados = $mysqli->query($SQL1);
		while ($registro = $resultados->fetch_assoc()) {
			$Cncomprobante=$registro['valor'];
		}

		if ($Cncomprobante==1) {
			$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$ano','".$_POST['tmovi']."','2','A');");
		}else{
			$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($Cncomprobante+1)."' WHERE tipo='".$_POST['tmovi']."' AND rutempresa='$RutEmpresa' AND ano='$ano'");
		}

		$Ctipo=$_POST['tmovi'];
	}

	$STRSQL = "INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,iddocref,tipodocref,ccosto) VALUES ";

	$i=1;
	while ($i <= $Clineas) {
		$Cuenta="Comp".$i;
		$Cuenta=$_POST[$Cuenta];
		
		if ($Cuenta!="") {
			$MDebe="Debe".$i;
			$MDebe=str_replace(".","",$_POST[$MDebe]);

			$MHaber="Haber".$i;
			$MHaber=str_replace(".","",$_POST[$MHaber]);

			$MSelCCosto="SelCCosto".$i;
			$Cccosto=$_POST[$MSelCCosto];

        	$STRSQL = $STRSQL." ('$Periodo','".$_SESSION['RUTEMPRESA']."','$xfecha','','$Cuenta','$MDebe','$MHaber','$FECHA','A','$keyas','$Cnfactura','$Crut','$Cncomprobante','$Ctipo','','','$Cccosto'),";
		}
		$i++;
	}

	// exit;
	$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='$keyas' AND rutempresa='".$_SESSION['RUTEMPRESA']."';");

	$STRSQL = $STRSQL." ('$Periodo','".$_SESSION['RUTEMPRESA']."','$xfecha','".$_POST['Glosa']."','$Cuenta','0','0','$FECHA','A','$keyas','$Cnfactura','$Crut','$Cncomprobante','$Ctipo','$Ciddocref','$Ctipodocref','0');";

	$mysqli->query($STRSQL);

	$mysqli->close();
	header("location:../RVoucher");
?>
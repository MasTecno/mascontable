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

	$TanoD = substr($_SESSION['PERIODO'],3,4);
	$xfecha=$TanoD."-01-01";

	$Periodo=$_POST['PApertura1'];
	$xglosa=$_POST['xglosa1'];
	$_SESSION['KEYASIENTO']=date("YmdHis");
	$KeyAs=$_SESSION['KEYASIENTO'];

	$FECHA=date("Y/m/d");
	$xttmovimiento="T";

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if ($_SESSION['CCOSTO']=="S"){
		$FolioComp=0;
		$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='$xttmovimiento' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
		$resultados = $mysqli->query($SQL1);
		while ($registro = $resultados->fetch_assoc()) {
			$FolioComp=$registro['valor'];
		}
		if ($FolioComp==0) {
			$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','$xttmovimiento','2','A');");
			$FolioComp=1;
		}else{
			$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$xttmovimiento' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
		}
	}else{
		$FolioComp=0;
	}

	$SQL="SELECT * FROM CTAperturaAsiento WHERE RutEmpresa ='".$RutEmpresa."' ";
	$consulta = $mysqli->query($SQL);
	while ($registro = $consulta->fetch_assoc()) {
		$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo) VALUES ('$Periodo','$RutEmpresa','$xfecha','','".$registro["Codigo"]."','".$registro["Debe"]."','".$registro["Haber"]."','$FECHA','A','$KeyAs','0','$xttmovimiento')");
	}	

	$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo) VALUES ('$Periodo','$RutEmpresa','$xfecha','$xglosa','0','0','0','$FECHA','A','$KeyAs','$FolioComp','$xttmovimiento')");

	$mysqli->query("DELETE FROM CTAperturaAsiento WHERE rutempresa='$RutEmpresa'");

	$mysqli->query("UPDATE CTHonorarios SET origen='M', movimiento='$KeyAs' WHERE origen='Z' AND rutempresa='$RutEmpresa'");

	$mysqli->query("UPDATE CTRegDocumentos SET origen='M', keyas='$KeyAs' WHERE origen='Z' AND rutempresa='$RutEmpresa'");

	$mysqli->close();
	$_SESSION['PERIODOPC']=$Periodo;
	$_SESSION['KEYASIENTO']=date("YmdHis");
	$_SESSION['KEYASIENTO']=$_SESSION['KEYASIENTO']+5;
	header("location:../RVoucher/Voucher.php");
	exit;
?>
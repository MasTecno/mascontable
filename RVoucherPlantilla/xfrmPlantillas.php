<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

	$Clineas=$_POST['Clineas'];
	$keyas=$_POST['keyas'];
	$StError="";
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

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
		$MDebe=$_POST[$MDebe];

		$MHaber="Haber".$i;
		$MHaber=$_POST[$MHaber];

		if ($MDebe>0 && $MHaber>0) {
			$StError="Por favor no trate de propocar errores poniedo monto en Debe y Haber para la Misma Cuenta";
			break;
		}
		$i++;
	}

	if ($StError!="") {
		echo $StError;
		$mysqli->close();
		exit;
	}

	$dia = substr($_POST['TFecha'],0,2);
    $mes = substr($_POST['TFecha'],3,2);
    $ano = substr($_POST['TFecha'],6,4);

	$Periodo=$mes."-".$ano;

    $xfecha=$ano."/".$mes."/".$dia;

	$FECHA=date("Y/m/d");

	$_SESSION['KEYASIENTO']=date("YmdHis");
	$KeyAs=$_SESSION['KEYASIENTO']+5;
	$TanoD = substr($Periodo,3,4);

	if (isset($_POST['ttmovimiento']) ) {
		$xttmovimiento=$_POST['ttmovimiento'];
	}else{
		$xttmovimiento="";
	}

	if ($xttmovimiento!="") {
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

	$Cncomprobante=$FolioComp;
	$Ctipo=$xttmovimiento;
	$Cccosto=$_POST['tccosto'];

	$STRSQL = "INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,iddocref,tipodocref,ccosto) VALUES ";

	$i=1;
	while ($i <= $Clineas) {
		$Cuenta="Comp".$i;
		$Cuenta=$_POST[$Cuenta];
		
		if ($Cuenta!="") {

			$MDebe="Debe".$i;
			$MDebe=$_POST[$MDebe];

			$MHaber="Haber".$i;
			$MHaber=$_POST[$MHaber];

			$MSelCCosto="SelCCosto".$i;
			$Cccosto=$_POST[$MSelCCosto];
			if($MDebe>0 || $MHaber>0){
        		$STRSQL = $STRSQL." ('$Periodo','".$_SESSION['RUTEMPRESA']."','$xfecha','','$Cuenta','".str_replace(".","",$MDebe)."','".str_replace(".","",$MHaber)."','$FECHA','A','$KeyAs','$Cnfactura','$Crut','0','','','','$Cccosto'),";
			}
		}

		$i++;
	}
	
	$STRSQL = $STRSQL." ('$Periodo','".$_SESSION['RUTEMPRESA']."','$xfecha','".$_POST['Glosa']."','','','','$FECHA','A','$KeyAs','$Cnfactura','$Crut','$Cncomprobante','$Ctipo','$Ciddocref','$Ctipodocref','$Cccosto');";

	$mysqli->query($STRSQL);

	$mysqli->query("UPDATE CTAsientoPlantilla SET tipo='$Ctipo' WHERE nombre='".$_POST['NomPlantilla']."' AND tipo=''");

	if(isset($_POST['swupdate']) && $_POST['swupdate']=="U"){
		$mysqli->query("DELETE FROM CTAsientoPlantilla WHERE nombre='".$_POST['NomPlantilla']."'");
		$i=1;
		while ($i <= $Clineas) {
			$Cuenta="Comp".$i;
			$Cuenta=$_POST[$Cuenta];
			
			if ($Cuenta!="") {
				$mysqli->query("INSERT INTO CTAsientoPlantilla VALUES('','$Ctipo','".$_POST['NomPlantilla']."','$Cuenta','A');");
			}
			$i++;
		}
		
	}


	$mysqli->close();

	$KeyAs=$KeyAs+5;
	$_SESSION['KEYASIENTO']=$KeyAs;
// exit;
	header("location:index.php");
?>
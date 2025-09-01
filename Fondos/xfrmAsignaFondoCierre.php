<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$Periodo=$_SESSION['PERIODO'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$FECHA=date("Y/m/d");


	$xfecha=$_POST['fdesde1'];
	$dia = substr($xfecha,0,2);
	$mes = substr($xfecha,3,2);
	$ano = substr($xfecha,6,4);

	$xfecha=$ano."/".$mes."/".$dia;

	$Periodo=$mes."-".$ano;

	$KeyAs=date("YmdHis");

	if(isset($_POST['tccosto']) && $_POST['tccosto']!=""){
		$xccosto=$_POST['tccosto'];
	}else{
		$xccosto=0;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

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

	// $SQL="SELECT * FROM CTFondoPersonal WHERE Id='".$_POST['idcierre']."' ORDER BY Nombre";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$xRut=$registro['Rut'];
	// }

	$masignado=0;
	$SQL1="SELECT sum(Monto) as masignado FROM `CTFondo` WHERE Id='".$_POST['idcierre']."' AND Tipo='I' AND Estado='A' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' GROUP by Id";
	$resultados1 = $mysqli->query($SQL1);
	while ($registro1 = $resultados1->fetch_assoc()) {
		$masignado=$registro1['masignado'];
		$xRut=$registro['Rut'];
	}

	$smonto=0;
	$SQL1="SELECT sum(Monto) as smonto FROM `CTFondo` WHERE IdPersonal='".$_POST['idcierre']."' AND Tipo='E' AND Estado='A' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' GROUP by IdPersonal";
	$resultados1 = $mysqli->query($SQL1);
	while ($registro1 = $resultados1->fetch_assoc()) {
		$smonto=$registro1['smonto'];
	}

	$smonto=$masignado-$smonto;

	if ($smonto>=0) {
		$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
		$SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$L2','".$smonto."','0','$FECHA','A','$KeyAs','','','0','',$xccosto),";
		$SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$L1','0','".$smonto."','$FECHA','A','$KeyAs','','','0','',$xccosto),";
		$TComp="T";
	}else{
		$smonto=$smonto*-1;
		$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
		$SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$L1','".$smonto."','0','$FECHA','A','$KeyAs','','','0','',$xccosto),";
		$SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$L2','0','".$smonto."','$FECHA','A','$KeyAs','','','0','',$xccosto),";
		$TComp="E";
//		exit;
		$smonto=$smonto*-1;
	}

	$TanoD = substr($Periodo,3,4);
	$FolioComp=0;
	$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='$TComp' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
	$resultados = $mysqli->query($SQL1);
	while ($registro = $resultados->fetch_assoc()) {
		$FolioComp=$registro['valor'];
	}

	if ($FolioComp==0) {
		$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','$TComp','2','A');");
		$FolioComp=1;
	}else{
		$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$TComp' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
	}

	$SQL = $SQL."('$Periodo','$RutEmpresa','$xfecha','".$_POST['titulo1']."','','','','$FECHA','A','$KeyAs','','','$FolioComp','$TComp',$xccosto);";

	$mysqli->query($SQL);
	
	$mysqli->query("INSERT INTO CTFondo VALUES('','".$_POST['idcierre']."','$xRut','$RutEmpresa','".$_POST['titulo1']."','$xfecha','$L2','".$smonto."','$KeyAs','$FECHA','E','A');");

	$mysqli->query("UPDATE CTFondo SET Estado='C' WHERE Id='".$_POST['idcierre']."' AND RutEmpresa='$RutEmpresa' AND Tipo='I'");

	$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$TComp' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");


	$mysqli->close();

	header("location:../Fondos/");
	exit;
?>
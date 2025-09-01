<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$Periodo=$_SESSION['PERIODO'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$FECHA=date("Y/m/d");


	$xfecha=$_POST['fdesde'];
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

	$SQL="SELECT * FROM CTFondoPersonal WHERE Id='".$_POST['SelAsignar']."' ORDER BY Nombre";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xRut=$registro['Rut'];
	}

	$SQL="SELECT * FROM CTCliPro  WHERE rut='$xRut'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$IdRut=$registro['id'];
	}

	$SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
	$SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$L1','".$_POST['monto']."','0','$FECHA','A','$KeyAs','','','0','',$xccosto),";
	$SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$L2','0','".$_POST['monto']."','$FECHA','A','$KeyAs','','','0','',$xccosto),";

	$xglosa=$_POST['tglosa'];

	$TanoD = substr($Periodo,3,4);
	$FolioComp=0;
	$SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
	$resultados = $mysqli->query($SQL1);
	while ($registro = $resultados->fetch_assoc()) {
		$FolioComp=$registro['valor'];
	}

	if ($FolioComp==0) {
		$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','T','2','A');");
		$FolioComp=1;
	}else{
		$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
	}

	$SQL = $SQL."('$Periodo','$RutEmpresa','$xfecha','".$_POST['titulo']."','','','','$FECHA','A','$KeyAs','','','$FolioComp','T',$xccosto);";

	$mysqli->query($SQL);
	
	$mysqli->query("INSERT INTO CTFondo VALUES('','$IdRut','$xRut','$RutEmpresa','".$_POST['titulo']."','$xfecha','$L1','".$_POST['monto']."','$KeyAs','$FECHA','I','A');");

	$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");


	$mysqli->close();

	header("location:../Fondos/");
?>
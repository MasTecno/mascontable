<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $PeriodoX=$Periodo;

    if($Periodo==""){
		header("location:../frmMain.php");
		exit;
    }
    
    $frm=$_POST['frm'];

    $dmes = substr($Periodo,0,2);
    $dano = substr($Periodo,3,4);
    
	function UltimoDiaMesD($periodo) { 
		$month = substr($periodo,0,2);
		$year = substr($periodo,3,4);
		$day = date("d", mktime(0,0,0, $month+1, 0, $year));

		return date('d', mktime(0,0,0, $month, $day, $year));
	};

    $textfecha=UltimoDiaMesD($Periodo)."-".$dmes."-".$dano;

	if ($dmes=="01") {
		$xMes="Enero";
	}
	if ($dmes=="02") {
		$xMes="Febrero";
	}
	if ($dmes=="03") {
		$xMes="Marzo";
	}
	if ($dmes=="04") {
		$xMes="Abril";
	}
	if ($dmes=="05") {
		$xMes="Mayo";
	}
	if ($dmes=="06") {
		$xMes="Junio";
	}
	if ($dmes=="07") {
		$xMes="Julio";
	}
	if ($dmes=="08") {
		$xMes="Agosto";
	}
	if ($dmes=="09") {
		$xMes="Septiembre";
	}
	if ($dmes=="10") {
		$xMes="Octubre";
	}
	if ($dmes=="11") {
		$xMes="Noviembre";
	}
	if ($dmes=="12") {
		$xMes="Diciembre";
	}

    if($frm=="C"){
        $GlosaAsi="CENTRALIZACI&Oacute;N COMPRAS ".strtoupper($xMes)." ".$dano;
        $GlosaPag="EGRESO POR COMPRAS ".strtoupper($xMes)." ".$dano;
    }else{
        $GlosaAsi="CENTRALIZACI&Oacute;N VENTAS ".strtoupper($xMes)." ".$dano;
        $GlosaPag="INGRESOS POR VENTAS ".strtoupper($xMes)." ".$dano;
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

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
    }

	/////// rEGITRO EN LIBRO DIARIO
if (isset($_POST['ace'])) {

	if($_SESSION['KEYASIENTO']==""){
		$_SESSION['KEYASIENTO']=date("YmdHis");
	}

	$KeyAs=$_SESSION['KEYASIENTO'];
    $FECHA=date("Y/m/d");


	$xfecha=$_POST['d1'];

	$dia = substr($xfecha,0,2);
    $mes = substr($xfecha,3,2);
    $ano = substr($xfecha,6,4);

    $xfecha=$ano."/".$mes."/".$dia;

    //$xfecha=date("Y/m/d");

    $xglosa="";

    $lineas = array();
    $sumarete=0;
    $sumaiva=0;
    if ($_SESSION["PLAN"]=="S"){
		$SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
    }else{
		$SQL="SELECT * FROM CTCuentas ORDER BY id";
    }
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $NUMCUE=$registro["numero"];
        $Cont=0;
        $Sexento=0;
        $Sneto=0;
        $Siva=0;
        $Srete=0;
        $Stotal=0;

        $SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND cuenta='$NUMCUE' AND lote='' ORDER BY fecha";
        $resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {

			$SQL2="SELECT * FROM CTTipoDocumento WHERE id='".$registro1["id_tipodocumento"]."'";
			$resultados2 = $mysqli->query($SQL2);
			while ($registro2 = $resultados2->fetch_assoc()) {
				$operador=$registro2["operador"];
			}

			if($operador=="R"){
				$operador=-1;
			}else{
				$operador=1;
			}

            $Cont=$Cont+1;
            $Sexento=$Sexento+($registro1["exento"]*$operador);
            $Sneto=$Sneto+($registro1["neto"]*$operador);
            $Siva=$Siva+($registro1["iva"]*$operador);
            $Srete=$Srete+($registro1["retencion"]*$operador);
            $Stotal=$Stotal+($registro1["total"]*$operador);

        }

		if ($Cont>0) {
			$lineas["$NUMCUE"]=($Sneto+$Sexento);
			$sumarete=$sumarete+$Srete;
			$sumaiva=$sumaiva+$Siva;
		}
    }

	$contlin=1;
	$totalizador=0;
	$xglosa=$_POST['tglosa'];
	$xglosap=$_POST['tglosap'];

    $FolioComp=0;
    $TanoD = substr($Periodo,3,4);
    $SQL="SELECT * FROM CTComprobanteFolio WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $FolioComp=$registro['valor'];
    }

    if ($FolioComp==0) {
        $mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','T','1','A');");
        $FolioComp=1;
    }else{
        $mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
    }
	
	if ($frm=="C") {

		foreach($lineas as $cuenta=>$valor){
			if ($_SESSION["PLAN"]=="S"){
		    	$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
		    	$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}
			if ($frm=="C") {
				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$cuenta','$valor','0','$FECHA','A','$KeyAs','','','0','')");
			}

			$totalizador=$totalizador+$valor;
		}

		if ($sumarete>0 || $sumarete<0) {
			$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			$resultados = $mysqli->query($SQL);

			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
			}

			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$cuenta=$registro["L3"];
			}
			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$cuenta','$sumarete','0','$FECHA','A','$KeyAs','','','0','')");
		}

		if ($frm=="C") {

			$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			$resultados = $mysqli->query($SQL);

			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
			}

			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$contra=$registro["L2"];
			}
			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}
			if ($sumaiva>0) {
				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUEs('$Periodo','$RutEmpresa','$xfecha','','$contra','$sumaiva','0','$FECHA','A','$KeyAs','','','0','')");
			}

			$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			$resultados = $mysqli->query($SQL);

			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$contra=$registro["L4"];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}

			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUEs('$Periodo','$RutEmpresa','$xfecha','','$contra','0','".($totalizador+$sumaiva+$sumarete)."','$FECHA','A','$KeyAs','','','0','')");
			$TPago=$totalizador+$sumaiva+$sumarete;
			$CtaProveedor=$contra;

			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUEs('$Periodo','$RutEmpresa','$xfecha','$xglosa','','0','0','$FECHA','A','$KeyAs','$KeyAs','','$FolioComp','T')");

			$_SESSION['KEYASIENTO']="";
		}

		$mysqli->query("UPDATE CTRegDocumentos SET lote='$KeyAs', keyas='$KeyAs' WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND lote=''");

		if (isset($_POST['PAuto'])) {
			$TanoD = substr($Periodo,3,4);
			$SQL="SELECT * FROM CTComprobanteFolio WHERE tipo='E' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$FolioComp=$registro['valor'];
			}

			if ($FolioComp==0) {
				$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','E','1','A');");
				$FolioComp=1;
			}else{
				$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='E' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
			}

			$KeyPag=$KeyAs+1;
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$CtaProveedor','$TPago','0','$FECHA','A','$KeyPag','','','0','')");
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','".$_POST['Comp4']."','0','$TPago','$FECHA','A','$KeyPag','','','0','')");
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','$xglosap','','0','0','$FECHA','A','$KeyPag','$KeyPag','','$FolioComp','E')");

			$SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND lote='$KeyAs' AND keyas='$KeyAs'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','".$registro['rut']."','$PeriodoX','".$registro['id_tipodocumento']."','".$registro['numero']."','$KeyPag','".$registro['total']."','".$registro['fecha']."','$FECHA','C','M','A')");
			}
		}

	}

	if ($frm=="V") {

		if ($frm=="V") {

			$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			$resultados = $mysqli->query($SQL);

			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$contra=$registro["L1"];
				$CtaCliente=$registro["L1"];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}

			foreach($lineas as $cuenta=>$valor){
				$totalizador=$totalizador+$valor;
			}
			$TPago=$totalizador+$sumaiva+$sumarete;

			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$contra','".($totalizador+$sumaiva+$sumarete)."','0','$FECHA','A','$KeyAs','','','0','')");

			$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			$resultados = $mysqli->query($SQL);

			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$contra=$registro["L3"];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
			}
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}
			if ($contra!="") {
				if ($sumaiva!=0) {
					if ($sumaiva<0) {
						$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$contra','".($sumaiva*-1)."','0','$FECHA','A','$KeyAs','','','0','')");
					}else{
						$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$contra','0','$sumaiva','$FECHA','A','$KeyAs','','','0','')");
					}
				}
			}
		}

		foreach($lineas as $cuenta=>$valor){

			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
			}

			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}
			if ($frm=="V") {
				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$cuenta','0','$valor','$FECHA','A','$KeyAs','','','0','')");
			}

			$totalizador=$totalizador+$valor;
		}

		if ($sumarete>0 || $sumarete<0) {

			$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			$resultados = $mysqli->query($SQL);

			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
			}
			
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$cuenta=$registro["L4"];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
			}else{
				$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
			}

			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$nombre=strtoupper($registro["detalle"]);
			}
			if($sumarete>0){
				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$cuenta','0','$sumarete','$FECHA','A','$KeyAs','','','','')");
			}else{
				$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$cuenta','".($sumarete*-1)."','0','$FECHA','A','$KeyAs','','','','')");
			}
		}

		$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','$xglosa','','0','0','$FECHA','A','$KeyAs','$KeyAs','','$FolioComp','T')");

		$mysqli->query("UPDATE CTRegDocumentos SET lote='$KeyAs', keyas='$KeyAs' WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND lote=''");




		if (isset($_POST['PAuto'])) {
			$TanoD = substr($Periodo,3,4);
			$SQL="SELECT * FROM CTComprobanteFolio WHERE tipo='I' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$FolioComp=$registro['valor'];
			}

			if ($FolioComp==0) {
				$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','I','1','A');");
				$FolioComp=1;
			}else{
				$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='I' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
			}

			$KeyPag=$KeyAs+1;
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','".$_POST['Comp4']."','$TPago','0','$FECHA','A','$KeyPag','','','0','')");
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$CtaCliente','0','$TPago','$FECHA','A','$KeyPag','','','0','')");
			$mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','$xglosap','','0','0','$FECHA','A','$KeyPag','$KeyPag','','$FolioComp','I')");


			$SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND lote='$KeyAs' AND keyas='$KeyAs'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','".$registro['rut']."','$PeriodoX','".$registro['id_tipodocumento']."','".$registro['numero']."','$KeyPag','".$registro['total']."','".$registro['fecha']."','$FECHA','V','M','A')");
			}
		}


		$_SESSION['KEYASIENTO']="";

	}

	}


	$mysqli->close();

	?>
	<!DOCTYPE html>
	<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">
		<script type="text/javascript">
			function Porce(){
				sw = document.getElementById("ace").checked;
				sw1 = document.getElementById("PAuto").checked;

				if (form1.tglosa.value=="") {
					alert("Ingrese Glosa");
				}else{
					if (sw1== true && (form1.Comp4.value=="" || form1.tglosap.value=="") ) {
						alert("Debe asignar cuanta para el Pago y/o no cuenta con una Glosa");
					}else{
						if (sw==true) {				
							form1.submit();
						}
					}
				}
			}

			function acept(){
				sw = document.getElementById("ace").checked;

				if (sw==false) {
					document.getElementById("bt").classList.remove("active");
					document.getElementById("bt").classList.add("disabled");
				}else{
					document.getElementById("bt").classList.remove("disabled");
					document.getElementById("bt").classList.add("active");
				}
			}

			function Volver(){
				form1.action="../frmMain.php";
				form1.submit();
			}

			jQuery(document).ready(function(e) {
				$('#myModal').on('shown.bs.modal', function() {
					$('input[name="BCodigo"]').focus();
				});
			});

		</script>

	</head>

	<body>


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<?php
				if ($xglosa=="") {
			?>			
    		<div class="col-sm-2">
			</div>
			<div class="col-sm-8">     

			<div class="col-sm-12 text-left">

			<h4 class="text-center">RESUMEN POR CUENTAS</h4>
				<input type="hidden" class="form-control" id="frm" name="frm" value="<?php echo $_POST['frm'];?>" >

					<table class="table table-hover table-condensed">
						<thead>
						<tr style="background-color: #d9d9d9;">
							<th>N&deg; Cuenta</th>
							<th>Nombre Cuenta</th>
							<th>Cantidad</th>
							<th>Exento</th>
							<th>Neto</th>
							<th>Iva</th>
							<th>Reten/Imp.Esp.</th>
							<th>Total</th>
						</tr>
						</thead>
						<?php 

							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$lineas = array();
							$sumarete=0;
							$sumaiva=0;
							$SwNegativo="N";

							$SQL="SELECT cuenta FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND lote='' AND origen<>'Z' GROUP BY cuenta ORDER BY fecha";

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$NUMCUE=$registro["cuenta"];

								if ($_SESSION["PLAN"]=="S"){   //cueta empresa
									$SQL2="SELECT * FROM CTCuentasEmpresa WHERE numero='$NUMCUE' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
								}else{
									$SQL2="SELECT * FROM CTCuentas WHERE numero='$NUMCUE' ORDER BY id";
								}

								$resultados2 = $mysqli->query($SQL2);
								while ($registro2 = $resultados2->fetch_assoc()) {
								$NOMBCUENTA=$registro2["detalle"];
								}

								$Cont=0;
								$Sexento=0;
								$Sneto=0;
								$Siva=0;
								$Srete=0;
								$Stotal=0;

								$SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' AND cuenta='$NUMCUE' AND lote='' ORDER BY fecha";
								// echo "<br>";
								$resultados1 = $mysqli->query($SQL1);
								while ($registro1 = $resultados1->fetch_assoc()) {

									$SQL2="SELECT * FROM CTTipoDocumento WHERE id='".$registro1["id_tipodocumento"]."'";
									$resultados2 = $mysqli->query($SQL2);
									while ($registro2 = $resultados2->fetch_assoc()) {
									$operador=$registro2["operador"];
									}

									if($operador=="R"){
										$operador=-1;
									}else{
										$operador=1;
									}

									$Cont=$Cont+1;
									$Sexento=$Sexento+($registro1["exento"]*$operador);
									$Sneto=$Sneto+($registro1["neto"]*$operador);
									$Siva=$Siva+($registro1["iva"]*$operador);
									$Srete=$Srete+($registro1["retencion"]*$operador);
									$Stotal=$Stotal+($registro1["total"]*$operador);
								}
								if ($Cont>0) {
									$lineas["$NUMCUE"]=($Sneto+$Sexento);
									$sumarete=$sumarete+$Srete;
									$sumaiva=$sumaiva+$Siva;

									echo '
										<tr>
											<td>'.$registro["cuenta"].'</td>
											<td>'.$NOMBCUENTA.'</td>
											<td>'.$Cont.'</td>
											<td align="right">$'.number_format(($Sexento), $NDECI, $DDECI, $DMILE).'</td>
											<td align="right">$'.number_format(($Sneto), $NDECI, $DDECI, $DMILE).'</td>
											<td align="right">$'.number_format(($Siva), $NDECI, $DDECI, $DMILE).'</td>
											<td align="right">$'.number_format(($Srete), $NDECI, $DDECI, $DMILE).'</td>
											<td align="right">$'.number_format(($Stotal), $NDECI, $DDECI, $DMILE).'</td>
										</tr>
									';
								}
								// if($Sexento<0 || $Sneto<0 || $Siva<0 || $Srete<0 || $Stotal<0 ){
								// 	$SwNegativo="S";
								// }
							}
							$mysqli->close();
						?>
					</table>

				<br>
				<div class="clearfix"></div>

				<div class="col-md-2">
					<label>Fecha Centralizaci&oacute;n</label>
					<input id="d1" name="d1" type="text" class="form-control text-right" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
				</div> 
				<div class="clearfix"></div>

				<?php
					$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

					if ($frm=="C") {
						echo "<br>";
						$contlin=1;
						$totalizador=0;

						foreach($lineas as $cuenta=>$valor){
							if ($_SESSION["PLAN"]=="S"){
								$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							}else{
								$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
							}
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$nombre=strtoupper($registro["detalle"]);
							}
							if ($frm=="C") {
								echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$cuenta.'"></div>';
								echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="'.$valor.'" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="clearfix"></div>';    	
								if($valor<0){
									$SwNegativo="S";
								}
							}

							$contlin++;
							$totalizador=$totalizador+$valor;
						}

						if ($sumarete>0 || $sumarete<0) {
							if ($frm=="C") {

								$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
								$resultados = $mysqli->query($SQL);

								$row_cnt = $resultados->num_rows;
								if ($row_cnt==0) {
									$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
								}
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									$cuenta=$registro["L3"];
								}
								if ($_SESSION["PLAN"]=="S"){
									$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
								}else{
									$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
								}
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									$nombre=strtoupper($registro["detalle"]);
								}

								echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$cuenta.'"></div>';
								echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="'.$sumarete.'" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="clearfix"></div>';	
							}

							$contlin++;	
						}

						if ($frm=="C") {
							$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							$resultados = $mysqli->query($SQL);

							$row_cnt = $resultados->num_rows;
							if ($row_cnt==0) {
								$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
							}
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$contra=$registro["L2"];
							}
							if ($_SESSION["PLAN"]=="S"){
								$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							}else{
								$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
							}
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$nombre=strtoupper($registro["detalle"]);
							}

							if ($sumaiva>0) {
								echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$contra.'"></div>';
								echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="'.$sumaiva.'" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="clearfix"></div>';		

								$contlin++;
							}


							$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							$resultados = $mysqli->query($SQL);

							$row_cnt = $resultados->num_rows;
							if ($row_cnt==0) {
								$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
							}
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$contra=$registro["L4"];
							}

							if ($_SESSION["PLAN"]=="S"){
								$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							}else{
								$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
							}
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$nombre=strtoupper($registro["detalle"]);
							}

							echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$contra.'"></div>';
							echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="'.($totalizador+$sumaiva+$sumarete).'" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="clearfix"></div>';		
						}
					}
					//VENTAS
					if ($frm=="V") {
						echo "<br>";
						$contlin=1;
						$totalizador=0;

						if ($frm=="V") {
							$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							$resultados = $mysqli->query($SQL);

							$row_cnt = $resultados->num_rows;
							if ($row_cnt==0) {
								$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
							}
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$contra=$registro["L1"];
							}
							if ($_SESSION["PLAN"]=="S"){
								$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							}else{
								$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
							}

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$nombre=strtoupper($registro["detalle"]);
							}

							foreach($lineas as $cuenta=>$valor){
								$totalizador=$totalizador+$valor;
							}

							echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$contra.'"></div>';
							echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="'.($totalizador+$sumaiva+$sumarete).'" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="clearfix"></div>';		

							$contlin++;

							$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							$resultados = $mysqli->query($SQL);
							$SQLdd=$SQL;

							$row_cnt = $resultados->num_rows;
							if ($row_cnt==0) {
								$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
							}
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$contra=$registro["L3"];
							}


								if ($_SESSION["PLAN"]=="S"){
									$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$contra' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
								}else{
									$SQL="SELECT * FROM CTCuentas WHERE numero='$contra' ORDER BY id";
								}

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$nombre=strtoupper($registro["detalle"]);
							}

							if ($contra!="") {
								echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$contra.'"></div>';
								echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';


								if ($sumaiva<0) {
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="'.($sumaiva*-1).'" onKeyPress="return soloNumeros(event)" ></div>';
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
								}else{
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="'.$sumaiva.'" onKeyPress="return soloNumeros(event)" ></div>';
								}

								echo '<div class="clearfix"></div>';		
							}
						}


						foreach($lineas as $cuenta=>$valor){

							if ($_SESSION["PLAN"]=="S"){
								$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
							}else{
								$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
							}

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$nombre=strtoupper($registro["detalle"]);
							}
							if ($frm=="V") {
								echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$cuenta.'"></div>';
								echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="'.$valor.'" onKeyPress="return soloNumeros(event)" ></div>';
								echo '<div class="clearfix"></div>';  
								if($valor<0){
									$SwNegativo="S";
								}
							}

							$contlin++;
							$totalizador=$totalizador+$valor;
						}

						if ($sumarete>0 || $sumarete<0) {


							if ($frm=="V") {
								// echo "SSSS";
								$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
								$resultados = $mysqli->query($SQL);

								$row_cnt = $resultados->num_rows;
								if ($row_cnt==0) {
									$SQL="SELECT * FROM CTAsiento WHERE tipo='$frm' AND rut_empresa=''";
								}
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									$cuenta=$registro["L4"];
								}

								if ($_SESSION["PLAN"]=="S"){
									$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$cuenta' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id";
								}else{
									$SQL="SELECT * FROM CTCuentas WHERE numero='$cuenta' ORDER BY id";
								}

								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									$nombre=strtoupper($registro["detalle"]);
								}

								echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$cuenta.'"></div>';
								echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nombre.'"></div>';
								if($sumarete<0){
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="'.($sumarete*-1).'" onKeyPress="return soloNumeros(event)" ></div>';
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';	
								}else{
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
									echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="'.$sumarete.'" onKeyPress="return soloNumeros(event)" ></div>';	
								}
								echo '<div class="clearfix"></div>';	
							}

							$contlin++;	
						}
					}
					$mysqli->close();
				?>

			<!-- </div> -->
			<div class="clearfix"></div>
			<br>

			<div class="col-sm-12">
				<div class="input-group">
					<span class="input-group-addon">Glosa</span>
					<input type="text" class="form-control" id="tglosa" name="tglosa" value="<?php echo $GlosaAsi; ?>" onChange="javascript:this.value=this.value.toUpperCase();" style="z-index: 1;" required>
				</div>			      	
			</div>


			<div class="col-sm-12">

				<!-- Modal  buscar codigo-->
				<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
					<div class="modal-header">
						<!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
						<h4 class="modal-title">Listado de Cuentas</h4>
					</div>

					<div class="modal-body">
						<div class="col-md-12">
							<input class="form-control" id="BCodigo" name="BCodigo" type="text" placeholder="Buscar...">
						</div>
						<div class="col-md-12">

							<table class="table table-condensed table-hover">
								<thead>
									<tr>
										<th>Codigo</th>
										<th>Detalle</th>
										<th>Tipo de Cuenta</th>
									</tr>
								</thead>
								<tbody id="TableCod">
								<?php 

									$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
									if ($_SESSION["PLAN"]=="S"){
										$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
									}else{
										$SQL="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
									}
									$resultados = $mysqli->query($SQL);
									while ($registro = $resultados->fetch_assoc()) {

										$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
										$res = $mysqli->query($SQL1);
										while ($reg = $res->fetch_assoc()) {
											$tcuenta=$reg["nombre"];
										}

										echo '
											<tr onclick="data(\''.$registro["numero"].'\')">
											<td>'.$registro["numero"].'</td>
											<td>'.strtoupper($registro["detalle"]).'</td>
											<td>'.$tcuenta.'</td>
											</tr>
										';


									}
									$mysqli->close();

								?>

								</tbody>
							</table>
							<script>
								$(document).ready(function(){
									$("#BCodigo").on("keyup", function() {
									var value = $(this).val().toLowerCase();
										$("#TableCod tr").filter(function() {
										$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
									});
									});
								});
							</script>

						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" id="cmodel">Cerrar</button>
					</div>
					</div>
				</div>
				</div>
				<!-- fin buscar codigo -->

				<script type="text/javascript">
					function BuscaCuenta(vall){
						var url= "../buscacuenta.php";
						var x1=$('#'+vall).val();
						$.ajax({
							type: "POST",
							url: url,
							data: ('dat1='+x1),
							success:function(resp)
							{

							var r=Number(vall.substr(4, 1));
							var r='DComp'+r;

							if(resp==""){
								alert("No se encontro cuenta");
								$('#'+vall).focus(); 
								$('#'+vall).select();
								document.getElementById(r).value="";
							}else{
								document.getElementById(r).value=resp;
							}
							}
						}); 
					}

					function data(valor){
						var cas=form1.casilla.value;
						document.getElementById(cas).value=valor;

						//$('#'+cas).val()=valor;
						BuscaCuenta(form1.casilla.value);
						document.getElementById("cmodel").click();
					}

				</script>

				<input type="hidden" name="casilla" id="casilla">
				<div class="clearfix"></div>
				<br>
				
				<div class="clearfix"></div>
				<br>
				<h4>Generar pago Inmediato</h4>
				<div class="col-md-3">
					<label>Cuenta</label>
					<div class="input-group"> 
						<input type="text" class="form-control text-right" id="Comp4" name="Comp4" required maxlength="50" value="<?php echo $XPago; ?>">
						<div class="input-group-btn"> 
							<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp4'">
								<span class="glyphicon glyphicon-search"></span>
							</a>
						</div> 
					</div> 
				</div>

				<div class="col-md-9">
					<label>Detalle</label>  
					<input type="text" class="form-control" id="DComp4" name="DComp4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnPago); ?>"  readonly="false" >
				</div>
				<div class="clearfix"></div>
				<br>

				<div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon">Glosa</span>
						<input type="text" class="form-control" id="tglosap" name="tglosap" value="<?php echo $GlosaPag; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
					</div>			      	
				</div>
			</div>

			<?php
				if($SwNegativo=="S"){
			?>
				<div class="col-sm-2"></div>
				<div class="col-sm-8 text-center">
					<h3>No se puede proceder dado que existen montos en negativo, puede ser provocado por Notas de Cr&eacute;dito. Se recomienda centralizar de forma individual</h3>
				</div>
			<?php	
				}else{
			?>

				<div class="col-sm-12 text-center">
					<label class="checkbox-inline"><input type="checkbox" id="PAuto" name="PAuto">Generar Pago Aut&oacute;matico</label>

					<br><br>
					<label class="checkbox-inline"><input type="checkbox" onclick="acept()" id="ace" name="ace">Aceptar</label> 
					<p>* Este proceso Genera La centralizaci&oacute;n masiva de todas las factura pendientes.</p>         	
					<p>** La Centralizaci&oacute;n dependera de las cuentas asigandas anteriormente.</p>         	
				</div>


				<div class="col-sm-12 text-center">
					<button type="button" class="btn btn-primary btn-md disabled" onclick="Porce()" id="bt" name="bt">Centralizar todas las facturas Pendientes</button>   
				</div>
			<?php
				}
			?>


			<div class="clearfix"></div>
			<br>
				
				<div class="col-sm-2">
				</div>
			<?php
				}else{
			?>
				<div class="col-sm-2">
				</div>
				<div class="col-sm-8 text-center">     
					<br>
					<br>
					<div class="col-sm-12 text-left">
						<div class="alert alert-success">
							<strong>Operati&oacute;n exitosa!</strong> Documentos Centralidados en Nombre; <?php echo $xglosa; ?>.
						</div>
					</div>
					<hr>
					<div class="col-sm-12">
						<button type="button" class="btn btn-primary btn-md " onclick="Volver()">Volver</button>   
					</div>    			
				</div>

				<div class="col-sm-2">
				</div>


			<?php }?>
		</form>

	</div>
	</div>
	
	

	<div class="clearfix"> </div>



	<?php include '../footer.php'; ?>

	</body>
	<script type="text/javascript">

		$( "#d1" ).datepicker({
			// Formato de la fecha
			dateFormat: "dd-mm-yy",
			// Primer dia de la semana El lunes
			firstDay: 1,
			// Dias Largo en castellano
			dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
			// Dias cortos en castellano
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			// Nombres largos de los meses en castellano
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
			// Nombres de los meses en formato corto 
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
			// Cuando seleccionamos la fecha esta se pone en el campo Input 
			onSelect: function(dateText) { 
				$('#d1').val(dateText);
			}
		});  
	</script>

</html>
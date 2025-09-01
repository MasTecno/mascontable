<?php
 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

// echo $_POST['dat2'];
// exit;
	if($_POST['dat2']!=""){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$lotefac=0;
		$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'";
		// exit;
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

			$mysqli->query("UPDATE CTHonorarios SET movimiento='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND movimiento='".$_POST['dat2']."'");

			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['dat2']."'");
			
			$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");

		}else{

			$mysqli->query("UPDATE CTRegDocumentos SET lote='', keyas='' WHERE estado='A' AND rutempresa='$RutEmpresa' AND keyas='".$_POST['dat2']."'");

			$mysqli->query("DELETE FROM CTFondo WHERE keyas='".$_POST['dat2']."' AND RutEmpresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");

			$mysqli->query("DELETE FROM CTControlDocumento WHERE keyas='".$_POST['dat2']."'");
			
			$mysqli->query("DELETE FROM CTControRegDocPago WHERE keyas='".$_POST['dat2']."' AND rutempresa='$RutEmpresa'");

		}

		$mysqli->close();
	}
	if ($_POST['Origen']=="Visualizar") {
		header("location:Visualizar.php");
		exit;
	}
	// session_start();
	// session_destroy();
	// header("location:../index.php?Msj=95");

?>
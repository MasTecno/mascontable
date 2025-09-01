<?php


	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../index.php?Msj=95");
		exit;
	}

	include 'conexionserver.php';
	include 'conexion.php';
	$mysqli=conectarServer();

	$sql = "SELECT * FROM UnionServer WHERE Server='Server99'";
	$resultado = $mysqli->query($sql);

	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		$xcla=$registro["Clave"];
		$xbas=$registro["Base"];
	}
	$mysqli->close();

	$mysqliX=xconectar($xusu,$xcla,$xbas);

	echo $x="DELETE FROM RMParametros WHERE periodo='".$_POST['TxtPeriodo']."';
	DELETE FROM RMFacturesIU WHERE periodo='".$_POST['TxtPeriodo']."';
	DELETE FROM RMAsigFamilia WHERE periodo='".$_POST['TxtPeriodo']."';
	DELETE FROM RMAfpTabla WHERE periodo='".$_POST['TxtPeriodo']."';
	"."\r\n";



	$DatPar='INSERT INTO RMParametros (id, periodo, SueldoMinimo, ValorUF, ValorUFAnt, ValorUTM, ProcSaludLegal, ProcFonasa, ProcCCAF, TopeAFC, TopePrevAFP, TopePrevIPS, TopeSaludIPS, PorcCesTrab, PorcCesEmp, PorcCesEmp11, PorcesIndSus, ZonaExtrema, CapIndividual, ExpectativaVida, RentabilidadProtegida, fechareg, fechamod, estado) VALUES';


	$sql = "SELECT * FROM RMParametros WHERE periodo='".$_POST['TxtPeriodo']."'";
	$resultado = $mysqliX->query($sql);
	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		if ($DatParDet!="") {
			$DatParDet=$DatParDet.",";
		}
		$DatParDet=$DatParDet."('','".$registro["periodo"]."','".$registro["SueldoMinimo"]."','".$registro["ValorUF"]."','".$registro["ValorUFAnt"]."','".$registro["ValorUTM"]."','".$registro["ProcSaludLegal"]."','".$registro["ProcFonasa"]."','".$registro["ProcCCAF"]."','".$registro["TopeAFC"]."','".$registro["TopePrevAFP"]."','".$registro["TopePrevIPS"]."','".$registro["TopeSaludIPS"]."','".$registro["PorcCesTrab"]."','".$registro["PorcCesEmp"]."','".$registro["PorcCesEmp11"]."','".$registro["PorcesIndSus"]."','".$registro["ZonaExtrema"]."','".$registro["CapIndividual"]."','".$registro["ExpectativaVida"]."','".$registro["RentabilidadProtegida"]."','".$registro["fechareg"]."','".$registro["fechamod"]."','".$registro["estado"]."')";
	}

	echo $DatParDet=$DatPar.$DatParDet.";"."\r\n\r\n";



	$DatIU='INSERT INTO RMFacturesIU (id, tramo, periodo, topetramo, factor, rebaja) VALUES';
	
	$sql = "SELECT * FROM RMFacturesIU WHERE periodo='".$_POST['TxtPeriodo']."'";
	$resultado = $mysqliX->query($sql);
	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		if ($DatIUDet!="") {
			$DatIUDet=$DatIUDet.",";
		}
		$DatIUDet=$DatIUDet."('','".$registro["tramo"]."','".$registro["periodo"]."','".$registro["topetramo"]."','".$registro["factor"]."','".$registro["rebaja"]."')";
	}

	echo $DatIUDet=$DatIU.$DatIUDet.";"."\r\n\r\n";


	$DatCarFam='INSERT INTO RMAsigFamilia (id, tramo, periodo, desde, hasta, valor) VALUES';
	
	$sql = "SELECT * FROM RMAsigFamilia WHERE periodo='".$_POST['TxtPeriodo']."'";
	$resultado = $mysqliX->query($sql);
	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		if ($DatCarFamDet!="") {
			$DatCarFamDet=$DatCarFamDet.",";
		}
		$DatCarFamDet=$DatCarFamDet."('','".$registro["tramo"]."','".$registro["periodo"]."','".$registro["desde"]."','".$registro["hasta"]."','".$registro["valor"]."')";
	}

	echo $DatCarFamDet=$DatCarFam.$DatCarFamDet.";"."\r\n\r\n";


	$DatAfp='INSERT INTO RMAfpTabla (id, id_Afp, periodo, valor, sis, independiente, fechareg, fechamod) VALUES';
	
	$sql = "SELECT * FROM RMAfpTabla WHERE periodo='".$_POST['TxtPeriodo']."'";
	$resultado = $mysqliX->query($sql);
	while ($registro = $resultado->fetch_assoc()) {
		$xusu=$registro["Usuario"];
		if ($DatAfpDet!="") {
			$DatAfpDet=$DatAfpDet.",";
		}
		$DatAfpDet=$DatAfpDet."('','".$registro["id_Afp"]."','".$registro["periodo"]."','".$registro["valor"]."','".$registro["sis"]."','".$registro["independiente"]."','".$registro["fechareg"]."','".$registro["fechamod"]."')";
	}

	echo $DatAfpDet=$DatAfp.$DatAfpDet.";"."\r\n\r\n";






	$mysqliX->close();

?> 
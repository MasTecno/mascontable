<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	// $NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	// $RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$Mes = substr($Periodo, 0, 2);
	$Ano = substr($Periodo, 3, 4);


	//////Elimina Asisntos Incompletos
	$SQL="SELECT * FROM CTRegLibroDiario WHERE glosa<>'' AND ncomprobante<=0 AND rutempresa='$RutEmpresa' AND periodo like '%-$Ano'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Xkeyas=$registro["keyas"];
		$zSQL="DELETE FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND keyas='$Xkeyas' AND periodo like '%-$Ano'";
		$mysqli->query($zSQL);
	}

	$Cadena="";
	
	/////Ve si los asientos esta cuadrados 
	$SQL="SELECT * FROM CTRegLibroDiario WHERE glosa<>'' AND ncomprobante>0 AND rutempresa='$RutEmpresa' AND periodo like '%-$Ano'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Xkeyas=$registro["keyas"];
		$Xfecha=date('d-m-Y',strtotime($registro["fecha"]));
		$Xglosa=$registro["glosa"];
		$Xncomprobante=$registro["ncomprobante"];

		$zSQL="SELECT sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE glosa='' AND ncomprobante=0 AND rutempresa='$RutEmpresa' AND keyas='$Xkeyas' AND periodo like '%-$Ano'";
		$Resu = $mysqli->query($zSQL);
		while ($Reg = $Resu->fetch_assoc()) {
			if ($Reg['sdebe']!=$Reg['shaber']) {
				$Cadena=$Cadena."El comprobante N: ".$Xncomprobante.", de Fecha: ".$Xfecha.", Segun Glosa: ".$Xglosa.", Descuadrado<br>";
			}
		}
	}

	if ($Cadena!="") {
		echo $Cadena;
		$mysqli->close();
		exit;
	}

	$SQL="SELECT * FROM CTRegLibroDiario WHERE glosa<>'' AND ncomprobante>0 AND rutempresa='$RutEmpresa' AND periodo like '%-$Ano'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Xkeyas=$registro["keyas"];
		$Xtipo=$registro["tipo"];

		$zSQL="UPDATE CTRegLibroDiario SET tipo='$Xtipo' WHERE ncomprobante=0 AND rutempresa='$RutEmpresa' AND keyas='$Xkeyas' AND periodo like '%-$Ano'";
		$mysqli->query($zSQL);

	}

	$SQL="SELECT * FROM CTAsientoApertura WHERE RutEmpresa='$RutEmpresa' AND Periodo like '%-$Ano'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$SQL="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND periodo like '%-$Ano' AND keyas='".$registro['KeyAs']."'";
		$resultados1 = $mysqli->query($SQL);
		$row_cnt = $resultados1->num_rows;

		if ($row_cnt==0) {
			$zSQL="DELETE FROM CTAsientoApertura WHERE RutEmpresa='$RutEmpresa' AND Periodo like '%-$Ano' AND KeyAs='".$registro['KeyAs']."'";
			$mysqli->query($zSQL);
		}

	}

	$SQL="SELECT * FROM CTAsientoApertura WHERE RutEmpresa='$RutEmpresa' AND Periodo like '%-$Ano'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;

	if ($row_cnt==0) {
		echo "Esta empresa NO cuenta con un asiento de apertura, operaci&oacute;n canceldada";
		$mysqli->close();
		exit;
	}

	if ($row_cnt>1) {
		echo "Esta empresa cuenta con MAS de un asiento de apertura, operaci&oacute;n canceldada";
		$mysqli->close();
		exit;
	}

	$SQL="SELECT * FROM CTAsientoApertura WHERE RutEmpresa='$RutEmpresa' AND Periodo like '%-$Ano'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$keyasInicio=$registro["KeyAs"];
	}

	$keyasInicio."<br>";

	unset($_SESSION['ALisKeyas']);
	$Indece=0;
	$SQL="SELECT id, fecha, glosa, tipo, ncomprobante, keyas FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND periodo like '%-$Ano' ORDER BY fecha, id ASC;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$Xkeyas=$registro["keyas"];

		$LisKeyas=array(
			'id'=>$registro['id'],
			'keyas'=>$registro['keyas'],
			'ncomprobante'=>$registro['ncomprobante'],
			'tipo'=>$registro['tipo']
		);
		$_SESSION['ALisKeyas'][$Indece]=$LisKeyas;
		$Indece++;
	}

	$ContIng=1;
	$ContEgr=1;
	$ContTra=2;

	if (isset($_SESSION['ALisKeyas'])) {

		foreach($_SESSION['ALisKeyas'] as $indice=>$LisKeyas){

			if ($LisKeyas['tipo']=="I") {
				$SQLi="UPDATE CTRegLibroDiario SET ncomprobante='$ContIng' WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND id='".$LisKeyas['id']."' AND tipo='".$LisKeyas['tipo']."' AND ncomprobante='".$LisKeyas['ncomprobante']."'";
				$mysqli->query($SQLi);
				$SQLi="UPDATE CTRegLibroDiario SET ncomprobante='$ContIng' WHERE rutempresa='$RutEmpresa' AND glosa='' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND tipo='".$LisKeyas['tipo']."'";
				$mysqli->query($SQLi);
				$ContIng++;
			}

			if ($LisKeyas['tipo']=="E") {
				$SQLe="UPDATE CTRegLibroDiario SET ncomprobante='$ContEgr' WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND id='".$LisKeyas['id']."' AND tipo='".$LisKeyas['tipo']."' AND ncomprobante='".$LisKeyas['ncomprobante']."'";
				$mysqli->query($SQLe);
				$SQLe="UPDATE CTRegLibroDiario SET ncomprobante='$ContEgr' WHERE rutempresa='$RutEmpresa' AND glosa='' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND tipo='".$LisKeyas['tipo']."'";
				$mysqli->query($SQLe);
				$ContEgr++;
			}

			if ($LisKeyas['tipo']=="T") {
				if ($keyasInicio==$LisKeyas['keyas']) {
					$SQLt="UPDATE CTRegLibroDiario SET ncomprobante='1' WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND id='".$LisKeyas['id']."' AND tipo='".$LisKeyas['tipo']."' AND ncomprobante='".$LisKeyas['ncomprobante']."'";
					$mysqli->query($SQLt);
					$SQLt="UPDATE CTRegLibroDiario SET ncomprobante='1' WHERE rutempresa='$RutEmpresa' AND glosa='' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND tipo='".$LisKeyas['tipo']."'";
					$mysqli->query($SQLt);
				}else{
					$SQLt="UPDATE CTRegLibroDiario SET ncomprobante='$ContTra' WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND id='".$LisKeyas['id']."' AND tipo='".$LisKeyas['tipo']."' AND ncomprobante='".$LisKeyas['ncomprobante']."'";
					$mysqli->query($SQLt);
					$SQLt="UPDATE CTRegLibroDiario SET ncomprobante='$ContTra' WHERE rutempresa='$RutEmpresa' AND glosa='' AND keyas='".$LisKeyas['keyas']."' AND periodo like '%-$Ano' AND tipo='".$LisKeyas['tipo']."'";
					$mysqli->query($SQLt);
					$ContTra++;				
				}
			}
		}
	}

	$zSQL="DELETE FROM CTComprobanteFolio WHERE rutempresa='$RutEmpresa' AND ano = '$Ano'";
	$mysqli->query($zSQL);

	$zSQL="INSERT INTO CTComprobanteFolio VALUES ('','$RutEmpresa','$Ano','I','$ContIng','A');";
	$mysqli->query($zSQL);

	$zSQL="INSERT INTO CTComprobanteFolio VALUES ('','$RutEmpresa','$Ano','E','$ContEgr','A');";
	$mysqli->query($zSQL);

	$zSQL="INSERT INTO CTComprobanteFolio VALUES ('','$RutEmpresa','$Ano','T','$ContTra','A');";
	$mysqli->query($zSQL);


	$mysqli->close();
 	echo "exito";
?>
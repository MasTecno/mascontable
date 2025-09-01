<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$frm=$_POST['tdocumentos'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if ($frm=="C" || $frm=="V") {
		if ($frm=="V") {
			$CodCliPro="C";
		}else{
			$CodCliPro="P";
		}

		$SQL="SELECT CTRegDocumentos.id, CTRegDocumentos.periodo, CTRegDocumentos.rutempresa, CTRegDocumentos.rut, CTCliPro.razonsocial, CTRegDocumentos.id_tipodocumento, CTRegDocumentos.numero, CTRegDocumentos.fecha, CTRegDocumentos.total, CTRegDocumentos.tipo, CTRegDocumentos.estado, CTRegDocumentos.lote, CTRegDocumentos.keyas FROM CTRegDocumentos LEFT JOIN CTCliPro ON CTRegDocumentos.rut = CTCliPro.rut WHERE 1=1";
		$SQL= $SQL." AND CTRegDocumentos.tipo='$frm' AND CTRegDocumentos.estado='A' AND CTRegDocumentos.rutempresa='$RutEmpresa' AND  CTRegDocumentos.keyas<>'' AND CTCliPro.tipo='$CodCliPro'";
		$SQL= $SQL." GROUP BY id,periodo,rutempresa,rut,razonsocial,id_tipodocumento,numero,fecha,total,tipo,estado,lote,keyas ORDER BY CTRegDocumentos.fecha";
	}

	if ($frm=="H") {
		$CodCliPro="P";
		$SQL ="SELECT CTHonorarios.id, CTHonorarios.fecha, CTHonorarios.rutempresa, CTHonorarios.numero, CTHonorarios.periodo, CTHonorarios.rut, CTCliPro.razonsocial, CTHonorarios.liquido, CTHonorarios.estado, CTHonorarios.movimiento FROM CTHonorarios INNER JOIN CTCliPro ON CTHonorarios.rut = CTCliPro.rut WHERE 1=1";
		$SQL= $SQL." AND CTHonorarios.estado='A' AND CTHonorarios.rutempresa='$RutEmpresa' AND  CTHonorarios.movimiento<>'' AND CTCliPro.tipo='$CodCliPro'";
		$SQL= $SQL." GROUP BY CTHonorarios.id, CTHonorarios.fecha, CTHonorarios.rutempresa, CTHonorarios.numero, CTHonorarios.rut, CTCliPro.razonsocial, CTHonorarios.liquido, CTHonorarios.estado, CTHonorarios.movimiento, CTHonorarios.periodo, CTHonorarios.tdocumento ORDER BY CTHonorarios.fecha";
	}

	$con=1;
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$NC=substr($registro["keyas"],0,2);

		$rsocial="";
		$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$rsocial=$registro1["razonsocial"];
		}

		if ($frm=="H") {

			$Totalreg=$registro["liquido"];
		}else{
			$Totalreg=$registro["total"];
		}

		$xsuma=0;
 
		$SQL1="SELECT sum(monto) as xsuma FROM CTControRegDocPago WHERE rutempresa='$RutEmpresa' AND id_tipodocumento='".$registro["id_tipodocumento"]."' AND rut='".$registro["rut"]."' AND ndoc='".$registro["numero"]."'";
		$SQL1=$SQL1." AND tipo='".$frm."'";

		$xsuma=0;
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$xsuma=$registro1["xsuma"];
		}

		if(is_null($xsuma)){
			$xsuma=0;
		}

		if ($xsuma<$Totalreg && $NC!="NC") {
			$arr[]=$registro["periodo"];
		}
	}
	echo '<option value="">Seleccione</option>';

	if(isset($arr)){
		$distinct = array_unique($arr);
		// print_r($distinct);
		if(count($distinct)>0){
			foreach ($distinct as &$valor) {
				echo '<option value="'.$valor.'">'.$valor.'</option>';
			}
		}
	}
	
	$mysqli->close();
?>
<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$checked_count = count($_POST['check_list']);

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$suma=0;
	$sw=0;
	if(is_array($_POST['check_list'])){
		foreach($_POST['check_list'] as $selected) {

			if ($_POST["tdocumentos"]=="H") {
				$SQL="SELECT * FROM CTHonorarios WHERE id='".$selected."' AND rutempresa='$RutEmpresa'";
			}else{
				$SQL="SELECT * FROM CTRegDocumentos WHERE id='".$selected."' AND tipo='".$_POST["tdocumentos"]."' AND rutempresa='$RutEmpresa'";				
			}
			
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
					$operador=$registro1["operador"];
				}

				if($operador=="R"){
					$operador=-1;
				}else{
					$operador=1;
				}

				if ($_POST["tdocumentos"]=="H") {
					$suma=$suma+($registro['liquido']*$operador);
				}else{
					$suma=$suma+($registro['total']*$operador);		
				}

				$xsuma=0;
				$SQL1="SELECT sum(monto) as xsuma FROM CTControRegDocPago WHERE rutempresa='$RutEmpresa' AND id_tipodocumento='".$registro["id_tipodocumento"]."' AND rut='".$registro["rut"]."' AND ndoc='".$registro["numero"]."' AND tipo='".$_POST["tdocumentos"]."'";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
					$suma=$suma-$registro1["xsuma"];
				}
				if ($suma=="") {
					$suma=0;
				}
			}
			$sw++;
			if ($sw==500) {
				break;
			}
		}
	}

	echo $suma.",".$sw;
?>
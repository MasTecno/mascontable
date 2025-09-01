<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


	$FCodigo=$_POST['Codigo'];
	$FSelCliPro=$_POST['SelCliPro'];
	$FDebe=$_POST['Debe'];
	$FHaber=$_POST['Haber'];
	$FRutUno=$_POST['RutUno'];


	// ******** CONSULTA CTA AUXILIARES
	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);

	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="CUEN_REND"){
			$CTAREND=$registro['valor'];	
		}
		if($registro['tipo']=="ANTI_PROV"){
			$ANTIPRO=$registro['valor'];	
		}
		if($registro['tipo']=="ANTI_CLIE"){
			$ANTICLI=$registro['valor'];	
		}
	}
	// ******** CONSULTA CTA AUXILIARES
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$SwSoloDoc="S";

	if ($CTAREND==$FCodigo && $FSelCliPro=="P" && $FHaber>0) {
		echo '<option value=""></option>';
		$SQL="SELECT * FROM CTFondo WHERE estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='I' AND Rut='".$FRutUno."' ORDER BY Fecha";
		// $SQLX=$SQL;
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$NomPersonal="";
			$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro['Rut']."'";
			// $SQLX=$SQL1;
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$NomPersonal=$registro1['razonsocial'];
			}

			$MontoEgreso=0;
			$TotFondo=0;
			$SQL1="SELECT * FROM CTFondo WHERE RutEmpresa='$RutEmpresa' AND IdPersonal='".$registro['Id']."' AND Tipo='E'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$MontoEgreso=$MontoEgreso+$registro1['Monto'];
			}

			$TotFondo=$registro['Monto']-$MontoEgreso;
			if ($TotFondo>0) {
				echo '<option value="'.$registro['Id'].'">'.$registro['Titulo'].' ('.$TotFondo.') - '.$NomPersonal.'</option>';
			}
			
		}
		$SwSoloDoc="N";
	}

	if ($ANTIPRO==$FCodigo && $FSelCliPro=="P" && $FHaber>0) {
		echo '<option value=""></option>';
		$SQL="SELECT * FROM CTAnticipos WHERE Estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='I' AND Rut='".$FRutUno."' AND Cuenta='$ANTIPRO' ORDER BY Fecha";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$NomPersonal="";
			$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro['Rut']."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$NomPersonal=$registro1['razonsocial'];
			}

			$MontoEgreso=0;
			$TotFondo=0;

			$SQL1="SELECT * FROM CTAnticipos  WHERE Estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='E' AND Rut='".$FRutUno."' AND Cuenta='$ANTIPRO'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$MontoEgreso=$MontoEgreso+$registro1['Monto'];
			}

			$TotFondo=$registro['Monto']-$MontoEgreso;
			if ($TotFondo>0) {
				echo '<option value="'.$registro['Id'].'">'.$registro['Glosa'].' ('.$TotFondo.') - '.$NomPersonal.'</option>';
			}
		}
		$SwSoloDoc="N";
	}


	if ($ANTICLI==$FCodigo && $FSelCliPro=="C" && $FDebe>0) {
		echo '<option value=""></option>';
		$SQL="SELECT * FROM CTAnticipos WHERE Estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='I' AND Rut='".$FRutUno."' AND Cuenta='$ANTICLI' ORDER BY Fecha";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$NomPersonal="";
			$SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro['Rut']."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$NomPersonal=$registro1['razonsocial'];
			}

			$MontoEgreso=0;
			$TotFondo=0;

			$SQL1="SELECT * FROM CTAnticipos  WHERE Estado='A' AND RutEmpresa='$RutEmpresa' AND Tipo='E' AND Rut='".$FRutUno."' AND Cuenta='$ANTICLI'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$MontoEgreso=$MontoEgreso+$registro1['Monto'];
			}

			$TotFondo=$registro['Monto']-$MontoEgreso;
			if ($TotFondo>0) {
				echo '<option value="'.$registro['Id'].'">'.$registro['Glosa'].' ('.$TotFondo.') - '.$NomPersonal.'</option>';
			}
		}
		$SwSoloDoc="N";
	}

	if ($SwSoloDoc=="S"){
		if ( $CTAREND!=$FCodigo && ($FSelCliPro=="C" || $FSelCliPro=="P")) {
			echo '<option value=""></option>';
			$SQL="SELECT * FROM `CTTipoDocumento` WHERE tiposii>0 AND estado='A' ORDER BY id";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				echo '<option value="'.$registro["tiposii"].'">'.$registro["tiposii"].' - '.strtoupper($registro["nombre"]).'</option>';
			}
		}
	}

	$mysqli->close();
?>

<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:index.php?Msj=95");
		exit;
	}

	$xDato1="Sin Datos";
	$xDato2="Sin Datos";
	$xDato3="Sin Datos";
	$xDato4="Sin Datos";
	$xDato5="Sin Datos";

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$Periodo= $_SESSION['PERIODO'];

	$dmes = substr($Periodo,0,2);
	$danol = substr($Periodo,3,4);
	$Xfdesde=$danol."-01-01";
	$Xfhasta=$danol."-12-31";

	/////Saldos de activo y pasivos
	$CtaError="";
    if ($_SESSION["PLAN"]=="S"){
        $SqlCta="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    }else{
        $SqlCta="SELECT * FROM CTCuentas WHERE estado<>'T'";
    }

	$SQL="SELECT cuenta, sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE fecha BETWEEN '$Xfdesde' AND '$Xfhasta' AND rutempresa='".$_SESSION['RUTEMPRESA']."' AND cuenta<>'0' GROUP BY cuenta;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$Resta=$registro['sdebe']-$registro['shaber'];

		$IdCategoria="";
		$NombreCta="";
		$SqlCta1=$SqlCta." AND numero='".$registro['cuenta']."'";
		$ResCta = $mysqli->query($SqlCta1);
		while ($RegCta = $ResCta->fetch_assoc()) {
			$IdCategoria=$RegCta['id_categoria'];
			$NombreCta=$RegCta['detalle'];
		}

		$CtaTipo="";
		$SqlCat="SELECT * FROM CTCategoria WHERE id='$IdCategoria'";
		$ResCat = $mysqli->query($SqlCat);
		while ($RegCat = $ResCat->fetch_assoc()) {
			$CtaTipo=$RegCat['tipo'];
		}

		if($CtaTipo=="ACTIVO" && $Resta<0){
			if($CtaError==""){
				$CtaError='<a href="javascript:MayorCta('.$registro["cuenta"].');">'.$registro['cuenta'].' - '.$NombreCta.'</a>';
			}else{
				$CtaError=$CtaError.'<br><a href="javascript:MayorCta('.$registro["cuenta"].');">'.$registro['cuenta'].' - '.$NombreCta.'</a>';
			}
		}

		if($CtaTipo=="PASIVO" && $Resta>0){
			if($CtaError==""){
				$CtaError='<a href="javascript:MayorCta('.$registro["cuenta"].');">'.$registro['cuenta'].' - '.$NombreCta.'</a>';
			}else{
				$CtaError=$CtaError.'<br><a href="javascript:MayorCta('.$registro["cuenta"].');">'.$registro['cuenta'].' - '.$NombreCta.'</a>';
			}
		}

	}
	if($CtaError!=""){
		$CtaError=$CtaError."<br> Advertencia de posible incongruencia de saldo";
	}
	/////Asientos cuadrados
	$KeyError="";
	$SQL="SELECT  keyas, sum(debe) as sdebe, sum(haber) as shaber FROM CTRegLibroDiario WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND fecha BETWEEN '$Xfdesde' AND '$Xfhasta' AND estado='A' GROUP BY keyas;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['sdebe'] <> $registro['shaber']){

			$Glosa="";
			$TipoCom="";
			$NumCom="";
			$PerAsi="";
			$SqlCat="SELECT * FROM CTRegLibroDiario WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND keyas='".$registro['keyas']."' AND glosa<>''";
			$ResCat = $mysqli->query($SqlCat);
			while ($RegCat = $ResCat->fetch_assoc()) {
				$Glosa=$RegCat['glosa'];
				$TipoCom=$RegCat['tipo'];
				$NumCom=$RegCat['ncomprobante'];
				$PerAsi=$RegCat['periodo'];
			}
			if($TipoCom=="I"){
				$TipoCom="Ing";
			}
			if($TipoCom=="E"){
				$TipoCom="Egr";
			}
			if($TipoCom=="T"){
				$TipoCom="Tra";
			}

			if($KeyError==""){
				$KeyError=$TipoCom."/".$NumCom." - ".$PerAsi." - ".$Glosa.$registro['keyas']."(".$registro['keyas'].") <br>";

			}else{
				$KeyError=$KeyError."".$TipoCom."/".$NumCom.", ".$PerAsi.", ".$Glosa."(".$registro['keyas'].") <br>";
			}
		}
	}


	// $KeyError=$KeyError.$SQL;








	// $SQL="SELECT * FROM DatosPersonales WHERE idServer='".$_POST['NIdServer']."' AND estado='A'";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$xDato1=utf8_encode($registro['Contacto']);	
	// 	$xDato2=date('d-m-Y',strtotime($registro['FPago']));
	// }


	// $SQL="SELECT * FROM DatosPersonales WHERE idServer='".$_POST['NIdServer']."' AND estado='A'";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	$xDato1=utf8_encode($registro['Contacto']);	
	// 	$xDato2=date('d-m-Y',strtotime($registro['FPago']));
	// }


	$mysqli->close();

	

	echo json_encode(
      array("dato1" => "$CtaError", 
      "dato2" => "$KeyError",
      "dato3" => "$xDato3", 
      "dato4" => "$xDato4", 
      "dato5" => "$xDato5")
      );
?>
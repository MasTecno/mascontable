<?php 
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$xper=$_GET['per'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTHonoGene WHERE id='$xper'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xano=$registro['periodo'];
	}

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if($registro['tipo']=="IVA"){
			$DIVA=$registro['valor']; 
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];  
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}

	$mes=array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	$factores=array();

	$SQL="SELECT * from CTHonoGene WHERE rutempresa ='$RutEmpresa' AND periodo='$xano'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$factores[]=$registro['mes1'];
		$factores[]=$registro['mes2'];
		$factores[]=$registro['mes3'];
		$factores[]=$registro['mes4'];
		$factores[]=$registro['mes5'];
		$factores[]=$registro['mes6'];
		$factores[]=$registro['mes7'];
		$factores[]=$registro['mes8'];
		$factores[]=$registro['mes9'];
		$factores[]=$registro['mes10'];
		$factores[]=$registro['mes11'];
		$factores[]=$registro['mes12'];
	}

	$SQL="SELECT rut, certificado, sum(retencion) as SwRete FROM CTHonoGeneDeta WHERE rutempresa='$RutEmpresa' AND idproceso='$xper' AND retencion>0  GROUP BY rut ORDER by rut";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xrut=$registro['rut'];

		$i=1;
		$trete=0;
		$tretec=0;
		$rete=0;
		$spresC=0;
		$spres=0;
		$variable1="";
		$Up3=0;

		while ( $i<= 12) {
			if ($i<10) {
				$peri="0".$i."-".$xano;
			}else{
				$peri=$i."-".$xano;
			}
			$SQL1="SELECT sum(bruto) as sbruto, sum(retencion) as sreten, sum(prestamo) as sprestamo, periodo from CTHonoGeneDeta WHERE rut ='$xrut' AND  rutempresa='$RutEmpresa' AND periodo='$peri'";

			// $SQL1="SELECT sum(retencion) as sreten, sum(prestamo) as sprestamo, periodo from CTHonoGeneDeta WHERE rut ='$xrut' AND  rutempresa='$RutEmpresa' AND periodo='$peri'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {

				$bruto=$registro1['sbruto'];
				$rete=$registro1['sreten'];
				$spresC=$registro1['sprestamo'];
				
				$spresC=$registro1['sprestamo'];
				if ($registro1['sreten']>0) {
					$variable1=$variable1.";X";
				}else{
					$variable1=$variable1.";";
				}
			}

			$trete=$trete+$rete;
			$tretec=$tretec+($rete*$factores[($i-1)]);
			$Up3=$Up3+($spresC*$factores[($i-1)]);
			$spres=$spres+$spresC;
			$i++;
		}

		$Rut=substr($registro['rut'], 0, -2);
		$Dig=substr($registro['rut'], -1);
		$variable=$variable.''.$Rut.';'.$Dig.';'.round($tretec);
		$variable=$variable.";0;0";
		$variable=$variable.$variable1;

		// $variable=$variable.';0;0;'.$spres.";".$registro['certificado']."\r\n";
		$variable=$variable.';0;'.round($Up3).";".$registro['certificado']."\r\n";

	}

	$mysqli->close();

	header("Content-Type: text/plain");
	header('Content-Disposition: attachment; filename="DJ1879-'.$xano.'-'.$RutEmpresa.'.csv"');
	echo $variable;
?>
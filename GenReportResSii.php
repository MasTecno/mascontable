<?php 
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
	
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

	$SQL="SELECT rut, certificado FROM CTHonoGeneDeta WHERE rutempresa='$RutEmpresa' and idproceso='$xper' GROUP BY rut ORDER by rut";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xrut=$registro['rut'];

		$i=1;
		$trete=0;
		$tretec=0;
		$rete=0;
		$variable1="";

		while ( $i<= 12) {
			if ($i<10) {
				$peri="0".$i."-".$xano;
			}else{
				$peri=$i."-".$xano;
			}

			$SQL1="SELECT sum(retencion) as sreten, periodo from CTHonoGeneDeta WHERE rut ='$xrut' AND  rutempresa='$RutEmpresa' AND periodo='$peri'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$rete=$registro1['sreten'];
				if ($registro1['sreten']>0) {
					$variable1=$variable1.";C";
				}else{
					$variable1=$variable1.";";
				}
			}

			$trete=$trete+$rete;
			$tretec=$tretec+($rete*$factores[($i-1)]);
			$i++;
		}

		$Rut=substr($registro['rut'], 0, -2);
		$Dig=substr($registro['rut'], -1);
		$variable=$variable.''.$Rut.';'.$Dig.';'.round($tretec);
		$variable=$variable.$variable1;

		$variable=$variable.';0;0;'.$registro['certificado']."\r\n";

	}


	$mysqli->close();

	header("Content-Type: text/plain");
	header('Content-Disposition: attachment; filename="DJ1879-'.$Periodo.'-'.$RutEmpresa.'.csv"');
	echo $variable;
?>
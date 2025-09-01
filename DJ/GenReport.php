<?php 

	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$xid=$_GET['id'];
	$xper=$_GET['per'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
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

	$SQL="SELECT * from CTCliPro WHERE id ='$xid'";
	$resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
		$xrut=$registro['rut'];
		$xrazon=$registro['razonsocial'];
	}

	$SQL="SELECT certificado from CTHonoGeneDeta WHERE rut ='$xrut' AND rutempresa='$RutEmpresa' AND periodo LIKE '%$xper%' GROUP BY certificado";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$ncertif=$registro['certificado'];
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
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<style>
			.container-fluid {
				font-size: 10px;			
			}
		</style>
		<script type="text/javascript">
			function printDiv(nombreDiv) {
				var contenido= document.getElementById(nombreDiv).innerHTML;
				var contenidoOriginal= document.body.innerHTML;
				document.body.innerHTML = contenido;
				window.print();
				document.body.innerHTML = contenidoOriginal;
			} 		
		</script>
	</head>
<body>

<div class="container-fluid">
<!-- <div class="row content"> -->

	<div class="col-sm-12 text-center">
		<input type="button" class="btn btn-default btn-sm" onclick="printDiv('DivImp')" value="Imprimir">
	</div>

<div class="" id="DivImp">


	<div class="col-sm-1">
	</div>
	<div class="col-sm-10">

			<div class="form-group row">
<?php
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		    $SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEmpresa'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
		    	$xNOM=$registro['razonsocial'];	
		    	$xRUT=$registro['rut'];	
		    	$xDIR=$registro['direccion'];	
		    	$xCUI=$registro['cuidad'];	
			   	$xGIR=$registro['giro'];	

		    }
		    $mysqli->close();

			echo $xRUT." - ".$xNOM,"<br>";
		    echo $xDIR.", ".$xCUI,"<br>";
		    echo $xGIR,"<br>";
?>


			<br><br>
			<h4 class="text-center">CERTIFICADO SOBRE HONORARIOS</h4>
			<h5 class="text-center">Certificado <?php echo $ncertif."/".$xper;?></h5>
			<br><br>
			<p>La empresa <?PHP echo $xNOM; ?>, certifica que el Sr(a) <?php echo $xrazon; ?>, Rut Nro. <?php echo $xrut; ?>, durante el a&ntilde;o <?php echo $xper; ?>, se le han pagado las siguientes rentas por concepto de honorarios, y sobre las cuales se le practicaron las retenciones de impuesto que se se&ntilde;alan:
			</p>


				<div class="clearfix"> </div>
			</div>

		<div class="clearfix"> </div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th width="20%">Periodo</th>
					<th>Cant. Documentos</th>
					<th>Honorario Bruto</th>
					<th>Retenci&oacute;n Impuesto</th>
					<th>Factor Actual</th>
					<th>Honorario Bruto</th>
					<th>Retenci&oacute;n Impuesto</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

				$mes=array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
				$factores=array();

				$SQL="SELECT * from CTHonoGene WHERE rutempresa ='$RutEmpresa' AND periodo='$xper'";
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



				$SQL="SELECT * from CTCliPro WHERE id ='$xid'";
				$resultados = $mysqli->query($SQL);
        		while ($registro = $resultados->fetch_assoc()) {
					$xrut=$registro['rut'];
				}

				$i=1;
				$tsbruto=0;
				$tsliquido=0;
				while ( $i<= 12) {

					if ($i<10) {
						$peri="0".$i."-".$xper;
					}else{
						$peri=$i."-".$xper;
					}

					$cont=0;
					$sbruto=0;
					$rete=0;
					$retec=0;
					$sliquido=0;
					$SQL="SELECT * from CTHonoGeneDeta WHERE rut ='$xrut' AND rutempresa='$RutEmpresa' AND periodo='$peri'";
					$resultados = $mysqli->query($SQL);
        			while ($registro = $resultados->fetch_assoc()) {
						$cont++;
						$sbruto=$sbruto+$registro['bruto'];
						$rete=$rete+$registro['retencion'];
						$sliquido=$sliquido+$registro['liquido'];
					}
		

						echo '
						<tr>
						<td>'.$mes[($i-1)].'</td>
						<td align="right">'.$cont.'</td>
						<td align="right">'.number_format($sbruto, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format($rete, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.$factores[($i-1)].'</td>
						<td align="right">'.number_format(($sbruto*$factores[($i-1)]), $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format(($rete*$factores[($i-1)]), $NDECI, $DDECI, $DMILE).'</td>
						</tr>';


					$tsbruto=$tsbruto+$sbruto;
					$trete=$trete+$rete;
					$tretec=$tretec+($sbruto*$factores[($i-1)]);
					$tsliquido=$tsliquido+($rete*$factores[($i-1)]);
					$i++;
				}


						echo '
						<tr>
						<td></td>
						<td>Totales</td>
						<td align="right">'.number_format($tsbruto, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format($trete, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right"></td>
						<td align="right">'.number_format($tretec, $NDECI, $DDECI, $DMILE).'</td>
						<td align="right">'.number_format($tsliquido, $NDECI, $DDECI, $DMILE).'</td>
						</tr>';

				$mysqli->close();
			?>
			</tbody>
		</table>		

	

		<div class="clearfix"> </div>

		
		<p>Se extiende el presente Certificado en cumplimiento de lo dispuesto en la Resoluci&oacute;n Ex Nro. 6509 del Servicio de Impuestos Internos, publicada en el Diarios Oficial de fecha 20 de Diciembre de 1993.</p>


		

	</div>
	<div class="col-sm-1">
	</div>
</div>
</div>

</body>
</html>
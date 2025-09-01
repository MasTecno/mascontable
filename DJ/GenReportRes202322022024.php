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

	if ($xano=="2019") {
		$facto=10;
	}
	if ($xano=="2020") {
		$facto=10.75;
	}
	if ($xano=="2021") {
		$facto=11.5;
	}
	if ($xano=="2022") {
		$facto=12.25;
	}
	if ($xano=="2023") {
		$facto=13;
	}
	if ($xano=="2024") {
		$facto=13.75;
	}
	if ($xano=="2025") {
		$facto=14.5;
	}
	if ($xano=="2026") {
		$facto=15.25;
	}
	if ($xano=="2027") {
		$facto=16;
	}
	if ($xano=="2028") {
		$facto=17;
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
<!-- 		<h3>Informe del Registro</h3>
		<h6>Impresa por: <?php echo strtoupper($_SESSION['NOMBRE']);?>, con fecha: <?php echo date('d/m/Y');?></h6>
		<hr> -->
<!-- 		<div class="alert alert-info">
		  Antecedentes 
		</div>		 -->
			<div class="form-group row">
			<h5 class="text-center">DECLARACION JURADA ANUAL SOBRE RETENCIONES EFECTUADAS (Form. 1879)</h5>
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
			echo "<br><br>Ejercicio: ".$xano,"<br><br>";
			echo $xRUT." - ".$xNOM,"<br>";
		    echo $xDIR.", ".$xCUI,"<br>";
		    echo $xGIR,"<br>";
?>


			<br><br>
			<br><br>
<!-- 			<p>La empresa <?PHP echo $xNOM; ?>, certifica que el Sr(a) <?php echo $xrazon; ?>, Rut Nro. <?php echo $xrut; ?>, durante el a&ntilde;o <?php echo $xper; ?>, se le han pagado las siguientes rentas por concepto de honorarios, y sobre las cuales se le practicaron las retenciones de impuesto que se se&ntilde;alan:
			 -->


				<div class="clearfix"> </div>
			</div>

	

		<div class="clearfix"> </div>

<!-- 		<div class="alert alert-info">
		  Detalle por Periodo
		</div> -->


		<div class="clearfix"> </div>
			<table class="table table-bordered">
			<tbody>
				<tr>
					<td colspan="2" rowspan="3" width="96"><strong>Rut Del Receptor De La Renta</strong></td>
					<td colspan="3" width="364"><strong>Monto Retenido Anual Actualizado (Del 01/01 AL 31/12)</strong></td>
					<td class="text-center" colspan="12" rowspan="2" width="364"><strong>Per&iacute;odo al cual Corresponden las Rentas</strong></td>
					<td rowspan="3" width="111"><strong>Monto pagado anual actualizafo por servicios prestados en Isla de Pascua</strong></td>
					<td rowspan="3" width="111"><strong>3% "Pr&eacute;stamo Tasa 0%"</strong></td>
					<td rowspan="3" width="82"><strong>N&uacute;mero Certificado</strong></td>
				</tr>
				<tr>
					<td width="364"><strong>Honorarios y Otros (Art.42 N 2)</strong></td>
					<td colspan="2" width="217"><strong>Remuneraci&oacute;n De Directores (Art. 48)</strong></td>
				</tr>
				<tr>
					<td class="text-center" width="364"><strong>Tasa <?php echo $facto; ?>%</strong></td>
					<td class="text-center" width="217"><strong>Tasa 10%</strong></td>
					<td class="text-center" width="111"><strong>Tasa 35%</strong></td>
					<td class="text-center" width="364"><strong>Ene</strong></td>
					<td class="text-center" width="111"><strong>Feb</strong></td>
					<td class="text-center" width="111"><strong>Mar</strong></td>
					<td class="text-center" width="111"><strong>Abr</strong></td>
					<td class="text-center" width="111"><strong>May</strong></td>
					<td class="text-center" width="111"><strong>Jun</strong></td>
					<td class="text-center" width="111"><strong>Jul</strong></td>
					<td class="text-center" width="111"><strong>Ago</strong></td>
					<td class="text-center" width="111"><strong>Sep</strong></td>
					<td class="text-center" width="111"><strong>Oct</strong></td>
					<td class="text-center" width="111"><strong>Nov</strong></td>
					<td class="text-center" width="111"><strong>Dic</strong></td>
				</tr>
				<?php 
					$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

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
					$trete=0;
					$Tspres=0;
					$SQL="SELECT rut, certificado FROM CTHonoGeneDeta WHERE rutempresa='$RutEmpresa' and idproceso='$xper' GROUP BY rut ORDER by rut";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$xrut=$registro['rut'];

						$i=1;
						
						$tretec=0;
						$rete=0;

						$spres=0;
						$spresC=0;
						$str="";

						while ( $i<= 12) {
							if ($i<10) {
								$peri="0".$i."-".$xano;
							}else{
								$peri=$i."-".$xano;
							}

							$SQL1="SELECT sum(bruto) as sbruto, sum(retencion) as sreten, sum(prestamo) as sprestamo, periodo from CTHonoGeneDeta WHERE rut ='$xrut' AND  rutempresa='$RutEmpresa' AND periodo='$peri'";
							// $SQL1="SELECT sum(bruto) as sreten, sum(prestamo) as sprestamo, periodo from CTHonoGeneDeta WHERE rut ='$xrut' AND  rutempresa='$RutEmpresa' AND periodo='$peri'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$bruto=$registro1['sbruto'];
								$rete=$registro1['sreten'];
								$spresC=$registro1['sprestamo'];
								if ($registro1['sreten']>0) {
									$str=$str.'<td width="364" class="text-center">C</td>';
								}else{
									$str=$str.'<td width="364" class="text-center"></td>';
								}
								
							}

							$tbruto=$tbruto+$bruto;
							$trete=$trete+$rete;
							$tretec=$tretec+($rete*$factores[($i-1)]);
							$spres=$spres+$spresC;
							$Tspres=$Tspres+$spresC;

							$i++;
						}

						$Rut=substr($registro['rut'], 0, -2);
						$Dig=substr($registro['rut'], -1);

						echo '
							<tr>
								<td width="96" class="text-center">'.$Rut.'</td>
								<td width="96" class="text-center">'.$Dig.'</td>
								<td width="364" class="text-right">'.number_format($tretec, $NDECI, $DDECI, $DMILE).'</td>
								<td width="217" class="text-right">0</td>
								<td width="111" class="text-right">0</td>
							';
						echo $str;
						echo '
								<td width="111" class="text-right">0</td>
								<td width="111" class="text-right">'.number_format($spres, $NDECI, $DDECI, $DMILE).'</td>
								<td width="82" class="text-center">'.$registro['certificado'].'</td>
							</tr>
						';
						$SumRetTotal=$SumRetTotal+$tretec;
						$maxCert=$registro['certificado'];
					}



				?>

				<!-- <tr>
					<td width="96"><strong></strong></td>
					<td width="96"><strong></strong></td>
					<td class="text-right" width="364"><strong><?php echo number_format($SumRetTotal, $NDECI, $DDECI, $DMILE);?></strong></td>
					<td class="text-right" width="217"><strong>0</strong></td>
					<td class="text-right" width="111"><strong>0</strong></td>
					<td width="364"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="111"><strong></strong></td>
					<td width="82"><strong></strong></td>
				</tr> -->
			</tbody>
			</table>


			<div class="col-md-10">
					<table class="table table-bordered">
					  <col width="69" span="12" />
					  <col width="54" />
					  <col width="45" />
					  <col width="69" span="2" />
					  <col width="54" />
					  <tr>
					    <td class="text-center" colspan="17" width="1119"><strong>CUADRO RESUMEN FINAL DE LA DECLARACI&Oacute;N</strong></td>
					  </tr>
					  <tr>
					    <td colspan="7"><strong>Monto Retenido Anual Actualizado</strong></td>
					    <td colspan="2" rowspan="4" width="138"><strong>Monto Pagado Anual Actualizado por Servicios Prestados en Isla de Pascua</strong></td>
					    <td colspan="2" rowspan="4" width="138"><strong>3% "Pr&eacute;stamo Tasa 0%"</strong></td>
					    <td colspan="3" rowspan="4" width="168"><strong>Total de Casos Informados</strong></td>
					    <td colspan="3" rowspan="4" width="192"><strong>Monto Total Honorarios (Sin Actualizar)</strong></td>
					  </tr>
					  <tr>
					    <td colspan="3" rowspan="2" width="207"><strong>Honorarios y Otros (Art.42 N 2)</strong></td>
					    <td colspan="4" rowspan="2" width="276"><strong>Remuneraci&oacute;n de Directores (Art. 48)</strong></td>
					  </tr>
					  <tr> </tr>
					  <tr>
					    <td class="text-center" colspan="3"><strong>Tasa <?php echo $facto; ?>%</strong></td>
					    <td class="text-center" colspan="2"><strong>Tasa 10%</strong></td>
					    <td class="text-center" colspan="2"><strong>Tasa 35%</strong></td>
					  </tr>
					  <tr>
					    <td class="text-right" colspan="3"><?php echo number_format($SumRetTotal, $NDECI, $DDECI, $DMILE);?></td>
					    <td class="text-right" colspan="2">0</td>
					    <td class="text-right" colspan="2">0</td>
					    <td class="text-right" colspan="2">0</td>
					    <td class="text-right" colspan="2"><?php echo number_format($Tspres, $NDECI, $DDECI, $DMILE); ?></td>
					    <td class="text-center" colspan="3"><?php echo $maxCert; ?></td>
					    <td class="text-right" colspan="3"><?php echo number_format($tbruto, $NDECI, $DDECI, $DMILE); ?></td>
					  </tr>
					</table>
			</div>





		<div class="clearfix"> </div>
		<br><br>
		
		<p>DECLARO BAJO JURAMENTO QUE LOS DATOS CONTENIDOS EN EL PRESENTE DOCUMENTO SON LA EXPRESION FIEL DE LA VERDAD, POR LO QUE ASUMO LA RESPONSABILIDAD CORRESPONDIENTE.</p>

        <div class="clearfix"> </div>
        <br><br><br>

        <div class="col-md-2">

        </div>
        <div class="col-md-4 text-center">
            Firma Representante Legal
        </div>

        <div class="col-md-4 text-center">
            Firma Contador(a)
        </div>


		

	</div>
	<div class="col-sm-1">
	</div>
</div>
</div>

</body>
</html>
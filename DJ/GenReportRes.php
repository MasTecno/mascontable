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
				$facto=12.25;
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
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<style>
			/* Remove the navbar's default margin-bottom and rounded borders */
			.navbar {
				margin-bottom: 0;
				border-radius: 0;
			}

			/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
			.row.content {height: 450px}

			/* Set gray background color and 100% height */
			.sidenav {
				padding-top: 20px;
				background-color: #f1f1f1;
				height: 100%;
			}

			/* Set black background color, white text and some padding */
			footer {
				background-color: #555;
				color: white;
				padding: 15px;
			}

			/* On small screens, set height to 'auto' for sidenav and grid */
			@media screen and (max-width: 767px) {
				.sidenav {
					height: auto;
					padding: 15px;
				}
				.row.content {height:auto;}
			}
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

	<dir class="col-sm-12 text-center">
		<input type="button" class="btn btn-default btn-sm" onclick="printDiv('DivImp')" value="Imprimir">
	</dir>

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
					<td colspan="12" rowspan="2" width="364"><strong>Per&iacute;odo al cual Corresponden las Rentas</strong></td>
					<td rowspan="3" width="111"><strong>Honorarios y Otros actualizados trabajadores de las artes y espect&aacute;culos</strong></td>
					<td rowspan="3" width="111"><strong>Monto pagado anual actualizafo por servicios prestados en Isla de Pascua</strong></td>
					<td rowspan="3" width="82"><strong>N&uacute;mero Certificado</strong></td>
		      <td rowspan="3" width="82"><strong>Honorarios y Otros (Art.42 N 2) - Sin Actualizar</strong></td>

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

					$SQL="SELECT rut, certificado FROM CTHonoGeneDeta WHERE rutempresa='$RutEmpresa' and idproceso='$xper' GROUP BY rut ORDER by rut";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$xrut=$registro['rut'];

						$i=1;
						$trete=0;
						$tretec=0;
						$rete=0;
						$str="";

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
									$str=$str.'<td width="364" class="text-center">C</td>';
								}else{
									$str=$str.'<td width="364" class="text-center"></td>';
								}
								
							}

							$trete=$trete+$rete;
							// $Xtrete=$Xtrete+$rete;
							$tretec=$tretec+($rete*$factores[($i-1)]);
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
								<td width="111" class="text-right">0</td>
								<td width="82" class="text-center">'.$registro['certificado'].'</td>
								<td width="82" class="text-right">'.number_format($trete, $NDECI, $DDECI, $DMILE).'</td>
							</tr>
						';
						$SumRetTotal=$SumRetTotal+$tretec;
						$SumRetTotal1=$SumRetTotal1+$trete;
					}



				?>

				<tr>
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
					<td class="text-right" width="111"><strong><?php echo number_format($SumRetTotal1, $NDECI, $DDECI, $DMILE);?></strong></td>
				</tr>
			</tbody>
			</table>



		<div class="clearfix"> </div>

		
		<p>Se extiende el presente Certificado en cumplimiento de lo dispuesto en la Resoluci&oacute;n Ex Nro. 6509 del Servicio de Impuestos Internos, publicada en el Diarios Oficial de fecha 20 de Diciembre de 1993.


		

	</div>
	<div class="col-sm-1">
	</div>
</div>
</div>

</body>
</html>
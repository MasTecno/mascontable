<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	if (isset($_POST['anoselect'])) {
		if ($_POST['anoselect']!=""){
			$dano=$_POST['anoselect'];
			$Xfdesde="01-01-".$dano;
			$Xfhasta="31-12-".$dano;
		}else{
			$dmes = substr($Periodo,0,2);
			$dano = substr($Periodo,3,4);
			$Xfdesde="01-01-".$dano;
			$Xfhasta="31-12-".$dano;
		} 
	}else{
		$dmes = substr($Periodo,0,2);
		$dano = substr($Periodo,3,4);
		$Xfdesde="01-01-".$dano;
		$Xfhasta="31-12-".$dano;
	} 

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";

	if (!$resultado = $mysqli->query($SQL)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		exit;
	}

	while ($registro = $resultado->fetch_assoc()) {

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 

    	if($registro['tipo']=="TIPO_MONE"){
    		$DMONE=$registro['valor'];	
    	}

	}

	$SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEmpresa'";

	if (!$resultado = $mysqli->query($SQL)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		exit;
	}

	while ($registro = $resultado->fetch_assoc()) {
		$xNOM=$registro['razonsocial'];	
		$xRUT=$registro['rut'];	
		$xDIR=$registro['direccion'];	
		$xCUI=$registro['ciudad'];	
		$xGIR=$registro['giro'];
		$representante=$registro['representante'];	
		$xRrep=$registro['rut_representante'];    
		$xRep=$registro['representante'];    
	}

	$mysqli->close();

	$anoselect=$_POST['anoselect'];

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
	</head>

	<body onload="xprint()">

	<div class="container-fluid" id="areaImprimir">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<br>
			<br>
			<?php

				if (isset($_POST['check3'])) {
					echo '<br><br><br><br><br><br>';
				}

				if (isset($_POST['check1'])) {

					if (strlen($xRUT)==9) {
						$RutPunto1=substr($xRUT,-10,1);
					}else{
						$RutPunto1=substr($xRUT,-10,2);
					}
					
					$RutPunto2=substr($xRUT,-5);
					$RutPunto3=substr($xRUT,-8,3);
					$srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

					echo '
						<table width="100%" border="0">
							<tr>
								<td width="10%">Contribuyente:</td>
								<td width="">'.$xNOM.'&nbsp;</td>
							</tr>
							<tr>
								<td>Rut:</td>
								<td>'.$srtRut.'&nbsp;</td>
							</tr>
							<tr>
								<td>Domicilio:</td>
								<td>'.$xDIR.'&nbsp;</td>
							</tr>
							<tr>
								<td>Ciudad:</td>
								<td>'.$xCUI.'&nbsp;</td>
							</tr>
							<tr>
								<td>Giro:</td>
								<td>'.$xGIR.'&nbsp;</td>
							</tr>
						</table>

						';

						if (isset($_POST['check7'])) {
							if (strlen($xRrep)==9) {
								$RutPunto1=substr($xRrep,-10,1);
							}else{
								$RutPunto1=substr($xRrep,-10,2);
							}
							
							$RutPunto2=substr($xRrep,-5);
							$RutPunto3=substr($xRrep,-8,3);
							$srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

							echo '
								<table width="100%" border="0">
									<tr>
										<td width="10%">Rep. Legal:</td>
										<td width="">'.$xRep.'&nbsp;</td>
									</tr>
									<tr>
										<td>Rep. Rut:</td>
										<td>'.$srtRut.'&nbsp;</td>
									</tr>
								</table>
							';
						}

					echo '
						<div class="clearfix"></div>
						<br>
					';
				}
			?>


			<div class="col-sm-12 text-center"><h3>Balance General Clasificado</h3>
			<?php 
				if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {

							// $dia = substr($_POST['fdesde'],0,2);
							// $mes = substr($_POST['fdesde'],3,2);
							// $ano = substr($_POST['fdesde'],6,4);

							// $LFdesde=$ano."-".$mes."-".$dia;

							// $dia = substr($_POST['fhasta'],0,2);
							// $mes = substr($_POST['fhasta'],3,2);
							// $ano = substr($_POST['fhasta'],6,4);

							// $LFhasta=$ano."-".$mes."-".$dia;

					echo "<h3>Desde ".$_POST['fdesde']." al ".$_POST['fhasta']."</h3>";
				}else{
					echo "<h3>Periodo ".$_POST['anoselect']."</h3>";	
				}


				
			?>
			<br><br></div>

			<div class="col-sm-6">
				<div class="panel panel-default">
				<div class="panel-body"> Activo<br><br>
					<?php

						if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {

							$dia = substr($_POST['fdesde'],0,2);
							$mes = substr($_POST['fdesde'],3,2);
							$ano = substr($_POST['fdesde'],6,4);

							$LFdesde=$ano."-".$mes."-".$dia;

							$dia = substr($_POST['fhasta'],0,2);
							$mes = substr($_POST['fhasta'],3,2);
							$ano = substr($_POST['fhasta'],6,4);

							$LFhasta=$ano."-".$mes."-".$dia;

							$SQLFecha=" AND (CTRegLibroDiario.fecha BETWEEN '".$LFdesde."' AND '".$LFhasta."')";
						}


						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

						$SQL="SELECT * FROM CTCategoria WHERE estado='A' AND tipo='ACTIVO' ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {

							if ($_SESSION["PLAN"]=="S"){
								$SQL1="SELECT * FROM CTCuentasEmpresa WHERE id_categoria ='".$registro["id"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
							}else{
								$SQL1="SELECT * FROM CTCuentas WHERE id_categoria ='".$registro["id"]."' ORDER BY detalle";
							}

							$Det="";
							$sumact=0;

							$resul1 = $mysqli->query($SQL1);
							while ($regi1 = $resul1->fetch_assoc()) {

								$SQL2="SELECT *, Sum(debe) AS sdebe, Sum(haber) AS shaber FROM CTRegLibroDiario WHERE cuenta ='".$regi1["numero"]."' AND periodo Like '%".$dano."%' AND glosa='' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
								if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
									$SQL2=$SQL2.$SQLFecha;
								}

								$resul2 = $mysqli->query($SQL2);
								while ($regi2 = $resul2->fetch_assoc()) {

									$sd=$regi2['sdebe'];
									$sh=$regi2['shaber'];

									if (($sd-$sh)!=0) {
										$Det=$Det.'
														<tr>
															<td>'.$regi1['numero'].'</td>
															<td>'.$regi1['detalle'].'</td>
															<td class="text-right">'.$DMONE." ".number_format(($sd-$sh), $NDECI, $DDECI, $DMILE).'</td>
														</tr>
										';
										$sumact=$sumact+($sd-$sh);
									}



								}
							}
							if($Det!=""){
								echo'
									<div class="panel panel-default">
										<div class="panel-heading">'.$registro['nombre'].'</div>
										<div class="panel-body">

											<div class="table-responsive">
											<table class="table">
												<thead>
												<tr>
													<th width="20%">Codigo</th>
													<th>Cuenta</th>
													<th width="20%">Monto</th>
												</tr>
												</thead>
												<tbody>';

								echo $Det;
										
								echo'
												<tr>
													<td></td>
													<td></td>
													<td class="text-right">'.$DMONE." ".number_format(($sumact), $NDECI, $DDECI, $DMILE).'</td>
												</tr>

												</tbody>
											</table>
											</div>

										</div>
									</div>
									<div class="clearfix"></div>
									<br>
								';
							}
							$tactivo=$tactivo+$sumact;
						}
					?>

				</div>
				</div>
			</div>



			<div class="col-sm-6">
				<div class="panel panel-default">
				<div class="panel-body"> Pasivo y Patrimonio<br><br>
					<?php

						if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {

							$dia = substr($_POST['fdesde'],0,2);
							$mes = substr($_POST['fdesde'],3,2);
							$ano = substr($_POST['fdesde'],6,4);

							$LFdesde=$ano."-".$mes."-".$dia;

							$dia = substr($_POST['fhasta'],0,2);
							$mes = substr($_POST['fhasta'],3,2);
							$ano = substr($_POST['fhasta'],6,4);

							$LFhasta=$ano."-".$mes."-".$dia;

							$SQLFecha=" AND (CTRegLibroDiario.fecha BETWEEN '".$LFdesde."' AND '".$LFhasta."')";
						}


						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

						$SQL="SELECT * FROM CTCategoria WHERE estado='A' AND tipo='PASIVO' ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {

							if ($_SESSION["PLAN"]=="S"){
								$SQL1="SELECT * FROM CTCuentasEmpresa WHERE id_categoria ='".$registro["id"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
							}else{
								$SQL1="SELECT * FROM CTCuentas WHERE id_categoria ='".$registro["id"]."' ORDER BY detalle";
							}

							$Det="";
							$sumpas=0;

							$resul1 = $mysqli->query($SQL1);
							while ($regi1 = $resul1->fetch_assoc()) {

								$SQL2="SELECT *, Sum(debe) AS sdebe, Sum(haber) AS shaber FROM CTRegLibroDiario WHERE cuenta ='".$regi1["numero"]."' AND periodo Like '%".$dano."%' AND glosa='' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
								if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
									$SQL2=$SQL2.$SQLFecha;
								}
								// if ($regi1["numero"]=="140101") {
								// 	echo $SQL2;
								// 	exit;
								// }
								$resul2 = $mysqli->query($SQL2);
								while ($regi2 = $resul2->fetch_assoc()) {

									$sd=$regi2['sdebe'];
									$sh=$regi2['shaber'];

									if (($sd-$sh)!=0) {
										$Det=$Det.'
														<tr>
															<td>'.$regi1['numero'].'</td>
															<td>'.$regi1['detalle'].'</td>
															<td class="text-right">'.$DMONE." ".number_format(($sh-$sd), $NDECI, $DDECI, $DMILE).'</td>
														</tr>
										';
										$sumpas=$sumpas+($sh-$sd);
									}
								}
							}
							if($Det!=""){
								echo'
									<div class="panel panel-default">
										<div class="panel-heading">'.$registro['nombre'].'</div>
										<div class="panel-body">

											<div class="table-responsive">
											<table class="table">
												<thead>
												<tr>
													<th width="20%">Codigo</th>
													<th>Cuenta</th>
													<th width="20%">Monto</th>
												</tr>
												</thead>
												<tbody>';

								echo $Det;
										
								echo'
												<tr>
													<td></td>
													<td></td>
													<td class="text-right">'.$DMONE." ".number_format(($sumpas), $NDECI, $DDECI, $DMILE).'</td>
												</tr>

												</tbody>
											</table>
											</div>

										</div>
									</div>
									<div class="clearfix"></div>
									<br>
								';
							}
							$tpasivo=$tpasivo+$sumpas;
						}

					?>

				</div>
				</div>
			</div>
<?php



					$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

					if ($_SESSION["PLAN"]=="S"){
						$SQL='SELECT CTRegLibroDiario.glosa, CTRegLibroDiario.cuenta, CTCuentasEmpresa.detalle AS ncuenta, Sum(CTRegLibroDiario.debe) AS sdebe, Sum(CTRegLibroDiario.haber) AS shaber, CTCategoria.nombre, CTCategoria.tipo FROM (CTRegLibroDiario LEFT JOIN CTCuentasEmpresa ON CTRegLibroDiario.cuenta = CTCuentasEmpresa.numero) LEFT JOIN CTCategoria ON CTCuentasEmpresa.id_categoria = CTCategoria.Id WHERE (((CTRegLibroDiario.rutempresa)="'.$RutEmpresa.'") AND ((CTCuentasEmpresa.rut_empresa)="'.$_SESSION['RUTEMPRESA'].'") AND ((CTCategoria.tipo)="RESULTADO") AND ((CTRegLibroDiario.periodo) Like "%'.$dano.'")';
							if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
								$SQL=$SQL.$SQLFecha;
							}
							$SQL=$SQL.') GROUP BY CTRegLibroDiario.glosa, CTRegLibroDiario.cuenta, CTCuentasEmpresa.detalle, CTCategoria.nombre, CTCategoria.tipo HAVING (((CTRegLibroDiario.cuenta)>0));';
					}else{
						$SQL='SELECT CTRegLibroDiario.glosa, CTRegLibroDiario.cuenta, CTCuentas.detalle AS ncuenta, Sum(CTRegLibroDiario.debe) AS sdebe, Sum(CTRegLibroDiario.haber) AS shaber, CTCategoria.nombre, CTCategoria.tipo FROM (CTRegLibroDiario LEFT JOIN CTCuentas ON CTRegLibroDiario.cuenta = CTCuentas.numero) LEFT JOIN CTCategoria ON CTCuentas.id_categoria = CTCategoria.Id WHERE (((CTRegLibroDiario.rutempresa)="'.$RutEmpresa.'") AND ((CTCategoria.tipo)="RESULTADO") AND ((CTRegLibroDiario.periodo) Like "%'.$dano.'")';
						if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
							$SQL=$SQL.$SQLFecha;
						}
						$SQL=$SQL.') GROUP BY CTRegLibroDiario.glosa, CTRegLibroDiario.cuenta, CTCuentas.detalle, CTCategoria.nombre, CTCategoria.tipo HAVING (((CTRegLibroDiario.cuenta)>0));';
					}



					if (!$resultado = $mysqli->query($SQL)) {
						echo "Lo sentimos, este sitio web está experimentando problemas.xxx";
						exit;
					}

					$ganancia=0;
					$perdida=0;
					while ($registro = $resultado->fetch_assoc()) {

						if($registro["sdebe"]<$registro["shaber"]){
							$ganancia=$ganancia+($registro["shaber"]-$registro["sdebe"]);
						}
						if($registro["sdebe"]>$registro["shaber"]){
							$perdida=$perdida+($registro["sdebe"]-$registro["shaber"]);
						}
						
					}

					$mysqli->close();

					$tresultado=$ganancia-$perdida;


?>


			<div class="clearfix"></div>



			<div class="col-sm-6">
				<div class="panel panel-default">
				<div class="panel-body"> 
					<div class="col-sm-6">P&eacute;rdida </div>
					<div class="col-sm-6 text-right">
						<?php
						if ($tresultado<0) {
							echo $DMONE." ".number_format(($tresultado*-1), $NDECI, $DDECI, $DMILE);
						}
						?>
					</div>
					</div>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="panel panel-default">
				<div class="panel-body"> 
					<div class="col-sm-6">Utilidad </div>
					<div class="col-sm-6 text-right">
						<?php
						if ($tresultado>=0) {
							echo  $DMONE." ".number_format($tresultado, $NDECI, $DDECI, $DMILE);
						}
						?>
					</div>
				</div>
				</div>
			</div>

			<dir class="clearfix"></dir>

			<div class="col-sm-6">
				<div class="panel panel-default">
				<div class="panel-body"> 
					<div class="col-sm-6">Total Activo</div>
					<div class="col-sm-6 text-right">
						<?php
						if ($tresultado<0) {
							echo  $DMONE." ".number_format($tactivo+($tresultado*-1), $NDECI, $DDECI, $DMILE);
						}else{
							echo  $DMONE." ".number_format($tactivo, $NDECI, $DDECI, $DMILE);
						}
						?>
					</div>
				</div>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="panel panel-default">
				<div class="panel-body"> 
					<div class="col-sm-6">Total Pasivo + Patrimonio </div>
					<div class="col-sm-6 text-right">
						<?php 
							if ($tresultado>=0) {
								echo  $DMONE." ".number_format($tresultado+$tpasivo, $NDECI, $DDECI, $DMILE);
							}else{
								echo  $DMONE." ".number_format($tpasivo, $NDECI, $DDECI, $DMILE); 
							}
						?>
					</div>
				</div>
				</div>
			</div>

			<dir class="clearfix"></dir>

			<br><br>



		</form>


			<?php
				if (isset($_POST['check2'])) {
					echo '<div class="col-sm-12">';
					echo 'Certificamos que el presente balance ha sido confeccionado con datos proporcionados por la empresa, conjuntamente con la documentaci&oacute;n de se encuentra en los libros de contabilidad (Art. 100 del C. Tributario)<br><br>';
					echo '</div>';
				}

			?>

				<div class="clearfix"></div>

			<?php
				if (isset($_POST['check5'])) {

					echo '<div class="col-sm-6 text-center">';
					echo strtoupper($_POST['representante']);
					echo '<br>Firma Representante Legal';
					echo '</div>';

					echo '<div class="col-sm-6 text-center">';
					echo strtoupper($_POST['contador']);
					echo '<br>Firma Contador';
					echo '</div>';
				}
			?>


		</form>

	</div>
	</div>

	<br>
	<br>
	<div class="clearfix"> </div>
	
		<script type="text/javascript">
			function xprint(){
				window.print();
			}
		</script>

	</body>
</html>
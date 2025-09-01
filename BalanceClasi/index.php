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
            // $dmes = substr($_POST['anoselect'],0,2);
            // $danol = substr($_POST['anoselect'],3,4);
            $danol=$_POST['anoselect'];
            $Xfdesde="01-01-".$danol;
            $Xfhasta="31-12-".$danol;
        }else{
            $dmes = substr($Periodo,0,2);
            $danol = substr($Periodo,3,4);
            $Xfdesde="01-01-".$danol;
            $Xfhasta="31-12-".$danol;
        } 
    }else{
        $dmes = substr($Periodo,0,2);
        $danol = substr($Periodo,3,4);
        $Xfdesde="01-01-".$danol;
        $Xfhasta="31-12-".$danol;
    } 
	

	$contador=$_SESSION['NOMBRE'];


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
	}

	$mysqli->close();

	if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
		$Xfdesde=$_POST['fdesde'];
		$Xfhasta=$_POST['fhasta'];
	}

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
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">

			function Print(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmBalanceClasificadoPrint.php";
				form1.submit();
				form1.target="";
				form1.action="#";        
			}

			function Updta(valor){
				form1.rfecha.value='';	
				if (valor==2 && form1.fdesde.value!="" && form1.fhasta.value!="") {
					form1.rfecha.value='1';	
				}
				form1.submit();  
			}
			$( function() {
				$("#fdesde").datepicker();
				$("#fhasta").datepicker();
			});

		</script>


	</head>

	<body>


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left" id="areaImprimir">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<br>
			<br>

			<div class="col-md-4">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading">
					<h3 class="panel-title">Balance General Clasificado</h3>
					<input type="hidden" name="CtaMayor" id="CtaMayor">
				</div>
				<div class="panel-body">

					<div class="col-md-12">
						<div class="input-group">
						<span class="input-group-addon">A&ntilde;o</span>
							<select class="form-control" id="anoselect" name="anoselect" required onchange="Updta(1)">
							<?php 
								$yoano=date('Y');
								$tano="2010";
								while($tano<=$yoano){
									if ($danol==$tano) {
										echo "<option value ='".$tano."' selected>".$tano."</option>";
									}else{
										echo "<option value ='".$tano."'>".$tano."</option>";
									}
									$tano=$tano+1;
								}
								$dano=$danol;
								?>
							</select>
						</div>
                    </div>
                    <br>
                    <hr>

                    <h4>Generar por rango de Fecha</h4>
                    <div class="col-md-12">
						<div class="input-group">
						<span class="input-group-addon">Desde</span>
						<input id="fdesde" name="fdesde" type="text" class="form-control text-right" value="<?php echo $Xfdesde; ?>" size="10" maxlength="10">
						</div>
					</div>
					<div class="clearfix"></div>
					<br>

					<div class="col-md-12">
						<div class="input-group">
						<span class="input-group-addon">Hasta</span>
						<input id="fhasta" name="fhasta" type="text" class="form-control text-right" value="<?php echo $Xfhasta; ?>" size="10" maxlength="10">
						</div>
					</div>
					<div class="clearfix"></div>
					<br>
					<div class="col-md-12 text-right">
						<button type="button" class="btn btn-modificar" onclick="Updta(2)">Generar</button>
					</div>
					<input type="hidden" name="rfecha" id="rfecha" value="<?php echo $_POST['rfecha']; ?>">
					<input type="hidden" name="Frfecha" id="Frfecha" value="<?php echo $_POST['rfecha']; ?>">
				</div>
			</div>
			</div>




			<div class="col-md-4">
			<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading">
					<h3 class="panel-title">Pie de Balance Clasificado</h3>
					<input type="hidden" name="aproceso" id="aproceso" value="<?php echo $dano; ?>">
				</div>

				<div class="panel-body">


					<div class="checkbox">

						<input type="checkbox" id="check1" name="check1"> Membrete
						<br>
						<input type="checkbox" id="check7" name="check7"> Membrete Representante
						<br>
						<input type="checkbox" id="check3" name="check3"> Espacio membrete
						<br>
						<input type="checkbox" id="check2" name="check2"> Insertar Art. 100 del C&oacute;digo Tributario
						<br>
						<input type="checkbox" id="check5" name="check5"> Insertar Representante y Contador
						<br><br>

						<div class="col-md-12">
							<div class="input-group">
							<span class="input-group-addon">Representante Legal</span>
								<input type="text" class="form-control" autocomplete="off" id="representante" name="representante" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $representante; ?>">
							</div>
						</div>

						<div class="clearfix"> </div>
						<br>
						<div class="col-md-12">
							<div class="input-group">
							<span class="input-group-addon">Contador</span>
								<input type="text" class="form-control" autocomplete="off" id="contador" name="contador" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $contador; ?>">
							</div>
						</div>

					</div>

				</div>
			</div>
			</div>

	 
			<div class="col-md-4">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading">
					<h3 class="panel-title">Impresi&oacute;n</h3>
					<input type="hidden" name="aproceso" id="aproceso" value="<?php echo $dano; ?>">
				</div>
				<div class="panel-body">
				<fieldset>

						<button type="button" class="btn btn-success btn-block" onclick="Print()">Imprimir</button><br>
						* Se recomienda utilizar Escala entre 80 y 70 % para ajustar, dependendo del tama&ntilde;o del papel a utilizar
				</fieldset>
				</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<br>

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
								$SQL1="SELECT * FROM CTCuentasEmpresa WHERE id_categoria ='".$registro["id"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero";
							}else{
								$SQL1="SELECT * FROM CTCuentas WHERE id_categoria ='".$registro["id"]."' ORDER BY numero";
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
								$SQL1="SELECT * FROM CTCuentasEmpresa WHERE id_categoria ='".$registro["id"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero";
							}else{
								$SQL1="SELECT * FROM CTCuentas WHERE id_categoria ='".$registro["id"]."' ORDER BY numero";
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

			<div class="clearfix"></div>

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

			<div class="clearfix"></div>

			<br><br>



		</form>

	</div>
	</div>

	<br>
	<br>
	<div class="clearfix"> </div>


	<?php include '../footer.php'; ?>

	</body>


	<script type="text/javascript">
		$("#fdesde").datepicker({
			// Formato de la fecha
			dateFormat: "dd-mm-yy",
			// Primer dia de la semana El lunes
			firstDay: 1,
			// Dias Largo en castellano
			dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
			// Dias cortos en castellano
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			// Nombres largos de los meses en castellano
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
			// Nombres de los meses en formato corto 
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
			// Cuando seleccionamos la fecha esta se pone en el campo Input 
			onSelect: function(dateText) { 
				// $('#d1').val(dateText);
				// $('#d2').focus();
				// $('#d2').select();
			}
		});

		$("#fhasta").datepicker({
			// Formato de la fecha
			dateFormat: "dd-mm-yy",
			// Primer dia de la semana El lunes
			firstDay: 1,
			// Dias Largo en castellano
			dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
			// Dias cortos en castellano
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			// Nombres largos de los meses en castellano
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
			// Nombres de los meses en formato corto 
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
			// Cuando seleccionamos la fecha esta se pone en el campo Input 
			onSelect: function(dateText) { 
				// $('#d1').val(dateText);
				// $('#d2').focus();
				// $('#d2').select();
			}
		});       
	</script>

</html>
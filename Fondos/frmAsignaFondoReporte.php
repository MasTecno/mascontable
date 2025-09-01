<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQS="SELECT * FROM CTParametros WHERE estado='A'";
		$resultados = $mysqli->query($SQS);
		while ($registro = $resultados->fetch_assoc()) {
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

		$SQL="SELECT * FROM CTFondo WHERE Tipo='I' AND Id='".$_POST['IdAsiga']."' ORDER BY Id";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xIdPersonal=$registro['IdPersonal'];
			$xRut=$registro['Rut'];
			$xMonto=$registro['Monto'];
			$xTitulo=$registro['Titulo'];
		}

		$xMontoUso=0;
		$SQL="SELECT * FROM CTFondo WHERE Estado='A' AND Tipo='E' AND IdPersonal='".$_POST['IdAsiga']."' ORDER BY Id";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xMontoUso=$xMontoUso+$registro['Monto'];
		}

		// $SQL="SELECT * FROM CTFondoPersonal WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Id='$xIdPersonal' ORDER BY Id";
		// $resultados= $mysqli->query($SQL);
		// while ($registro = $resultados->fetch_assoc()) {
		// 	$xRut=$registro['Rut'];
		// 	$xNombre=$registro['Nombre'];
		// }


		$SQL="SELECT * FROM CTCliPro WHERE id='$xIdPersonal'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xRut=$registro['rut'];
			$xNombre=$registro['razonsocial'];
		}



	$mysqli->close();
?>
<!DOCTYPE html>
<html>
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

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">	

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
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-md-12 text-center">
				<br>
					<a href="javascript: history.go(-1)" class="btn btn-warning btn-sm">
						<span class="glyphicon glyphicon-chevron-left"></span> Volver
					</a>

					<a href="#" class="btn btn-success btn-sm" onclick="printDiv('DivImp')">
						<span class="glyphicon glyphicon-print"></span> Imprimir
					</a>
				<br>
			</div>

		<div id="DivImp">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<br>
				<div class="col-sm-12 text-left">

					<div class="col-sm-12 text-center"> <h1>Resumen Fondo</h1> </div>
					
					<div class="col-sm-2">
						<strong>Rut:</strong>
					</div>
					<div class="col-sm-10">
						<?php
							echo $xRut;
						?>
					</div>

					<div class="col-sm-2">
						<strong>Nombre:</strong>
					</div>
					<div class="col-sm-10">
						<?php
							echo $xNombre;
						?>
					</div>

					<div class="col-sm-2">
						<strong>Fondo:</strong>
					</div>
					<div class="col-sm-10">
						<?php
							echo $xTitulo;
						?>
					</div>

					<div class="clearfix"></div>
					<br>

					<div class="col-sm-12">
						

						<table class="table table-condensed">
						<thead>
							<tr>
								<th class="text-center" width="33%">Monto Asignado</th>
								<th class="text-center" width="33%">Monto Utilizado</th>
								<th class="text-center" width="*">Monto Diferencia</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-center"><?php echo number_format($xMonto, $NDECI, $DDECI, $DMILE); ?></td>
								<td class="text-center"><?php echo number_format($xMontoUso, $NDECI, $DDECI, $DMILE); ?></td>
								<td class="text-center"><?php echo number_format(($xMonto-$xMontoUso), $NDECI, $DDECI, $DMILE); ?></td>
							</tr>
						</tbody>
						</table>


					</div>

					<div class="clearfix"></div>
					<br>
					<br>
					<div class="col-sm-12">
						

						<table class="table table-condensed">
						<thead>
							<tr>
								<th width="20%">Fecha</th>
								<th>Registro</th>
								<th width="20%">Monto</th>
							</tr>
						</thead>
						<tbody>
							<?php

								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$SQL="SELECT * FROM CTFondo WHERE Estado='A' AND Tipo='E' AND IdPersonal='".$_POST['IdAsiga']."' ORDER BY Id";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {

									if ($_SESSION["PLAN"]=="S"){
										$Sql1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["Cuenta"]."' AND rut_empresa='$RutEmpresa'";
									}else{
										$Sql1="SELECT * FROM CTCuentas WHERE  numero='".$registro["Cuenta"]."'";
									}

									$NCodigo="";
									$resultados1 = $mysqli->query($Sql1);
									while ($Reg = $resultados1->fetch_assoc()) {
										$NCodigo= " (".$registro["Cuenta"]."-".$Reg['detalle'].")";
									}

									echo '
										<tr>
											<td>'.date('d-m-Y',strtotime($registro['Fecha'])).'</td>
											<td>'.$registro['Titulo']."  ".$NCodigo.'</td>
											<td>'.number_format($registro['Monto'], $NDECI, $DDECI, $DMILE).'</td>
										</tr>
									';
								}
								$mysqli->close();
							?>
						</tbody>
						</table>


					</div>
				</div>
			</div>
		</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


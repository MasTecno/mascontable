<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:frmMain.php");
		exit;
	}

?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/StConta.css">

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


		</style>

	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="xfrmConfCategoria.php" method="POST" name="form1" id="form1">
			<div class="col-md-2">



			</div>
			<div class="col-md-8">
				<br>
				<h3>IMPORTANTE</h3>
					<br>Con el fin de ir mejorando nuestros servicios, le solicitamos confirmar o corregir la codificaci&oacute;n de las cuentas.
					<br>* Nivel 1 (N1): Corresponde al primer n&uacute;mero del c&oacute;digo, Tipo de Cuentas (Ej: Activo, Pasivo, Resultado).
					<br>* Nivel 2 (N2): Corresponde al segundo n&uacute;mero del c&oacute;digo, Categor&iacute;a de Cuentas (Ej: Activos Circulantes, Pasivos Circulantes, Resultado Ganancia, Etc.).
					<br>De tener duda, estamos atento para ayudarle.
				<br>
				<br>


				<table class="table table-hover">
				<thead>
					<tr>
						<th>Tipo</th>
						<th>Categoria</th>
						<th>N1</th>
						<th>N2</th>
						<th>Cta Ejemplo</th>
					</tr>
				</thead>

				<tbody>
					<?php 
						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
						$SQL="SELECT * FROM CTCategoria WHERE estado='A'";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
						
							$SQL1="SELECT * FROM CTCuentas WHERE id_categoria='".$registro["id"]."' AND estado='A' LIMIT 1";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$cta=$registro1["numero"];
								$det=$registro1["detalle"];
								$d1=substr($registro1["numero"],0,1);
								$d2=substr($registro1["numero"],1,1);
							}

							echo '
							<tr>
								<td>'.$registro["tipo"].'</td>
								<td>'.$registro["nombre"].'</td>
								<td><input type="number" name="N1j'.$registro["id"].'" id="N1j'.$registro["id"].'" value="'.$d1.'" required></td>
								<td><input type="number" name="N2j'.$registro["id"].'" id="N2j'.$registro["id"].'" value="'.$d2.'" required></td>
								<td>'.$cta.' - '.$det.'</td>
							</tr>
							';
						}
						$mysqli->close();

					?>
				</tbody>
				</table>
				<br>
					<div class="col-md-12 text-right">
						<button type="submit" class="btn btn-sm btn btn-primary">
							<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
						</button>
					</div>

			</div>
			<div class="col-md-2">
			</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>
	<br>
	<br>
	

	<?php include 'footer.php'; ?>

	</body>
</html>
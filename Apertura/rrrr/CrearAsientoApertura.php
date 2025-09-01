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
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
		<script src="../js/jquery.dataTables.min.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="../css/StConta.css">

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
			.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
				padding: 2px;
			}
		</style>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-md-12">
				<br>

				<div class="col-md-4">
					<div class="input-group">
							<span class="input-group-addon">Total Activos</span>
							<input type="text" class="form-control" id="TActivo" name="TActivo" readonly>
					</div>					
				</div>
				<div class="col-md-4">
					<div class="input-group">
							<span class="input-group-addon">Total Pasivos</span>
							<input type="text" class="form-control" id="TPasivo" name="TPasivo" readonly>
					</div>
				</div>
				<div class="col-md-4">
					<div class="input-group">
							<span class="input-group-addon">Total Resultado</span>
							<input type="text" class="form-control" id="TResultado" name="TResultado" readonly>
					</div>					
				</div>

				<div class="clearfix"></div>
				<br>
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Debe</span>
							<input type="text" class="form-control" id="TDebe" name="TDebe" readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Haber</span>
							<input type="text" class="form-control" id="THaber" name="THaber" readonly>
						</div>
					</div>
				<div class="clearfix"></div>
				<br>
				<br>

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#home">Activos</a></li>
					<li><a data-toggle="tab" href="#menu1">Pasivos</a></li>
					<li><a data-toggle="tab" href="#menu2">Resultado</a></li>
					<li><a data-toggle="tab" href="#menu3">Procesar</a></li>
				</ul>

				<div class="tab-content">
					<div id="home" class="tab-pane fade in active">
						<h3>Cuentas de Activo</h3>

						<table class="table table-striped">
							<thead>
								<tr>
									<th>Cuenta</th>
									<th>Detalle</th>
									<th>Debe</th>
									<th>Haber</th>
								</tr>
							</thead>
							<tbody>

							<?php
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$SQL="SELECT * FROM Vista_Cuentas WHERE tipo='ACTIVO' AND estado='A' ORDER BY detalle";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()){

									echo '
										<tr>
											<td>'.$registro['numero'].'</td>
											<td>'.$registro['detalle'].'</td>
											<td><input type="text" id="DB'.$registro['numero'].'"></td>
											<td><input type="text" id="HB'.$registro['numero'].'"></td>
										</tr>
									';
								}
							?>

							</tbody>
						</table>

					</div>

					<div id="menu1" class="tab-pane fade">
						<h3>Cuentas de Pasivo</h3>
						<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>

					<div id="menu2" class="tab-pane fade">
						<h3>Cuentas de Resultado</h3>
						<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
					</div>

					<div id="menu3" class="tab-pane fade">
						<h3>Procesar</h3>
						<p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
					</div>
				</div>

			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


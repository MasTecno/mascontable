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

		</style>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-sm-12 text-left">

				

			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


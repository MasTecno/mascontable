<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
			<br>
			<div class="panel panel-info">
				<div class="panel-heading">Libro de Compra/Venta</div>
				<div class="panel-body">
					<a href="frmPLCompraVenta.php" class="btn btn-primary" role="button">Ingresar</a>
				</div>
			</div>
				

			</div>
			<div class="col-sm-2">
			</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include 'footer.php'; ?>

	</body>
</html>
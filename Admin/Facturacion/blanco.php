<?php
	include '../../conexion/conexion.php';
	include '../../js/funciones.php';
	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../../index.php?Msj=95");
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


	</head>
	<body>
		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-sm-12 text-left">
				<br>

				<div class="well well-sm">
					<strong>Bitacora Server </strong>
				</div>

				<form action="#" method="POST" name="form1" id="form1">

					<div class="col-md-2">
					<div class="input-group">
						<span class="input-group-addon">Fecha</span>
						<input id="d1" name="d1" type="text" class="form-control" size="10" maxlength="10" required="" value="<?php echo $textfecha; ?>" >
					</div>
					</div> 

					<div class="col-md-8">
					<div class="input-group">
						<span class="input-group-addon">Nombre</span>
						<textarea class="form-control" rows="5" id="comment" required=""></textarea>
					</div>
					</div> 


					<dir class="clearfix"></dir>
				</form>
			</div>

		</div>
		</div>
	</body>
</html>
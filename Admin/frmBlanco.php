<?php
	include 'conexion/conexion.php';
	include 'js/funciones.php';
	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:index.php?Msj=95");
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

				<form method="POST" enctype="multipart/form-data" action="xSubirGaleria.php" id="form1" name="form1"> 

					<h2 class="text-center">Subir Galeria</h2>
					<input type="hidden" name="fim" id="fim">
					<input type="hidden" name="ffo" id="ffo" value="<?php echo $X; ?>" >

					<div class="col-md-12">
						<label class="btn btn-block btn-primary">
							<input type="file" name="uploadedfile" id="uploadedfile" required="required">
						</label>
					</div>

					<div class="clearfix"></div>
					
					<hr>
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-primary">Subir</button>
						<button type="button" class="btn btn-danger" onclick="Volver()">Volver</button>
					</div>
					<dir class="clearfix"></dir>
				</form>
			</div>

		</div>
		</div>
	</body>
</html>
<?php
	//include 'conexion/conexion.php';
	// include 'js/funciones.php';
	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:index.php?Msj=95");
		exit;
	}
	
?> 
<!DOCTYPE html>
<html >
	<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
		<title>MasContable</title>
		
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


				<form action="#" method="POST" name="form1" id="form1">

					<div class="well well-sm">
						<strong>Administraci&oacute;n</strong>
					</div>
                        <div class="col-md-12 text-center">
					        <a href="listserver.php" class="btn btn-info btn-lg" role="button">Gestion de Servidores </a>
					    </div>
                    
                        <!--<div class="col-md-6">
					        <a href="Remu/listserver.php" class="btn btn-info btn-sm" role="button">Estado Servidores Remuneraciones</a>
					    </div>-->
                    <dir class="clearfix"></dir>
					<br>
					<br>
					
					<div class="well well-sm">
						<strong>Facturaci&oacute;n</strong>
					</div>
                        <div class="col-md-12 text-center">
					        <a href="Facturacion/" class="btn btn-info btn-lg" role="button">Subir Facturas</a>
					    </div>
                    
                        <!--<div class="col-md-6">
					        <a href="Remu/listserver.php" class="btn btn-info btn-sm" role="button">Estado Servidores Remuneraciones</a>
					    </div>-->
                    <dir class="clearfix"></dir>
					<br>
					<br>


					<div class="well well-sm">
						<strong>Facturaci&oacute;n</strong>
					</div>
                        <div class="col-md-12 text-center">
					        <a href="frmEnviar.php" class="btn btn-info btn-lg" role="button">Mensajero</a>
					    </div>
                    
                        <!--<div class="col-md-6">
					        <a href="Remu/listserver.php" class="btn btn-info btn-sm" role="button">Estado Servidores Remuneraciones</a>
					    </div>-->
                    <dir class="clearfix"></dir>
					<br>
					<br>


					<div class="well well-sm">
						<strong>Utilitarios</strong>
					</div>

					<a href="frmSQLProceso2.php" class="btn btn-info btn-lg" role="button">In Sql Contabilidad</a>
                    <dir class="clearfix"></dir>
					<a href="Remu/frmSQLProceso2.php" class="btn btn-info btn-lg" role="button">In Sql Remuneraciones</a>

					<dir class="clearfix"></dir>
				</form>
			</div>

		</div>
		</div>
	</body>
</html>
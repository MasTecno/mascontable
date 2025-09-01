<?php
	include 'conexionserver.php';

	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../index.php?Msj=95");
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
		<script type="text/javascript">
	        function ProceString(){

	            var url= "xsqlProceso.php";

	            $.ajax({
	                type: "POST",
	                url: url,
	                data: $('#form1').serialize(),
	                success:function(resp){
	                    $('#Msj').html(resp);
	                }

	            });
	        } 

		</script>

	</head>
	<body>
		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-sm-12 text-left">
				<br>

				<div class="well well-sm">
					<h1>Sistema de Remuneraciones</h1>
				</div>

				<form action="#" method="POST" name="form1" id="form1">

					<div class="col-md-2">
					<div class="input-group">
						<span class="input-group-addon">Server</span>
						<select class="form-control" id="sel1" name="sel1">
							<?php

								$mysqli=conectarServer();

								$sql = "SELECT * FROM UnionServer WHERE Estado='A' ORDER BY Server";
								$resultado = $mysqli->query($sql);

								while ($registro = $resultado->fetch_assoc()) {
									echo '<option value="'.$registro["id"].'">'.$registro["Server"].'</option>';
								}
								$mysqli->close();

							?>
						</select>
					</div>
					</div> 

					<div class="col-md-8">
					<div class="input-group">
						<span class="input-group-addon">String SQL</span>
						<textarea class="form-control" rows="5" id="SqlScript" name="SqlScript" required=""></textarea>
					</div>
					</div> 
					<dir class="clearfix"></dir>

					<button type="button" class="btn btn-danger" onclick="ProceString()">Procesar</button>

					<dir class="clearfix"></dir>

					<dir id="Msj">
						
					</dir>
				</form>
			</div>

		</div>
		</div>
	</body>
</html>
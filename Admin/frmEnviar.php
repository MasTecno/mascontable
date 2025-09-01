<?php
	include 'conexionserver.php';
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
		<script type="text/javascript">
			
			function GenCorreo(){

				// var url= "GfrmEnviar.php";
				// $.ajax({
				// 	type: "POST",
				// 	url: url,
				// 	contentType: 'multipart/form-data',
				// 	data: $('#form1').serialize(),
				// 	success:function(resp){
				// 		$(Ms).html(resp);
				// 	}
				// });
				form1.action="";
				form1.submit();

			} 

			function DContacto(){
				var url= "DatosContacto.php";
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					success:function(resp){
						$("#TCorreo").val(resp.dato1);
						$("#TContacto").val(resp.dato2);
						$("#TCorto").val(resp.dato3);
						$("#TMonto").val(resp.dato4);
						$("#comment").html(resp.dato5);
						$("#MesCorto").val(resp.dato6);
						$("#TxtPeriodo").val(resp.dato7);
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
					<strong>Mensajero</strong>
				</div>

				<form action="GfrmEnviar.php" method="POST" id="form1" name="form1" enctype="multipart/form-data">

					<div class="col-md-4">
						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-addon">Servidores</span>
								<select class="form-control" id="ListServ" name="ListServ" onchange="DContacto()" required>
									<option value="">Seleccione</option>
								<?php
									$mysqli=conectarServer();

									$sql = "SELECT * FROM UnionServer WHERE Estado='A' ORDER BY Numero";
									$resultado = $mysqli->query($sql);

									while ($registro = $resultado->fetch_assoc()) {
										echo '<option value="'.$registro["id"].'">'.$registro["Server"].'</option>';
									}
									$mysqli->close();
								?>
								</select>
							</div>
						</div>						
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-addon">Correo</span>
								<input class="form-control" type="text" name="TCorreo" id="TCorreo" required>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-addon">Contacto</span>
								<input class="form-control" type="text" name="TContacto" id="TContacto">
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-addon">Nombre Corto</span>
								<input class="form-control" type="text" name="TCorto" id="TCorto" required>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-addon">Mes</span>
								<input class="form-control" type="text" name="MesCorto" id="MesCorto" required>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>
						
						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-addon">Periodo</span>
								<input class="form-control" type="text" name="TxtPeriodo" id="TxtPeriodo" required>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-addon">Monto</span>
								<input class="form-control" type="text" name="TMonto" id="TMonto" required>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12 text-left">
							<div class="input-group">
								<span class="input-group-addon">Seleccionar Archivo</span>
								<input type="file" class="form-control-file" multiple="true" name="archivos[]" required>
								<!-- <input type="hidden" value="upload" name="action" /> -->
							</div>
							<small style="font-size: 65%;">	
								* Peso Maximo 1 MB <br>
								* Selecci&oacute;n multiples archivos
							</small>	
						</div> 

						<div class="clearfix"></div>
						<br>


					</div>

					<div class="col-md-6">
						<div class="col-md-12">
							<div class="form-group">
								<label for="comment">Comentario:</label>
								<textarea class="form-control" id="comment" name="comment" rows="20"></textarea>
							</div>						
						</div>
						<div class="clearfix"></div>
						<br>
						<div class="col-md-12 text-center">
							<button type="submit" class="btn btn-primary">Subir</button>
							<button type="button" class="btn btn-danger" onclick="Volver()">Volver</button>
						</div>

					</div>

					<div class="col-md-2">

						<?php
							$mysqli=conectarServer();

							$sql = "SELECT * FROM LogEnvio WHERE Estado='A' ORDER BY id DESC";
							$resultado = $mysqli->query($sql);

							while ($registro = $resultado->fetch_assoc()) {
								echo $registro["Server"].' # '.$registro["Periodo"].' # '.$registro["Fecha"]."<br>";
							}
							$mysqli->close();
						?>
						
					</div>

					<div class="clearfix"></div>

					<div class="clearfix"></div>
					
					<hr>

					<dir class="clearfix"></dir>
				</form>
			</div>

		</div>
		</div>
	</body>
</html>
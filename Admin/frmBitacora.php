<?php
	include 'conexionserver.php';
	include 'conexion.php';


	session_start();

	$textfecha=date("d-m-Y");
	$idServer=$_POST['Bita'];

	if ($_POST['comment']!="" && $_POST['fecha']!="") {
		
		$mysqli=conectarServer();
		/* comprobar la conexión */
		if ($mysqli->connect_errno) {
		    printf("Falló la conexión: %s\n", $mysqli->connect_error);
		    exit();
		}

		$dia = substr($_POST['fecha'],0,2);
		$mes = substr($_POST['fecha'],3,2);
		$ano = substr($_POST['fecha'],6,4);

		$Fecha=$ano."/".$mes."/".$dia;

		$mysqli->query("INSERT INTO Bitacora VALUES('','".$_POST['Bita']."','".$Fecha."','".$_POST['sel1']."','".$_POST['comment']."','A')");

		$mysqli->close();

	}

	$mysqli=conectarServer();

	$XRut="";
	$XRSocial="";
	$XContacto="";
	$XCorreo="";
	$XTelefono="";

	$sql = "SELECT * FROM DatosPersonales WHERE idServer='$idServer' AND Estado='A' ORDER BY id DESC";
	if (!$resultado = $mysqli->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		exit;
	}

	while ($registro = $resultado->fetch_assoc()) {
		$XRut=$registro["Rut"];
		$XRSocial=$registro["RSocial"];
		$XContacto=$registro["Contacto"];
		$XCorreo=$registro["Correo"];
		$XTelefono=$registro["Telefono"];
	}



	$sql = "SELECT * FROM UnionServer WHERE id='$idServer' AND Estado='A' ORDER BY id DESC";
	if (!$resultado = $mysqli->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		exit;
	}

	while ($registro = $resultado->fetch_assoc()) {
		$NomServer=$registro["Server"];
		$AliServer=$registro["Alias"];


		if($XRut==""){

			$mysqliX=xconectar($registro["Usuario"],$registro["Clave"],$registro["Base"]);

			$sqlin = "SELECT * FROM CTContadores where Correo<>'admin@mastecno.cl' AND estado='A'";
			if (!$resultadoin = $mysqliX->query($sqlin)) {
				echo "Lo sentimos, este sitio web está experimentando problemas.";
				exit;
			}

			while ($registroin = $resultadoin->fetch_assoc()) {
				$XRSocial=$registroin["nombre"];
				$XContacto=$registroin["nombre"];
				$XCorreo=$registroin["correo"];
			}
			$mysqliX->close();
		}
	}

	$mysqli->close();

					// $mysqli=conectarServer();

					// $sql = "SELECT * FROM UnionServer ORDER BY Server ASC";
					// if (!$resultado = $mysqli->query($sql)) {
					// 	echo "Lo sentimos, este sitio web está experimentando problemas.";
					// 	exit;
					// }

					// while ($registro = $resultado->fetch_assoc()) {




?> 

<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	</head>
	<script type="text/javascript">
		function Volver(){
			form1.action="listserver.php";
			form1.submit();
		}
	</script>

	<body>
		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-sm-12 text-left">
				<br>

				<div class="well well-sm">
					<strong>Bitacora <?php echo "$NomServer - $AliServer ";?></strong>
				</div>

				<form action="#" method="POST" name="form1" id="form1">

					<dir class="col-sm-12 table-responsive">
						<dir class="col-md-4">
						 	<strong> Rut:</strong> <?php echo $XRut; ?><br>
							<strong> Razon Social:</strong> <?php echo $XRSocial; ?><br>
							<strong> Contacto:</strong> <?php echo $XContacto; ?><br>
						</dir>
						<dir class="col-md-4">
							<strong> Correo:</strong> <?php echo $XCorreo; ?><br>
							<strong> Telefono:</strong> <?php echo $XTelefono; ?><br>
						</dir>
						<dir class="clearfix"></dir>
					<!-- <table class="table">
						<thead>
							<tr>
							<th>Rut</th>
							<th>Razon Social</th>
							<th>Email</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							<td><?php echo $XRut; ?></td>
							<td><?php echo $XRSocial; ?></td>
							<td><?php echo $XContacto; ?></td>
							</tr>
						</tbody>
					</table>
					</dir>

					<dir class="col-sm-12 table-responsive">
					<table class="table">
						<thead>
							<tr>
							<th>Correo</th>
							<th>Telefono</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							<td><?php echo $XContacto; ?></td>
							<td><?php echo $XTelefono; ?></td>
							</tr>
						</tbody>
					</table> -->
					</dir>


					<input type="hidden" name="Bita" id="Bita" value="<?php echo $idServer; ?>">
					<div class="col-md-2">
					<div class="input-group">
						<span class="input-group-addon">Fecha</span>
						<input id="fecha" name="fecha" type="text" class="form-control" size="10" maxlength="10" required="" value="<?php echo $textfecha; ?>" >
					</div>
					</div> 

					<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">Contacto por</span>
						
						<select class="form-control" id="sel1" name="sel1">
							<?php

								$mysqli=conectarServer();

								$sql = "SELECT * FROM FContactos WHERE Estado='A'";
								if (!$resultado = $mysqli->query($sql)) {
									echo "Lo sentimos, este sitio web está experimentando problemas.";
									exit;
								}

								while ($registro = $resultado->fetch_assoc()) {
									echo '<option value="'.$registro["Nombre"].'">'.$registro["Nombre"].'</option>';
								}
								$mysqli->close();

							?>
						</select>

					</div>
					</div> 

						<dir class="clearfix"></dir>
					<div class="col-md-12">
					<div class="input-group">
						<span class="input-group-addon">Comentario</span>
						<textarea class="form-control" rows="5" id="comment" name="comment" required=""></textarea>
					</div>
					</div> 

						<dir class="clearfix"></dir>
					<br>
					<div class="col-md-12">
						<button type="submit" class="btn btn-default">Grabar</button>
						<button type="button" class="btn btn-default" onclick="Volver()">Cancelar</button>
					</div>
						<dir class="clearfix"></dir>


					<hr>
					<table class="table table-bordered">
						<thead>
						<tr>
						<th width="20%">Fecha</th>
						<th>Contacto</th>
						<th>Comentario</th>
						</tr>
						</thead>
					<tbody>
						<?php
							$mysqli=conectarServer();

							$sql = "SELECT * FROM Bitacora WHERE idServer='$idServer' AND Estado='A' ORDER BY Fecha DESC, id DESC";
							if (!$resultado = $mysqli->query($sql)) {
								echo "Lo sentimos, este sitio web está experimentando problemas.";
								exit;
							}

							while ($registro = $resultado->fetch_assoc()) {
								echo'
									<tr>
										<td>'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
										<td>'.$registro["Contacto"].'</td>
										<td>'.nl2br($registro["Comentario"]).'</td>
									</tr>
								';
							}

						?>
					</tbody>
					</table>

				</form>
			</div>

		</div>
		</div>
	</body>
<script type="text/javascript">

$( "#fecha" ).datepicker({
    // Formato de la fecha
    dateFormat: "dd-mm-yy",
    // Primer dia de la semana El lunes
    firstDay: 1,
    // Dias Largo en castellano
    dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
    // Dias cortos en castellano
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    // Nombres largos de los meses en castellano
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
    // Nombres de los meses en formato corto 
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
    // Cuando seleccionamos la fecha esta se pone en el campo Input 
    onSelect: function(dateText) { 
        // $('#fecha').val(dateText);
    }
});  
</script>	
</html>
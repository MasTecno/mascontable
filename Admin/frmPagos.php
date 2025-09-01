<?php
	include 'conexionserver.php';
	include 'conexion.php';

	$idServer=$_POST['Bita'];


	if ($_POST['comment']!="" && $_POST['periodo']!="" && $_POST['fecha']!="" && $_POST['monto']!="") {
		
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

		$mysqli->query("INSERT INTO Pagos VALUES('','".$_POST['Bita']."','".$Fecha."','".$_POST['sel1']."','".$_POST['comment']."','A')");

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

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<script type="text/javascript">
		function Volver(){
			form1.action="listserver.php";
			form1.submit();
		}
	</script>

	</head>
	<body>
		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-sm-12 text-left">
				<br>

				<div class="well well-sm">
					<strong>Registro de Pago</strong>
				</div>

				<form action="#" method="POST" name="form1" id="form1">
					<div class="col-md-12">

						<dir class="col-md-4">
						 	<strong> Rut:</strong> <?php echo $XRut; ?><br>
							<strong> Razon Social:</strong> <?php echo $XRSocial; ?><br>
							<strong> Contacto:</strong> <?php echo $XContacto; ?><br>
						</dir>
						<dir class="col-md-4">
							<strong> Correo:</strong> <?php echo $XCorreo; ?><br>
							<strong> Telefono:</strong> <?php echo $XTelefono; ?><br>
							<input type="hidden" name="Bita" id="Bita" value="<?php echo $idServer; ?>">
						</dir>
					</div>

					<dir class="clearfix"></dir>

					<div class="col-md-2">
					<div class="input-group">
						<span class="input-group-addon">Fecha</span>
						<input id="fecha" name="fecha" type="text" class="form-control" size="10" maxlength="10" required>
					</div>
					</div> 

					<div class="col-md-2">
					<div class="input-group">
						<span class="input-group-addon">Periodo</span>
						<input id="periodo" name="periodo" type="text" class="form-control" size="7" maxlength="7" required>
					</div>
					</div> 

					<div class="col-md-2">
					<div class="input-group">
						<span class="input-group-addon">Monto</span>
						<input id="monto" name="monto" type="text" class="form-control" required>
					</div>
					</div> 
					<dir class="clearfix"></dir>

					<div class="col-md-8">
					<div class="input-group">
						<span class="input-group-addon">Obsercación</span>
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

					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
								<th>Fecha</th>
								<th>Periodo</th>
								<th>Monto</th>
								<th>Comentario</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$mysqli=conectarServer();

									$sql = "SELECT * FROM Pagos WHERE idServer='$idServer' AND Estado='A' ORDER BY Fecha DESC, id DESC";
									if (!$resultado = $mysqli->query($sql)) {
										echo "Lo sentimos, este sitio web está experimentando problemas.";
										exit;
									}

									while ($registro = $resultado->fetch_assoc()) {
										echo'
											<tr>
												<td>'.date('d-m-Y',strtotime($registro["FechaPago"])).'</td>
												<td>'.$registro["Periodo"].'</td>
												<td>'.$registro["Monto"].'</td>
												<td>'.nl2br($registro["Comentario"]).'</td>
											</tr>
										';
									}

								?>
								
							</tbody>
						</table>
					</div>


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
<?php
	include 'conexionserver.php';
	// include '../js/funciones.php';
	session_start();

	$textfecha=date("d-m-Y");
	$idServer=$_POST['Bita'];


	if ($_POST['Elim']!="" && $idServer!="") {
		$mysqli=conectarServer();
		$mysqli->query("UPDATE DatosPersonales SET Estado='B' WHERE idServer='$idServer' AND Estado='A'");
		$mysqli->close();
	}

	if ($_POST['Bita']!="" && $_POST['rut']!="" && $_POST['rsocial']!="" && $_POST['Elim']=="") {
		
		//// inserta o actuliza datos de la empresa 
		$mysqli=conectarServer();

		$sql = "SELECT * FROM DatosPersonales WHERE idServer='$idServer' AND Estado='A'";
		if (!$resultado = $mysqli->query($sql)) {
			echo "Lo sentimos, este sitio web está experimentando problemas.";
			exit;
		}

		$row_cnt = $resultado->num_rows;

		$dia = substr($_POST['fpago'],0,2);
		$mes = substr($_POST['fpago'],3,2);
		$ano = substr($_POST['fpago'],6,4);

		$xfecha=$ano."/".$mes."/".$dia;


		if($row_cnt>0){
			$mysqli->query("UPDATE DatosPersonales SET idPlan='".$_POST['sel1']."', Rut='".$_POST['rut']."', RSocial='".$_POST['rsocial']."', Contacto='".$_POST['contacto']."', Corto='".$_POST['corto']."', Direccion='".$_POST['direccion']."', Comuna='".$_POST['comuna']."', Giro='".$_POST['giro']."', Correo='".$_POST['correo']."', Telefono='".$_POST['telefono']."', Monto='".$_POST['monto']."', FPago='".$xfecha."' WHERE idServer='$idServer' AND Estado='A'");

		}else{

			$mysqli->query("INSERT INTO DatosPersonales VALUES('','$idServer','".$_POST['sel1']."','".$_POST['rut']."','".$_POST['rsocial']."','".$_POST['contacto']."','".$_POST['corto']."','".$_POST['direccion']."', '".$_POST['comuna']."','".$_POST['giro']."','".$_POST['correo']."', '".$_POST['telefono']."', '".$_POST['monto']."','".$xfecha."','A')");

		}

		$sql = "SELECT * FROM UnionServer WHERE id='$idServer'";
		if (!$resultado = $mysqli->query($sql)) {
			echo "Lo sentimos, este sitio web está experimentando problemas.";
			exit;
		}

		while ($registro = $resultado->fetch_assoc()) {
			$xxbase=$registro['Base'];
			$xxusua=$registro['Usuario'];
			$xxclav=$registro['Clave'];
		}

		$sql = "SELECT * FROM Planes WHERE id='".$_POST['sel1']."'";

		if (!$resultado = $mysqli->query($sql)) {
			echo "Lo sentimos, este sitio web está experimentando problemas.";
			exit;
		}

		while ($registro = $resultado->fetch_assoc()) {
			$NomPlan=$registro['Nombre'];
			$NomPlan=$NomPlan.', Valor: '.number_format($registro['Valor'], 1, ".", ",");
		}


		$mysqli->close();

		///// actualiza base server x, con datos de la empresa

		include 'conexion.php';

		$mysqli=xconectar($xxusua,$xxclav,$xxbase);

			$mysqli->query("TRUNCATE TABLE Licencia");

			$mysqli->query("INSERT INTO Licencia VALUES('','".$_POST['rut']."','".$_POST['rsocial']."','".$_POST['direccion']."','".$_POST['comuna']."','".$_POST['giro']."','".$_POST['contacto']."','".$_POST['correo']."','".$_POST['telefono']."','".$NomPlan."','".$xfecha."')");

		$mysqli->close();

	}

	if ($idServer=="") {
		$idServer=$_POST['Pers'];
	}

	$mysqli=conectarServer();

	$sql = "SELECT * FROM UnionServer WHERE id='$idServer' AND Estado='A' ORDER BY id DESC";
	if (!$resultado = $mysqli->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		exit;
	}

	while ($registro = $resultado->fetch_assoc()) {
		$NomServer=$registro["Server"];
		$AliServer=$registro["Alias"];
	}


	$sql = "SELECT * FROM DatosPersonales WHERE idServer='$idServer' AND Estado='A' ORDER BY id DESC";
	if (!$resultado = $mysqli->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		exit;
	}

	while ($registro = $resultado->fetch_assoc()) {
		$xRut=$registro["Rut"];
		$xRSocial=$registro["RSocial"];
		$xContacto=$registro["Contacto"];
		$xCorto=$registro["Corto"];
		$xDireccion=$registro["Direccion"];
		$xComuna=$registro["Comuna"];
		$xGiro=$registro["Giro"];
		$xCorreo=$registro["Correo"];
		$xTelefono=$registro["Telefono"];
		$xMonto=$registro["Monto"];
		$xidPlan=$registro["idPlan"];
		$xFPago=$registro["FPago"];
	}

	if ($xFPago=="0000-00-00") {
		$xFPago="";
	}else{
		if ($xFPago=="") {
			$xFPago="";
		}else{
			$xFPago= date('d-m-Y',strtotime($xFPago));
		}
	}

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

		function Borrar(){

			var txt;
			var r = confirm("Desea Eliminar estos antecedentes");
			if (r == true) {
				form1.Elim.value="S";
				form1.submit();
			}
			
		}


	</script>

	<body>
		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-sm-12 text-left">
				<br>

				<div class="well well-sm">
					<strong>Informaci&oacute;n del <?php echo " $NomServer - $AliServer"; ?> </strong>
				</div>

				<form action="#" method="POST" name="form1" id="form1">
					<input type="hidden" name="Bita" id="Bita" value="<?php echo $idServer; ?>">
					<input type="hidden" name="Elim" id="Elim" value="">
					<div class="col-md-2">
					<div class="input-group">
						<span class="input-group-addon">Rut</span>
						<input id="rut" name="rut" type="text" class="form-control" autocomplete="false" required value="<?php echo $xRut; ?>" >
					</div>
					</div> 

					<div class="col-md-10">
					<div class="input-group">
						<span class="input-group-addon">Raz&oacute;n Social</span>
						<input id="rsocial" name="rsocial" type="text" class="form-control" autocomplete="false" required value="<?php echo $xRSocial; ?>" >
					</div>
					</div> 
						<dir class="clearfix"></dir>

					<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">Direcci&oacute;n</span>
						<input id="direccion" name="direccion" type="text" class="form-control" autocomplete="false" required value="<?php echo $xDireccion; ?>" >
					</div>
					</div> 

					<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">Comuna</span>
						<input id="comuna" name="comuna" type="text" class="form-control" autocomplete="false" required value="<?php echo $xComuna; ?>" >
					</div>
					</div> 
						<dir class="clearfix"></dir>

					<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">Giro</span>
						<input id="giro" name="giro" type="text" class="form-control" autocomplete="false" required value="<?php echo $xGiro; ?>" >
					</div>
					</div> 
						<dir class="clearfix"></dir>

					<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">Contacto</span>
						<input id="contacto" name="contacto" type="text" class="form-control" autocomplete="false" value="<?php echo $xContacto; ?>" >
					</div>
					</div> 
					<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">Nombre Corto</span>
						<input id="corto" name="corto" type="text" class="form-control" autocomplete="false" value="<?php echo $xCorto; ?>" >
					</div>
					</div> 

					<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">Correo</span>
						<input id="correo" name="correo" type="email" class="form-control" autocomplete="false" value="<?php echo $xCorreo; ?>" >
					</div>
					</div> 
						<dir class="clearfix"></dir>

					<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">Telefono</span>
						<input id="telefono" name="telefono" type="text" class="form-control" autocomplete="false" value="<?php echo $xTelefono; ?>" >
					</div>
					</div> 
						<dir class="clearfix"></dir>

					<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">Plan</span>
								<select class="form-control" id="sel1" name="sel1" required>
									<option value="" selected></option>
									<?php
										$sql = "SELECT * FROM Planes WHERE Estado='A'";
										if (!$resultado = $mysqli->query($sql)) {
											echo "Lo sentimos, este sitio web está experimentando problemas.";
											exit;
										}

										while ($registro = $resultado->fetch_assoc()) {
											if ($xidPlan == $registro["id"]) {
												echo '<option value="'.$registro["id"].'" selected>'.$registro["Nombre"].', Valor: '.number_format($registro["Valor"], 2, ".", ",").'</option>';
											}else{
												echo '<option value="'.$registro["id"].'">'.$registro["Nombre"].', Valor: '.number_format($registro["Valor"], 2, ".", ",").'</option>';
											}
										}
										
										$mysqli->close();
									?>

								</select>
					</div>
					</div> 

					<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">Monto</span>
						<input id="monto" name="monto" type="text" class="form-control" autocomplete="false" value="<?php echo $xMonto; ?>" >
					</div>
					</div> 

					<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">Fecha Pago</span>
						<input id="fpago" name="fpago" type="text" class="form-control" autocomplete="false" value="<?php echo $xFPago; ?>" >
					</div>
					</div> 
						<dir class="clearfix"></dir>


					<br>
					<div class="col-md-12">
						<button type="submit" class="btn btn-default">Grabar</button>
						<button type="button" class="btn btn-default" onclick="Volver()">Cancelar</button>
						<button type="button" class="btn btn-default" onclick="Borrar()">Eliminar</button>
					</div>
						<dir class="clearfix"></dir>

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
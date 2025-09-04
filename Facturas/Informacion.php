<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	// include '../conexion/secciones.php';

	// echo $_SESSION['NOMBRE'];
	// exit;
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$PeriodoX=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];
	$_SESSION['SWFACTURA']="N";

	$mysqli=ConCobranza();

	$SQL="SELECT * FROM Contacto WHERE IdServer='".$_SESSION['xIdServer']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xnombre=$registro['Nombre'];  
		$xcorreo=$registro['Correo'];  
		$xtelefono=$registro['Telefono'];  
	}
// Maestra
	$SQL="SELECT * FROM Maestra WHERE IdServer='".$_SESSION['xIdServer']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$lRutFactura=$registro['RutFactura'];  
		$lRSocial=$registro['RSocial'];  
		$lDireccion=$registro['Direccion'];  
		$lComuna=$registro['Comuna'];  
		$lGiro=$registro['Giro'];  
		$lTelefono=$registro['Telefono'];  
		$lCorreo=$registro['Correo'];  
		$lexenta=$registro['Exenta'];
		$lvalor=$registro['Valor'];
		$lplan=$registro['IdPlan'];
	}

	$SQL="SELECT * FROM Sistemas WHERE Id='$lplan'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$lplan=$registro['Nombre']; 
	}






	
	$mysqli->close();

?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<script src="../js/jquery.Rut.js" type="text/javascript"></script>
		<script src="../js/jquery.validate.js" type="text/javascript"></script>	

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">

		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script type="text/javascript">
			$(document).ready(function(){
			$('#rut').Rut({ 
				on_error: function(){alert('Rut incorrecto'); $('#rut').val(""); $('#rut').focus();} 
			});

			$('#rutrep').Rut({ 
				on_error: function(){alert('Rut incorrecto'); $('#rutrep').val(""); $('#rutrep').focus();} 
			});
			});

			function NumYGuion(e){
			var key = window.Event ? e.which : e.keyCode
				return (key >= 48 && key <= 57 || key == 45 || key==75 || key==107)
			}
			function FEnvio(){
				form1.action="xfrmInformacion.php";
				form1.submit();
			}
		</script> 

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
			
			<div class="space-y-8">
				<form action="xfrmInformacion.php" name="form1" id="form1" method="POST">

					<div class="sticky top-0 bg-white z-50 md:static flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-8">

						<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-save mr-2"></i>Grabar
						</button>

						<a href="../Facturas" 
							class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-xmark mr-2"></i>Cancelar
						</a>
					</div>

					<div class="bg-white rounded-lg shadow-sm border border-gray-200">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fa fa-address-book text-lg text-blue-600"></i>
							</div>
							<div class="text-lg font-semibold text-gray-700">Datos de Contacto</div>
						</div>


						<div class="p-6 pt-1 space-y-6">

							<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-2 mt-3">

								<div>
									<label for="nombre" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-user mr-1"></i>Nombre
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xnombre; ?>" required>
								</div>

								<div>
									<label for="correo" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-envelope mr-1"></i>Correo
									</label>
									<input type="mail" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="correo" name="correo" onChange="javascript:this.value=this.value.toLowerCase();" value="<?php echo $xcorreo; ?>" required>
								</div>

								<div>
									<label for="telefono" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-phone mr-1"></i>Tel&eacute;fono +569
									</label>
									<input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="telefono" name="telefono" maxlength="8" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xtelefono; ?>" required>
								</div>
							</div>
						</div>
					</div>

					<div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-5">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fa-solid fa-list text-lg text-blue-600"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-800">
									Facturaci&oacute;n
								</h3>
								<!-- <p class="text-sm text-gray-600">Datos para ingresar un contacto</p>      -->
							</div>
							
                           
                    	</div>
						<div class="p-6 pt-1 space-y-6">

							<div class="w-full p-2 mt-2 text-center">
								<strong>Documento</strong><br>
								<div class="flex justify-center items-center gap-10 mt-2">
									<label class="radio-inline"><input type="radio" name="SelDoc" id="SelDoc" value="FACTURA" <?php if ($lexenta=="FACTURA") { echo "checked"; } ?>>FACTURA</label>
									<label class="radio-inline"><input type="radio" name="SelDoc" id="SelDoc" value="BOLETA" <?php if ($lexenta=="BOLETA") { echo "checked"; } ?>>BOLETA</label>
									<label class="radio-inline"><input type="radio" name="SelDoc" id="SelDoc" value="INTERNA" <?php if ($lexenta=="INTERNA") { echo "checked"; } ?>>INTERNA</label>									
								</div>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2 mt-3">
								<div>
									<label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-id-card mr-1"></i>Rut
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="rut" autocomplete="off" name="rut" onChange="javascript:this.value=this.value.toUpperCase();" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" value="<?php echo $lRutFactura; ?>" required>
								</div>

								<div>
									<label for="rsocial" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-building mr-1"></i>Raz&oacute;n Social
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="rsocial" name="rsocial" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lRSocial; ?>" required>
								</div>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-2 mt-3">
								<div>
									<label for="direccion" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-location-dot mr-1"></i>Direcci&oacute;n
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="direccion" name="direccion" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lDireccion; ?>" required>
								</div>

								<div>
									<label for="comuna" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-city mr-1"></i>Comuna
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="comuna" name="comuna" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lComuna; ?>" required>
								</div>
								
								<div>
									<label for="giro" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-briefcase mr-1"></i>Giro
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="giro" name="giro" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lGiro; ?>" required>
								</div>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2 mt-3">
								<div>
									<label for="cenvio" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-envelope mr-1"></i>Correo Envio
									</label>
									<input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="cenvio" name="cenvio" onChange="javascript:this.value=this.value.toLowerCase();" value="<?php echo $lCorreo; ?>" required>
								</div>

								<div>
									<label for="etelefono" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-phone mr-1"></i>Tel&eacute;fono +569
									</label>
									<input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="etelefono" name="etelefono" maxlength="8" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lTelefono; ?>" required>
								</div>
							</div>


							<div class="col-md-12 text-center">
								<strong>Informaci&oacute;n Plan</strong>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2 mt-3">
								<div>
									<label for="plan" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-circle-info mr-1"></i>Plan
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="" name="" value="<?php echo $lplan; ?>" readonly="false">
								</div>
								<div>
									<label for="valor" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-money mr-1"></i>Valor
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="" name="" value="<?php echo $lvalor; ?>" readonly="false">
								</div>
							</div>
						</div>
					</div>

					<div class="text-center p-2">
						<samp>* Todos los Datos son Obligatorios</samp>
					</div>

					


				</form>
			</div>

		</div>
		</div>
		<?php include '../footer.php'; ?>
	</body>
</html>
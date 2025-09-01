<?php
	if ($_GET['Adm']!="" && isset($_GET['Adm'])) {

		include '../conexion/conexionmysqli.php';
		include '../js/funciones.php';
		session_start();


		// $_SESSION['PERIODO']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']

		$_SESSION['NOMBRE']="Admini";
		$_SESSION['RAZONSOCIAL']="Admini";
		$_SESSION['ROL']="A";

		$mysqli=ConCobranza();
		
		$SQL="SELECT * FROM Servidores WHERE Id='".descript($_GET['Adm'])."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$NServer=$registro['Nombre'];
			$_SESSION['xIdServer']=$registro['Id'];  
		}

		$mysqli->close();

		$mysqli=conectarUnion();

		$Pref=randomText(35);
		$Suf=randomText(8);

		$SQL="SELECT * FROM UnionServer WHERE Alias='".$NServer."' OR Server='".$NServer."' AND Estado='A'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$NumServer=$registro["Numero"];
			$_SESSION['NomServer']=$registro["Server"];
			$_SESSION['BaseSV']=$registro["Base"];
			$_SESSION['UsuariaSV']=$registro["Usuario"];
			$_SESSION['PassSV']=$Pref.$registro["Clave"].$Suf;
		}

		$mysqli->close();

	}else{
		include '../conexion/conexionmysqli.php';
		include '../js/funciones.php';
		include '../conexion/secciones.php';
	}
	

	// echo $_SESSION['xIdServer'];
	// echo $_SESSION['NOMBRE'];


	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

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
	// echo $_SESSION['NOMBRE'];

	$SQL="SELECT * FROM Sistemas WHERE Id='$lplan'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$lplan=$registro['Nombre']; 
	}

	if ($lplan=="") {
		$lplan="Sin Plan Asignado";
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
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-md-2">
				<a href="Informacion.php" class="btn btn-block btn-modificar"><span class="glyphicon glyphicon-user"></span> Antecedentes</a>
				<a href="Documentos.php" class="btn btn-block btn-grabar"><span class="glyphicon glyphicon-list"></span> Facturaci&oacute;n</a>			
				<a href="Transferencias.php" class="btn btn-block btn-cancelar"><span class="glyphicon glyphicon-resize-small"></span> Transferencias</a>			
			</div>
			<div class="col-md-8 text-left">
				<form action="#" name="form1" id="form1" method="POST">

					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Datos de Contacto</div>
						<div class="panel-body">
							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Nombre</span>
									<input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xnombre; ?>"  readonly="false">
								</div>
							</div>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Correo</span>
									<input type="mail" class="form-control" id="correo" name="correo" onChange="javascript:this.value=this.value.toLowerCase();" value="<?php echo $xcorreo; ?>"  readonly="false">
								</div>
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Tel&eacute;fono +569</span>
									<input type="number" class="form-control" id="telefono" name="telefono" maxlength="8" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xtelefono; ?>"  readonly="false">
								</div>
							</div>
						</div>
					</div>

					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Facturaci&oacute;n</div>
						<div class="panel-body">

							<div class="col-md-12 text-center">
								<strong>
									Documento 
									<?php 
										if ($lexenta=="1") { 
											echo "Exenta"; 
										} 
										if ($lexenta=="") { 
											echo "Afecta"; 
										} 
										if ($lexenta=="I") { 
											echo "Interna"; 
										} 
									?>
								</strong><br>
								
							</div>
							<div class="clearfix"> </div>
							<br>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Rut </span>
									<input type="text" class="form-control" id="rut" autocomplete="off" name="rut" onChange="javascript:this.value=this.value.toUpperCase();" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" value="<?php echo $lRutFactura; ?>"  readonly="false">
								</div>
							</div> 
							<div class="col-md-8">
								<div class="input-group">
									<span class="input-group-addon">Raz&oacute;n Social</span>
									<input type="text" class="form-control" autocomplete="off" id="rsocial" name="rsocial" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lRSocial; ?>"  readonly="false">
								</div>
							</div>

							<div class="clearfix"> </div>
							<br>
							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Direcci&oacute;n</span>
									<input type="text" class="form-control" autocomplete="off" id="direccion" name="direccion" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lDireccion; ?>"  readonly="false">
								</div>
							</div>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Comuna</span>
									<input type="text" class="form-control" autocomplete="off" id="comuna" name="comuna" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lComuna; ?>"  readonly="false">
								</div>            
							</div>


							<div class="clearfix"> </div>
							<br>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Giro</span>
									<input type="text" class="form-control" autocomplete="off" id="giro" name="giro" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lGiro; ?>"  readonly="false">
								</div>            
							</div>

							<div class="clearfix"> </div>
							<br>
							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Correo Envio</span>
									<input type="email" class="form-control" autocomplete="off" id="cenvio" name="cenvio" onChange="javascript:this.value=this.value.toLowerCase();" value="<?php echo $lCorreo; ?>"  readonly="false">
								</div>
							</div>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Tel&eacute;fono +569</span>
									<input type="number" class="form-control" autocomplete="off" id="etelefono" name="etelefono" maxlength="8" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $lTelefono; ?>"  readonly="false">
								</div>            
							</div>


							<div class="clearfix"> </div>
							<br>
							<div class="col-md-12 text-center">
								<strong>Informaci&oacute;n Plan</strong>
							</div>
							<div class="clearfix"> </div>
							<br>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Plan</span>
									<input type="text" class="form-control" autocomplete="off" id="" name="" value="<?php echo $lplan; ?>" readonly="false">
								</div>
							</div>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Valor</span>
									<input type="text" class="form-control" autocomplete="off" id="" name="" value="<?php echo $lvalor; ?>" readonly="false">
								</div>            
							</div>


							<div class="clearfix"> </div>
							<br>
						</div>
					</div>
				</form>
			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


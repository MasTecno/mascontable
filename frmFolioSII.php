<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:index.php?Msj=95");
		exit;
	}
	
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    if($Periodo==""){
      header("location:frmMain.php");
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
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/select2.css">
	</head>

	<body>
		<?php include 'nav.php'; ?>

		<div class="container-fluid text-left">
		<div class="row content">

			<form action="frmFolioSIIPDF.php" method="POST" target="_blank" name="form1" id="form1">

				<div class="col-md-2">
				</div>
				<div class="col-md-8">
					<br>
					<br>
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">
							<h3 class="panel-title">Foliador de Hojas</h3>
						</div>

						<div class="panel-body">
							<fieldset>

							<div class="input-group">
								<span class="input-group-addon">Folio Inicial</span>
								<input type="text" class="form-control" id="finicial" name="finicial" placeholder="Inicio de Folio 36" required>
							</div>
							</br>

							<div class="input-group">
								<span class="input-group-addon">N&uacute;mero de Hojas</span>
								<input type="text" class="form-control" id="nhojas" name="nhojas" placeholder="N&uacute;mero de Hojas 10, ternimando en 45" required>
							</div>
							</br>

							<!-- <div class="input-group">
								<span class="input-group-addon">Largo de Folio</span>
								<input type="text" class="form-control" id="lfolio" name="lfolio" placeholder="Anteponer con 0 o largo del folio para rellenar con 0. Folio 36 con 5 de Largo = Folio: 00036 " required>
							</div>
							<div class="clearfix"></div>
							<br> -->

							<div class="input-group">
								<span class="input-group-addon">Orientaci&oacute;n</span>
								<select class="form-control" id="horinta" name="horinta" required>
									<option value="P">Vertical</option>
									<option value="L">Horizontal</option>
								</select>
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="input-group">
								<span class="input-group-addon">Membrete</span>
								<select class="form-control" id="Membre" name="Membre" required>
									<option value="N">NO</option>
									<option value="S">SI Completo</option>
									<option value="F">SI, Sin Representante</option>
								</select>
							</div>
							<br>

							<button type="submit" class="btn btn-default">Procesar</button>

							</fieldset>
						</div>
					</div>



				</div>
				<div class="col-md-2">
				</div>
			</form>

		</div>
		</div>

		<div class="clearfix"> </div>
		<?php include 'footer.php'; ?>
	</body>
</html>
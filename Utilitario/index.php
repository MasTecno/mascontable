<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
		<script src="../js/jquery.dataTables.min.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="../css/StConta.css">

		<style>
			/* Remove the navbar's default margin-bottom and rounded borders */
			.navbar {
			margin-bottom: 0;
			border-radius: 0;
			}

			/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
			.row.content {height: 450px}

			/* Set gray background color and 100% height */
			.sidenav {
			padding-top: 20px;
			background-color: #f1f1f1;
			height: 100%;
			}

			/* Set black background color, white text and some padding */
			footer {
			background-color: #555;
			color: white;
			padding: 15px;
			}

			/* On small screens, set height to 'auto' for sidenav and grid */
			@media screen and (max-width: 767px) {
			.sidenav {
			height: auto;
			padding: 15px;
			}
			.row.content {height:auto;}
			}

		</style>
		<script type="text/javascript">
			
			function Proce(r1){
				if (r1==1) {
					sw = document.getElementById("Acep1").checked;
					if (sw==true) {
						form1.Sw1.value=1;
					}else{
						form1.Sw1.value=0;
					}
				}
				if (r1==2) {
					sw = document.getElementById("Acep2").checked;
					if (sw==true) {
						form1.Sw2.value=1;
					}else{
						form1.Sw2.value=0;
					}
				}
				if (r1==3) {
					sw = document.getElementById("Acep3").checked;
					if (sw==true) {
						form1.Sw3.value=1;
					}else{
						form1.Sw3.value=0;
					}
				}

			}
			function vali(){
				if (document.getElementById("Acep1").checked==false || document.getElementById("Acep2").checked==false || document.getElementById("Acep3").checked==false) {
					alert("Para iniciar el proceso debe leer y aceptar la condiciones anteriores");

				}else{

					document.getElementById("BtrProce").style.display = 'none';
					document.getElementById("Mensa1").style.display = 'inline';

					document.getElementById("Mensa2").style.display = 'none';
					document.getElementById("Mensa3").style.display = 'none';

					var url= "ProcesaRefolio.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){

							document.getElementById("BtrProce").style.display = 'inline';
							document.getElementById("Mensa1").style.display = 'none';

							if (resp=="exito") {
								document.getElementById("Mensa3").style.display = 'inline';
								resp="&Eacute;xito en el proceso, no se registraron errores, Saludos.";
								$('#Msjexito').html(resp);
							}else{
								document.getElementById("Mensa2").style.display = 'inline';
								$('#Msjerror').html(resp);
							}

						}
					});	

				}

			}
		</script>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<br>
			<div class="col-sm-12">
				<input type="hidden" name="Sw1" id="Sw1" value="0">
				<input type="hidden" name="Sw2" id="Sw2" value="0">
				<input type="hidden" name="Sw3" id="Sw3" value="0">
				<div class="col-md-2"></div>
				
				<div class="col-md-10">
					<div class="col-md-8">
						<div class="input-group">
								<span class="input-group-addon">A&ntilde;o que se ejecutara el proceso</span>
								<input type="text" class="form-control" id="PApertura" name="PApertura" readonly value="<?php echo substr($Periodo, 3, 4);; ?>">
						</div>
					</div>
					<div class="clearfix"></div>

					<br>
					<label class="checkbox-inline">
						<input type="checkbox" onclick="Proce('1')" id="Acep1" name="Acep1">1. He realizado la marca del asiento de apertura.
					</label> 
					<br>
					<label class="checkbox-inline">
						<input type="checkbox" onclick="Proce('2')" id="Acep2" name="Acep2">2. No se est&aacute;n ejecutado o registrando procesos en esta empresa.
					</label> 
					<br>
					<label class="checkbox-inline">
						<input type="checkbox" onclick="Proce('3')" id="Acep3" name="Acep3">3. Esperare que este proceso termine solo, ya que si realizo una operaci&oacute;n puedo descuadrar los registros de la empresa.
					</label> 
					<br>
					<br>
					<br>
					<!-- <div class="col-md-12 text-center"> -->
						<button type="button" onclick="vali()" id="BtrProce" class="btn btn-default">Procesar</button>
					<!-- </div> -->
					<div class="clearfix"></div>
					<br>

					<div class="col-md-12 text-center">
						<div class="alert alert-warning" id="Mensa1" style="background-color: #fbc7c7; border-color: #b35c5c; display:none;">
							<strong>Importante!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
						</div>					

						<div class="col-md-12" id="Mensa2" style="background-color: #eb0909; border-color: #e90f0f; color: #fff; display:none;">
							<z id="Msjerror"></z>
						</div>
						<!-- <div class="alert alert-warning" id="Mensa32" style="background-color: #eb0909; border-color: #e90f0f; color: #fff; display:none;">
							<strong>Error!</strong> .
						</div>		 -->			

						<div class="alert alert-warning" id="Mensa3" style="background-color: #ddfbc7; border-color: #5cb35f; display:none;">
							<strong>Exito!</strong> <z id="Msjexito"></z>
						</div>					

					</div>


				</div>

			</div>
		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


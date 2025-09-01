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

			function Procesar(){
				if (form1.sdocumentos.value!="") {
					var url= "frmNDocumentosDet.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#grilla').html(resp);
						}
					});	
				}
			}

			function printDiv(nombreDiv) {
				var contenido= document.getElementById(nombreDiv).innerHTML;
				var contenidoOriginal= document.body.innerHTML;

				document.body.innerHTML = contenido;

				window.print();

				document.body.innerHTML = contenidoOriginal;
			}


		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<form action="" method="POST" name="form1" id="form1">
			<br>
			<div class="col-sm-12 text-left">

				<div class="col-md-4">
				<div class="input-group">
					<span class="input-group-addon">Documentos</span>
					<select class="form-control" id="sdocumentos" name="sdocumentos">
						<option value="">Seleccione</option>
						<option value="V">Ingreso</option>
						<option value="C">Egreso</option>
						
					</select>
				</div>
				</div>


				<div class="col-md-4">
				<div class="input-group">
					<span class="input-group-addon">Tipo</span>
					<select class="form-control" id="tdocumentos" name="tdocumentos">
						<option value="">Todos</option>
						<option value="C">Cheque</option>
						<option value="T">Trasferencias</option>
						<option value="O">Otros</option>
						
					</select>
				</div>
				</div>

				<div class="clearfix"></div>
				<br>

				<div class="col-md-4">
				<div class="input-group">
					<span class="input-group-addon">Fecha</span>
					<select class="form-control" id="sfecha" name="sfecha">
						<option value="E">Emisi&oacute;n</option>
						<option value="V">Vencimiento</option>						
					</select>
				</div>
				</div>


				<div class="col-md-3">
				<div class="input-group">
					<span class="input-group-addon">Desde</span>
					<input id="fdesde" name="fdesde" type="text" class="form-control text-right" size="10" maxlength="10">
				</div>
				</div>

				<div class="col-md-3">
				<div class="input-group">
					<span class="input-group-addon">Hasta</span>
					<input id="fhasta" name="fhasta" type="text" class="form-control text-right" size="10" maxlength="10">
				</div>
				</div>

				<div class="clearfix"></div>
				<br>

				<div class="col-md-4">
				<div class="input-group">
					<button type="button" class="btn btn-default btn-sm" onclick="Procesar()">Visualizar</button>
				</div>
				</div>
				

			</div>
			<div class="clearfix"></div>
			<br>

			<div class="col-sm-12" id="grilla">
	
			</div>

			</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

		<script type="text/javascript">
			$( "#fdesde" ).datepicker({
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
					// $('#d1').val(dateText);
					// $('#d2').focus();
					// $('#d2').select();
				}
			});

			$( "#fhasta" ).datepicker({
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
					// $('#d1').val(dateText);
					// $('#d2').focus();
					// $('#d2').select();
				}
			});				
		</script>

	</body>

</html>


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
	$XanoD = substr($Periodo,3,4);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script type="text/javascript">

			function Procesar(){
				if (form1.id_tipodocumento.value=="") {
					alert("Selecione Entidad parea realizar la consulta");
				}else{

					document.getElementById("BtrProce").style.display = 'none';
					document.getElementById("Mensa").style.display = 'inline';

					if (form1.pendiente.value=="N") {
						var url= "frmNComVenHonDet.php";
					}

					if (form1.pendiente.value=="S") {
						var url= "frmNComVenHonDetPen.php";
					}

					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#grilla').html(resp);
							document.getElementById("BtrProce").style.display = 'inline';
							document.getElementById("Mensa").style.display = 'none';
						}
					});	
				}
			}

			$(window).load(function(){
				$('#trazon').select2();
			});


			function UpEntidad(){
				if (form1.id_tipodocumento.value!="") {
					var url= "BuscaEntidad.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#trazon').html(resp);
							$('#trazon').select2();
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

			$( function() {
				$( "#fdesde" ).datepicker();
				$( "#fhasta" ).datepicker();
			} );

			function GenLibro(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmNComVenHonDetExcel.php";
				form1.submit();
				form1.target="";
				form1.action="#";        
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

				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Anal&iacute;tico Clientes - Proveedores - Honorarios </div>
					<div class="panel-body">

						<div class="col-md-4">
						<div class="input-group">
							<span class="input-group-addon">Entidades</span>
							<select class="form-control" id="id_tipodocumento" name="id_tipodocumento" onchange="UpEntidad()" required>
								<option value="">Seleccione</option>
								<option value="C">Clientes</option>
								<option value="P">Proveedores</option>
								<option value="H">Honorarios</option>
							</select>
						</div>
						</div>

						<div class="col-md-4">
						<div class="input-group">
							<span class="input-group-addon">Raz&oacute;n Social</span>
							<select class="form-control" id="trazon" name="trazon">
								<option value="0">Seleccione</option>

							</select>
						</div>
						</div>

						<div class="clearfix"></div>
						<br>

						<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">Desde</span>
							<input id="fdesde" name="fdesde" type="text" class="form-control text-right" size="10" maxlength="10" value="">
						</div>
						</div>

						<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">Hasta</span>
							<input id="fhasta" name="fhasta" type="text" class="form-control text-right" size="10" maxlength="10" value="">
						</div>
						</div>

						<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">N&deg;</span>
							<input id="ndocu" name="ndocu" type="text" class="form-control text-right">
						</div>
						</div>

						<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">Solo Pendientes</span>
							<select class="form-control" id="pendiente" name="pendiente">
								<option value="N">No</option>
								<option value="S">Si</option>
							</select>
						</div>
						</div>

						<div class="clearfix"></div>
						<br>

						<div class="col-md-4">
						<div class="input-group">
							<button type="button" class="btn btn-grabar" name="BtrProce" id="BtrProce" onclick="Procesar()">Generar</button> 
							<button type="button" class="btn btn-modificar" onclick="GenLibro()">Generar Excel</button>
						</div>
						</div>

					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<br>

			<div class="col-md-12 text-center">
			<!-- <div class="input-group"> -->
				<div class="alert alert-warning" id="Mensa" style="background-color: #fbc7c7; border-color: #b35c5c; display:none;">
					<strong>Generando!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
				</div>					
			<!-- </div> -->
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


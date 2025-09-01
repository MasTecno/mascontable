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

	// $textfecha=date("d");

	function UltimoDiaMesD($periodo) { 
		$month = substr($periodo,0,2);
		$year = substr($periodo,3,4);
		$day = date("d", mktime(0,0,0, $month+1, 0, $year));

		return date('d', mktime(0,0,0, $month, $day, $year));
	};

    $textfecha=UltimoDiaMesD($Periodo);

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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">
			function CargGrilla(){
				if (form1.tdocumentos.value=="") {
					alert("Seleccione Documentos a Buscar");
				}else{
					var url= "grilla.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#grilla').html(resp);
							//LisPeriodo1();
						}
					});					
				}
			}

			function NConsulta(){
				window.location.href = "../GVoucher";
			}

			function Calculo(){
				var url= "calculo.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){

						mySplits = resp.split(","); 

						form1.monto.value=mySplits[0];
						form1.monto1.value=mySplits[0];
						form1.ContDoc.value=mySplits[1];
						if (mySplits[1]>1) {
							document.getElementById("monto").readOnly = true;
						}else{
							document.getElementById("monto").readOnly = false;
						}
						if (form1.fpago.value=="B") {
							UpFPago();
						}
					}
				});					
			}

			function LisPeriodo(){
				var url= "periodos.php";
				//form1.valcc.value=valor;
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#LSelPeriodo').html(resp);
					}
				});	
			}

			function LisPeriodo1(){
				var url= "periodos.php";
				//form1.valcc.value=valor;
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#LSelPeriodoDoc').html(resp);
					}
				});	
			}

			function UpFPago(){
				if (form1.fpago.value=="B") {
					var url= "banco.php";
				
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#divfpago').html(resp);
							UpFecha();
							// $( "#fdoc" ).datepicker();
							// $( "#fdocven" ).datepicker();
						}
					});
				}
				if (form1.fpago.value=="C") {
					var url= "efectivo.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#divfpago').html(resp);
						}
					});					
				}

				if (form1.fpago.value=="A") {
					var url= "fondorendir.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#divfpago').html(resp);
						}
					});					
				}
			}

			function UpFecha(){
				$( "#fdoc" ).datepicker({
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

				$( "#fdocven" ).datepicker({
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
			}

			function Grb(){
				if (form1.ttmovimiento.value=="" || form1.tfecha.value=="" || form1.cacuenta.value=="" || form1.fpago.value=="" || form1.monto.value==""  || form1.glosa.value=="") {
					alert("Falta informaci\363n para continuar");
				}else{
					if (form1.tfecha.value<1 || form1.tfecha.value>31) {
						alert("Error en la fecha");
					}else{
						if (form1.cuentaasi.value=="") {
							alert("Selecciones la cuenta del movimiento");
						}else{
							$SwGR=1;
							d1=0;
							d2=0;
							d1=form1.monto.value;
							d2=form1.monto1.value;

							if (parseInt(d1)>parseInt(d2)) {
								var r = confirm("El monto Asignado es mayor al monto original de documento\r\nDesea Continuar?");
								if (r == true) {
									$SwGR=1;
								}else{
									$SwGR=0;
								}
							}

							if ($SwGR=="1") {
								document.getElementById("BtnGraba").style.visibility = "hidden";
								var url= "procesar.php";
								$.ajax({
									type: "POST",
									url: url,
									data: $('#form1').serialize(),
									success:function(resp){
										if (resp=="") {
											form1.glosa.value="";
											form1.monto.value="";
											form1.monto1.value="";
											CargGrilla();
											// showMessage();
											alert("Documento Procesado con Exito");
										}else{
											alert(resp);
										}
										document.getElementById("BtnGraba").style.visibility = "visible";
									}
								});
							}
						}
					}
				}
			}

			function seleccionar_todo(){
			for (i=0;i<document.form1.elements.length;i++)
				if(document.form1.elements[i].type == "checkbox"){
					document.form1.elements[i].checked=1;
				}
				Calculo();
			}

			function deseleccionar_todo(){
			for (i=0;i<document.form1.elements.length;i++)
				if(document.form1.elements[i].type == "checkbox"){
					document.form1.elements[i].checked=0
				}
				Calculo();
			}
		</script>

	<style>	
        /* body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        } */

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.5s, visibility 0.5s;
        }

        #overlay.visible {
            visibility: visible;
            opacity: 1;
        }

        #messageBox {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        #messageBox i {
            font-size: 50px;
            color: green;
            margin-bottom: 10px;
        }

        #messageBox p {
            font-size: 24px;
            margin: 0;
        }

        #triggerButton {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        #triggerButton:hover {
            background-color: #0056b3;
        }
    </style>

	</head>
	<body onload="">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">

		<div id="overlay">
			<div id="messageBox">
				<i>&#10004;</i> <!-- Icono de check -->
				<p>Grabado con éxito</p>
			</div>
		</div>

		<form action="#" method="POST" name="form1" id="form1">
			<br>

			<div class="col-sm-12">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading">Procesar Documentos</div>
				<div class="panel-body">
					<!-- <div class="col-md-2"></div> -->
					<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon">Registros</span>
						<select class="form-control" id="tdocumentos" name="tdocumentos" onchange="CargGrilla(); LisPeriodo1();" required>
							<option value="">Seleccione</option>
							<option value="C">Documentos de Compra</option>
							<option value="V">Documentos de Venta</option>
							<option value="H">Honorarios</option>
						</select>
					</div>
					</div>

					<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon">Periodos</span>
						<select class="form-control" id="LSelPeriodoDoc" name="LSelPeriodoDoc">
							<option value="">Seleccione</option>
						</select>
					</div>
					</div>

					<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">Rut - Raz&oacute;n Social - N&uacute;mero</span>
							<input type="text" class="form-control" name="cadena" id="cadena">
						</div>
					</div>

					<div class="col-md-1">
						<button type="button" class="btn btn-modificar btn-sm" id="BotLim" name="BotLim" onclick="CargGrilla()">Consultar</button>
					</div>

				</div>
				</div>

				<div class="clearfix"></div>
				<hr>

				<div class="col-md-8">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Genera Voucher</div>
					<div class="panel-body">

						<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Tipo</span>
							<select class="form-control" id="ttmovimiento" name="ttmovimiento" required>
								<option value="">Seleccione</option>
								<option value="I">Ingreso</option>
								<option value="E">Egreso</option>
								<option value="T">Traspaso</option>
							</select>
						</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">Dia</span>
							<input id="tfecha" name="tfecha" type="number" class="form-control text-right" size="4" maxlength="2" required value="<?php echo $textfecha; ?>">
						</div>
						</div>

						<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">Periodo</span>
							<input id="swee" name="swee" type="text" class="form-control text-right" readonly size="4" maxlength="4" value="<?php echo $Periodo; ?>">
						</div>
						</div>


						<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Cuenta Cargo/Abono</span>
							<select class="form-control" id="cacuenta" name="cacuenta">
								<option value="">Seleccione</option>
								<?php
									$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
									if ($_SESSION["PLAN"]=="S"){
										$SQL="SELECT * FROM CTCuentasEmpresa WHERE auxiliar='X' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
									}else{
										$SQL="SELECT * FROM CTCuentas WHERE auxiliar='X' ORDER BY detalle";
									}
									$resultados = $mysqli->query($SQL);
									while ($registro = $resultados->fetch_assoc()) {
										echo '<option value="'.$registro['numero'].'">'.$registro['numero'].' - '.$registro['detalle'].'</option>';
									}
									$mysqli->close();
								?>
							</select>
						</div>
						</div>

						<div class="clearfix"></div>
						<br>

						<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Forma de Pago</span>
							<select class="form-control" id="fpago" name="fpago" onchange="UpFPago()" required>
								<option value="">Seleccione</option>
								<option value="C">Efectivo</option>
								<option value="B">Banco/Otro Documento</option>
								<option value="A">Fondo a Rendir</option>
							</select>
						</div>
						</div>

						<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Monto</span>
							<input type="number" class="form-control text-right" name="monto" id="monto">
							<input type="hidden" class="form-control text-right" name="monto1" id="monto1">
						</div>
						</div>						

						<div class="clearfix"></div>
						<br>

						<div class="col-md-10">
						<div class="input-group">
							<span class="input-group-addon">Glosa</span>
							<input type="text" class="form-control text-right" onChange="javascript:this.value=this.value.toUpperCase();" name="glosa" id="glosa">
						</div>
						</div>

						<div class="col-md-2">
							<button type="button" class="btn btn-grabar btn-block" id="BtnGraba" name="BtnGraba" onclick="Grb()">Grabar</button>
						</div>			
					</div>
					</div>

				</div>

				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Forma de Pago</div>
					<div class="panel-body" id="divfpago">

					</div>
					</div>
					
				</div>
			

				<div class="clearfix"></div>
				<br>

				<div class="col-md-12">

					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Documentos Pendientes</div>
					<div class="panel-body">
						<div class="col-md-6">
							<input class="form-control" id="myInput" type="text" placeholder="Buscador...">
						</div>

						<div class="col-md-2">
						<div class="input-group">
							<span class="input-group-addon">Cant.Doc.</span>
							<input class="form-control" type="text" name="ContDoc" id="ContDoc" readonly="false">
						</div>
						</div>

						<div class="col-md-12" id="grilla">
						</div>

					</div>
					</div>

				</div>

				<br>
			</div>
		</form>
		</div>
		</div>
		<script>
			$(document).ready(function(){
			$("#myInput").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#myTable tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});
			});

			<?php 
				if (isset($_GET['Msj']) && $_GET['Msj']=="5") {
					echo 'alert("Error en la concatenacion de la Fecha, campo dia");';
				}
			?>

			document.getElementById('tdocumentos').addEventListener('change', function() {
				var tdocumentosValue = this.value;
				var ttmovimiento = document.getElementById('ttmovimiento');

				if (tdocumentosValue === 'V') {
					ttmovimiento.value = 'I';
				} else if (tdocumentosValue === 'C' || tdocumentosValue === 'H') {
					ttmovimiento.value = 'E';
				} else {
					ttmovimiento.value = '';
				}

				document.getElementById('cacuenta').value='';
			});


			function showMessage() {
				document.getElementById('overlay').classList.add('visible');
				setTimeout(() => {
					document.getElementById('overlay').classList.remove('visible');
				}, 3000); // Duración de 3 segundos
			}


		</script>
		<?php include '../footer.php'; ?>

	</body>

</html>
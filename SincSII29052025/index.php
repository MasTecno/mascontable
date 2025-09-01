<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];


	//$SwBaja="SI";
	$SwBaja="NO";

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	$ValRSII="";
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTCliPro WHERE tipo='P' ORDER BY id ASC;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$PRut=$registro['rut'];

		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P'";
		$Resul = $mysqli->query($SQL1);
		$row_cnt = $Resul->num_rows;
		if($row_cnt>1){
			$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P' ORDER BY id ASC LIMIT 1;";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$IdReg=$registro1['id'];
			}			
			$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='P'");
			// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
		}

	}

	$SQL="SELECT * FROM CTCliPro WHERE tipo='C' ORDER BY id ASC;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$PRut=$registro['rut'];

		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C'";
		$Resul = $mysqli->query($SQL1);
		$row_cnt = $Resul->num_rows;
		if($row_cnt>1){
			$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C' ORDER BY id ASC LIMIT 1;";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$IdReg=$registro1['id'];
			}			
			$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='C'");
			// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
		}

	}

	$SQL="SELECT * FROM CTCliPro WHERE tipo='2' ORDER BY id ASC;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$PRut=$registro['rut'];

		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2'";
		$Resul = $mysqli->query($SQL1);
		$row_cnt = $Resul->num_rows;
		if($row_cnt>1){
			$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2' ORDER BY id ASC LIMIT 1;";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$IdReg=$registro1['id'];
			}			
			$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='2'");
			// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
		}

	}


	$SQL="SELECT * FROM DTEParametros WHERE RutEmpresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$ValRSII=$registro['RutSii']; 
		$ValCSII=$registro['PasSii']; 
	}
	if ($ValRSII=="") {
		$ValRSII=$_SESSION['RUTEMPRESA'];
	}

	$mysqli->close();

	// function pingUrl($url) {
	// 	$ch = curl_init($url);
	
	// 	// Configura las opciones de CURL
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	curl_setopt($ch, CURLOPT_HEADER, true);
	// 	curl_setopt($ch, CURLOPT_NOBODY, true);
	// 	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
	// 	// Ejecuta la petición CURL
	// 	$response = curl_exec($ch);
	
	// 	// Obtiene el código de respuesta HTTP
	// 	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	// 	curl_close($ch);
	// 	$MsjSII ="SI";
	// 	if ($http_code >= 200 && $http_code < 300) {
	// 		//$MsjSII = "SI";
	// 	} else {
	// 		//$MsjSII = "Conexión con el SII, temporalmente fuera de servicio. <br> SII con servicio de sincronización abajo, HTTP Code: $http_code";
	// 	}
	// 	return $MsjSII;
	// }
	
	// Usar la función de ping con una URL
	// $Msj = pingUrl("https://herculesr.sii.cl/cgi_AUT2000/CAutInicio.cgi");

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



			function Procesar() {

				let promesa = Promise.resolve(); // Inicia con una promesa resuelta

				if (document.getElementById("CkCompra").checked) {
					promesa = promesa.then(() => ProcesarC());
				}
				if (document.getElementById("CkVenta").checked) {
					promesa = promesa.then(() => ProcesarV());
				}
				if (document.getElementById("CkHonorario").checked) {
					promesa = promesa.then(() => ProcesarH());
				}
				if (document.getElementById("CkTercero").checked) {
					promesa = promesa.then(() => ProcesarHT());
				}

				document.getElementById("BtrProce").style.display = 'inline';
			}

			function ProcesarC(){
				return new Promise((resolve, reject) => {
					document.getElementById("C03").style.display = 'none';
					document.getElementById("V03").style.display = 'none';
					document.getElementById("H03").style.display = 'none';
					document.getElementById("HT03").style.display = 'none';

					form1.SWOperacion.value="COMPRA";
					var url= "DTE.php";
					$("#C01").html('0');
					document.getElementById("C02").style.display = 'inline';
					
					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp){
							$("#C01").html(resp.dato4);
							$("#C03").html(resp.dato1);
							// logCompraX=(JSON.stringify(resp.XML, null, 2));

							if(logCompraX != "\"NoXML\""){
								$("#logCompra").val(logCompraX);
								document.getElementById("btnLogCompra").style.display = 'inline';
							}

							if(resp.dato1=="S\/IServer error: `POST http:\/\/200.73.113.41:8000\/api\/sync_sii` resulted in a `500 Internal Server Error` response:\nInternal Server Error\n"){
								$("#C03").html("API de Compra y Venta del SII no disponible intente más tarde.");
							}else{
								if(resp.dato1=="S/I"){
									$("#C03").html("NO SE ENCONTRARON COMPRAS PARA SINCRONIZAR.");
								}else{
									$("#Visor").html(resp.dato2);
								}
							}

							document.getElementById("C03").style.display = 'inline';
							document.getElementById("C02").style.display = 'none';

							resolve();
						},
						error: function(error) {
							reject(error);
						}

					});	
				});
			}

			function ProcesarV(){
				return new Promise((resolve, reject) => {
					form1.SWOperacion.value="VENTA";
					var url= "DTE.php";
					$("#V01").html('0');
					document.getElementById("V02").style.display = 'inline';

					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp1){
							$("#V01").html(resp1.dato4);
							$("#V03").html(resp1.dato1);
							// logVentaX=(JSON.stringify(resp1.XML, null, 2));

							if(logVentaX != "\"NoXML\""){
								$("#logVenta").val(logVentaX);
								document.getElementById("btnLogVenta").style.display = 'inline';
							}

							if(resp1.dato1=="S\/IServer error: `POST http:\/\/200.73.113.41:8000\/api\/sync_sii` resulted in a `500 Internal Server Error` response:\nInternal Server Error\n"){
								$("#V03").html("API de Compra y Venta del SII no disponible intente más tarde.");
							}else{
								if(resp1.dato1=="S/I"){
									$("#V03").html("NO SE ENCONTRARON VENTAS PARA SINCRONIZAR.");
								}else{
									$("#Visor").html(resp1.dato2);
								}
							}

							document.getElementById("V03").style.display = 'inline';
							document.getElementById("V02").style.display = 'none';

							resolve();
						},
						error: function(error) {
							reject(error);
						}

					});	
				});
			}

			function ProcesarH(){
				return new Promise((resolve, reject) => {
					var url= "DTEHonorarioRecibidas.php";
					$("#H01").html('0');
					document.getElementById("H02").style.display = 'inline';

					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp2){
							$("#H01").html(resp2.dato4);
							$("#H03").html(resp2.dato1);
							document.getElementById("H03").style.display = 'inline';
							document.getElementById("H02").style.display = 'none';
							resolve();
						},
						error: function(error) {
							reject(error);
						}							
					});	
				});
			}

			function ProcesarHT(){
				return new Promise((resolve, reject) => {
					var url= "DTEHonorarioTerceros.php";
					$("#HT01").html('0');
					document.getElementById("HT02").style.display = 'inline';

					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp3){
							$("#HT01").html(resp3.dato4);
							$("#HT03").html(resp3.dato1);
							document.getElementById("HT03").style.display = 'inline';
							document.getElementById("HT02").style.display = 'none';
							resolve();
						},
						error: function(error) {
							reject(error);
						}
					});	
				});
			}

			function CsvData(r1){
				form1.swData.value=r1;
				form1.method="POST";
				form1.target="_blank";
				form1.action="data.php";
				form1.submit();
				form1.target="";
				form1.action="#";	
			}

		</script>
		<style>
			.checkbox, .radio {
				margin-top: 1px; 
				margin-bottom: 1px;
			}			
		</style>
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
				<?php
					if($SwBaja=="SI"){
						echo '
							<div class="col-sm-12 text-center" style="font-size: 16px;">
								<br><br><br><br>
								SII esta presentando problema con el Sincronizador, lo que está provocando que nuestros sistemas se han ralentizado, por el momento hemos decidido desactivar.<br> 
								Cuando detectemos que se estabilizo el SII, volveremos a activar.<br>
								Disculpe las molestias.<br><br>
							</div>

							<div class="col-md-12 text-center">
								<a href="https://youtu.be/cqquKBsGa9Q" style="font-size: 30px;" class="btn btn-success" target="_blank" role="button">Tutorial Importación mediante Archivo</a>
							</div>
			</div>
		</form>
		</div>
		</div>

						';
						include '../footer.php';
						exit;
					}
				?>

				<div class="col-md-1"></div>
				<div class="col-md-10">

					<input type="hidden" name="SWOperacion" id="SWOperacion" value="">
					<input type="hidden" name="swData" id="swData">
					<div class="col-md-4 text-right">
						<div class="input-group">
							<span class="input-group-addon">Mes</span>
							<select class="form-control" id="messelect" name="messelect" required>
							<?php 
								$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
								$i=1;
								$dmes=$dmes*1;
								while($i<=12){

									if ($i==$dmes) {
										echo "<option value ='".$i."' selected>".$Meses[($i-1)]."</option>";
									}else{
										echo "<option value ='".$i."'>".$Meses[($i-1)]."</option>";
									}
									$i++;
								}
							?>
							</select>
						</div>
					</div>

					<div class="col-md-4">
						<div class="input-group">
							<span class="input-group-addon">A&ntilde;o</span>
							<select class="form-control" id="anoselect" name="anoselect" required>
							<?php 
								$yoano=date('Y');
								$tano="2017";

								while($tano<=($yoano+1)){
									if ($dano==$tano) {
										echo "<option value ='".$tano."' selected>".$tano."</option>";
									}else{
										echo "<option value ='".$tano."'>".$tano."</option>";
									}
									$tano=$tano+1;
								}
							?>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
					<br>

					<div class="col-md-4">
						<div class="input-group">
							<span class="input-group-addon">Clave</span>
							<input class="form-control" type="password" name="CSiiCrip" id="CSiiCrip" value="<?php echo $ValCSII; ?>" required>
						</div>
						<span>* La primera vez que se utilice se grabara de forma automatica</span>
					</div>

					<div class="col-md-2">
						<div class="input-group">
							<button type="button" id="BtrProce" name="BtrProce" onclick="Procesar()" class="btn btn-grabar btn-block">Procesar</button>
						</div>
					</div>

					<div class="col-md-4">
						<div class="input-group">
							<label class="checkbox-inline"><input type="checkbox" name="EmpExt" id="EmpExt" value="">Empresa exenta, los impuestos debe ser considerado como parte del valor neto.</label>
						</div>
					</div>
					<div class="clearfix"></div>
					<br>

					<div class="col-md-4" style="visibility: hidden;">
						<div class="input-group">
						<span class="input-group-addon">Rut</span>
						<input type="text" class="form-control" id="rutsii" name="rutsii" autocomplete="off" maxlength="10" placeholder="Ej: 13520300-5" value="<?php echo $ValRSII; ?>" required>
						</div>
					</div> 

				</div>
				<div class="clearfix"></div>
				<br>
				<div class="col-md-2"></div>
				<div class="col-md-8 text-center" style="font-size: 16px;">
					<?php
						if($Msj=="SI"){
						}else{
							echo $Msj;
						}
					?>
				</div>

				<div class="clearfix"></div>
				<br>

				<style>
					.glyphicon-refresh-animate {
						-animation: spin .7s infinite linear;
						-webkit-animation: spin2 .7s infinite linear;
					}

					@-webkit-keyframes spin2 {
						from { -webkit-transform: rotate(0deg);}
						to { -webkit-transform: rotate(360deg);}
					}

					@keyframes spin {
						from { transform: scale(1) rotate(0deg);}
						to { transform: scale(1) rotate(360deg);}
					}								
				</style>

				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading" style="text-align: center;">
							<div class="checkbox">
								<label><input type="checkbox" value="" id="CkCompra" checked>Compras</label>
							</div>				
						</div>
						<div class="panel-body" style="text-align: center;">
							<l id="C01" style="font-size: 30px;">0</l>
							<br>
							<l id="C02" class="btn btn-xs btn-warning" style="display: none;">
								<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Procesando...
							</l>
							<l id="C03"></l>
							<button type="button" class="btn btn-mastecno btn-xs" onclick="CsvData('C')" style="display: none;" id="btnLogCompra">Log XML</button>
						</div>
					</div>					
				</div>

				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading" style="text-align: center;">
							<div class="checkbox">
								<label><input type="checkbox" value="" id="CkVenta" checked>Ventas</label>
							</div>				
						</div>
						<div class="panel-body" style="text-align: center;">
							<l id="V01" style="font-size: 30px;">0</l>
							<br>
							<l id="V02" class="btn btn-xs btn-warning" style="display: none;">
								<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Procesando...
							</l>
							<l id="V03"></l>
							<button type="button" class="btn btn-mastecno btn-xs" onclick="CsvData('V')" style="display: none;" id="btnLogVenta">Log XML</button>
						</div>
					</div>					
				</div>

				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading" style="text-align: center;">
							<div class="checkbox">
								<label><input type="checkbox" value="" id="CkHonorario" checked>Honorarios</label>
							</div>				
						</div>
						<div class="panel-body" style="text-align: center;">
							<l id="H01" style="font-size: 30px;">0</l>
							<br>
							<l id="H02" class="btn btn-xs btn-warning" style="display: none;">
								<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Procesando...
							</l>
							<l id="H03"></l>
						</div>
					</div>					
				</div>

				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading" style="text-align: center;">
							<div class="checkbox">
								<label><input type="checkbox" value="" id="CkTercero" checked>Terceros</label>
							</div>				
						</div>
						<div class="panel-body" style="text-align: center;">
							<l id="HT01" style="font-size: 30px;">0</l>
							<br>
							<l id="HT02" class="btn btn-xs btn-warning" style="display: none;">
								<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Procesando...
							</l>
							<l id="HT03"></l>
						</div>
					</div>					
				</div>

				<div class="col-md-12">
					<p id="Visor"></p>

						<div class="form-group">
							<textarea class="form-control" style="display: none;" rows="50" id="logCompra" name="logCompra"><?php echo $_POST['logCompra']; ?></textarea>
						</div>

						<div class="form-group">
							<textarea class="form-control" style="display: none;" rows="50" id="logVenta" name="logVenta"><?php echo $_POST['logVenta']; ?></textarea>
						</div>

					<br>
					<div class="col-md-1"></div>
					<div class="col-md-4" style="text-align: justify;">
						<strong>Importante</strong><br>
						<br>
						El SII sigue realizando modificación, ahora no se puede descargar de forma directa los tipos de documentos que superen los 900 registros aprox., y está entregando un resumen comprimido que debe ser descargado de forma directa, y procesado con el importador destinado para ello. Si necesita ayuda con este procedimiento, no dude contactar a su ejecutivo.<br><br>
						Si cuentas con menos de la cantidad indicada por tipo de documentos, no presentarás problemas en las descargas, pero siempre debe estar atento.<br><br>
						Saludos.<br>
					</div>
					<div class="col-md-1"></div>
					<div class="col-md-4">
						<strong>Nota:</strong> "El proceso de sincronización de honorarios recibidos y de terceros puede tomar un tiempo más prolongado. En caso que eso suceda, se sugiere sincronizar de manera independiente cada tipo de documento".
					</div>
				</div>
				<div class="clearfix"></div>
				<br>
			
				<div class="col-md-12 text-center">
					<div class="input-group">
						<div class="alert alert-warning" id="Mensa" style="background-color: #fbc7c7; border-color: #b35c5c; display:none;">
							<strong>Generando!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
						</div>					
					</div>
				</div>
				<div class="clearfix"></div>
				<br>

				<!-- <div class="col-md-12 text-center">
					<a href="https://youtu.be/cqquKBsGa9Q" target="_blank" style="font-size: 25px; color: white; background-color: red; border-style: inset;">Tutorial Importación mediante Archivo</a>
				</div> -->

			</div>
		</form>

		<script>
			// alert("Servicio temporalmente no disponible, si necesita realizar el proceso de importación, dejaremos un tutorial para realizar este proceso.");
			
			// alert("El SII sigue presentando problemas e inestabilidad en sus servicios de API (WebService). Por lo cual puede presentar, que algunos tipos de documentos no sincronicen de forma adecuada.\n\nSeguimos monitoreando para entregar una pronta solución");
		</script>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


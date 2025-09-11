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
		<script src="../js/jquery.min.js"></script>
		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>
		
		
		<script type="text/javascript">



			function Procesar() {

				// var anoc=document.getElementById("anoselect").value;

				document.getElementById("BtrProce").style.display = 'none';


				r=form1.anoselect.value;

				// alert(form1.anoselect.value);
				if(r<"2020" && document.getElementById("CkTerceroSII").checked==true){
					alert("La descarga de documentos de emitidos a terceros, solo esta disponible desde el 01 de Enero 2020, por este metodo de descarga.");
					document.getElementById("CkTerceroSII").checked=false;
					document.getElementById("CkTerceroSII").disabled=true;
				}
				
				if(r<"2020" && document.getElementById("CkTerceroBTE").checked==true){
					alert("La descarga de documentos de emitidos a terceros, solo esta disponible desde el 01 de Enero 2020, por este metodo de descarga.");
					document.getElementById("CkTerceroBTE").checked=false;
					document.getElementById("CkTerceroBTE").disabled=true;
				}
				
				// return;

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
				if (document.getElementById("CkTerceroSII").checked) {
					promesa = promesa.then(() => ProcesarHTSII());
				}
				if (document.getElementById("CkTerceroBTE").checked) {
					promesa = promesa.then(() => ProcesarHTBTE());
				}

				
				promesa.then(() => {
					setTimeout(() => {
						alert("Descarga de documentos completada");
						document.getElementById("BtrProce").style.display = 'inline';
					}, 500);
					
				}).catch((error) => {
					// console.error("Error en el proceso:", error);
					alert("Error en el proceso de descarga");
					document.getElementById("BtrProce").style.display = 'inline';
				});
			}

			function ProcesarC(){
				return new Promise((resolve, reject) => {
					document.getElementById("C03").classList.add('hidden');
					document.getElementById("V03").classList.add('hidden');
					document.getElementById("H03").classList.add('hidden');
					document.getElementById("HT03").classList.add('hidden');

					form1.SWOperacion.value="COMPRA";
					var url= "DTE.php";
					$("#C01").html('0');
					document.getElementById("C02").classList.remove('hidden');
					
					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp){
							$("#C01").html(resp.dato4);
							$("#C03").html(resp.dato1);
							logCompraX=(JSON.stringify(resp.XML, null, 2));

							if(logCompraX != "\"NoXML\""){
								$("#logCompra").val(logCompraX);
								document.getElementById("btnLogCompra").classList.remove('hidden');
							}

							if(resp.dato1=="S\/IServer error: `POST http:\/\/200.73.113.41:8000\/api\/sync_sii` resulted in a `500 Internal Server Error` response:\nInternal Server Error\n"){
								$("#C03").html("API de Compra y Venta del SII no disponible intente más tarde.");
							}else{
								if(resp.dato1=="S/I"){
									$("#C03").html("NO SE ENCONTRARON COMPRAS PARA SINCRONIZAR.");
									$("#C01").html("0");
								}else{
									$("#Visor").html(resp.dato2);
								}
							}

							document.getElementById("C03").classList.remove('hidden');
							document.getElementById("C02").classList.add('hidden');

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
					document.getElementById("V02").classList.remove('hidden');

					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp1){
							$("#V01").html(resp1.dato4);
							$("#V03").html(resp1.dato1);
							logVentaX=(JSON.stringify(resp1.XML, null, 2));

							if(logVentaX != "\"NoXML\""){
								$("#logVenta").val(logVentaX);
								document.getElementById("btnLogVenta").classList.remove('hidden');
							}

							if(resp1.dato1=="S\/IServer error: `POST http:\/\/200.73.113.41:8000\/api\/sync_sii` resulted in a `500 Internal Server Error` response:\nInternal Server Error\n"){
								$("#V03").html("API de Compra y Venta del SII no disponible intente más tarde.");
							}else{
								if(resp1.dato1=="S/I"){
									$("#V03").html("NO SE ENCONTRARON VENTAS PARA SINCRONIZAR.");
									$("#V01").html("0");
								}else{
									$("#Visor").html(resp1.dato2);
								}
							}

							document.getElementById("V03").classList.remove('hidden');
							document.getElementById("V02").classList.add('hidden');

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
					document.getElementById("H02").classList.remove('hidden');

					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp2){
							$("#H01").html(resp2.dato4);
							$("#H03").html(resp2.dato1);
							document.getElementById("H03").classList.remove('hidden');
							document.getElementById("H02").classList.add('hidden');
							resolve();
						},
						error: function(error) {
							reject(error);
						}							
					});	
				});
			}

			function ProcesarHTSII(){
				return new Promise((resolve, reject) => {
					var url= "DTEHonorarioTerceros.php";
					$("#HT01").html('0');
					document.getElementById("HT02").classList.remove('hidden');

					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp3){
							$("#HT01").html(resp3.dato4);
							$("#HT03").html(resp3.dato1);
							document.getElementById("HT03").classList.remove('hidden');
							document.getElementById("HT02").classList.add('hidden');
							resolve();
						},
						error: function(error) {
							reject(error);
						}
					});	
				});
			}

			function ProcesarHTBTE(){
				return new Promise((resolve, reject) => {
					var url= "DTEHonorarioTercerosBTE.php";
					$("#HTB01").html('0');
					document.getElementById("HTB02").classList.remove('hidden');

					$.ajax({
						type: "POST",
						url: url,
						dataType: 'json',
						data: $('#form1').serialize(),
						success:function(resp3){
							$("#HTB01").html(resp3.dato4);
							$("#HTB03").html(resp3.dato1);
							document.getElementById("HTB03").classList.remove('hidden');
							document.getElementById("HTB02").classList.add('hidden');
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
	<body class="bg-gray-50">
		<?php 
			include '../nav.php';
		?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<form action="#" name="form1" id="form1" method="POST">
			<div class="space-y-8">
				<?php
					if($SwBaja=="SI"){
						echo '
							<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
								<div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
									<i class="fas fa-exclamation-triangle text-2xl text-yellow-600"></i>
								</div>
								<h2 class="text-xl font-semibold text-gray-800 mb-4">Servicio Temporalmente Desactivado</h2>
								<p class="text-gray-600 mb-6">
									SII está presentando problemas con el Sincronizador, lo que está provocando que nuestros sistemas se han ralentizado, por el momento hemos decidido desactivar.<br><br>
									Cuando detectemos que se estabilizó el SII, volveremos a activar.<br><br>
									Disculpe las molestias.
								</p>
								<a href="https://youtu.be/cqquKBsGa9Q" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" target="_blank">
									<i class="fas fa-play mr-2"></i>
									Tutorial Importación mediante Archivo
								</a>
							</div>
						';
						include '../footer.php';
						exit;
					}
				?>

				<!-- Formulario Principal -->
				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-4 pb-4 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fas fa-sync-alt text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Sincronización SII
							</h3>
							<p class="text-sm text-gray-600">Configuración de parámetros para la sincronización</p>
						</div>
					</div>

					<div class="p-6 space-y-6">
						<input type="hidden" name="SWOperacion" id="SWOperacion" value="">
						<input type="hidden" name="swData" id="swData">

						<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
							<div>
								<label for="messelect" class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="messelect" name="messelect" required>
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

							<div>
								<label for="anoselect" class="block text-sm font-medium text-gray-700 mb-2">Año</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="anoselect" name="anoselect" required>
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

							<div>
								<label for="CSiiCrip" class="block text-sm font-medium text-gray-700 mb-2">Clave SII</label>
								<input class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="password" name="CSiiCrip" id="CSiiCrip" value="<?php echo $ValCSII; ?>" required>
								<p class="text-xs text-gray-500 mt-1">* La primera vez que se utilice se grabará de forma automática</p>
							</div>
						</div>

						<div class="flex flex-wrap items-center gap-4">
							<button type="button" id="BtrProce" name="BtrProce" onclick="Procesar()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
								<i class="fas fa-play mr-2"></i>
								Procesar
							</button>

							<label class="flex items-center space-x-2 cursor-pointer">
								<input type="checkbox" name="EmpExt" id="EmpExt" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
								<span class="text-sm text-gray-700">Empresa exenta, los impuestos debe ser considerado como parte del valor neto</span>
							</label>
						</div>

						<div class="hidden">
							<input type="text" class="form-control" id="rutsii" name="rutsii" autocomplete="off" maxlength="10" placeholder="Ej: 13520300-5" value="<?php echo $ValRSII; ?>" required>
						</div>
					</div>
				</div>
				<!-- Mensaje de estado -->
				<?php if($Msj!="SI" && $Msj!=""): ?>
				<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
					<p class="text-blue-800"><?php echo $Msj; ?></p>
				</div>
				<?php endif; ?>

				<style>
					.glyphicon-refresh-animate {
						animation: spin .7s infinite linear;
						-webkit-animation: spin2 .7s infinite linear;
					}

					@keyframes spin {
						from { transform: scale(1) rotate(0deg);}
						to { transform: scale(1) rotate(360deg);}
					}

					@-webkit-keyframes spin2 {
						from { -webkit-transform: rotate(0deg);}
						to { -webkit-transform: rotate(360deg);}
					}								
				</style>

				<!-- Paneles de Documentos -->
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					<!-- Documentos de Compra -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
						<div class="p-4 border-b border-gray-200">
							<div class="flex items-center justify-between">
								<div class="flex items-center">
									<input type="checkbox" value="" id="CkCompra" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
									<label for="CkCompra" class="ml-2 text-sm font-medium text-gray-700">Documentos de Compra</label>
								</div>
								<div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
									<i class="fas fa-shopping-cart text-green-600 text-sm"></i>
								</div>
							</div>
						</div>
						<div class="p-6 text-center">
							<div id="C01" class="text-4xl font-bold text-gray-800 mb-2">0</div>
							<div id="C02" class="hidden">
								<div class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
									<i class="fas fa-spinner fa-spin mr-2"></i>
									Procesando...
								</div>
							</div>
							<div id="C03" class="text-sm text-gray-600 mt-2"></div>
							<button type="button" class="hidden mt-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-1 px-3 rounded transition duration-200" onclick="CsvData('C')" id="btnLogCompra">
								<i class="fas fa-file-code mr-1"></i>
								Log XML
							</button>
						</div>
					</div>

					<!-- Documentos de Venta -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
						<div class="p-4 border-b border-gray-200">
							<div class="flex items-center justify-between">
								<div class="flex items-center">
									<input type="checkbox" value="" id="CkVenta" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
									<label for="CkVenta" class="ml-2 text-sm font-medium text-gray-700">Documentos de Venta</label>
								</div>
								<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
									<i class="fas fa-chart-line text-blue-600 text-sm"></i>
								</div>
							</div>
						</div>
						<div class="p-6 text-center">
							<div id="V01" class="text-4xl font-bold text-gray-800 mb-2">0</div>
							<div id="V02" class="hidden">
								<div class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
									<i class="fas fa-spinner fa-spin mr-2"></i>
									Procesando...
								</div>
							</div>
							<div id="V03" class="text-sm text-gray-600 mt-2"></div>
							<button type="button" class="hidden mt-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium py-1 px-3 rounded transition duration-200" onclick="CsvData('V')" id="btnLogVenta">
								<i class="fas fa-file-code mr-1"></i>
								Log XML
							</button>
						</div>
					</div>

					<!-- Honorarios Recibidos -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
						<div class="p-4 border-b border-gray-200">
							<div class="flex items-center justify-between">
								<div class="flex items-center">
									<input type="checkbox" value="" id="CkHonorario" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
									<label for="CkHonorario" class="ml-2 text-sm font-medium text-gray-700">Honorarios Recibidos</label>
								</div>
								<div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
									<i class="fas fa-user-tie text-purple-600 text-sm"></i>
								</div>
							</div>
						</div>
						<div class="p-6 text-center">
							<div id="H01" class="text-4xl font-bold text-gray-800 mb-2">0</div>
							<div id="H02" class="hidden">
								<div class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
									<i class="fas fa-spinner fa-spin mr-2"></i>
									Procesando...
								</div>
							</div>
							<div id="H03" class="text-sm text-gray-600 mt-2"></div>
						</div>
					</div>

					<!-- Emitidos a Terceros SII -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
						<div class="p-4 border-b border-gray-200">
							<div class="flex items-center justify-between">
								<div class="flex items-center">
									<input type="checkbox" value="" id="CkTerceroSII" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
									<label for="CkTerceroSII" class="ml-2 text-sm font-medium text-gray-700">Emitidos a Terceros SII</label>
								</div>
								<div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
									<i class="fas fa-file-invoice text-orange-600 text-sm"></i>
								</div>
							</div>
						</div>
						<div class="p-6 text-center">
							<div id="HT01" class="text-4xl font-bold text-gray-800 mb-2">0</div>
							<div id="HT02" class="hidden">
								<div class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
									<i class="fas fa-spinner fa-spin mr-2"></i>
									Procesando...
								</div>
							</div>
							<div id="HT03" class="text-sm text-gray-600 mt-2"></div>
						</div>
					</div>

					<!-- Emitidos a Terceros BTE's -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
						<div class="p-4 border-b border-gray-200">
							<div class="flex items-center justify-between">
								<div class="flex items-center">
									<input type="checkbox" value="" id="CkTerceroBTE" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
									<label for="CkTerceroBTE" class="ml-2 text-sm font-medium text-gray-700">Emitidos a Terceros BTE's</label>
								</div>
								<div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
									<i class="fas fa-file-alt text-red-600 text-sm"></i>
								</div>
							</div>
						</div>
						<div class="p-6 text-center">
							<div id="HTB01" class="text-4xl font-bold text-gray-800 mb-2">0</div>
							<div id="HTB02" class="hidden">
								<div class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
									<i class="fas fa-spinner fa-spin mr-2"></i>
									Procesando...
								</div>
							</div>
							<div id="HTB03" class="text-sm text-gray-600 mt-2"></div>
						</div>
					</div>
				</div>

				<!-- Visor de resultados -->
				<div id="Visor" class="mt-6"></div>

				<!-- Textareas ocultas para logs -->
				<div class="hidden">
					<textarea class="form-control" rows="50" id="logCompra" name="logCompra"><?php echo $_POST['logCompra']; ?></textarea>
					<textarea class="form-control" rows="50" id="logVenta" name="logVenta"><?php echo $_POST['logVenta']; ?></textarea>
				</div>

				<!-- Información Importante -->
				<div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mt-3">
					<div class="flex items-start">
						<div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
							<i class="fas fa-exclamation-triangle text-amber-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-amber-800 mb-4">Información Importante</h3>
							<div class="space-y-3 text-sm text-amber-700">
								<div class="flex items-start">
									<span class="font-semibold mr-2">•</span>
									<span><strong>Los procesos de Boleta de Honorarios a Terceros y BTE's</strong> están en proceso de validación, por lo cual puede presentar intermitencia en la sincronización.</span>
								</div>
								<div class="flex items-start">
									<span class="font-semibold mr-2">•</span>
									<span>El SII restringe descarga de documentos con más de 900 registros, por lo que se entrega un resumen comprimido que debe ser descargado de forma directa, y procesado con el importador destinado para ello.</span>
								</div>
								<div class="flex items-start">
									<span class="font-semibold mr-2">•</span>
									<span>Si cuentas con menos de la cantidad indicada por tipo de documentos, no presentarás problemas en las descargas, pero siempre debe estar atento.</span>
								</div>
								<div class="flex items-start">
									<span class="font-semibold mr-2">•</span>
									<span>La descarga de documentos de honorarios y terceros puede tomar un tiempo más prolongado, en caso que eso suceda, se sugiere sincronizar de manera independiente cada tipo de documento.</span>
								</div>
								<div class="flex items-start">
									<span class="font-semibold mr-2">•</span>
									<span>La descarga de documentos de terceros están disponibles desde el 01 de Enero 2020, en adelante.</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			
				<!-- Alerta de proceso -->
				<div id="Mensa" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 text-center">
					<div class="flex items-center justify-center">
						<i class="fas fa-spinner fa-spin text-red-600 mr-3"></i>
						<span class="text-red-800 font-medium">Generando! El proceso tomará un tiempo, dependiendo de la cantidad de registros.</span>
					</div>
				</div>

			</div>
		</form>

		<script>
			// alert("Servicio temporalmente no disponible, si necesita realizar el proceso de importación, dejaremos un tutorial para realizar este proceso.");
			
			// alert("El SII sigue presentando problemas e inestabilidad en sus servicios de API (WebService). Por lo cual puede presentar, que algunos tipos de documentos no sincronicen de forma adecuada.\n\nSeguimos monitoreando para entregar una pronta solución");
		</script>

		</div>
		</div>

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>

</html>


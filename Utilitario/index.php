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
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<script src="../js/jquery.min.js"></script>

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>
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

							document.getElementById("BtrProce").style.display = 'block';
							document.getElementById("Mensa1").style.display = 'none';

							if (resp=="exito") {
								document.getElementById("Mensa3").style.display = 'block';
								resp="&Eacute;xito en el proceso, no se registraron errores, Saludos.";
								$('#Msjexito').html(resp);
							}else{
								document.getElementById("Mensa2").style.display = 'block';
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

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

			<div class="space-y-8">
			<form action="#" name="form1" id="form1" method="POST">
				<input type="hidden" name="Sw1" id="Sw1" value="0">
				<input type="hidden" name="Sw2" id="Sw2" value="0">
				<input type="hidden" name="Sw3" id="Sw3" value="0">

				<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-cogs text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Proceso de Refolio
							</h3>
							<p class="text-sm text-gray-600">Configuraci&oacute;n y ejecuci&oacute;n del proceso</p>
						</div>
					</div> 
					
					<div class="p-6 pt-1 space-y-6">
						<div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-3">
							<div>
								<label class="block text-sm font-medium text-gray-700 mb-2">A&ntilde;o que se ejecutara el proceso</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100" id="PApertura" name="PApertura" readonly value="<?php echo substr($Periodo, 3, 4);; ?>">
							</div>
						</div>

						<div class="space-y-4">
							<h4 class="text-md font-semibold text-gray-800 mb-3">Condiciones del Proceso</h4>
							
							<div class="space-y-3">
								<label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150 ease-in-out cursor-pointer">
									<input type="checkbox" onclick="Proce('1')" id="Acep1" name="Acep1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
									<span class="text-sm text-gray-700">1. He realizado la marca del asiento de apertura.</span>
								</label> 
								
								<label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150 ease-in-out cursor-pointer">
									<input type="checkbox" onclick="Proce('2')" id="Acep2" name="Acep2" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
									<span class="text-sm text-gray-700">2. No se est&aacute;n ejecutado o registrando procesos en esta empresa.</span>
								</label> 
								
								<label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150 ease-in-out cursor-pointer">
									<input type="checkbox" onclick="Proce('3')" id="Acep3" name="Acep3" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
									<span class="text-sm text-gray-700">3. Esperare que este proceso termine solo, ya que si realizo una operaci&oacute;n puedo descuadrar los registros de la empresa.</span>
								</label> 
							</div>
						</div>

						<div class="flex flex-wrap justify-start items-center gap-2 rounded-md p-2 mb-5">
							<button type="button" onclick="vali()" id="BtrProce" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
								<i class="fa fa-play mr-2"></i> Procesar
							</button>
						</div>

						<div class="space-y-4">
							<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4" id="Mensa1" style="display:none;">
								<div class="flex">
									<div class="flex-shrink-0">
										<i class="fa fa-exclamation-triangle text-yellow-400"></i>
									</div>
									<div class="ml-3">
										<h3 class="text-sm font-medium text-yellow-800">
											Importante!
										</h3>
										<div class="mt-2 text-sm text-yellow-700">
											<p>El proceso tomara un tiempo, dependiendo de la cantidad de registro.</p>
										</div>
									</div>
								</div>
							</div>					

							<div class="bg-red-50 border border-red-200 rounded-lg p-4" id="Mensa2" style="display:none;">
								<div class="flex">
									<div class="flex-shrink-0">
										<i class="fa fa-exclamation-circle text-red-400"></i>
									</div>
									<div class="ml-3">
										<h3 class="text-sm font-medium text-red-800">
											Error!
										</h3>
										<div class="mt-2 text-sm text-red-700">
											<p id="Msjerror"></p>
										</div>
									</div>
								</div>
							</div>

							<div class="bg-green-50 border border-green-200 rounded-lg p-4" id="Mensa3" style="display:none;">
								<div class="flex">
									<div class="flex-shrink-0">
										<i class="fa fa-check-circle text-green-400"></i>
									</div>
									<div class="ml-3">
										<h3 class="text-sm font-medium text-green-800">
											&Eacute;xito!
										</h3>
										<div class="mt-2 text-sm text-green-700">
											<p id="Msjexito"></p>
										</div>
									</div>
								</div>
							</div>					
						</div>
					</div>
				</div>
			</form>
			</div>
		</div>
		</div>

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>

</html>


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
		<script src="js/jquery.min.js"></script>

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<script src="js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type='text/javascript' src="js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/select2.css">
	</head>

	<body>

	<?php include 'nav.php'; ?>

	<div class="min-h-screen bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

		<div class="space-y-8">
		<form action="frmFolioSIIPDF.php" method="POST" target="_blank" name="form1" id="form1">

			<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
				<button type="submit" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
					<i class="fa fa-file-pdf-o mr-2"></i> Generar PDF
				</button>

				<button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="history.back()">
					<i class="fa fa-times mr-2"></i> Cancelar
				</button> 
			</div>

			<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
				<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
					<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
						<i class="fa-solid fa-file-lines text-lg text-blue-600"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-800">
							Foliador de Hojas
						</h3>
						<p class="text-sm text-gray-600">Configuración para generar folios numerados</p>
					</div>
				</div> 
					
				<div class="p-6 pt-1 space-y-6">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">

						<div>
							<label for="finicial" class="block text-sm font-medium text-gray-700 mb-2">Folio Inicial</label>
							<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="finicial" name="finicial" placeholder="Inicio de Folio 36" required>
						</div>

						<div>
							<label for="nhojas" class="block text-sm font-medium text-gray-700 mb-2">N&uacute;mero de Hojas</label>
							<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="nhojas" name="nhojas" placeholder="N&uacute;mero de Hojas 10, terminando en 45" required>
						</div>

						<div>
							<label for="horinta" class="block text-sm font-medium text-gray-700 mb-2">Orientaci&oacute;n</label>
							<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="horinta" name="horinta" required>
								<option value="P">Vertical</option>
								<option value="L">Horizontal</option>
							</select>
						</div>

						<div>
							<label for="Membre" class="block text-sm font-medium text-gray-700 mb-2">Membrete</label>
							<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="Membre" name="Membre" required>
								<option value="N">NO</option>
								<option value="S">SI Completo</option>
								<option value="F">SI, Sin Representante</option>
							</select>
						</div>

					</div>
				</div>
			</div>

		</form>
	</div>
	</div>
	</div>

	<div class="clearfix"> </div>

	<?php include 'footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
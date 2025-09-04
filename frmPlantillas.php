<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
?>
<!DOCTYPE html>
<html>
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
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="min-h-screen bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

		<div class="space-y-8">
		<form action="" method="POST" name="form1" id="form1">
			
			<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
				<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
					<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
						<i class="fa-solid fa-file-invoice text-lg text-blue-600"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-800">
							Libro de Compra/Venta
						</h3>
						<p class="text-sm text-gray-600">Gesti&oacute;n de plantillas contables</p>
					</div>
				</div> 
					
				<div class="p-6 pt-1 space-y-6">
					<div class="flex flex-col items-center justify-center py-8">
						<div class="text-center">
							<div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
								<i class="fa-solid fa-file-invoice text-2xl text-blue-600"></i>
							</div>
							<h4 class="text-lg font-medium text-gray-900 mb-2">Plantillas de Libro de Compra/Venta</h4>
							<p class="text-gray-600 mb-6">Accede a las plantillas para gestionar el libro de compras y ventas</p>
							<a href="frmPLCompraVenta.php" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
								<i class="fa-solid fa-plus mr-2"></i>
								Ingresar Plantilla
							</a>
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
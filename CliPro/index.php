<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
	
	if($RutEmpresa==""){
		header('Location: frmMain.php');
		exit;
	}

	$sw=0;

	if(isset($_POST['idemp']) && $_POST['idemp']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTCliPro WHERE id='".$_POST['idemp']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$rut=$registro["rut"];
			$razonsocial=$registro["razonsocial"];
			$direccion=$registro["direccion"];
			$giro=$registro["giro"];
			$ciudad=$registro["ciudad"];
			$correo=$registro["correo"];
			$pinicio=$registro["periodo"];
			$cuenta=$registro["cuenta"];
		}

		if ($cuenta==0) {
			$SQL1="SELECT * FROM CTCliProCuenta WHERE rut='".$rut."' AND rutempresa='$RutEmpresa'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$cuenta=$registro1['cuenta'];
			}			
		}

		// $mysqli->close();
	}



	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// $SQL="SELECT * FROM CTCliPro WHERE tipo='P' ORDER BY id ASC;";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {

	// 	$PRut=$registro['rut'];

	// 	$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P'";
	// 	$Resul = $mysqli->query($SQL1);
	// 	$row_cnt = $Resul->num_rows;
	// 	if($row_cnt>1){
	// 		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P' ORDER BY id ASC LIMIT 1;";
	// 		$resultados1 = $mysqli->query($SQL1);
	// 		while ($registro1 = $resultados1->fetch_assoc()) {
	// 			$IdReg=$registro1['id'];
	// 		}			
	// 		$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='P'");
	// 		// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
	// 	}

	// }

	// $SQL="SELECT * FROM CTCliPro WHERE tipo='C' ORDER BY id ASC;";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {

	// 	$PRut=$registro['rut'];

	// 	$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C'";
	// 	$Resul = $mysqli->query($SQL1);
	// 	$row_cnt = $Resul->num_rows;
	// 	if($row_cnt>1){
	// 		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C' ORDER BY id ASC LIMIT 1;";
	// 		$resultados1 = $mysqli->query($SQL1);
	// 		while ($registro1 = $resultados1->fetch_assoc()) {
	// 			$IdReg=$registro1['id'];
	// 		}			
	// 		$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='C'");
	// 		// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
	// 	}
	// }

	// $SQL="SELECT * FROM CTCliPro WHERE tipo='2' ORDER BY id ASC;";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {

	// 	$PRut=$registro['rut'];

	// 	$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2'";
	// 	$Resul = $mysqli->query($SQL1);
	// 	$row_cnt = $Resul->num_rows;
	// 	if($row_cnt>1){
	// 		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2' ORDER BY id ASC LIMIT 1;";
	// 		$resultados1 = $mysqli->query($SQL1);
	// 		while ($registro1 = $resultados1->fetch_assoc()) {
	// 			$IdReg=$registro1['id'];
	// 		}			
	// 		$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='2'");
	// 		// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
	// 	}
	// }


	// Determinar el título según el tipo de formulario
	if ($_GET['nomfrm']=="C") {
		$titulo="Cliente";
		$icono="fa-user";
		$color="blue";
	}else{
		$titulo="Proveedor";
		$icono="fa-truck";
		$color="green";
	}
	
	$MsjEmpresa="Selecciona un ".strtolower($titulo)." de la lista";
?>
<!DOCTYPE html>
<html lang="es"> 
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<script src="../js/jquery.min.js"></script>

		<!-- Tailwind CSS -->
		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<script src="../js/jquery.Rut.js" type="text/javascript"></script>
		<script src="../js/jquery.validate.js" type="text/javascript"></script>	

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">
			function OrdeCli(){
				if(form1.orden.value==1){
					form1.orden.value=0;
				}else{
					form1.orden.value=1;
				}
				form1.action="";
				form1.submit();
			}

			function Volver(){
				form1.action="../frmMain.php";
				form1.submit();
			}

			function data(valor){
				console.log('Elemento seleccionado:', valor);
				
				form1.cuenta.value=valor;

				const modal = document.getElementById('searchModal');
				
				const closeButton = document.querySelector('[data-modal-hide="searchModal"]');
				if (closeButton) {
					closeButton.click();
				}

			}

			$(document).ready(function(){
				$('#rut').Rut({ 
					on_error: function(){alert('Rut incorrecto'); $('#rut').val(""); $('#rut').focus();} 
				});
			});

			function GenLibro(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmCliProXLS.php";
				form1.submit();
				form1.target="";
				form1.action="#";
			}


			jQuery(document).ready(function(e) {
				$('#myModal').on('shown.bs.modal', function() {
					$('input[name="BCodigo"]').focus();
				});
			});

		</script>  

	</head>

	<body>
	<?php 
		include '../nav.php';
	?>

		<div class="min-h-screen bg-gray-50">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
				<div class="space-y-8">

				<form method="POST" name="form1" id="form1" class="space-y-8">

					<!-- Barra de botones -->
					<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2">
						<button type="button" 
								class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
								onclick="limpiarFormulario()">
							<i class="fa fa-plus mr-2"></i>Nuevo
						</button>
							
						<button type="submit" id="btnGrabar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-save mr-2"></i>Grabar
						</button>

						<button type="button" hidden id="btnEliminar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-trash mr-2"></i>Eliminar
						</button>

						<button type="button" onclick="GenLibro()" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa-solid fa-file-excel text-gray-600 mr-2"></i>Exportar Excel
						</button>

						<button id="btnBuscar" data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" type="button">
							<i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
						</button>

						<button type="button" id="btnCancelar" 
								class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
								onclick="Volver()">
							<i class="fa fa-times mr-2"></i>Cancelar
						</button>
					</div>
					
					<!-- Información del Cliente/Proveedor -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-<?php echo $color; ?>-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fas <?php echo $icono; ?> text-lg text-<?php echo $color; ?>-600"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-800">
									Antecedentes del <?php echo $titulo; ?>
								</h3>
								<p class="text-sm text-gray-600">Ingresa los parametros del <?php echo strtolower($titulo); ?></p>     
							</div>
						</div>
						
						<div class="p-6 pt-1 space-y-6">

							<!-- Hidden inputs -->
							<input type="hidden" name="idemp" id="idemp" value="<?php echo $_POST['idemp']; ?>">
							<input type="hidden" name="idempb" id="idempb">
							<input type="hidden" name="idempa" id="idempa">
							<input type="hidden" name="ideli" id="ideli">
							<input type="hidden" name="nomfrm" id="nomfrm" value="<?php echo $_GET['nomfrm']; ?>">
							<input type="hidden" name="orden" id="orden" value="<?php if ($_POST['orden']!="") { echo $_POST['orden'];}else{echo "0";}?>">

							<div id="divAlertas"></div>

							<!-- Primera fila: RUT y Razón Social -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<div>
									<label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-id-card mr-1"></i>RUT
									</label>
									<input type="text" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="rut" 
										   name="rut" 
										   autocomplete="off" 
										   placeholder="Ej: 13520300-5" 
										   value="<?php echo $rut; ?>" 
										   <?php if($sw==1){ echo 'readonly="false"';} ?>
										   >
								</div>

								<div>
									<label for="rsocial" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-building mr-1"></i>Razón Social
									</label>
									<input type="text" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="rsocial" 
										   name="rsocial" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $razonsocial; ?>" 
										   autocomplete="off">
								</div>
							</div>

							<!-- Segunda fila: Dirección y Giro -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<div>
									<label for="direccion" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-map-marker mr-1"></i>Dirección
									</label>
									<input type="text" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="direccion" 
										   name="direccion" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $direccion; ?>" 
										   autocomplete="off">
								</div>

								<div>
									<label for="giro" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-briefcase mr-1"></i>Giro
									</label>
									<input type="text" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="giro" 
										   name="giro" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $giro; ?>" 
										   autocomplete="off">
								</div>
							</div>

							<!-- Tercera fila: Ciudad, Correo y Cuenta Contable -->
							<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
								<div>
									<label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-city mr-1"></i>Ciudad
									</label>
									<input type="text" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="ciudad" 
										   name="ciudad" 
										   maxlength="50" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $ciudad; ?>" 
										   autocomplete="off">
								</div>

								<div>
									<label for="correo" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-envelope mr-1"></i>Correo
									</label>
									<input type="email" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="correo" 
										   name="correo" 
										   maxlength="50" 
										   value="<?php echo $correo; ?>" 
										   autocomplete="off">
								</div>

								<div>
									<label for="cuenta" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-calculator mr-1"></i>Cuenta Contable
									</label>
									<div class="flex">
										<input type="text" 
											   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" 
											   id="cuenta" 
											   name="cuenta" 
											   value="<?php echo $cuenta; ?>">
										<button type="button" 
												class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" 
												data-modal-target="searchModal" 
												data-modal-toggle="searchModal">
											<i class="fa fa-search text-gray-600"></i>
										</button>
									</div>
								</div>
							</div>

							<!-- Atributos especiales para tipo X -->
							<!-- <?php if ($_GET['nomfrm']=="X"): ?>
							<div class="border-t border-gray-200 pt-6">
								<div class="bg-gray-50 rounded-lg p-4">
									<h4 class="text-sm font-medium text-gray-700 mb-3">
										<i class="fa fa-cog mr-2"></i>Atributos Especiales
									</h4>
									<div class="flex items-center">
										<?php
											$row_cnt=0;
											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											$SQL="SELECT * FROM CTFondoPersonal WHERE Rut='$rut' AND RutEmpresa='$RutEmpresa' AND Estado='A'";
											$resultados = $mysqli->query($SQL);
											$row_cnt = $resultados->num_rows;
											$mysqli->close();

											$SwAFondo="";
											if ($row_cnt>0) {
												$SwAFondo="checked";
											}
										?>
										<label class="flex items-center">
											<input type="checkbox" 
												   name="AFondo" 
												   id="AFondo" 
												   value="" 
												   <?php echo $SwAFondo; ?>
												   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
											<span class="ml-2 text-sm text-gray-700">Asignación de Fondos</span>
										</label>
									</div>
								</div>
							</div>
							<?php endif; ?> -->

						</div>
					</div>

					<!-- Tabla de resultados -->
					<div id="TableCliPro" class="bg-white rounded-lg shadow-sm border border-gray-200">
						<!-- La tabla se carga dinámicamente aquí -->
					</div>

				</form>

				</div>
			</div>
		</div>

		<!-- Modal para buscar código de cuenta - Flowbite -->
		<div id="searchModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
			<div class="relative p-4 w-full max-w-7xl max-h-full">
				<!-- Modal content -->
				<div class="relative bg-white rounded-lg shadow">
					<!-- Modal header -->
					<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
						<h3 class="text-lg font-semibold text-gray-900">
							<i class="fa fa-search mr-2 text-blue-600"></i>Listado de Cuentas
						</h3>
						<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="searchModal">
							<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
							</svg>
							<span class="sr-only">Cerrar modal</span>
						</button>
					</div>
					<!-- Modal body -->
					<div class="p-4 md:p-5 space-y-4">
						<div class="mb-4">
							<input type="text" 
								   id="BCodigo" 
								   name="BCodigo" 
								   placeholder="Buscar cuenta..." 
								   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						</div>
						<div class="overflow-x-auto">
							<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
								<thead class="text-xs text-gray-700 uppercase bg-gray-50">
									<tr>
										<th scope="col" class="px-6 py-3">Código</th>
										<th scope="col" class="px-6 py-3">Detalle</th>
										<th scope="col" class="px-6 py-3">Tipo de Cuenta</th>
									</tr>
								</thead>
								<tbody id="TableCod">
									<?php 
										$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
										if ($_SESSION["PLAN"]=="S"){
											$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='$RutEmpresa' ORDER BY detalle";
										}else{
											$SQL="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
										}
										$resultados = $mysqli->query($SQL);
										while ($registro = $resultados->fetch_assoc()) { 
											$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$tcuenta=$registro1["nombre"];
											}

											echo '
												<tr onclick="data(\''.$registro["numero"].'\')" class="bg-white border-b hover:bg-gray-50 cursor-pointer">
												<td class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap">'.$registro["numero"].'</td>
												<td class="px-6 py-2">'.strtoupper($registro["detalle"]).'</td>
												<td class="px-6 py-2">'.$tcuenta.'</td>
												</tr>
											';
										}
										$mysqli->close();
									?>
								</tbody>
							</table>
						</div>
					</div>
					<!-- Modal footer -->
					<div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
						<button type="button" 
								class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" 
								data-modal-hide="searchModal" 
								id="cmodel">
							<i class="fa fa-times mr-2"></i>Cerrar
						</button>
					</div>
				</div>
			</div>
		</div>

		<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
			<div class="relative p-4 w-full max-w-7xl max-h-full">
				<!-- Modal content -->
				<div class="relative bg-white rounded-lg shadow-sm">
					<!-- Modal header -->
					<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
						<div class="flex items-center">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fa fa-list text-lg text-primary-500"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-900 flex items-center">
									Buscar <?php echo strtolower($titulo); ?>
								</h3>	
								<p class="text-sm text-gray-600"><?php echo $MsjEmpresa; ?></p>
							</div>	
						</div>
						<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
							<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
							</svg>
							<span class="sr-only">Close modal</span>
						</button>
					</div>
					<!-- Modal body -->
					<div class="p-4 md:p-5 space-y-4">
						<div class="mb-4">
						<input class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
							id="myInput" 
							type="text" 
							placeholder="Buscar cuentas...">
						</div>
						<div class="overflow-x-auto max-h-96">
							<div id="contenidoTablaModal">
								<table class="min-w-full divide-y divide-gray-200">
									<thead class="bg-gray-50">
										<tr>
											<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider" align="right" width="10%">Rut</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Razon Social</th>
											<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider">Direccion</th>
											<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider">Giro</th>
											<th class="px-6 py-2 text-xs font-medium text-gray-700 text-center uppercase tracking-wider">Correo</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" align="right">Numero</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="3">Acciones</th>
										</tr>
									</thead>
									<tbody id="myTable">
								
									</tbody>
								</table>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>

		<!-- Script para búsqueda en tiempo real -->
		<script>
			$(document).ready(function(){
				$("#BCodigo").on("keyup", function() {
					var value = $(this).val().toLowerCase();
					$("#TableCod tr").filter(function() {
						$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
					});
				});
			});
		</script>

		<script>
			$(document).ready(function(){
				$("#myInput").on("keyup", function() {
					var value = $(this).val().toLowerCase();
					$("#myTable tr").filter(function() {
						$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
					});
				});

				// Búsqueda en el modal
				$("#buscarTabla").on("keyup", function() {
					var value = $(this).val().toLowerCase();
					$("#contenidoTablaModal #myTable tr").filter(function() {
						$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
					});
				});

				
			});
		</script>

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
		<script src="../js/alertas.js"></script>

		<script>

			function handleFetchErrors(response) {
				if (!response.ok) {
					throw Error(response.statusText);
				}
				return response.json();
			}

			function ingresarClientePro(e) {
				e.preventDefault();
				
				const rut = document.getElementById("rut").value;
				const rsocial = document.getElementById("rsocial").value;
				const direccion = document.getElementById("direccion").value;
				const giro = document.getElementById("giro").value;
				const ciudad = document.getElementById("ciudad").value;
				const correo = document.getElementById("correo").value;
				const cuenta = document.getElementById("cuenta").value;
				const nomfrm = document.getElementById("nomfrm").value;

				const campos = ["rut", "rsocial", "cuenta"];

				const camposVacios = campos.some(campo => !document.getElementById(campo).value);
				
				if (camposVacios) {
					mostrarMensaje("Faltan datos", "info");
					return;
				}

				const clienteProData = {
					rut: rut,
					rsocial: rsocial,
					direccion: direccion,
					giro: giro,
					ciudad: ciudad,
					correo: correo,
					cuenta: cuenta,
					nomfrm: nomfrm
				};

				const idemp = document.getElementById("idemp").value;
				if(idemp !== "") clienteProData.idemp = idemp;

				const action = idemp === "" ? "ingresarClientePro" : "modificarClientePro";

				fetch(`router/router.php?action=${action}`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify(clienteProData),
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					
					if(data.success) {
						mostrarMensaje(data.mensaje, "success");
					}else if(data.error) {
						mostrarMensaje(data.mensaje, "error");
					}else {
						mostrarMensaje("Error al procesar los datos: " + data.mensaje, "error");
					}
				}).catch(error => {
					console.log(error.message);
				});

			}

			function cargarClientePro(buscar = "") {
				console.log(buscar);
				const myInput = document.getElementById("myInput");
				const nomfrm = document.getElementById("nomfrm").value;
				const nombre = nomfrm === "C" ? "clientes" : "proveedores";
				
				let url;

				if(buscar) {
					url = `router/router.php?action=cargarClientePro&nomfrm=${nomfrm}&buscar=${buscar}`;
				} else {
					url = `router/router.php?action=cargarClientePro&nomfrm=${nomfrm}`;
				}

				const tabla = document.getElementById("myTable");
				
				// Mostrar indicador de carga
				tabla.innerHTML = "";
				const trCargando = document.createElement("tr");
				trCargando.className = "bg-white";
				trCargando.innerHTML = `
					<td colspan="8" class="px-6 py-8 whitespace-nowrap text-center">
						<div class="flex items-center justify-center">
							<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
							<span class="text-gray-600">Cargando...</span>
						</div>
					</td>
				`;
				tabla.appendChild(trCargando);

				fetch(url)
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					const clase = "px-6 py-2 whitespace-nowrap text-sm text-gray-900";
					if(data.clientesProveedores.length === 0) {
						tabla.innerHTML = "";
						const tr = document.createElement("tr");
						tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";
						tr.innerHTML = `
							<td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">No hay registros</td>
						`;
						tabla.appendChild(tr);
						return;
					} else {
						tabla.innerHTML = "";
						data.clientesProveedores.forEach(cliPro => {
							const tr = document.createElement("tr");
							tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";
							tabla.appendChild(tr);
							
							const tdRut = document.createElement("td");
							tdRut.className = clase;
							tdRut.align = "center";
							tdRut.textContent = cliPro.rut;
							tr.appendChild(tdRut);

							const tdRazonSocial = document.createElement("td");
							tdRazonSocial.className = clase;
							tdRazonSocial.textContent = cliPro.razonsocial;
							tr.appendChild(tdRazonSocial);

							const tdDireccion = document.createElement("td");
							tdDireccion.className = clase;
							tdDireccion.textContent = cliPro.direccion;
							tr.appendChild(tdDireccion);

							const tdGiro = document.createElement("td");
							tdGiro.className = clase;
							tdGiro.textContent = cliPro.giro;
							tr.appendChild(tdGiro);

							const tdCorreo = document.createElement("td");
							tdCorreo.className = clase;
							tdCorreo.textContent = cliPro.correo;
							tr.appendChild(tdCorreo);

							const tdNumero = document.createElement("td");
							tdNumero.className = clase;
							tdNumero.textContent = cliPro.nuCuenta;
							tr.appendChild(tdNumero);

							const tdCuenta = document.createElement("td");
							tdCuenta.className = clase;
							tdCuenta.textContent = cliPro.nCuenta;
							tr.appendChild(tdCuenta);

							const tdAcciones = document.createElement("td");
							tdAcciones.className = clase;
							tr.appendChild(tdAcciones);

							const divAcciones = document.createElement("div");
							divAcciones.className = "flex space-x-2";
							tdAcciones.appendChild(divAcciones);

							const btnModificar = document.createElement("button");
							btnModificar.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
							btnModificar.innerHTML = `<i class="fa fa-edit mr-1"></i>Modificar`;
							btnModificar.addEventListener("click", function() {
								document.getElementById("idemp").value = cliPro.id;
								document.getElementById("rut").value = cliPro.rut;
								document.getElementById("rsocial").value = cliPro.razonsocial;
								document.getElementById("direccion").value = cliPro.direccion;
								document.getElementById("giro").value = cliPro.giro;
								document.getElementById("correo").value = cliPro.correo;
								document.getElementById("cuenta").value = cliPro.nuCuenta;
								document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Modificar";

								const btnEliminar = document.getElementById("btnEliminar");
								btnEliminar.hidden = false;

								btnEliminar.addEventListener("click", function() {
									eliminarClientePro(cliPro.id, nombre);
								});

								const closeButton = document.querySelector("[data-modal-hide='default-modal']");
								if (closeButton) closeButton.click();
							});
							divAcciones.appendChild(btnModificar);

							const btnEstado = document.createElement("button");
							btnEstado.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
							
							const estadoTexto = cliPro.estado === "A" ? "Activa" : "Inactiva";
							const estadoIcono = cliPro.estado === "A" ? "fa-check" : "fa-ban";

							btnEstado.innerHTML = `<i class="fa ${estadoIcono} mr-1"></i>${estadoTexto}`;
							btnEstado.addEventListener("click", function() {
								cambiarEstado(cliPro.id);
							});
							divAcciones.appendChild(btnEstado);
							
						});
						
						


					}
				})
				.catch(error => {
					console.log(error.message);
				});
			}

			function eliminarClientePro(id, nombre) {
				
				fetch(`router/router.php?action=eliminarClientePro`, {
					method: "POST",
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ id: id, nombre: nombre })
				})
				.then(handleFetchErrors)
				.then(data => {
					if(data.success) {
						mostrarMensaje(data.mensaje, "success");
					} else if(data.warning) {
						mostrarMensaje(data.mensaje, "info");
					} else {
						console.log(data.mensaje);
						mostrarMensaje(data.mensaje, "error");
					}
				})
				.catch(error => {
					console.error("Error:", error);
					console.log("Error al eliminar la empresa: " + error.message);
				});

			}

			function cambiarEstado(id) {
				fetch(`router/router.php?action=estadoClientePro`, { 
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ id: id })
				})
					.then(handleFetchErrors)
					.then(data => {
						if (data.success) {
							const closeButton = document.querySelector("[data-modal-hide='default-modal']");
							if (closeButton) closeButton.click();
						} else if (data.error) {
							throw new Error(data.mensaje);
						}
					})
					.catch(error => {
						console.error("Error:", error.mensaje);
					});
			}

			function deshabilitarBoton(buttonId) {
				const button = document.getElementById(buttonId);
				if (button) {
					button.disabled = true;
					button.className = "bg-gray-100 cursor-not-allowed opacity-50 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
				}
			}

			function limpiarFormulario() {
				document.getElementById("rut").value = "";
				document.getElementById("rsocial").value = "";
				document.getElementById("direccion").value = "";
				document.getElementById("giro").value = "";
				document.getElementById("ciudad").value = "";
				document.getElementById("correo").value = "";
				document.getElementById("cuenta").value = "";

				document.getElementById("btnGrabar").className = "bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
				document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";

				const btnEliminar = document.getElementById("btnEliminar");
				btnEliminar.hidden = true;
				btnEliminar.onclick = null;

				document.getElementById("idemp").value = "";
				document.getElementById("idempb").value = "";
				document.getElementById("idempa").value = "";
				document.getElementById("ideli").value = "";
			}

			document.addEventListener("DOMContentLoaded", function() {
				const btnBuscar = document.getElementById("btnBuscar");
				btnBuscar.addEventListener("click", function() {
					cargarClientePro();

					const myInput = document.getElementById("myInput");
					
					setTimeout(() => {
						myInput.focus();
					}, 100);
					
					myInput.addEventListener("input", function() {
						cargarClientePro(myInput.value);
					});
				});

				document.getElementById("form1").addEventListener("submit", ingresarClientePro);
			});

		</script>
	</body>
</html>


<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if($registro['tipo']=="IVA"){
			$DIVA=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];	
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];	
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];	
		}	

		if($registro['tipo']=="RETE_HONO"){
			$DPORC=$registro['valor'];	
		}	

		if($registro['tipo']=="RETE_FACT"){
			$DFACT=$registro['valor'];	
		}	
		if($registro['tipo']=="PPM"){
			$DPPM=$registro['valor'];	
		}	

		// if($registro['tipo']=="CUEN_REND"){
		// 	$CUENANT=$registro['valor'];	
		// }

		// if($registro['tipo']=="ANTI_PROV"){
		// 	$ANTIPRO=$registro['valor'];	
		// }
		// if($registro['tipo']=="ANTI_CLIE"){
		// 	$ANTICLI=$registro['valor'];	
		// }	 
		
		if($registro['tipo']=="CERO_FOLI"){
			$CFOLIO=$registro['valor'];	
		}
		if($registro['tipo']=="TEXT_FOLI"){
			$TFOLIO=$registro['valor'];	
		}
	}
    $mysqli->close();

?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="../css/bootstrap.min.css"> -->
		<script src="../js/jquery.min.js"></script>
		<!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script type="text/javascript">
			function Volver(){
				form1.action="../frmMain.php";
				form1.submit();
			}

			function data(valor){
				var cas=form1.casilla.value;
				var r=cas.substr(0,4);
				document.getElementById(cas).value=valor;
				const closeButton = document.querySelector('[data-modal-hide="default-modal"]');
				if (closeButton) {
					closeButton.click();
				}
			}

			jQuery(document).ready(function(e) {
				// Focus on search input when modal opens
				document.addEventListener('DOMContentLoaded', function() {
					const modal = document.getElementById('default-modal');
					const searchInput = document.getElementById('BCodigo');
					
					if (modal && searchInput) {
						modal.addEventListener('shown.bs.modal', function() {
							searchInput.focus();
						});
					}
				});
			});

		</script>
	</head>

	<body>


	<?php include '../nav.php'; ?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

			<div class="space-y-8">
			<form action="XfrmParGlobales.php" method="POST" name="form1" id="form1">
				<input type="hidden" name="casilla" id="casilla">

				<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
					<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
						<i class="fa fa-save mr-2"></i> Grabar
					</button>

					<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
						<i class="fa fa-times mr-2"></i> Cancelar
					</button> 
				</div>

				<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fas fa-cogs text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Parametros Globales
							</h3>    
						</div>
					</div>

					<div class="p-6 pt-1 space-y-6">
						<div class="bg-red-50 border-l-4 border-red-400 p-2.5 mb-6 mt-5">
							<div class="flex">
								<div class="flex-shrink-0">
									<i class="fas fa-calculator text-red-400"></i>
								</div>
								<div class="ml-3">
									<p class="text-sm text-red-700 font-semibold">
										Datos de Cálculo y Moneda
									</p>
								</div>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div>
								<label for="DIVA" class="block text-sm font-medium text-gray-700 mb-2">IVA</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="DIVA" name="DIVA" maxlength="2" value="<?php echo $DIVA; ?>" required>
							</div> 

							<div>
								<label for="DMONE" class="block text-sm font-medium text-gray-700 mb-2">Símbolo Moneda</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DMONE" name="DMONE" maxlength="3" value="<?php echo $DMONE; ?>" required>
							</div> 
						</div>

						<div class="bg-blue-50 border-l-4 border-blue-400 p-2.5 mb-6">
							<div class="flex">
								<div class="flex-shrink-0">
									<i class="fas fa-list text-blue-400"></i>
								</div>
								<div class="ml-3">
									<p class="text-sm text-blue-700 font-semibold">
										Datos de Números y Lista
									</p>
								</div>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
							<div>
								<label for="DMILE" class="block text-sm font-medium text-gray-700 mb-2">Separador de Miles</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DMILE" name="DMILE" required>
									<?php 
										if ($DMILE==",") {
											echo "<option value=',' selected> , Coma</option>";
											echo "<option value='.'> . Punto</option>";
										}else{
											echo "<option value=','> , Coma</option>";
											echo "<option value='.' selected> . Punto</option>";
										}
									?>
								</select>
							</div> 

							<div>
								<label for="DDECI" class="block text-sm font-medium text-gray-700 mb-2">Decimal</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DDECI" name="DDECI" required>
									<?php 
										if ($DDECI==",") {
											echo "<option value=',' selected> , Coma</option>";
											echo "<option value='.'> . Punto</option>";
										}else{
											echo "<option value=','> , Coma</option>";
											echo "<option value='.' selected> . Punto</option>";
										}
									?>
								</select>
							</div> 			        

							<div>
								<label for="DLIST" class="block text-sm font-medium text-gray-700 mb-2">Separador de Lista</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DLIST" name="DLIST" required>
									<?php 
										if ($DLIST==",") {
											echo "<option value=',' selected> , Coma</option>";
											echo "<option value=';'> ; Punto y coma</option>";
										}else{
											echo "<option value=','> , Coma</option>";
											echo "<option value=';' selected> ; Punto y coma</option>";
										}
									?>
								</select>
							</div> 			        

							<div>
								<label for="NDECI" class="block text-sm font-medium text-gray-700 mb-2">Cantidad de Decimales</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="NDECI" name="NDECI" maxlength="3" value="<?php echo $NDECI; ?>" required>
							</div> 			        
						</div>

						<div class="bg-green-50 border-l-4 border-green-400 p-2.5 mb-6">
							<div class="flex">
								<div class="flex-shrink-0">
									<i class="fas fa-percentage text-green-400"></i>
								</div>
								<div class="ml-3">
									<p class="text-sm text-green-700 font-semibold">
										Parámetros Honorarios
									</p>
								</div>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div>
								<label for="DPORC" class="block text-sm font-medium text-gray-700 mb-2">% Retención</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-right" id="DPORC" name="DPORC" maxlength="2" value="<?php echo $DPORC; ?>" readonly>
							</div> 

							<div>
								<label for="DFACT" class="block text-sm font-medium text-gray-700 mb-2">Factor Retención</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-right" id="DFACT" name="DFACT" maxlength="3" value="<?php echo $DFACT; ?>" readonly>
							</div> 
						</div>

						<div class="bg-purple-50 border-l-4 border-purple-400 p-2.5 mb-6">
							<div class="flex">
								<div class="flex-shrink-0">
									<i class="fas fa-file-invoice text-purple-400"></i>
								</div>
								<div class="ml-3">
									<p class="text-sm text-purple-700 font-semibold">
										Parámetros 14Ter
									</p>
								</div>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-1 gap-6">
							<div>
								<label for="Comp1" class="block text-sm font-medium text-gray-700 mb-2">Cuenta PPM</label>
								<div class="flex items-center gap-2"> 
									<input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="Comp1" name="Comp1" value="<?php echo $DPPM; ?>"> 
									<button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-2 px-3 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp1'" >
										<i class="fa-solid fa-magnifying-glass"></i> 
									</button>
								</div> 
							</div> 
						</div>

						<div class="bg-orange-50 border-l-4 border-orange-400 p-2.5 mb-6">
							<div class="flex">
								<div class="flex-shrink-0">
									<i class="fas fa-file-alt text-orange-400"></i>
								</div>
								<div class="ml-3">
									<p class="text-sm text-orange-700 font-semibold">
										Folio de Documentos
									</p>
									<p class="text-xs text-orange-600 mt-1">
										Largo de 3 caracteres: 001, 002, 003, etc. --- Texto: Folio: 001
									</p>
								</div>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div>
								<label for="CFOLIO" class="block text-sm font-medium text-gray-700 mb-2">Largo de Folio</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="CFOLIO" name="CFOLIO" maxlength="5" value="<?php echo $CFOLIO; ?>" required>
							</div> 

							<div>
								<label for="TFOLIO" class="block text-sm font-medium text-gray-700 mb-2">Texto folio</label>
								<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="TFOLIO" name="TFOLIO" required>
									<?php 
										if ($TFOLIO=="NO") {
											echo "<option value='NO' selected>NO</option>";
											echo "<option value='SI'>SI</option>";
										}else{
											echo "<option value='NO'>NO</option>";
											echo "<option value='SI' selected>SI</option>";
										}
									?>
								</select>
							</div> 			        
						</div>

						<div class="bg-indigo-50 border-l-4 border-indigo-400 p-2.5 mb-6">
							<div class="flex">
								<div class="flex-shrink-0">
									<i class="fas fa-money-bill-wave text-indigo-400"></i>
								</div>
								<div class="ml-3">
									<p class="text-sm text-indigo-700 font-semibold">
										Configuración de Anticipos y Rendiciones
									</p>
									<p class="text-xs text-indigo-600 mt-1">
										Configuración de cuentas para anticipos y rendiciones
									</p>
								</div>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
							<div>
								<label for="Comp4" class="block text-sm font-medium text-gray-700 mb-2">Rendiciones</label>
								<div class="flex items-center gap-2"> 
									<input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="Comp4" name="Comp4" value="<?php echo $CUENANT; ?>"> 
									<button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-2 px-3 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp4'" >
										<i class="fa-solid fa-magnifying-glass"></i> 
									</button>
								</div> 
							</div> 

							<div>
								<label for="Comp2" class="block text-sm font-medium text-gray-700 mb-2">Anticipo Proveedores</label>
								<div class="flex items-center gap-2"> 
									<input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="Comp2" name="Comp2" value="<?php echo $ANTIPRO; ?>"> 
									<button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-2 px-3 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp2'" >
										<i class="fa-solid fa-magnifying-glass"></i> 
									</button>
								</div> 
							</div> 

							<div>
								<label for="Comp3" class="block text-sm font-medium text-gray-700 mb-2">Anticipo Clientes</label>
								<div class="flex items-center gap-2"> 
									<input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="Comp3" name="Comp3" value="<?php echo $ANTICLI; ?>"> 
									<button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-2 px-3 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp3'" >
										<i class="fa-solid fa-magnifying-glass"></i> 
									</button>
								</div> 
							</div> 
						</div>

						<!-- Modal  buscar codigo-->
						<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
							<div class="relative p-4 w-full max-w-7xl max-h-full">
								<!-- Modal content -->
								<div class="relative bg-white rounded-lg shadow-sm">
									<!-- Modal header -->
									<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
										<h3 class="text-xl font-semibold text-gray-900">
											Listado de Cuentas
										</h3>
										<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
											<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
												<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
											</svg>
											<span class="sr-only">Close modal</span>
										</button>
									</div>
									<!-- Modal body -->
									<div class="p-4 md:p-5 space-y-4">
										
										<div class="block">
											<input class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="BCodigo" name="BCodigo" type="text" placeholder="Buscar...">
										</div>
										<div class="col-md-12">

											<table class="min-w-full divide-y divide-gray-200">
												<thead class="bg-gray-50">
													<tr>
														<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Codigo</th>
														<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Detalle</th>
														<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Tipo de Cuenta</th>
													</tr>
												</thead>
												<tbody id="TableCod">
													<?php 
														$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
														if ($_SESSION["PLAN"]=="S"){
															$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
														}else{
															$SQL="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
														}
														$resultados = $mysqli->query($SQL);
														while ($registro = $resultados->fetch_assoc()) {
															
															$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
															$resultados1 = $mysqli->query($SQL1);
															while ($registro1= $resultados1->fetch_assoc()) {
																$tcuenta=$registro1["nombre"];
															}

															echo '
																<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out" onclick="data(\''.$registro["numero"].'\')">
																<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["numero"].'</td>
																<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.strtoupper($registro["detalle"]).'</td>
																<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$tcuenta.'</td>
																</tr>
															';
														}
														$mysqli->close();
													?>

												</tbody>
											</table>
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
										</div>


									</div>
									<!-- Modal footer -->
									<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
										<button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100" id="cmodel">Cerrar</button>
									</div>
								</div>
							</div>
						</div>
						<!-- fin buscar codigo -->   					

						<div class="clearfix"> </div>


					</div>
				</div>
			</form>
		</div>
		</div>
		</div>

	<div class="clearfix"> </div>


	<?php include '../footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
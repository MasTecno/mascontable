<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	if(isset($_POST['idmodcat']) && $_POST['idmodcat']!=""){
		$swcat=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTEstResultadoCab WHERE Id='".$_POST['idmodcat']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xnomcat=$registro["Nombre"];
			$XTipo=$registro["Tipo"];
		}  
		$mysqli->close();
	}

	if (isset($_POST['idestadocat']) && $_POST['idestadocat']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQL="SELECT * FROM CTEstResultadoCab WHERE id='".$_POST['idestadocat']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			if ($registro["Estado"]=="A") {
				$mysqli->query("UPDATE CTEstResultadoCab SET Estado='B' WHERE Id='".$_POST['idestadocat']."'");
			}else{
				$mysqli->query("UPDATE CTEstResultadoCab SET Estado='A' WHERE Id='".$_POST['idestadocat']."'");
			}
		}  
		$mysqli->close();
	}

	if (isset($_POST['sw1']) && $_POST['sw1']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTEstResultadoCab SET Nombre='".$_POST['NombreCat']."', Tipo='".$_POST['Tipo']."' WHERE Id='".$_POST['sw1']."'");
		$mysqli->close();
	}

	if (isset($_POST['idelimcat']) && $_POST['idelimcat']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("DELETE FROM CTEstResultadoCab WHERE Id='".$_POST['idelimcat']."'");
		$mysqli->query("DELETE FROM CTEstResultadoDet WHERE IdCab='".$_POST['idelimcat']."'");
		$mysqli->close();
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
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">

			function Volver(){
				form2.action="frmResultadoConf.php";
				form2.submit();
			}

			function ModCat(valor){
				form2.idmodcat.value=valor;
				form2.action="#";
				form2.submit();
			}
			function EstadoCat(valor){
				form2.idestadocat.value=valor;
				form2.action="#";
				form2.submit();			
			}

			function EliCat(valor){
				form2.action="#";
				var r = confirm("Al eliminar la categoria se eliminaran las cuenta que esta  asigandas a ella.\r\nDesea Eliminar la Categoria?");
				if (r == true) {
					form2.idelimcat.value=valor;
					limpiarFormulario();
					form2.submit();
														
				}

				
			}

			function EliminarCategoria() {
				const id = document.getElementById("idmodcat").value;

				EliCat(id);
			}

			function limpiarFormulario(){
				document.getElementById("NombreCat").value = "";
				document.getElementById("Tipo").value = "";
				document.getElementById("idmodcat").value = "";

				form2.idmodcat.value = "";
				document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";
				
				const btnEliminar = document.getElementById("btnEliminar");
				if(btnEliminar) {
					btnEliminar.remove();
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
		<form action="xfrmResultadoConfCat.php" method="POST" name="form2" id="form2">

			<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
					
				<button type="button" 
					class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
					onclick="limpiarFormulario()">
					<i class="fa fa-plus mr-2"></i>Nueva
				</button>

				<?php if ($swcat==1) { ?>
					<button type="submit" id="btnGrabar" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
						<i class="fa fa-edit mr-2"></i> Modificar
					</button>
					<button type="button" id="btnEliminar" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="EliminarCategoria()">
						<i class="fa fa-trash mr-2"></i> Eliminar
					</button>
				<?php }else{ ?>
					<button type="submit" id="btnGrabar" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
						<i class="fa fa-save mr-2"></i> Grabar
					</button>
				<?php } ?>

					<button data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" type="button">
						<i class="fa-solid fa-magnifying-glass mr-2"></i>Buscar
					</button>

					<button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
						<i class="fa fa-times mr-2"></i> Cancelar
					</button> 

			</div>

			<div class="bg-white rounded-lg shadow-sm border border-gray-200">
				<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
					<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
						<i class="fa-solid fa-folder-plus text-lg text-blue-600"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-800">
							Nueva Categor&iacute;a
						</h3>
					</div>
				</div>
				
				<div class="p-6 pt-1 space-y-6">

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
						<div>
							<label for="NombreCat" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
							<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="NombreCat" name="NombreCat" value="<?php echo $xnomcat; ?>" autocomplete="off" required>
							<input type="hidden" name="idmodcat" id="idmodcat" value="<?php echo $_POST['idmodcat']; ?>">
							<input type="hidden" name="idestadocat" id="idestadocat">
							<input type="hidden" name="idelimcat" id="idelimcat">
							<input type="hidden" name="sw1" id="sw1" value="<?php echo $_POST['idmodcat']; ?>">
						</div>

						<div>
							<label for="Tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
							<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="Tipo" name="Tipo" required>
								<option value="">Seleccionar</option>
								<option value="I" <?php if($XTipo=="I"){ echo "selected"; } ?>>Ingreso</option>
								<option value="E" <?php if($XTipo=="E"){ echo "selected"; } ?>>Egreso</option>
							</select>
						</div>
					</div>

					</div>
				</div>

			<!-- <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-5">
				<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
					<div class="w-10 h-10 bg-green-100 rounded-lg flex justify-center items-center mr-4">
						<i class="fa-solid fa-list text-lg text-green-600"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-800">
							Categor&iacute;as Registradas
						</h3>
					</div>
				</div> -->

				<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
					<div class="relative p-4 w-full max-w-6xl max-h-full">
						<!-- Modal content -->
						<div class="relative bg-white rounded-lg shadow-sm">
							<!-- Modal header -->
							<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
								<h3 class="text-xl font-semibold text-gray-900">
									Categorias Registradas
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
								<div class="mb-4 mt-4">         
									<input class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="myInput" type="text" placeholder="Buscar...">
								</div>
								
								<div class="overflow-x-auto">
									<table class="min-w-full divide-y divide-gray-200">
										<thead class="bg-gray-50">
											<tr>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Nombre</th>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Tipo</th>
												<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 tracking-wider">Editar</th>
												<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 tracking-wider">Estado</th>
												<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 tracking-wider">Eliminar</th>
											</tr>
										</thead>

										<tbody id="myTable" class="bg-white divide-y divide-gray-200">
										<?php 
											$BotEstado='';
											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											$SQL="SELECT * FROM CTEstResultadoCab ORDER BY Id, Tipo";

											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {

												if ($registro['Tipo']=="I") {
													$Tipo="Ingreso";
												}else{
													$Tipo="Egreso";
												}

												if ($registro['Estado']=="A") {
													$BotEstado='<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="EstadoCat('.$registro['Id'].')">
													<i class="fa fa-eye mr-2"></i>Activo
													</button>';
												}else{
													$BotEstado='<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="EstadoCat('.$registro['Id'].')">
														<i class="fa fa-eye-slash mr-2"></i>Inactivo
													</button>';
												}

												echo '
													<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
													<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro['Nombre'].'</td>
													<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">'.$Tipo.'</td>
													<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
														<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="ModCat('.$registro['Id'].')">
															<i class="fa fa-edit mr-2"></i>Modificar
														</button>
													</td>
													<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">'.$BotEstado.'</td>
													<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
														<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="EliCat('.$registro['Id'].')">
															<i class="fa fa-trash mr-2"></i>Eliminar
														</button>
													</td>
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
							<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
								<button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700">Cancelar</button>
							</div>
						</div>
					</div>
				</div>
				
				<!-- <div class="p-6 pt-1 space-y-6"> -->
					<!-- <div class="mb-4 mt-4">         
						<input class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="myInput" type="text" placeholder="Buscar...">
					</div> -->
					
					<!-- <div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
								<tr>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Nombre</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Tipo</th>
									<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 tracking-wider">Editar</th>
									<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 tracking-wider">Estado</th>
									<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 tracking-wider">Eliminar</th>
								</tr>
							</thead>

							<tbody id="myTable" class="bg-white divide-y divide-gray-200">
							<?php 
								$BotEstado='';
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$SQL="SELECT * FROM CTEstResultadoCab ORDER BY Id, Tipo";

								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {

									if ($registro['Tipo']=="I") {
										$Tipo="Ingreso";
									}else{
										$Tipo="Egreso";
									}

									if ($registro['Estado']=="A") {
										$BotEstado='<button type="button" class="bg-green-100 hover:bg-green-200 text-green-800 text-xs font-medium py-1 px-2 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" onclick="EstadoCat('.$registro['Id'].')"><i class="fa fa-eye"></i></button>';
									}else{
										$BotEstado='<button type="button" class="bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium py-1 px-2 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="EstadoCat('.$registro['Id'].')"><i class="fa fa-eye-slash"></i></button>';
									}

									echo '
										<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro['Nombre'].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">'.$Tipo.'</td>
										<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
											<button type="button" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs font-medium py-1 px-2 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2" onclick="ModCat('.$registro['Id'].')"><i class="fa fa-edit"></i></button>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">'.$BotEstado.'</td>
										<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
											<button type="button" class="bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium py-1 px-2 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="EliCat('.$registro['Id'].')"><i class="fa fa-trash"></i></button>
										</td>
										</tr>
									';
								}       
								$mysqli->close();
							?>
							</tbody>
						</table>
					</div> -->
				<!-- </div> -->
			<!-- </div> -->

		</form>
		</div>
	</div>

		<?php
			if(isset($_GET['Err']) && $_GET['Err']==1){
				echo '<script>alert("Este Codigo ya esta ingresado");</script>';
			}
		?>
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
	</script>
	
	<?php include '../footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>
</html>


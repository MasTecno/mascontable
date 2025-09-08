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

	$PerInsert = substr($Periodo,3,4);
	$PerInsert = $Periodo;
	// $PerInsert = "12-".($PerInsert-0);
	// $PerInsert = $_POST['PApertura1'];

	$CantHono=0;
	$CantCoVe=0;

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT count(*) as CantHono FROM CTHonorarios WHERE origen='Z' AND periodo='$PerInsert' AND movimiento='' AND rutempresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) { 
		$CantHono=$registro["CantHono"];
	}
	
	$SQL="SELECT count(*) as CantCoVe FROM CTRegDocumentos WHERE origen='Z' AND periodo='$PerInsert' AND keyas='' AND rutempresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) { 
		$CantCoVe=$registro["CantCoVe"];
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

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script type="text/javascript">

			function data(valor){
				ProBal.cuenta.value=valor;

				console.log("Cerrando modal");

				const closeButton = document.querySelector('[data-modal-hide="myModal"]');
				
				if (closeButton) {
					closeButton.click();
				}

			}

			function acept(){
				sw = document.getElementById("ace").checked;

				if (sw==false) {
					document.getElementById("bt").classList.remove("active");
					document.getElementById("bt").classList.add("disabled");
				}else{
					document.getElementById("bt").classList.remove("disabled");
					document.getElementById("bt").classList.add("active");
				}
			}

			function acept1(){
				sw = document.getElementById("ace1").checked;

				if (sw==false) {
					document.getElementById("bt1").classList.remove("active");
					document.getElementById("bt1").classList.add("disabled");
				}else{
					document.getElementById("bt1").classList.remove("disabled");
					document.getElementById("bt1").classList.add("active");
				}
			}

			// function GenAsiApe(){
			// 	GenAsiApertura.action="CrearAsientoApertura.php";
			// 	GenAsiApertura.submit();
			// }

			function GenAsiApe(){
				GenAsiApertura.action="PlantillaXLS.php";
				GenAsiApertura.submit();
			}

			function CarAsiApe(){
				if(GenAsiApertura.CsvCuentas.value==""){
					alert("No a seleccionado el archivo para la carga");
				}else{
					GenAsiApertura.action="XPlantillaXLS.php";
					GenAsiApertura.submit();
				}
			}

			function UpAsiento(){
				var url= "DatosAsiento.php";
				$.ajax({
					type: "POST",
					dataType: 'json',
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$("#TDebe").html(resp.dato1);
						$("#THaber").html(resp.dato2);
					}
				});	
			}
			function Ref(){
				UpAsiento();
			}
			function GrHono(){
				if (GenAsiApertura.CsvHonorario.value=="") {
					alert("Seleccione el archivo a procesar");
				}else{				
					GenAsiApertura.action="xProcesaHonorario.php";
					GenAsiApertura.submit();
				}
			}
			function GrComVen(){
				if (GenAsiApertura.CsvCompraVenta.value=="") {
					alert("Seleccione el archivo a procesar");
				}else{
					GenAsiApertura.action="xProcesaCompraVenta.php";
					GenAsiApertura.submit();					
				}
			}
			function DescHono(){
					GenAsiApertura.action="CargaMasivaH.csv";
					GenAsiApertura.submit();
			}
			function DescVenCom(){
				GenAsiApertura.action="CargaMasivaCV.csv";
				GenAsiApertura.submit();
			}
			jQuery(document).ready(function(e) {
				// Handle modal show with Flowbite
				$('[data-modal-toggle="myModal"]').on('click', function() {
					$('#myModal').removeClass('hidden');
					$('input[name="BCodigo"]').focus();
				});
			});

			
		</script>
	</head>

	<body onload="Ref()">
		<?php 
			include '../nav.php';
		?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
			<div class="space-y-8">
				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-balance-scale text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Apertura a partir de Balance Periodo Anterior
							</h3>
						</div>
					</div>
					
					<div class="p-6 pt-1 space-y-6">
						<form action="ProcesaApertura.php" method="POST" id="ProBal" name="ProBal">
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
								<div>
									<label for="anoselect" class="block text-sm font-medium text-gray-700 mb-2">Año Balance</label>
									<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="anoselect" name="anoselect" required>
									<?php 
										$yoano=date('Y');
										$tano="2010";

										while($tano<=($yoano+1)){
											if (($yoano-1)==$tano) {
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
									<label for="PApertura" class="block text-sm font-medium text-gray-700 mb-2">Año Apertura</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100" id="PApertura" name="PApertura" readonly value="<?php echo $Periodo; ?>">
								</div>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-3.5 mt-3">
								<div>
									<label for="cuenta" class="block text-sm font-medium text-gray-700 mb-2">Cuenta Contable</label>
									<div class="flex items-center gap-2">
										<input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="cuenta" name="cuenta" required value="<?php echo $cuenta; ?>">
										<button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-2 px-3 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" data-modal-target="myModal" data-modal-toggle="myModal">
											<i class="fa-solid fa-magnifying-glass"></i>
										</button>
									</div>
								</div>

								<div>
									<label for="glosa" class="block text-sm font-medium text-gray-700 mb-2">Glosa</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="glosa" name="glosa" autocomplete="off" required onChange="javascript:this.value=this.value.toUpperCase();">
								</div>
							</div>

							<div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
								<div class="flex items-start">
									<input type="checkbox" onclick="acept()" id="ace" name="ace" class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
									<div>
										<label for="ace" class="text-sm font-medium text-gray-700">Aceptar</label>
										<div class="mt-2 text-sm text-gray-600 space-y-1">
											<p>* Considero que el Balance que estoy seleccionado esta correcto y traspasare su información.</p>
											<p>** Este Proceso puede ser ejecutado en cualquier momento, si será insertado en Enero del Año de Apertura.</p>
											<p>*** La cuenta seleccionada corresponde para el proceso de Apertura.</p>
											<p>**** Este proceso no cierra periodos.</p>
										</div>
									</div>
								</div>
							</div>

							<div class="flex justify-end">
								<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled disabled" onclick="Porce()" id="bt" name="bt">Procesar</button>
							</div>
						</form>
					</div>
				</div>

				<!-- Modal  buscar codigo-->
				<div id="myModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
					<div class="relative p-4 w-full max-w-7xl max-h-full">
						<!-- Modal content -->
						<div class="relative bg-white rounded-lg shadow-sm">
							<!-- Modal header -->
							<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
								<h3 class="text-xl font-semibold text-gray-900">
									Listado de Cuentas
								</h3>
								<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="myModal" data-dismiss="modal" id="cmodel">
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
													while ($registro1 = $resultados1->fetch_assoc()) { 
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
								<button data-dismiss="modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100" id="cmodel">Cerrar</button>
							</div>
						</div>
					</div>
				</div>
				<!-- fin buscar codigo -->   

				</form>				
			</div>

				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-green-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-upload text-lg text-green-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Apertura por Migración
							</h3>
						</div>
					</div>
					
					<div class="p-6 pt-1 space-y-6">
						<form action="ProcesaAperturaMigra.php" method="POST" id="GenAsiApertura" name="GenAsiApertura" enctype="multipart/form-data">
							<!-- Asiento de Apertura Section -->
							<div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-5 mt-3">
								<h4 class="text-md font-semibold text-blue-800 mb-4">Asiento de Apertura</h4>
								
								<div class="grid grid-cols-1 gap-4">
									<div>
										<label for="CsvCuentas" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Archivo</label>
										<input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200" id="CsvCuentas" name="CsvCuentas" aria-describedby="fileHelp">
										<small id="fileHelp" class="text-sm text-gray-500">* Solo archivo CSV.</small>
									</div>
									
									<div class="flex justify-start">
										<button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" onclick="CarAsiApe()">Cargar Cuentas</button>
									</div>

									<div class="bg-white border border-gray-200 rounded-md p-4">
										<table class="min-w-full divide-y divide-gray-200">
											<thead class="bg-gray-50">
												<tr>
													<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Debe</th>
													<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Haber</th>
												</tr>
											</thead>
											<tbody>
												<tr class="bg-white">
													<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" id="TDebe">0</td>
													<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" id="THaber">0</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<!-- Libros Section -->
							<div class="bg-green-50 border border-green-200 rounded-md p-4 mb-5">
								<h4 class="text-md font-semibold text-green-800 mb-4">Libros</h4>
								
								<div class="space-y-6">
									<div>
										<label for="CsvCompraVenta" class="block text-sm font-medium text-gray-700 mb-2">Archivo Compra-Venta</label>
										<input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200" id="CsvCompraVenta" name="CsvCompraVenta" aria-describedby="fileHelp">
										<small id="fileHelp" class="text-sm text-gray-500">* Solo archivo CSV.</small>
									</div>
									
									<div class="flex justify-between items-center">
										<button type="button" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" onclick="GrComVen()">Cargar Compras y Ventas</button>
										<span class="text-sm text-gray-600">Documentos Cargados: <span class="font-semibold"><?php echo $CantCoVe; ?></span></span>
									</div>

									<div>
										<label for="CsvHonorario" class="block text-sm font-medium text-gray-700 mb-2">Archivo Honorario</label>
										<input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200" id="CsvHonorario" name="CsvHonorario" aria-describedby="fileHelp">
										<small id="fileHelp" class="text-sm text-gray-500">* Solo archivo CSV.</small>
									</div>
									
									<div class="flex justify-between items-center">
										<button type="button" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" onclick="GrHono()">Cargar Honorarios</button>
										<span class="text-sm text-gray-600">Documentos Cargados: <span class="font-semibold"><?php echo $CantHono; ?></span></span>
									</div>
								</div>
							</div>

							<!-- Descarga Archivos Section -->
							<div class="bg-purple-50 border border-purple-200 rounded-md p-4 mb-5">
								<h4 class="text-md font-semibold text-purple-800 mb-4">Descarga Archivos Tipo</h4>
								
								<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
									<button type="button" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" onclick="GenAsiApe()">Descarga Plan de Cuenta</button>
									<button type="button" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" onclick="DescVenCom()">Descarga Compra y Ventas</button>
									<button type="button" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" onclick="DescHono()">Descarga Honorarios</button>
								</div>
							</div>

							<!-- Form Fields -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-3">
								<div>
									<label for="PApertura1" class="block text-sm font-medium text-gray-700 mb-2">Año Apertura</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100" id="PApertura1" name="PApertura1" readonly value="<?php echo $Periodo; ?>">
								</div>

								<div>
									<label for="xglosa1" class="block text-sm font-medium text-gray-700 mb-2">Glosa</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="xglosa1" name="xglosa1" autocomplete="off" required onChange="javascript:this.value=this.value.toUpperCase();">
								</div>
							</div>

							<!-- Acceptance Section -->
							<div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-3">
								<div class="flex items-start">
									<input type="checkbox" onclick="acept1()" id="ace1" name="ace1" class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
									<div>
										<label for="ace1" class="text-sm font-medium text-gray-700">Aceptar</label>
										<div class="mt-2 text-sm text-gray-600 space-y-1">
											<p>* Considero que la información, en el asiento de apertura esta correcta.</p>
											<p>** Los archivos de Compra, Venta y Honorarios, están correctos.</p>
											<p>*** Este proceso no cierra periodos.</p>
										</div>
									</div>
								</div>
							</div>

							<div class="flex justify-end">
								<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled" id="bt1" name="bt1">Procesar</button>
							</div>
						</form>
					</div>
				</div>
				
			</div>

			</div>
		</div>
		</div>

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>

</html>


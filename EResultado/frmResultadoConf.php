<?php

	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	if (isset($_POST['idcab']) && $_POST['idcab']!="") {
		if ($_POST['idcuenta']!="") {
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


			$SQL="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL1="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa=''";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
      				$mysqli->query("INSERT INTO CTEstResultadoDet VALUES('','".$registro1['IdCab']."','".$_SESSION['RUTEMPRESA']."','".$registro1['Cuenta']."')");

				}
			}

			$SQL="SELECT * FROM CTEstResultadoDet WHERE Cuenta='".$_POST['idcuenta']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
      			$mysqli->query("INSERT INTO CTEstResultadoDet VALUES('','".$_POST['idcab']."','".$_SESSION['RUTEMPRESA']."','".$_POST['idcuenta']."')");
			}

			$mysqli->close();
		}
	}
	
	if (isset($_POST['DefeAsie'])) {

		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$mysqli->query("DELETE FROM CTEstResultadoDet WHERE RutEmpresa=''");

		$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' ORDER BY Id";
		$resultadosint = $mysqli->query($SQLint);
		while ($registroint = $resultadosint->fetch_assoc()) {
			$mysqli->query("INSERT INTO CTEstResultadoDet VALUES('','".$registroint['IdCab']."','','".$registroint['Cuenta']."')");	
		}

		$mysqli->close();
	}


	if (isset($_POST['ridcuenta']) && $_POST['ridcuenta']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
      	$mysqli->query("DELETE FROM CTEstResultadoDet WHERE Id='".$_POST['ridcuenta']."'");
		$mysqli->close();
	}

?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="../css/bootstrap.min.css"> -->
		<script src="../js/jquery.min.js"></script>
		<!-- <script src="../js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">

			$(document).ready(function() {
				$('#example').DataTable();
			} );

			function BuscaCuentas(){
				var url= "../buscaitems.php";

				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#items').html(resp);
				}

				});
			}


			function NewCuenta(value){
				form1.idcab.value=value;
				form1.idcuenta.value="";
			}

			function remov(valor){
				form1.idcab.value="";
				form1.idcuenta.value="";
				form1.ridcuenta.value=valor;
				form1.submit();
			}

			function data(valor){
				form1.idcuenta.value=valor;
				cerrarModal();
				
				if (form1.idcab.value!="" && form1.idcuenta.value!="") {
					form1.submit();
				}
			}

			function cerrarModal(){
				const modal = document.getElementById('default-modal');
				if (modal) {
					modal.classList.add('hidden');
					modal.classList.remove('flex');
				}
			}

			function abrirModal(){
				const modal = document.getElementById('default-modal');
				if (modal) {
					modal.classList.remove('hidden');
					modal.classList.add('flex');

					setTimeout(function() {
						document.getElementById('BCodigo').focus();
					}, 100);
				}
			}

			document.addEventListener('DOMContentLoaded', function() {
				const botonesModal = document.querySelectorAll('[data-modal-target="default-modal"]');
				botonesModal.forEach(function(boton) {
					boton.addEventListener('click', function(e) {
						e.preventDefault();
						abrirModal();
					});
				});
			});

		</script>


	</head>

	<body>


	<?php include '../nav.php'; ?>

	<div class="min-h-screen bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

		<div class="space-y-8">
		<form action="#" method="POST" name="form1" id="form1">
			<input type="hidden" name="idcab" id="idcab">
			<input type="hidden" name="idcuenta" id="idcuenta">
			<input type="hidden" name="ridcuenta" id="ridcuenta">

			<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
				<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="window.location.href='frmResultadoConfCat.php'">
					<i class="fa fa-cog mr-2"></i> Genera Niveles
				</button>
			</div>
				  <!-- Modal  buscar codigo-->
				  <!-- <div class="modal fade" id="myModal" role="dialog">
				    <div class="modal-dialog modal-lg">
				      <div class="modal-content">
				        <div class="modal-header">
				          <h4 class="modal-title">Listado de Cuentas</h4>
				        </div>

				        <div class="modal-body">
						<div class="col-md-12">
										<input class="form-control" id="BCodigo" name="BCodigo" type="text" placeholder="Buscar...">
									</div>
									<div class="col-md-12">

								<table class="table table-condensed table-hover">
									<thead>
										<tr>
											<th>Codigo</th>
											<th>Detalle</th>
											<th>Tipo de Cuenta</th>
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
											
											$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."' AND tipo='RESULTADO'";
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$tcuenta=$registro1["nombre"];

												echo '
													<tr onclick="data(\''.$registro["numero"].'\')">
														<td>'.$registro["numero"].'</td>
														<td>'.strtoupper($registro["detalle"]).'</td>
														<td>'.$tcuenta.'</td>
													</tr>
												';

											}


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

				        <div class="modal-footer">
				          <button type="button" class="btn btn-default" data-dismiss="modal" id="cmodel">Cerrar</button>
				        </div>
				      </div>
				    </div>
				  </div> -->
				  <!-- fin buscar codigo -->

				
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
							<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="cerrarModal()">
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
											
											$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."' AND tipo='RESULTADO'";
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$tcuenta=$registro1["nombre"];

												echo '
													<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out" onclick="data(\''.$registro["numero"].'\')">
													<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["numero"].'</td>
													<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.strtoupper($registro["detalle"]).'</td>
													<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$tcuenta.'</td>
													</tr>
												';

											}


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
							<button onclick="cerrarModal()" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Cancelar</button>
						</div>
					</div>
				</div>
			</div>


			<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
				<!-- Sección Ingreso -->
				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-green-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-arrow-up text-lg text-green-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Ingreso
							</h3>
						</div>
					</div>
					
					<div class="p-6 pt-1 space-y-6">
						<?php
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$swnivel=1;
							$SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='I' ORDER BY Id";
							$resultados = $mysqli->query($SQL);
							$cont=1;
							while ($registro = $resultados->fetch_assoc()) {

								echo '
									<div class="border border-gray-200 rounded-lg p-4 mb-4 mt-4">
										<div class="flex justify-between items-center mb-3">
											<div class="flex items-center">
												<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-3">'.$cont.'</span>
												<span class="text-sm font-medium text-gray-900">'.$registro['Nombre'].'</span>
											</div>
											<button type="button" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="NewCuenta('.$registro['Id'].')">
												<i class="fa fa-plus mr-1"></i> Agregar
											</button>
										</div>
								';

								$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
								$resultados1 = $mysqli->query($SQLint);
								$row_cnt = $resultados1->num_rows;
								if ($row_cnt==0) {
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='' ORDER BY Cuenta";
								}else{
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' ORDER BY Cuenta";
								}

								$resultadosint = $mysqli->query($SQLint);
								while ($registroint = $resultadosint->fetch_assoc()) {
									if ($_SESSION["PLAN"]=="S"){
										$SQLint2="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registroint['Cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}else{
										$SQLint2="SELECT * FROM CTCuentas WHERE numero='".$registroint['Cuenta']."'";
									}
									$resultados2 = $mysqli->query($SQLint2);
									while ($registroint2 = $resultados2->fetch_assoc()) {
										$Xoper=$registroint2['detalle'];
									}

									echo '
										<div class="flex items-center justify-between bg-gray-50 rounded-md p-2 mb-1">
											<div class="flex items-center">
												<span class="text-sm text-gray-600 font-mono mr-2">'.$registroint['Cuenta'].'</span>
												<span class="text-sm text-gray-800">'.$Xoper.'</span>
											</div>
											<button type="button" class="text-red-500 hover:text-red-700 text-sm" onclick="remov('.$registroint['Id'].')">
												<i class="fa fa-times"></i>
											</button>
										</div>
									';
								}
								echo '</div>';
								$cont++;
							}
							$mysqli->close();
						?>
					</div>
				</div>

				<!-- Sección Egreso -->
				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-red-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-arrow-down text-lg text-red-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Egreso
							</h3>
						</div>
					</div>
					
					<div class="p-6 pt-1 space-y-6">
						<?php
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$swnivel=1;
							$SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='E' ORDER BY Id";
							$resultados = $mysqli->query($SQL);
							$cont=1;
							while ($registro = $resultados->fetch_assoc()) {

								echo '
									<div class="border border-gray-200 rounded-lg p-4 mb-4 mt-4">
										<div class="flex justify-between items-center mb-3">
											<div class="flex items-center">
												<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-3">'.$cont.'</span>
												<span class="text-sm font-medium text-gray-900">'.$registro['Nombre'].'</span>
											</div>
											<button type="button" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="NewCuenta('.$registro['Id'].')">
												<i class="fa fa-plus mr-1"></i> Agregar
											</button>
										</div>
								';

								$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
								$resultados1 = $mysqli->query($SQLint);
								$row_cnt = $resultados1->num_rows;
								if ($row_cnt==0) {
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='' ORDER BY Cuenta";
								}else{
									$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' ORDER BY Cuenta";
								}

								$resultadosint = $mysqli->query($SQLint);
								while ($registroint = $resultadosint->fetch_assoc()) {
									if ($_SESSION["PLAN"]=="S"){
										$SQLint2="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registroint['Cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}else{
										$SQLint2="SELECT * FROM CTCuentas WHERE numero='".$registroint['Cuenta']."'";
									}
									$resultados2 = $mysqli->query($SQLint2);
									while ($registroint2 = $resultados2->fetch_assoc()) {
										$Xoper=$registroint2['detalle'];
									}

									echo '
										<div class="flex items-center justify-between bg-gray-50 rounded-md p-2 mb-1">
											<div class="flex items-center">
												<span class="text-sm text-gray-600 font-mono mr-2">'.$registroint['Cuenta'].'</span>
												<span class="text-sm text-gray-800">'.$Xoper.'</span>
											</div>
											<button type="button" class="text-red-500 hover:text-red-700 text-sm" onclick="remov('.$registroint['Id'].')">
												<i class="fa fa-times"></i>
											</button>
										</div>
									';
								}
								echo '</div>';
								$cont++;
							}
							$mysqli->close();
						?>
					</div>
				</div>
			</div>

			<!-- Checkbox de configuración por defecto -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
				<div class="p-6">
					<div class="flex items-center">
						<input type="checkbox" id="DefeAsie" name="DefeAsie" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-2 border-gray-300 rounded" onclick="javascript:form1.submit();">
						<label for="DefeAsie" class="ml-2 block text-sm text-gray-900">
							Dejar esta Configuraci&oacute;n por defecto
						</label>
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
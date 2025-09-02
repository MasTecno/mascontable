<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	if (isset($_GET['Cont'])) {
		$XidCont=$_GET['Cont'];
	}else{
		if (isset($_POST['ListCont'])) {
			$XidCont=$_POST['ListCont'];
		}
	}


	

?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
		<script src="js/jquery.min.js"></script>
		<!-- <script src="js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<style>
			/* Remove the navbar's default margin-bottom and rounded borders */
			.navbar {
				margin-bottom: 0;
				border-radius: 0;
			}

			/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
			.row.content {height: 450px}

			/* Set gray background color and 100% height */
			.sidenav {
				padding-top: 20px;
				background-color: #f1f1f1;
				height: 100%;
			}

			/* Set black background color, white text and some padding */
			footer {
				background-color: #555;
				color: white;
				padding: 15px;
			}

			/* On small screens, set height to 'auto' for sidenav and grid */
			@media screen and (max-width: 767px) {
				.sidenav {
					height: auto;
					padding: 15px;
				}
				.row.content {height:auto;}
			}


		</style>
		<script type="text/javascript">
			function Refr(){
				const listCont = document.getElementById("ListCont").value;
				const myInput = document.getElementById("myInput");

				if(listCont === ""){
					myInput.hidden = true;
				}else{
					myInput.hidden = false;
				}

				form1.action="frmAsignaEmpresa.php?Cont="+form1.ListCont.value;
				form1.submit();
			}
			function Elim(valor){
				form1.idmov.value=valor;
				form1.action="xfrmAsignaEmpresa.php";
				form1.submit();
			}
			function Asig(valor){
				form1.idmov.value=valor;
				form1.action="xfrmAsignaEmpresa.php";
				form1.submit();
			}


		</script>

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
							<i class="fas fa-list-alt text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Asignar Empresas
							</h3>
							<p class="text-sm text-gray-600">Gestión de asignación de empresas a contadores</p>     
						</div>
					</div>

						<div class="p-6 pt-1 space-y-6">

							<div class="grid grid-cols-1 md:grid-cols-1 gap-6">
								<div class="mt-3">
									<label for="ListCont" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
									<i class="fa fa-user mr-1"></i>Contador
								</label>
								<select id="ListCont" name="ListCont" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="Refr()" required>
									<option value="">Usuarios</option>
									<?php
										$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
										$SQL="SELECT * FROM CTContadores WHERE estado='A' AND tipo='U'";
										$resultados = $mysqli->query($SQL);
										while ($registro = $resultados->fetch_assoc()) {
											if ($XidCont==$registro["id"]) {
												echo '<option value="'.$registro["id"].'" selected>'.$registro["nombre"].'</option>';
											}else{
												echo '<option value="'.$registro["id"].'">'.$registro["nombre"].'</option>';
											}
										}  
										$mysqli->close();

									?>
								</select>
								<input type="hidden" name="idmov" id="idmov">
								<span class="p-3">* Si el Contador no tiene acceso a una empresa, tiene disponible el acceso a todas las empresas.</span>
							</div> 
						</div>

						<div class="relative">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<i class="fa fa-search text-gray-400"></i>
							</div>
							<input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" id="myInput" onkeyup="myFunction()" placeholder="Buscar por Razón Social...">	
						</div>
						
								
						<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center" width="1%">Permiso</th>
								<th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center" width="10%">Rut</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="">Raz&oacute;n Social</th>
							</tr>
						</thead>
						<tbody id="myTable">
							<?php

							if ($XidCont!="") {

								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$SQL="SELECT * FROM CTEmpresas WHERE estado='A' ";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {

									$SQL1="SELECT * FROM CTContadoresAsignado WHERE rutempresa='".$registro["rut"]."' AND idcontador='".$XidCont."'";
									$resultados1 = $mysqli->query($SQL1);
									$row_cnt = $resultados1->num_rows;
									if ($row_cnt==0) {
										echo '
											<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: right;">
													<button  type="button" class="inline-flex border border-red-300 items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200" onclick="Asig('.$registro["id"].');">
														<i class="fa fa-xmark mr-1"></i> Sin Acceso
													</button>
												</td>
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: right;">'.$registro["rut"].'</td>
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["razonsocial"].'</td>
											</tr>							
										';
									}else{
										echo '
											<tr>
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: right;">
													<button type="button" class="w-full inline-flex border border-green-300 justify-evenly items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" onclick="Elim('.$registro["id"].');">
														<i class="fa fa-check mr-1"></i> Acceso
													</button>
												</td>
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: right;">'.$registro["rut"].'</td>
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["razonsocial"].'</td>
											</tr>							
										';
									}
								}  

								$mysqli->close();
							}

							?>
						</tbody>
						</table>




					</div>
					<div class="col-md-2">
					</div>	

				</div>
				
			</form>

		</div>



	</div>
	</div>

	<div class="clearfix"> </div>
	<br>
	<br>
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

	<?php include 'footer.php'; ?>

	</body>
</html>
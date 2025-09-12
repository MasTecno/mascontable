<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
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

		<script src="../js/jquery.Rut.js" type="text/javascript"></script>
		<script src="../js/jquery.validate.js" type="text/javascript"></script>	

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">
			function Grilla(){
				var url= "frmGrilla.php";
				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#contenidoTablaModal').html(resp);
				}
				});				
			}

			function Baja(valor){
				form1.idempb.value=valor;
				form1.action="#";
				form1.submit();
			}

			function Alta(valor){
				form1.idempa.value=valor;
				form1.action="#";
				form1.submit();
			}

			function Elimina(valor){
				form1.ideli.value=valor;
				form1.action="#";
				form1.submit();
				limpiarFormulario();
			}

			function EliminarContador(){
				const ideli = document.getElementById("idmod").value;
				form1.ideli.value=ideli;
				form1.action="#";
				form1.submit();
				limpiarFormulario();
				window.location.href = "index.php";
			}

			function Volver(){
				form1.action="../frmMain.php";
				form1.submit();
			}

			$(document).ready(function(){
				$('#rut').Rut({ 
					on_error: function(){alert('Rut incorrecto'); $('#rut').val(""); $('#rut').focus();} 
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
			
					<form action="xfrmIndex.php" method="POST" name="form1" id="form1">

						<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2">
							<button type="button" 
									class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
									onclick="limpiarFormulario()">
								<i class="fa fa-plus mr-2"></i>Nuevo
							</button>
								
							<button type="submit" id="btnGrabar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
								<i class="fa fa-save mr-2"></i>Grabar
							</button>
							
							<button type="button" id="btnEliminar" hidden class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" tabindex="16">
								<i class="fa fa-trash mr-2"></i>Eliminar
							</button>
								

							<button id="btnBuscar" data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" type="button">
								<i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
							</button>

							<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
								<i class="fa fa-times mr-2"></i>Cancelar
							</button>
						</div>
					
						<input type="hidden" name="idempb" id="idempb">
						<input type="hidden" name="idempa" id="idempa">
						<input type="hidden" name="ideli" id="ideli">
						<input type="hidden" name="idmod" id="idmod">

						<br>
						<div class="bg-white rounded-lg shadow-sm border border-gray-200">
							<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
								<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
									<i class="fas fa-file-alt text-lg text-blue-600"></i>
								</div>
								<div>
									<h3 class="text-lg font-semibold text-gray-800">
										Mantenedor de Contadores
									</h3>
									<p class="text-sm text-gray-600">Datos para ingresar un contador</p>     
								</div>
							</div>
							<div class="p-6 pt-1 space-y-6">

								<div id="divAlertas" class="mt-3"></div>

								<div class="grid grid-cols-1 gap-6 mt-5">
									<div>
										<label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
											<i class="fa fa-id-card mr-1"></i>Rut
										</label>
										<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="rut" name="rut" autocomplete="off" placeholder="Ej: 13520300-5" value="<?php echo $rut; ?>" required>
									</div>
								</div>

								<div class="grid grid-cols-2 gap-6 mt-5">
									<div>
										<label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
											<i class="fa fa-user mr-1"></i>Nombre Completo
										</label>
										<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="100" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xnombre; ?>"  autocomplete="off" required>
									</div>

									<div>
										<label for="cargo" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
											<i class="fa fa-user-tie mr-1"></i>Cargo
										</label>
										<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="100" id="cargo" name="cargo" autocomplete="off" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xcargo; ?>"  autocomplete="off">
									</div>
								</div>
							</div>
						</div>

						
						<!-- <div class="col-md-12">
							<div cl class="col-sm-10">
								<input class="form-control" id="myInput" type="text" placeholder="Buscar...">
							</div>

							<div class="clearfix"></div>
							<br>
							<div id="TableContadores">
							</div>
						</div> -->
					</form>
				</div>
			</div>
		</div>

		<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
			<div class="relative p-4 w-full max-w-7xl max-h-full">
				<!-- Modal content -->
				<div class="relative bg-white rounded-lg shadow-sm">
					<!-- Modal header -->
					<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
						<div class="flex items-center">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fa fa-list text-lg text-primary-500"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-900 flex items-center">
									Contadores Ingresados
								</h3>	
								<!-- <p class="text-sm text-gray-600">Lista de contadores ingresados</p> -->
							</div>	
						</div>
						<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
							<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
							</svg>
							<span class="sr-only">Close modal</span>
						</button>
					</div>

					<div class="p-4 md:p-5 space-y-4">
						<div class="mb-4">
							<div class="relative">
								<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
									<i class="fa fa-search text-gray-400"></i>
								</div>
								<input class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" 
									   id="myInput" 
									   type="text" 
									   placeholder="Buscar contadores...">
							</div>
						</div>
						<div class="overflow-x-auto max-h-96">
							<div id="contenidoTablaModal">
								<table class="min-w-full divide-y divide-gray-200">
									<thead class="bg-gray-50">
										<tr>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="10%">Rut</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%">Acciones</th>
											
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

		<script>
			<?php
				if (isset($_GET['ex']) && $_GET['ex']=="yes") {
					echo 'alert ("Contador ya registrado");';
				}
				if ($NoElimina=="N") {
					echo 'alert ("Esta cuenta tiene movimientos, no se puede eliminar.");';
				}
				if ($NoEliminaCom=="N") {
					echo 'alert ("Esta cuenta tiene movimientos y puede estar utilizada en alguna empresa, ya que es plan de cuenta comun, no se puede eliminar.");';                
				}
			?>
		</script>			

		<?php include 'footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
		<script src="../js/funciones.js"></script>
		<script>

			function handleFetchErrors(response) {
				if (!response.ok) {
					throw Error(response.statusText);
				}
				return response.json();
			}

			function ingresarContador(e) {
				e.preventDefault();

				const rut = document.getElementById("rut").value;
				const nombre = document.getElementById("nombre").value;
				const cargo = document.getElementById("cargo").value;

				const contadorData = {
					rut: rut,
					nombre: nombre,
					cargo: cargo
				};

				const idmod = document.getElementById("idmod").value;
				if(idmod !== "") contadorData.idmod = idmod;

				const action = idmod === "" ? "ingresarContador" : "modificarContador";

				fetch(`router/router.php?action=${action}`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify(contadorData)
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					if(data.success) {
						mostrarMensaje(data.message, "success");
						limpiarFormulario();
					}else if(data.error) {
						mostrarMensaje(data.message, "error");
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});

			}

			function cargarContadores(buscar = "") {
				let url;

				if(buscar) {
					url = "router/router.php?action=cargarContadores&buscar=" + buscar;
				} else {
					url = "router/router.php?action=cargarContadores";
				}
				
				fetch(url, {
					method: "GET",
					headers: {
						'Content-Type': 'application/json',
					}
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					if(data.success) {
						const myTable = document.getElementById("myTable");
						myTable.innerHTML = "";

						if(data.contadores.length === 0) {
							return;
						}else{
							const clase = "px-6 py-2 whitespace-nowrap text-sm text-gray-900";
							data.contadores.forEach(contador => {
								const tr = document.createElement("tr");
								tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";
								myTable.appendChild(tr);

								const tdRut = document.createElement("td");
								tdRut.className = clase;
								tdRut.textContent = contador.Rut;
								tr.appendChild(tdRut);

								const tdNombre = document.createElement("td");
								tdNombre.className = clase;
								tdNombre.textContent = contador.Nombre;
								tr.appendChild(tdNombre);

								const tdCargo = document.createElement("td");
								tdCargo.className = clase;
								tdCargo.textContent = contador.Cargo;
								tr.appendChild(tdCargo);

								const tdAcciones = document.createElement("td");
								tdAcciones.className = clase;
								tr.appendChild(tdAcciones);

								const divAcciones = document.createElement("div");
								divAcciones.className = "flex space-x-2";
								tdAcciones.appendChild(divAcciones);

								const btnModificar = document.createElement("button");
								btnModificar.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
								btnModificar.innerHTML = "<i class='fa fa-edit mr-1'></i>Modificar";
								btnModificar.addEventListener("click", function() {
									document.getElementById("idmod").value = contador.Id;
									document.getElementById("rut").value = contador.Rut;
									document.getElementById("nombre").value = contador.Nombre;
									document.getElementById("cargo").value = contador.Cargo;
									document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Modificar";

									const btnEliminar = document.getElementById("btnEliminar");
									btnEliminar.hidden = false;

									const permiso = verificarPermisos();
									if(permiso) {
										btnEliminar.addEventListener("click", function() {
											eliminarContador(contador.Id);
										});
									} else {
										deshabilitarBoton("btnEliminar");
										btnEliminar.addEventListener("click", function() {
											mostrarMensaje("No tienes permisos para eliminar este contador", "error");
										});
									}
								

									const closeButton = document.querySelector("[data-modal-hide='default-modal']");
									if (closeButton) closeButton.click();
								});

								divAcciones.appendChild(btnModificar);

								const btnEstado = document.createElement("button");
								btnEstado.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
								
								const estadoTexto = contador.Estado === "A" ? "Alta" : "Baja";
								const estadoIcono = contador.Estado === "A" ? "fa-check" : "fa-ban";
								
								btnEstado.innerHTML = `<i class='fa ${estadoIcono} mr-1'></i>${estadoTexto}`;
								btnEstado.addEventListener("click", function() {
									estadoContador(contador.Id);
								});
								divAcciones.appendChild(btnEstado);

							});
						}
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function eliminarContador(id) {
				fetch(`router/router.php?action=eliminarContador`, {
					method: "POST",
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ id: id })
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					if(data.success) {
						mostrarMensaje(data.message, "success");
					}else if(data.error) {
						mostrarMensaje(data.message, "error");
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function verificarPermisos() {
				fetch(`router/router.php?action=verificarPermisos`, {
					method: "GET",
					headers: {
						'Content-Type': 'application/json',
					}
				})
				.then(handleFetchErrors)
				.then(data => {
					return data.permiso;
				});
			}

			function deshabilitarBoton(buttonId) {
				const button = document.getElementById(buttonId);
				if (button) {
					button.disabled = true;
					button.className = "bg-gray-100 cursor-not-allowed opacity-50 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
				}
			}

			function estadoContador(id) {
				fetch(`router/router.php?action=estadoContador`, {
					method: "POST",
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ id: id })
				})
				.then(handleFetchErrors)
				.then(data => {
					// console.log(data);
					cargarContadores();
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function limpiarFormulario(){
				document.getElementById("rut").value = "";
				document.getElementById("nombre").value = "";
				document.getElementById("cargo").value = "";
				document.getElementById("idempb").value = "";
				document.getElementById("idempa").value = "";
				document.getElementById("ideli").value = "";
				document.getElementById("idmod").value = "";

				document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";

				const btnEliminar = document.getElementById("btnEliminar");
				btnEliminar.hidden = true;
				btnEliminar.onclick = null;

				document.getElementById("idmod").value = "";
			}

			document.addEventListener("DOMContentLoaded", function() {

				const btnBuscar = document.getElementById("btnBuscar");
				btnBuscar.addEventListener("click", function() {
					cargarContadores();

					const myInput = document.getElementById("myInput");
					
					setTimeout(() => {
						myInput.focus();
					}, 100);
					
					myInput.addEventListener("input", function() {
						cargarContadores(myInput.value);
					});

				});

				document.getElementById("form1").addEventListener("submit", ingresarContador);
			});

		</script>
	</body>
</html>
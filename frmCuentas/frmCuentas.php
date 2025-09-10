<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SWCTA=0;
	$SQL="SELECT * FROM CTCategoria WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro["N1"]==0) {
			$SWCTA=1;
		}
	}	
	
	if ($SWCTA==1) {
		$SQL="SELECT * FROM CTCategoria WHERE estado='A'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
		
			$SQL1="SELECT * FROM CTCuentas WHERE id_categoria='".$registro["id"]."' AND estado='A' LIMIT 1";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$d1=substr($registro1["numero"],0,1);
				$d2=substr($registro1["numero"],1,1);

				$scr="UPDATE CTCategoria SET N1='".$d1."', N2='".$d2."' WHERE id='".$registro["id"]."';";
				$mysqli->query($scr);

			}
		}
	}

	$mysqli->close();

	// $sw=0;
	// $xauxiliar="O";
	// $sw1=0;
	// if(isset($_POST['idmod']) && $_POST['idmod']!=""){
	// 	$sw=1;
		// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		// if ($_SESSION["PLAN"]=="S") {
		// 	$SQL="SELECT * FROM CTCuentasEmpresa WHERE id='".$_POST['idmod']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		// }else{
		// 	$SQL="SELECT * FROM CTCuentas WHERE id='".$_POST['idmod']."'";
		// }

		// $resultados = $mysqli->query($SQL);
		// while ($registro = $resultados->fetch_assoc()) {
		// 	$xnumero=$registro["numero"];
		// 	$xdetalle=strtoupper($registro["detalle"]);
		// 	$xidcategoria=$registro["id_categoria"];
		// 	$xauxiliar=$registro["auxiliar"];
		// 	if ($registro["ingreso"]=="S"){
		// 		$sw1=1;
		// 	}
		// } 
		// $mysqli->close();
	// }

	// if (isset($_POST['idempb']) && $_POST['idempb']!="") {
	// 	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// 	if ($_SESSION["PLAN"]=="S") {
	// 		$mysqli->query("UPDATE CTCuentasEmpresa SET estado='B' WHERE id='".$_POST['idempb']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
	// 	}else{
	// 		$mysqli->query("UPDATE CTCuentas SET estado='B' WHERE id='".$_POST['idempb']."'");
	// 	}
	// 	$mysqli->close();
	// }

	// if (isset($_POST['idempa']) && $_POST['idempa']!="") {
	// 	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// 	if ($_SESSION["PLAN"]=="S"){
	// 		$mysqli->query("UPDATE CTCuentasEmpresa SET estado='A' WHERE id='".$_POST['idempa']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
	// 	}else{
	// 		$mysqli->query("UPDATE CTCuentas SET estado='A' WHERE id='".$_POST['idempa']."'");
	// 	}
	// 	$mysqli->close();
	// }


	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTIngresoEgreso WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		$SQL="SELECT * FROM CTIngresoEgreso WHERE estado='A'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {		
			$mysqli->query("UPDATE CTCuentasEmpresa SET ingreso='S' WHERE numero='".$registro['cuenta']."' AND estado='A'");
			$mysqli->query("UPDATE CTCuentas SET ingreso='S' WHERE numero='".$registro['cuenta']."' AND estado='A'");
		}
		$mysqli->query("DELETE FROM CTIngresoEgreso");
	}
?> 
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
		<script src="../js/jquery.min.js"></script>
		<!-- <script src="js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<!-- CSS personalizado - cargado después de Tailwind para evitar conflictos -->
		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">

			function Grilla(){
				var url= "frmCuentasGrilla.php";
				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					// $('#TableCta').html(resp);
					$("#contenidoTablaModal").html(resp);
				}
				});				
			}

			function Volver(){
				form1.action="frmMain.php";
				form1.submit();
			}
			function GenLibro(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmCuentasXLS.php";
				form1.submit();
				form1.target="";
				form1.action="#";
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

				<form method="POST" name="form1" id="form1">
					<!-- Hidden inputs -->
					<input type="hidden" name="idempb" id="idempb">
					<input type="hidden" name="idempa" id="idempa">
					<input type="hidden" name="ideli" id="ideli">
					<input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">

					<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-6">
						<button type="button" 
								class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
								onclick="limpiarFormulario()">
							<i class="fa fa-plus mr-2"></i>Nueva
						</button>

						<button type="submit" id="btnGrabar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-save mr-2"></i>Grabar
						</button>

						<button type="button" id="btnEliminar" hidden class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-trash mr-2"></i>Eliminar
						</button>

						<button id="btnBuscar" data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" type="button">
							<i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
						</button>

						<button type="button" 
							class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
							onclick="GenLibro()">
							<i class="fa fa-file-excel-o mr-2"></i>Exportar Excel
						</button>   

						<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
								onclick="Volver()">
							<i class="fa fa-times mr-2"></i>Cancelar
						</button>
					</div>

					<!-- Main Form Card -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fas fa-list-alt text-lg text-blue-600"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-800">
									Mantenedor de Cuentas
								</h3>
								<p class="text-sm text-gray-600">Gestión de cuentas contables</p>     
							</div>
						</div>
						<div class="p-6 pt-1 space-y-6">

							<div class="mt-3" id="divAlertas"></div>

							<!-- Category Selection -->
							<div class="grid grid-cols-1 md:grid-cols-1 gap-6">
								<div class="mt-3">
									<label for="SelCat" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-tags mr-1"></i>Categoría
									</label>
									<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="SelCat" name="SelCat" required>
										<option value="">Seleccione</option>
									</select>
								</div>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<div>
									<label for="numero" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-hashtag mr-1"></i>Número
									</label>
									<input type="text" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="numero" 
										   name="numero" 
										   autocomplete="off"
										   required>
								</div>

								<div>
									<label for="nombre" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-font mr-1"></i>Nombre
									</label>
									<input type="text" 
										   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
										   id="nombre" 
										   name="nombre" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   autocomplete="off" 
										   required>
								</div>
							</div>
							</div>
						</div>
					</div>

					<!-- Control Cards Row -->
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
						<!-- Income/Expense Control Card -->
						<div class="bg-white rounded-lg shadow-sm border border-gray-200">
							<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
								<div class="w-10 h-10 bg-green-100 rounded-lg flex justify-center items-center mr-4">
									<i class="fas fa-exchange-alt text-lg text-green-600"></i>
								</div>
								<div>
									<h3 class="text-lg font-semibold text-gray-800">
										Control de Ingreso o Egreso
									</h3>
									<p class="text-sm text-gray-600">Tipo de cuenta</p>     
								</div>
							</div>
							<div class="p-6 pt-1 space-y-4 mt-3">
								<div class="flex items-center">
									<input type="radio" id="ingreso_si" name="t1" value="S" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="ingreso_si" class="ml-2 text-sm font-medium text-gray-700">Sí</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="ingreso_no" name="t1" value="N" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="ingreso_no" class="ml-2 text-sm font-medium text-gray-700">No</label>
								</div>
							</div>
						</div>

						<!-- Auxiliary Control Card -->
						<div class="bg-white rounded-lg shadow-sm border border-gray-200">
							<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
								<div class="w-10 h-10 bg-purple-100 rounded-lg flex justify-center items-center mr-4">
									<i class="fas fa-cogs text-lg text-purple-600"></i>
								</div>
								<div>
									<h3 class="text-lg font-semibold text-gray-800">
										Control Auxiliar
									</h3>
									<p class="text-sm text-gray-600">Tipo de control</p>     
								</div>
							</div>
							<div class="p-6 pt-1 space-y-4 mt-5">
								<div class="flex items-center">
									<input type="radio" id="aux_x" name="opt1" value="X" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="aux_x" class="ml-2 text-sm font-medium text-gray-700">Auxiliar</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="aux_e" name="opt1" value="E" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="aux_e" class="ml-2 text-sm font-medium text-gray-700">Efectivo</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="aux_b" name="opt1" value="B" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="aux_b" class="ml-2 text-sm font-medium text-gray-700">Banco</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="aux_n" name="opt1" value="N" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="aux_n" class="ml-2 text-sm font-medium text-gray-700">No Aplica</label>
								</div>
							</div>
						</div>
					</div>


				</form>
				</div>
			</div>
		</div>
		<script>
			<?php
				if (isset($_GET['ex']) && $_GET['ex']=="yes") {
					echo 'alert ("Numero de cuenta ya registrada");';
				}
				if ($NoElimina=="N") {
					echo 'alert ("Esta cuenta tiene movimientos, no se puede eliminar.");';
				}
				if ($NoEliminaCom=="N") {
					echo 'alert ("Esta cuenta tiene movimientos y puede estar utilizada en alguna empresa, ya que es plan de cuenta comun, no se puede eliminar.");';                
				}
			?>
		</script>	
		
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
									Cuentas Creadas
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
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider" width="10%">Codigo</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Cuenta</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">T&iacute;po</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Categor&iacute;a</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Ingreso/Egreso</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Auxiliar</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider" width="1%">Acciones</th>
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


		<?php include 'footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
		<script src="../js/alertas.js"></script>

		<script>

			function handleFetchErrors(response) {
				if (!response.ok) {
					throw Error(response.statusText);
				}
				return response.json();
			}

			function cargarCategorias() {
				fetch("router/router.php?action=cargarCategorias", {
					method: "GET",
					headers: {
						'Content-Type': 'application/json',
					}
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					if (data.success) {
						const select = document.getElementById("SelCat");
						select.innerHTML = "<option value=''>Seleccione</option>";
						
						data.categorias.forEach(categoria => {
							const option = document.createElement("option");
							option.value = categoria.id;
							option.textContent = categoria.nombre;
							select.appendChild(option);
						});
					} else {
						console.error("Error del servidor:", data.message);
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function ctaCont() {
				const select = document.getElementById("SelCat").value;
				
				fetch("router/router.php?action=ctaCont", {
					method: "POST",
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ id: select })
				})
				.then(handleFetchErrors)
				.then(data => {
					if(data.success) {
						const numero = document.getElementById("numero");
						numero.value = data.cta;
					} else {
						console.error("Error:", data.message);
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}
			
			function ingresarCuenta(e) {
				e.preventDefault();

				const selCat = document.getElementById("SelCat").value;
				const numero = document.getElementById("numero").value;
				const nombre = document.getElementById("nombre").value;
				const opt1 = document.querySelector('input[name="opt1"]:checked').value;
				const t1 = document.querySelector('input[name="t1"]:checked').value;
				

				const cuentaData = {
					selCat: selCat,
					numero: numero,
					nombre: nombre,
					opt1: opt1,
					t1: t1
				};

				const idmod = document.getElementById("idmod").value;
				if(idmod !== "") cuentaData.idmod = idmod;

				const action = idmod === "" ? "ingresarCuenta" : "modificarCuenta";

				fetch(`router/router.php?action=${action}`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify(cuentaData)
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					if(data.success) {
						mostrarMensaje(data.message, "success");
						limpiarFormulario();	
					}else{
						mostrarMensaje(data.message, "error");
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function cargarCuentas(buscar = "") {

				const myTable = document.getElementById("myTable");
				const myInput = document.getElementById("myInput");

				let url;

				if(buscar) {
					url = "router/router.php?action=cargarCuentas&buscar=" + buscar;
				} else {
					url = "router/router.php?action=cargarCuentas";
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
					if (data.success) {
						const clase = "px-6 py-2 whitespace-nowrap text-sm text-gray-900";
						if(data.cuentas.length === 0) {
							return;
						}else{
							myTable.innerHTML = "";
							data.cuentas.forEach(cuenta => {
								const tr = document.createElement("tr");
								tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";
								myTable.appendChild(tr);

								const tdNumero = document.createElement("td");
								tdNumero.className = clase;
								tdNumero.textContent = cuenta.numero;
								tr.appendChild(tdNumero);

								const tdDetalle = document.createElement("td");
								tdDetalle.className = clase;
								tdDetalle.textContent = cuenta.detalle;
								tr.appendChild(tdDetalle);

								const tdTipo = document.createElement("td");
								tdTipo.className = clase;
								tdTipo.textContent = cuenta.tipTipo;
								tr.appendChild(tdTipo);

								const tdCategoria = document.createElement("td");
								tdCategoria.className = clase;
								tdCategoria.textContent = cuenta.tipCat;
								tr.appendChild(tdCategoria);

								const tdIngresoEgreso = document.createElement("td");
								tdIngresoEgreso.className = clase;
								tdIngresoEgreso.textContent = cuenta.mens;
								tr.appendChild(tdIngresoEgreso);

								const tdAuxiliar = document.createElement("td");
								tdAuxiliar.className = clase;
								tdAuxiliar.textContent = cuenta.auxiliar;
								tr.appendChild(tdAuxiliar);

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
									// document.getElementById("SelCat").value = cuenta.id_categoria;
									// document.getElementById("idmod").value = cuenta.id;
									// document.getElementById("numero").value = cuenta.numero;
									// document.getElementById("nombre").value = cuenta.detalle;
									document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Modificar";
									editarCuenta(cuenta.id);
									
									const radioAux = document.querySelector(`input[name="opt1"][value="${cuenta.opt1}"]`);
									if (radioAux) radioAux.checked = true;
									
									const radioIngreso = document.querySelector(`input[name="t1"][value="${cuenta.t1}"]`);
									if (radioIngreso) radioIngreso.checked = true;

									const btnEliminar = document.getElementById("btnEliminar");
									btnEliminar.hidden = false;
									btnEliminar.onclick = function() {
										eliminarCuenta(cuenta.id);
									};

									const closeButton = document.querySelector("[data-modal-hide='default-modal']");
									if (closeButton) closeButton.click();
								});

								divAcciones.appendChild(btnModificar);

								const btnEstado = document.createElement("button");
								btnEstado.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
								
								const estadoTexto = cuenta.estado === "A" ? "Alta" : "Baja";
								const estadoIcono = cuenta.estado === "A" ? "fa-check" : "fa-ban";
								
								btnEstado.innerHTML = `<i class="fa ${estadoIcono} mr-1"></i>${estadoTexto}`;
								btnEstado.id = `btnEstado_${cuenta.id}`;
								btnEstado.addEventListener("click", function() {
									estadoCuenta(cuenta.id, btnEstado.id);
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

			function editarCuenta(id) {
				fetch(`router/router.php?action=obtenerCuenta`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ id: id })
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					if(data.success) {
						document.getElementById("idmod").value = id;
						document.getElementById("SelCat").value = data.cuenta.id_categoria;
						document.getElementById("numero").value = data.cuenta.numero;
						document.getElementById("nombre").value = data.cuenta.detalle;
						const radioAux = document.querySelector(`input[name="opt1"][value="${data.cuenta.opt1}"]`);
						if (radioAux) radioAux.checked = true;
									
						const radioIngreso = document.querySelector(`input[name="t1"][value="${data.cuenta.t1}"]`);
						if (radioIngreso) radioIngreso.checked = true;
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function eliminarCuenta(id) {
				fetch(`router/router.php?action=eliminarCuenta`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ id: id })
				})
				.then(handleFetchErrors)
				.then(data => {
					if(data.success) {
						mostrarMensaje(data.message, "success");
					}else{
						mostrarMensaje(data.message, "error");
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function estadoCuenta(id, buttonId) {
				fetch(`router/router.php?action=estadoCuenta`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ id: id })
				})
				.then(handleFetchErrors)
				.then(data => {
					data.estado = data.estado === "A" ? "Alta" : "Baja";

					if(data.estado === "Alta") {
						document.getElementById(buttonId).innerHTML = `<i class="fa fa-check mr-1"></i>${data.estado}`;
					}else{
						document.getElementById(buttonId).innerHTML = `<i class="fa fa-ban mr-1"></i>${data.estado}`;
					}
				})
				.catch(error => {
					console.error("Error:", error);
				});
			}

			function limpiarFormulario() {
				document.getElementById("SelCat").value = "";
				document.getElementById("numero").value = "";
				document.getElementById("nombre").value = "";
				document.getElementById("ingreso_no").checked = true;
				document.getElementById("aux_n").checked = true;
				document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";
				// Reset form action
				// form1.action = "xfrmCuentas.php";
				form1.idmod.value = "";
			}

			document.addEventListener("DOMContentLoaded", function() {
				cargarCategorias();

				const select = document.getElementById("SelCat");
				select.addEventListener("change", ctaCont);

				const btnBuscar = document.getElementById("btnBuscar");
				
				btnBuscar.addEventListener("click", function() {
					cargarCuentas();

					const myInput = document.getElementById("myInput");
					
					setTimeout(() => {
						myInput.focus();
					}, 100);

					myInput.addEventListener("input", function() {
						cargarCuentas(myInput.value);
					});
				});
			
				document.getElementById("form1").addEventListener("submit", ingresarCuenta);
			});

		</script>

		
	</body>
</html>
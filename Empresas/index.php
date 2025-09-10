<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../clases/clasesCss.php';
	// include '../conexion/secciones.php';
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../?Msj=95");
		exit;
	}

	$Periodo=$_SESSION['PERIODO'];

	if($Periodo!=""){
		echo "
		<script>
			alert('Para crear una empresa, debe salir de la actual.');
			location.href ='../frmMain.php';
		</script>
		";
		exit;
	}

	$rut = "";
	$dmes = "";
	$sw = 0;
	$rut = "";
	$razonsocial = "";
	$rutrep = "";
	$representante = "";
	$direccion = "";
	$giro = "";
	$ciudad = "";
	$correo = "";
	$pinicio = "";
	$pcomprobante = "";
	$pplancta = "";
	$fechainicio = "";
	$dmes = "";
	$dano = "";

	if(isset($_POST['idemp']) && $_POST['idemp']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL = "SELECT * FROM CTEmpresas WHERE id = ?";
		$stmt = $mysqli->prepare($SQL);
		$stmt->bind_param("i", $_POST['idemp']);
		$stmt->execute();
		$resultados = $stmt->get_result();

		if($resultados->num_rows == 0){
			print_r("Esta vacio");
			exit;
		}

		while ($registro = $resultados->fetch_assoc()) {
			
			$rut=$registro["rut"];
			$razonsocial=$registro["razonsocial"];
			$rutrep=$registro["rut_representante"];
			$representante=$registro["representante"];
			$direccion=$registro["direccion"];
			$giro=$registro["giro"];
			$ciudad=$registro["ciudad"];
			$correo=$registro["correo"];
			$pinicio=$registro["periodo"];
			$pcomprobante=$registro["comprobante"];
			$pplancta=$registro["plan"];
			if($registro["fechainicio"]=="1969-12-31" || $registro["fechainicio"]=="0000-00-00"){	
				$fechainicio="";
			}else{
				$fechainicio=date('d-m-Y', strtotime($registro["fechainicio"]));
			}
		}

		$swcom=0;
		$SQL="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$rut'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$swcom=3;
		}

		$SQL="SELECT * FROM CTRegDocumentos WHERE rutempresa='$rut'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$swcom=3;
		}

		$dmes = substr($pinicio,0,2);
		$dmes = $dmes*1;
		$dano = substr($pinicio,3,4);
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
		<script src="../js/jquery.min.js"></script>

		<!-- tailwind css -->
		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />


		<script src="../js/jquery.Rut.js" type="text/javascript"></script>	
		<script src="../js/jquery.validate.js" type="text/javascript"></script>

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>
		

		<script type="text/javascript">

			function Volver(){
				form1.action="../frmMain.php";
				form1.submit();
			}

			$(document).ready(function(){
				$('#rut').Rut({ 
					on_error: function(){RutMal(); $('#rut').val(""); $('#rut').focus();} 
				});
				$('#rutrep').Rut({ 
					on_error: function(){RutMal(); $('#rutrep').val(""); $('#rutrep').focus();} 
				});
			});

			function ExportCSV(){
				form1.action="frmEmpresasCSV.php";
				form1.submit();
			}

			function NumYGuion(e){
				var key = window.Event ? e.which : e.keyCode
					return (key >= 48 && key <= 57 || key == 45 || key==75 || key==107)
			}


			function SinInfo(r1){
				alert(r1);				
			}

			function RutMal(){
				alert('El Rut ingresado es incorrecto, favor validar e intentar nuevamente.');				
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

				<form action="xfrmEmpresas.php" method="POST" name="form1" id="form1" class="space-y-8">

					<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2">
						<button type="button" 
								class="bg-slate-100 text-sm hover:bg-blue-200 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" 
								onclick="limpiarFormulario()">
							<i class="fa fa-plus mr-2"></i>Nueva
						</button>
						
						<button type="submit" id="btnGrabar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" tabindex="15">
							<i class="fa fa-save mr-2"></i>Grabar
						</button>
							

						<button type="button" hidden id="btnEliminar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" tabindex="16">
							<i class="fa fa-trash mr-2"></i>Eliminar
						</button>

						<button type="button" id="btnBuscar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
								data-modal-target="searchModal" 
								data-modal-toggle="searchModal">
							<i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
						</button>
						<button id="btnImprimir" type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa-solid fa-print text-gray-600 mr-2"></i>Imprimir
						</button>

						<button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-black bg-gray-100 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg transition duration-200 text-sm px-2 py-1 border-2 border-gray-600 text-center inline-flex items-center" type="button">
							<i class="fa-solid fa-download mr-2"></i>Exportar
							<i class="fa-solid fa-chevron-down ml-2.5"></i>
						</button>

						<!-- Dropdown menu -->
						<div id="dropdown" class="z-10 hidden bg-gray-100 divide-y divide-gray-100 rounded-lg shadow-sm w-44">
							<ul class="py-2 text-sm text-gray-200" aria-labelledby="dropdownDefaultButton">
								<li>
									<button type="button" onclick="ExportCSV()" class="w-full block px-4 py-2 hover:bg-gray-300 text-black text-left font-medium">
										<i class="fa-solid fa-file-csv mr-2"></i>CSV
									</button>
								</li>
								<li>
									<a href="#" class="w-full block px-4 py-2 hover:bg-gray-300 text-black text-left font-medium">
										<i class="fa-solid fa-file-pdf mr-2"></i>PDF
									</a>
								</li>
							</ul>
						</div>

						<button type="button" id="btnCancelar" 
								class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
								onclick="Volver()">
							<i class="fa fa-times mr-2"></i>Cancelar
						</button>
					</div>
					
					<!-- Company Information Card -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fas fa-building text-lg text-blue-600"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-800">
									Información de la empresa
								</h3>
								<p class="text-sm text-gray-600">Datos para ingresar una empresa</p>     
							</div>
							
                           
                    	</div>
						<div class="p-6 pt-1 space-y-6">

							<!-- Hidden inputs -->
							<input type="hidden" name="idemp" id="idemp">
							<input type="hidden" name="txtCambioEstado" id="txtCambioEstado">
							<input type="hidden" name="idempa" id="idempa">
							<input type="hidden" name="eliemp" id="eliemp">
							<input type="hidden" name="elirut" id="elirut">

							<div id="divAlertas">
								<div class="border-l-4 p-2.5 mb-6 hidden" id="divMensaje">
									<div class="flex items-center">
										<div class="flex-shrink-0" id="iconoMensaje">
											<i class="fas fa-file-invoice"></i>
										</div>
										<div class="ml-3">
											<p class="text-sm font-semibold" id="textoMensaje"></p>
										</div>
									</div>
								</div>
							</div>
							

							<!-- First Row: RUT and SII -->
							<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-2">
								<div>
									<label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-id-card mr-1"></i>RUT
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   id="rut" 
										   autocomplete="off" 
										   name="rut" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   onKeyPress="return NumYGuion(event)" 
										   maxlength="10" 
										   placeholder="Ej. 96900500-1" 
										   value="<?php echo $rut; ?>" 
										   <?php if($sw==1){ echo 'readonly="false"';} ?> 
										   >
								</div>

								<div>
									<label for="clasii" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-key mr-1"></i>Clave SII
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   autocomplete="off" 
										   id="clasii" 
										   name="clasii" 
										   value="">
								</div>

								<div class="flex items-end">
									<button id="btnSincronizar" type="button" 
											class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" 
											onclick="sincronizarSII()">
											
										<i class="fa fa-sync mr-2"></i><span id="textoSincronizar">Sincronizar</span>
									</button>
								</div>
							</div>

							<!-- Second Row: Company Name and Constitution Date -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<div>
									<label for="rsocial" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-building mr-1"></i>Razón Social
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   autocomplete="off" 
										   id="rsocial" 
										   name="rsocial" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $razonsocial; ?>" 
										   >
								</div>

								<div>
									<label for="finicio" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-calendar mr-1"></i>Fecha Constitución
									</label>
									<input type="date" 
										   class="<?php input_css(); ?> text-right"  
										   autocomplete="off" 
										   id="finicio" 
										   name="finicio" 
										   value="<?php echo $fechainicio; ?>">
								</div>
							</div>

							<!-- Third Row: Representative RUT and Legal Representative -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<div>
									<label for="rutrep" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-user mr-1"></i>RUT Representante
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   id="rutrep" 
										   autocomplete="off" 
										   name="rutrep" 
										   onKeyPress="return NumYGuion(event)" 
										   maxlength="10" 
										   placeholder="Ej. 96900500-1" 
										   value="<?php echo $rutrep; ?>" 
										   >
								</div>

								<div>
									<label for="representante" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-user-tie mr-1"></i>Representante Legal
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   autocomplete="off" 
										   id="representante" 
										   name="representante" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $representante; ?>" 
										   >
								</div>
							</div>

							<!-- Fourth Row: Address, Business Line, City, Email -->
							<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
								<div>
									<label for="direccion" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-map-marker mr-1"></i>Dirección
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   autocomplete="off" 
										   id="direccion" 
										   name="direccion" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $direccion; ?>" 
										   >
								</div>

								<div>
									<label for="giro" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-briefcase mr-1"></i>Giro
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   autocomplete="off" 
										   id="giro" 
										   name="giro" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $giro; ?>" 
										   >
								</div>

								<div>
									<label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-city mr-1"></i>Ciudad
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   id="ciudad" 
										   autocomplete="off" 
										   name="ciudad" 
										   maxlength="50" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   value="<?php echo $ciudad; ?>" 
										   >
								</div>

								<div>
									<label for="correo" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-envelope mr-1"></i>Correo
									</label>
									<input type="email" 
										   class="<?php input_css(); ?>" 
										   id="correo" 
										   autocomplete="off" 
										   name="correo" 
										   maxlength="50" 
										   value="<?php echo $correo; ?>">
								</div>
							</div>
						</div>
					</div>

					<!-- Accounting Configuration Card -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fas fa-calculator text-lg text-blue-600"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-800">
									Contabilización
								</h3>
								<!-- <p class="text-sm text-gray-600">Datos para ingresar una empresa</p>      -->
							</div>
                           
                    	</div>
						<div class="p-6">
							<input type="hidden" name="SeleMes" id="SeleMes" value="<?php if($dmes==""){echo date('n');}else{ echo $dmes;} ?>" />
							<input type="hidden" name="SeleAno" id="SeleAno" value="<?php  if($dano==""){echo date('Y');}else{ echo $dano;}  ?>" />

							<!-- <div class="max-w-lg"> -->
								<label for="plancta" class="block text-sm font-medium text-gray-700 mb-2">
									<i class="fa fa-list mr-1"></i>Plan de Cuenta
								</label>
								<select class="<?php input_css(); ?>" 
										id="plancta" 
										name="plancta" 
										<?php if ($pplancta=="S") { echo "disabled"; } ?>>
									<option value="">Seleccione</option>
									<option value="N" <?php if ($pplancta=="N") { echo "selected"; } ?>>Común</option>
									<option value="S" <?php if ($pplancta=="S") { echo "selected"; } ?>>Individual</option>
								</select>
								<p class="mt-2 text-sm text-gray-600">
									<i class="fa fa-info-circle mr-1"></i>
									El Plan Individual realizará una copia del Plan Común para su personalización
								</p>
							<!-- </div> -->
						</div>
					</div>

					<?php if(isset($MsjBloqueo) && $MsjBloqueo != ""): ?>
						<div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
							<div class="flex">
								<div class="flex-shrink-0">
									<i class="fa fa-exclamation-triangle text-red-400"></i>
								</div>
								<div class="ml-3">
									<p class="text-sm text-red-700">
										<?php echo $MsjBloqueo; ?>
									</p>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</form>

				

					<div id="searchModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
			<div class="relative p-4 w-full max-w-7xl max-h-full">
				<div class="relative bg-white rounded-lg shadow">
					<div class="p-4 md:p-5">
						
					<div class="bg-gray-100 rounded-lg shadow-sm border border-gray-200">
					<div class="px-6 py-4 border-b border-gray-200">
						<div class="flex items-center justify-between">
							<div class="flex items-center">
								<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
									<i class="fa fa-list text-lg text-primary-500"></i>
								</div>
								<div>
									<h3 class="text-lg font-semibold text-gray-900 flex items-center">
										Empresas Creadas
									</h3>	
									<p class="text-sm text-gray-600" id="msgEmpresa"></p>
								</div>	
							</div>

							
                           <div class="flex justify-end gap-3">
								<a href="#" 
								   onclick="ExportCSV()" 
								   class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
									<i class="fa fa-download mr-2"></i>Descargar
								</a>
								<a href="frmEmpresasImport.php" 
								   class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
									<i class="fa fa-upload mr-2"></i>Importar Masivo
								</a>
								<button
									data-modal-hide="searchModal" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
									<i class="fa fa-xmark mr-2"></i>Cerrar
								</button>
							</div>
                    	</div>
							
						</div>
					</div>
					<div class="p-6">
						<div class="mb-4">
							<div class="relative">
								<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
									<i class="fa fa-search text-gray-400"></i>
								</div>
								<input class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" 
									   id="myInput" 
									   type="text" 
									   placeholder="Buscar empresas...">
							</div>
						</div>
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RUT</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Razón Social</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Activo</th>
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
		</div>


				</div>
			</div>
		</div>

			<script type="text/javascript">
				<?php 
					if($sw==3){
						echo 'alert("Esta empresa cuenta con registros en sistema, no se puede eliminar solo se puede dar de baja"); form1.eliemp.value="";';
					}
				?>
			</script>

		</div>
		</div>

		
		

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

			function sincronizarSII() {
				const url = "DatosSII.php";
				// const btnSincronizar = document.getElementById("btnSincronizar");
				// const textoSincronizar = document.getElementById("textoSincronizar");

				const rut = document.getElementById("rut").value;
				const clasii = document.getElementById("clasii").value;

				if(rut === "" || clasii === "") {
					mostrarMensaje("Por favor, ingrese el RUT y la Clave SII", "warning");
					return;
				}
				
				const datosSII = {
					rut: rut,
					clasii: clasii
				};

				const btnSincronizar = document.getElementById("btnSincronizar");
				const textoSincronizar = document.getElementById("textoSincronizar");

				btnSincronizar.classList.remove("bg-primary-500", "hover:bg-blue-600");
				btnSincronizar.classList.add("bg-blue-300", "opacity-75", "cursor-wait");
				btnSincronizar.disabled = true;
				textoSincronizar.textContent = "Sincronizando...";
				
				// Agregar animación de rotación al icono
				const icono = btnSincronizar.querySelector('i');
				icono.classList.add('animate-spin');

				fetch(url, {
					method: "POST",
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(datosSII)
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					document.getElementById("rsocial").value = data.razonSocial;
					document.getElementById("representante").value = data.RazonRepresentante;
					document.getElementById("finicio").value = data.fechaConstitucion;
					document.getElementById("correo").value = data.eMail;
					document.getElementById("ciudad").value = data.ciudad;
					document.getElementById("direccion").value = data.calle;
					document.getElementById("rutrep").value = data.rRepresentante;
					document.getElementById("giro").value = data.glosaActividad;

					setTimeout(() => {
						btnSincronizar.classList.remove("bg-blue-300", "opacity-75", "cursor-wait")
						btnSincronizar.classList.add("bg-blue-500", "hover:bg-blue-600");;
						btnSincronizar.disabled = false;
						textoSincronizar.textContent = "Sincronizar";
						
						// Remover animación de rotación del icono
						const icono = btnSincronizar.querySelector('i');
						icono.classList.remove('animate-spin');
					}, 1000);
				})
				.catch(error => {
					console.error("Error:", error);
					console.log("Error al sincronizar con SII: " + error.message);
					
					// Restaurar botón y remover animación en caso de error
					btnSincronizar.classList.remove("bg-blue-300", "opacity-75", "cursor-wait");
					btnSincronizar.classList.add("bg-blue-500", "hover:bg-blue-600");
					btnSincronizar.disabled = false;
					textoSincronizar.textContent = "Sincronizar";
					
					const icono = btnSincronizar.querySelector('i');
					icono.classList.remove('animate-spin');
				});
				
				
			}

			function ingresarEmpresa(e) {
				e.preventDefault();
				
				const rut = document.getElementById("rut").value;
				const clasii = document.getElementById("clasii").value;
				const rsocial = document.getElementById("rsocial").value;
				const representante = document.getElementById("representante").value;
				const correo = document.getElementById("correo").value;
				const ciudad = document.getElementById("ciudad").value;
				const direccion = document.getElementById("direccion").value;
				const rutrep = document.getElementById("rutrep").value;
				const giro = document.getElementById("giro").value;
				const finicio = document.getElementById("finicio").value;
				const plancta = document.getElementById("plancta").value;

				const seleMes = document.getElementById("SeleMes").value;
				const seleAno = document.getElementById("SeleAno").value;

				const campos = ["rut", "rsocial", "representante", "ciudad", "direccion", "rutrep", "giro", "plancta"];

				const camposVacios = campos.some(campo => !document.getElementById(campo).value);

				if (camposVacios) {
					mostrarMensaje("Faltan datos", "info")
					return;
				}

				const idemp = document.getElementById("idemp").value;

				const empresaData = {
					rut: rut,
					clasii: clasii,
					rsocial: rsocial,
					representante: representante,
					correo: correo,
					ciudad: ciudad,
					direccion: direccion,
					rutrep: rutrep,
					giro: giro,
					finicio: finicio,
					plancta: plancta,
					seleMes: seleMes,
					seleAno: seleAno
				};

				if(idemp !== "") empresaData.idemp = idemp;

				const action = idemp === "" ? "ingresarEmpresa" : "modificarEmpresa";

				fetch(`router/router.php?action=${action}`, {
					method: "POST",
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(empresaData)
				})
				.then(handleFetchErrors)
				.then(data => {
					console.log(data);
					if (data.success) {
						console.log(data.mensaje);
						cargarEmpresas();
						limpiarFormulario();
						mostrarMensaje(data.mensaje, "success");
					} else if(data.error) {
						console.log(data.mensaje);
					} else {
						console.log("Error al procesar los datos: " + data.mensaje);
					}
				})
				.catch(error => {
					console.error("Error:", error);
					console.log("Error al procesar los datos: " + error.message);
				});
			}

			function cargarEmpresas(buscar = "") {

				const myInput = document.getElementById("myInput");

				let url;

				if(buscar) {
					url = "router/router.php?action=cargarEmpresas&buscar=" + buscar;
				} else {
					url = "router/router.php?action=cargarEmpresas";
				}
				
				const tabla = document.getElementById("myTable");

				fetch(url, {
					method: "GET",
					headers: {
						'Content-Type': 'application/json',
					}
				})
				.then(handleFetchErrors)
				.then(data => {
					const clase = "px-6 py-2 whitespace-nowrap text-sm text-gray-900";
					if(data.empresas.length === 0) {
						tabla.innerHTML = "";
						const tr = document.createElement("tr");
						tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";
						tr.innerHTML = `
							<td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">No se encontraron empresas</td>
						`;
						tabla.appendChild(tr);
					} else {
						tabla.innerHTML = "";
						let contador = 1;
						document.getElementById("msgEmpresa").textContent = data.msgEmpresa;
						data.empresas.forEach(empresa => {
							
							let xPlan = "";
							let xComprobante = "";

							const plan = empresa.plan;
							const comprobante = empresa.comprobante;

							if(plan === "S") {
								xPlan = "Individual";
							}else{
								xPlan = "Común";
							}

							if(comprobante === "S") {
								xComprobante = "Soporte Auxiliar";
							}else{
								xComprobante = "Tradicional";
							}

							const tr = document.createElement("tr");
							tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";

							const tdContador = document.createElement("td");
							tdContador.className = clase;
							tdContador.textContent = contador++;
							tr.appendChild(tdContador);

							const tdAcciones = document.createElement("td");
							tdAcciones.className = clase;
							tr.appendChild(tdAcciones);

							const divAcciones = document.createElement("div");
							divAcciones.className = "flex space-x-2";
							tdAcciones.appendChild(divAcciones);

							const btnModificar = document.createElement("button");
							btnModificar.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
							btnModificar.innerHTML = '<i class="fa fa-edit mr-1"></i>Modificar';
							btnModificar.onclick = function() {
								document.getElementById("idemp").value = empresa.id;
								document.getElementById("rut").value = empresa.rut;
								document.getElementById("rsocial").value = empresa.razonsocial;
								document.getElementById("finicio").value = empresa.fechainicio;
								document.getElementById("rutrep").value = empresa.rut_representante;
								document.getElementById("representante").value = empresa.representante;
								document.getElementById("ciudad").value = empresa.ciudad;
								document.getElementById("direccion").value = empresa.direccion;
								document.getElementById("giro").value = empresa.giro;
								document.getElementById("correo").value = empresa.correo;
								document.getElementById("plancta").value = empresa.plan;

								document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Modificar";

								const btnEliminar = document.getElementById("btnEliminar");
								btnEliminar.hidden = false;


								const permiso = verificarPermisos();
								if(permiso) {
									btnEliminar.onclick = function() {
										eliminarEmpresa(empresa.id, empresa.rut, empresa.razonsocial);
									}
								} else {
									deshabilitarBoton("btnEliminar");
									btnEliminar.onclick = function() {
										mostrarMensaje("No tienes permisos para eliminar esta empresa", "warning");
									}
								}

								const closeButton = document.querySelector("[data-modal-hide='searchModal']");
								if (closeButton) closeButton.click();

							};
							divAcciones.appendChild(btnModificar);

							const btnEstado = document.createElement("button");
							btnEstado.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
							
							const estadoTexto = empresa.estado === 'A' ? 'Activa' : 'Inactiva';
							const estadoIcono = empresa.estado === 'A' ? 'fa-check' : 'fa-ban';
							btnEstado.innerHTML = `<i class="fa ${estadoIcono} mr-1"></i>${estadoTexto}`;

							btnEstado.addEventListener("click", function() {
								cambiarEstado(empresa.id);
							});


							divAcciones.appendChild(btnEstado);

							const tdRut = document.createElement("td");
							tdRut.className = clase;
							tdRut.textContent = empresa.rut;
							tr.appendChild(tdRut);

							const tdRazonSocial = document.createElement("td");
							tdRazonSocial.className = clase;
							tdRazonSocial.textContent = empresa.razonsocial;
							tr.appendChild(tdRazonSocial);

							const tdPlan = document.createElement("td");
							tdPlan.className = clase;
							tdPlan.textContent = xPlan;
							tr.appendChild(tdPlan);

							tabla.appendChild(tr);
						});
					}
				})
				.catch(error => {
					console.error("Error:", error);
					console.log("Error al cargar las empresas: " + error.message);
				});
			}

			function eliminarEmpresa(id, rut, razonsocial) {
				
				const empresaDelete = {
					id: id,
					rut: rut,
					razonsocial: razonsocial
				};

				fetch(`router/router.php?action=eliminarEmpresa`, {
					method: "POST",
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(empresaDelete)
				})
				.then(handleFetchErrors)
				.then(data => {
					if(data.success) {
						console.log(data.mensaje);
						cargarEmpresas();
						limpiarFormulario();
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

			function cambiarEstado(idEmp) {
				fetch(`router/router.php?action=estadoEmpresa`, { 
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ idEmp: idEmp })
				})
					.then(handleFetchErrors)
					.then(data => {
						if (data.success) {
							console.log(data.mensaje);
							cargarEmpresas();

							const closeButton = document.querySelector("[data-modal-hide='searchModal']");
							if (closeButton) closeButton.click();
						} else if (data.error) {
							throw new Error(data.mensaje);
						}
					})
					.catch(error => {
						console.error('Error:', error);
						alert('Error al actualizar el estado: ' + error.message);
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

			function limpiarFormulario() {

				const campos = ["rut", "clasii", "rsocial", "representante", "correo", "ciudad", "direccion", "rutrep", "giro", "finicio", "plancta", "txtCambioEstado", "idemp", "idempa", "eliemp", "elirut"];
				campos.forEach(campo => {
					document.getElementById(campo).value = "";
				});

				document.getElementById("btnGrabar").className = "bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
				document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";

				const btnEliminar = document.getElementById("btnEliminar");
				btnEliminar.hidden = true;
				btnEliminar.onclick = null;
				btnEliminar.disabled = false;
				btnEliminar.className = "bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";

			}

			function deshabilitarBoton(buttonId) {
				const button = document.getElementById(buttonId);
				if (button) {
					button.disabled = true;
					button.className = "bg-gray-100 cursor-not-allowed opacity-50 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
				}
			}

			document.addEventListener("DOMContentLoaded", function() {

				const btnBuscar = document.getElementById("btnBuscar");
				btnBuscar.addEventListener("click", function() {
					cargarEmpresas();
					
					const myInput = document.getElementById("myInput");
					
					setTimeout(() => {
						myInput.focus();
					}, 100);
					
					myInput.addEventListener("input", function() {
						cargarEmpresas(myInput.value);
					});
					
					
				});

				document.getElementById("form1").addEventListener("submit", ingresarEmpresa);

			});


		</script>

	</body>
</html>


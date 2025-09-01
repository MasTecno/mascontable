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
		// header("location:frmMain.php");
		exit;
	}

	$rut = "";
	$dmes = "";
	if(isset($_POST['idemp']) && $_POST['idemp']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTEmpresas WHERE id='".$_POST['idemp']."'";
		$resultados = $mysqli->query($SQL);
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

	if (isset($_POST['idempb']) && $_POST['idempb']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTEmpresas SET estado='B' WHERE id='".$_POST['idempb']."'");
		$mysqli->close();
	}

	if (isset($_POST['idempa']) && $_POST['idempa']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTEmpresas SET estado='A' WHERE id='".$_POST['idempa']."'");
		$mysqli->close();
	}

	if(isset($_POST['eliemp']) && $_POST['eliemp']!=""){
		$SoloAdmin=0;
		if ($_SESSION['ROL']=="A"){
			$RutEliEmp=$_POST['elirut'];

			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


			$SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEliEmp'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$razonsocial=$registro['razonsocial'];
			}


			$mysqli->query("DELETE FROM CT14D WHERE RutEmpresa='$RutEliEmp';");

			$SQL="SELECT * FROM CT14TerCab WHERE rutempresa='$RutEliEmp'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$mysqli->query("DELETE FROM CT14TerDet WHERE idcab='".$registro['id']."';");
			}

			$mysqli->query("DELETE FROM CT14TerCab WHERE rutempresa='$RutEliEmp';");

			$mysqli->query("DELETE FROM CTAnticipos WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTAsiento WHERE rut_empresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTAsientoApertura WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTAsientoBolEle WHERE rut_empresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTAsientoFondo WHERE rut_empresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTAsientoHono WHERE rut_empresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTAsientoNoBase WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTBoletasDTE WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTCCosto WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTCliProCuenta WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTComprobanteFolio WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTConciliacionCab WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTConciliacionDet WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTConciliacionLog WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTContadoresAsignado WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTControRegDocPago WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTCuentas14Ter WHERE rut_empresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTCuentasEmpresa WHERE rut_empresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTEmpresas WHERE rut='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTEstResultadoDet WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTFondo WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTFondoPersonal WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTHonoGene WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTHonoGeneDeta WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTHonorarios WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTPeriodoEmpresa WHERE RutEmpresa='$RutEliEmp';");

			$SQL="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEliEmp'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$mysqli->query("DELETE FROM CTRegDocumentosDiv WHERE Id_Doc='".$registro['id']."';");
			}
			$mysqli->query("DELETE FROM CTRegDocumentos WHERE rutempresa='$RutEliEmp';");

			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTRegLibroDiarioCome WHERE rutempresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM CTVoucherT WHERE RutEmpresa='$RutEliEmp';");
			$mysqli->query("DELETE FROM DTEParametros WHERE RutEmpresa='$RutEliEmp';");


			$FECHA=date("Y-m-d");
			$mysqli->query("INSERT INTO CTEmpresasLog VALUES('','$RutEliEmp','$razonsocial','$FECHA','".date("H:i:s")."','".$_SESSION['NOMBRE']."');");

			$mysqli->close();
			
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
			
			$_POST['idemp'] = "";
			$_POST['idempb'] = "";
			$_POST['idempa'] = "";
			$_POST['eliemp'] = "";
			$_POST['elirut'] = "";
			
			// Mensaje de confirmación
			$MsjEliminacion = "Empresa eliminada exitosamente.";
		}else{
			$SoloAdmin=5;
		}
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT count(razonsocial) AS CantEmp FROM CTEmpresas WHERE estado<>'X' ORDER BY razonsocial";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$TotalEmpresa=$registro['CantEmp'];
	}
	$mysqli->close();


	$MsjEmpresa = $TotalEmpresa." de ".$_SESSION['PlanConta'];

	// $BloqueBtn="";
	// if($TotalEmpresa>$_SESSION['PlanConta']){
	// 	// 
	// 	$MsjBloqueo="<strong>Alcanz&oacute; el l&iacute;mite de empresas en su plan, puede eliminar empresas para ganar cupos, de lo contrario contactar a su soporte para el aumento de plan.</strong> 
	// 	<br> Por el momento es un mensaje para su conociemiento.";
	// }

	// Crear objeto DateTime para la fecha actual
	$fechaActual = new DateTime();

	// Crear objeto DateTime para la fecha especificada "01-01-2024"
	$fechaComparacion = new DateTime("2024-01-01");

	// Comparar las fechas
	if ($TotalEmpresa>$_SESSION['PlanConta'] && $fechaActual > $fechaComparacion) {
		$BloqueBtn="disabled";
		$MsjBloqueo="<strong>Alcanz&oacute; el l&iacute;mite de empresas en su plan, puede eliminar empresas para ganar cupos, de lo contrario contactar a su soporte para el aumento de plan.</strong>";
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
			function Modifi(id, rut, razonsocial){
				form1.idemp.value = id;
				form1.elirut.value = rut;
				form1.action="./";
				form1.submit();
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

			function Elim(c1,c2,c3){
				var r = confirm("Desea eliminar la empresa Rut: "+c2+", Razón Social: "+c3+".");
				if (r == true) {
					var r = confirm("Est\u00e1 eliminando toda la informaci\u00F3n y no se podr\u00e1 recuperar.\n\nConfirmar?");
					// var r = confirm("Esta consciente que esta operaci\u00F3n realizara una eliminaci\u00F3n completa de toda la informaci\u00F3n asociada a este Rut.\n\nConfirmar?");
					if (r == true) {
						form1.eliemp.value=c1;
						form1.elirut.value=c2;
						form1.action="#";
						form1.submit();
					}
				}
			}

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

			function SIIData(){
				var url = "DatosSII.php";

				const btnSincronizar = document.getElementById("btnSincronizar");
				const textoSincronizar = document.getElementById("textoSincronizar");

				btnSincronizar.classList.remove("bg-primary-500", "hover:bg-blue-600");
				btnSincronizar.classList.add("bg-blue-300", "opacity-75", "cursor-wait");
				btnSincronizar.disabled = true;
				textoSincronizar.textContent = "Sincronizando...";

				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					
					success: function(resp1) {
						if(resp1.razonSocial=="" && resp1.eMail=="" && resp1.ciudad=="" && resp1.calle=="" && resp1.rRepresentante=="" && resp1.glosaActividad==""){
							r1='No se ha podido obtener información desde el SII. Verifique el RUT y la contraseña e intente nuevamente.';
							SinInfo(r1);
						}else{
							console.log("Se sincronizo correctamente");
							$("#rsocial").val(resp1.razonSocial);
							$("#representante").val(resp1.RazonRepresentante);
							$("#correo").val(resp1.eMail);
							$("#ciudad").val(resp1.ciudad);
							$("#direccion").val(resp1.calle);
							$("#rutrep").val(resp1.rRepresentante);
							$("#giro").val(resp1.glosaActividad);
							$("#finicio").val(resp1.fechaConstitucion);

							if(resp1.rRepresentante=="SinRut"){
								r1='El Rut del representa no esta disponible en la consulta.';
								SinInfo(r1);
							}

						}
					}
				});	
				
				setTimeout(() => {
					btnSincronizar.classList.remove("bg-blue-300", "opacity-75", "cursor-wait")
					btnSincronizar.classList.add("bg-blue-500", "hover:bg-blue-600");;
					btnSincronizar.disabled = false;
					textoSincronizar.textContent = "Sincronizar";
				}, 1500);
			}

			function SinInfo(r1){
				alert(r1);
				// Swal.fire({
				// 	title: 'Advertencia',
				// 	text: r1,
				// 	icon: 'info',
				// 	confirmButtonText: 'Aceptar'
				// });					
			}

			function ExisteEmpresa(){
				alert('El Rut ingresado para la creación de una nueva empresa, ya está registrado.');
				// Swal.fire({
				// 	title: 'Advertencia',
				// 	text: 'El Rut ingresado para la creación de una nueva empresa, ya está registrado.',
				// 	icon: 'warning',
				// 	confirmButtonText: 'Aceptar'
				// });					
			}

			function RutMal(){
				alert('El Rut ingresado es incorrecto, favor validar e intentar nuevamente.');
				// Swal.fire({
				// 	title: 'Advertencia',
				// 	text: 'El Rut ingresado es incorrecto, favor validar e intentar nuevamente.',
				// 	icon: 'warning',
				// 	confirmButtonText: 'Aceptar'
				// });					
			}

			function limpiarFormulario() {

				document.getElementById("rut").value = "";
				document.getElementById("clasii").value = "";
				document.getElementById("rsocial").value = "";
				document.getElementById("representante").value = "";
				document.getElementById("correo").value = "";
				document.getElementById("ciudad").value = "";
				document.getElementById("direccion").value = "";
				document.getElementById("rutrep").value = "";
				document.getElementById("giro").value = "";
				document.getElementById("finicio").value = "";

				document.getElementById("btnGrabar").className = "bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
				document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";

				document.getElementById("idempb").value = "";
				document.getElementById("idempa").value = "";
				document.getElementById("eliemp").value = "";
				document.getElementById("elirut").value = "";

				window.location.href = "index.php";

			}

			function EliminarEmpresaActual() {
				const id = document.getElementById("idemp").value;
				const rut = document.getElementById("rut").value;
				const razonsocial = document.getElementById("rsocial").value;

				console.log(id, rut, razonsocial);
				
				Elim(id, rut, razonsocial);
			}

			function Exportar() {
				console.log("Exportando");
				deshabilitarBoton("btnImprimir");

			}

			function deshabilitarBoton(buttonId) {
				const button = document.getElementById(buttonId);
				if (button) {
					button.disabled = true;
					button.className = "bg-gray-100 cursor-not-allowed opacity-50 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
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

				<form action="xfrmEmpresas.php" method="POST" name="form1" id="form1" class="space-y-8">

					<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2">
						<button type="button" 
								class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
								onclick="limpiarFormulario()">
							<i class="fa fa-plus mr-2"></i>Nueva
						</button>
						<?php 
							if ($sw==1) {
								echo '<button type="submit" id="btnGrabar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" tabindex="15">
										<i class="fa fa-edit mr-2"></i>Modificar
									</button>';
								if ($_SESSION['ROL']=="A") {
									echo '<button type="button" id="btnEliminar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="EliminarEmpresaActual()" tabindex="16">
											<i class="fa fa-trash mr-2"></i>Eliminar
										</button>';
								}
							}else{
								echo '<button type="submit" id="btnGrabar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" tabindex="15" '.$BloqueBtn.'>
										<i class="fa fa-save mr-2"></i>Grabar
									</button>';
							}
						?>
						<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
								data-modal-target="searchModal" 
								data-modal-toggle="searchModal">
							<i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
						</button>
						<button id="btnImprimir" onclick="Exportar()" type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa-solid fa-print text-gray-600 mr-2"></i>Imprimir
						</button>

						<button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-black bg-gray-100 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg transition duration-200 text-sm px-2 py-1 border-2 border-gray-600 text-center inline-flex items-center" type="button">
							<i class="fa-solid fa-download mr-2"></i>Exportar
						</button>

						<!-- Dropdown menu -->
						<div id="dropdown" class="z-10 hidden bg-gray-100 divide-y divide-gray-100 rounded-lg shadow-sm w-44">
							<ul class="py-2 text-sm text-gray-200" aria-labelledby="dropdownDefaultButton">
								<li>
									<a href="#" class="block px-4 py-2 hover:bg-gray-300 text-black font-medium">CSV</a>
								</li>
								<li>
									<a href="#" class="block px-4 py-2 hover:bg-gray-300 text-black font-medium">PDF</a>
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
							<input type="hidden" name="idemp" id="idemp" value="<?php echo $_POST['idemp']; ?>">
							<input type="hidden" name="idempb" id="idempb">
							<input type="hidden" name="idempa" id="idempa">
							<input type="hidden" name="eliemp" id="eliemp">
							<input type="hidden" name="elirut" id="elirut">

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
										   required>
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
											onclick="SIIData()">
											
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
										   required>
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
										   required>
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
										   required>
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
										   required>
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
										   required>
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
										   required>
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
										required 
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

					<!-- Action Buttons -->
					<!-- <div class="flex justify-end space-x-4">
						<?php 
							if ($sw==1) {
								echo '<button type="submit" class="bg-warning-500 hover:bg-warning-600 text-white font-medium py-2 px-6 rounded-md transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-warning-500 focus:ring-offset-2" tabindex="15">
										<i class="fa fa-edit mr-2"></i>Modificar
									</button>';
							}else{
								echo '<button type="submit" class="bg-success-500 hover:bg-success-600 text-white font-medium py-2 px-6 rounded-md transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-success-500 focus:ring-offset-2" tabindex="15" '.$BloqueBtn.'>
										<i class="fa fa-save mr-2"></i>Grabar
									</button>';
							}
						?>
						<button type="button" 
								class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
								onclick="Volver()">
							<i class="fa fa-times mr-2"></i>Cancelar
						</button>
					</div> -->

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
									<p class="text-sm text-gray-600"><?php echo $MsjEmpresa; ?></p>
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
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RUT</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Razón Social</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giro</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contabilización</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Activo</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
									</tr>
								</thead>
						<tbody id="myTable">
							<?php 
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$cont=1;
								$SQL="SELECT * FROM CTEmpresas WHERE estado<>'X' ORDER BY razonsocial";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									if ($registro["plan"]=="S") {
										$xplan="Individual";
									}else{
										$xplan="Com&uacute;n";
									}
									if ($registro['comprobante']=="S") {
										$xtipo="Soporte Auxiliar";
									}else{
										$xtipo="Tradicional";
									}
									echo '
									<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$cont.'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">'.$registro["rut"].'</td>
										<td class="px-6 py-4 text-sm text-gray-900">'.$registro["razonsocial"].'</td>
										<td class="px-6 py-4 text-sm text-gray-900">'.$registro["direccion"].'</td>
										<td class="px-6 py-4 text-sm text-gray-900">'.$registro["giro"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">'.$registro["periodo"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">'.$xtipo.'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">'.$xplan.'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
											<div class="flex space-x-2">
												<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Modifi(\''.$registro["id"].'\',\''.$registro["rut"].'\',\''.$registro["razonsocial"].'\')">
													<i class="fa fa-edit mr-1"></i>Modificar
												</button>
												<!-- <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-danger-700 bg-danger-100 hover:bg-danger-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-danger-500 transition duration-200" onclick="Elim(\''.$registro["id"].'\',\''.$registro["rut"].'\',\''.$registro["razonsocial"].'\')">
													<i class="fa fa-trash mr-1"></i>Eliminar
												</button> -->';
	
									if($registro["estado"]=="B"){
										echo '<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-success-700 bg-success-100 hover:bg-success-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 transition duration-200" onclick="Alta('.$registro["id"].')">
													<i class="fa fa-check mr-1"></i>Alta
												</button>';
									}else{
										echo '<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="Baja('.$registro["id"].')">
													<i class="fa fa-ban mr-1"></i>Baja
												</button>';
									}

									echo '
											</div>
										</td>
									</tr>
									';

									$cont++;
								}       
								$mysqli->close();
							?>
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
					if($_GET['Err']==4){
						echo 'ExisteEmpresa();';
					}
					if($SoloAdmin==5){
						echo 'alert("Solo la cuenta administrador, puede realizar el proceso de eliminar empresas.");';
					}
					if($_GET['Mjs']=="EmpCreCor"){
						echo 'alert("Empresa creada correctamente.");';
					}
					if($_GET['Mjs']=="EmpActCor"){
						echo 'alert("Empresa actualizada correctamente.");';
					}
				?>
			</script>

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

		</div>
		</div>

		
		

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

	</body>
</html>


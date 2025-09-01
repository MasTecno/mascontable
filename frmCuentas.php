<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

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

	$sw=0;
	$xauxiliar="O";
	$sw1=0;
	if(isset($_POST['idmod']) && $_POST['idmod']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S") {
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE id='".$_POST['idmod']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE id='".$_POST['idmod']."'";
		}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xnumero=$registro["numero"];
			$xdetalle=strtoupper($registro["detalle"]);
			$xidcategoria=$registro["id_categoria"];
			$xauxiliar=$registro["auxiliar"];
			if ($registro["ingreso"]=="S"){
				$sw1=1;
			}
		} 
		$mysqli->close();
	}

	if (isset($_POST['idempb']) && $_POST['idempb']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S") {
			$mysqli->query("UPDATE CTCuentasEmpresa SET estado='B' WHERE id='".$_POST['idempb']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("UPDATE CTCuentas SET estado='B' WHERE id='".$_POST['idempb']."'");
		}
		$mysqli->close();
	}

	if (isset($_POST['idempa']) && $_POST['idempa']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S"){
			$mysqli->query("UPDATE CTCuentasEmpresa SET estado='A' WHERE id='".$_POST['idempa']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("UPDATE CTCuentas SET estado='A' WHERE id='".$_POST['idempa']."'");
		}
		$mysqli->close();
	}

	if (isset($_POST['ideli']) && $_POST['ideli']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		if ($_SESSION["PLAN"]=="S") {
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE id='".$_POST['ideli']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE id='".$_POST['ideli']."'";
		}










		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$Lxnumero=$registro["numero"];
		} 

		if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTRegLibroDiario WHERE cuenta='$Lxnumero' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$mysqli->query("DELETE FROM CTCuentasEmpresa WHERE id='".$_POST['ideli']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
			}else{
				$NoElimina="N";
			}
		}else{
			$SQL="SELECT * FROM CTRegLibroDiario WHERE cuenta='$Lxnumero'";// AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$mysqli->query("DELETE FROM CTCuentas WHERE id='".$_POST['ideli']."'");
			}else{
				$NoEliminaCom="N";
			}
		}
		$mysqli->close();
	}

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
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
		<script src="js/jquery.min.js"></script>
		<!-- <script src="js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">
			function CtaCont(){
				var url= "frmCuentasBuscar.php";
				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					form1.numero.value=resp;
				}
				});				
			}

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
			function Modifi(valor){
				form1.idmod.value=valor;
				form1.action="#";
				form1.submit();
			}
			function Elimina(valor){
				form1.ideli.value=valor;
				form1.action="#";
				form1.submit();
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

			function limpiarFormulario() {
				document.getElementById("SelCat").value = "";
				document.getElementById("numero").value = "";
				document.getElementById("nombre").value = "";
				document.getElementById("ingreso_no").checked = true;
				document.getElementById("aux_n").checked = true;
				
				// Reset form action
				form1.action = "xfrmCuentas.php";
				form1.idmod.value = "";
			}
				

		</script>
	</head>
	<body onload="Grilla()">

	<?php 
		include 'nav.php';
	?>

		<div class="min-h-screen bg-gray-50">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
				<div class="space-y-8">

				<form action="xfrmCuentas.php" method="POST" name="form1" id="form1">
					<!-- Hidden inputs -->
					<input type="hidden" name="idempb" id="idempb">
					<input type="hidden" name="idempa" id="idempa">
					<input type="hidden" name="ideli" id="ideli">
					<input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">

					<!-- Action Buttons -->
					<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-6">
						<button type="button" 
								class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
								onclick="limpiarFormulario()">
							<i class="fa fa-plus mr-2"></i>Nueva
						</button>
						<?php 
							if ($sw==1) {
								echo '<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
										<i class="fa fa-edit mr-2"></i>Modificar
									</button>';
							}else{
								echo '<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
										<i class="fa fa-save mr-2"></i>Grabar
									</button>';
							}
						?>

						<button data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" type="button">
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

							<!-- Category Selection -->
							<div class="grid grid-cols-1 md:grid-cols-1 gap-6">
								<div class="mt-3">
									<label for="SelCat" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-tags mr-1"></i>Categoría
									</label>
									<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="SelCat" name="SelCat" onchange="CtaCont()" required>
										<option value="">Seleccione</option>
										<?php 
											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											$SQL="SELECT * FROM CTCategoria WHERE estado<>'X'";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												if ($xidcategoria!="") {
													if ($xidcategoria==$registro["id"]) {
														echo "<option value ='".$registro["id"]."' selected>".$registro["nombre"]."</option>";
													}else{
														echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
													}
												}else{
													echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
												}
											}
											$mysqli->close();
										?>
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
										   value="<?php echo $xnumero; ?>" 
										   <?php if($sw==1){ echo 'readonly="false"';}?> 
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
										   value="<?php echo $xdetalle; ?>"  
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
									<input type="radio" id="ingreso_si" name="t1" value="S" <?php if ($sw1==1) { echo "checked"; } ?> class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="ingreso_si" class="ml-2 text-sm font-medium text-gray-700">Sí</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="ingreso_no" name="t1" value="N" <?php if ($sw1==0) { echo "checked"; } ?> class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
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
									<input type="radio" id="aux_x" name="opt1" value="X" <?php if ($xauxiliar=="X") { echo "checked"; } ?> class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="aux_x" class="ml-2 text-sm font-medium text-gray-700">Auxiliar</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="aux_e" name="opt1" value="E" <?php if ($xauxiliar=="E") { echo "checked"; } ?> class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="aux_e" class="ml-2 text-sm font-medium text-gray-700">Efectivo</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="aux_b" name="opt1" value="B" <?php if ($xauxiliar=="B") { echo "checked"; } ?> class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
									<label for="aux_b" class="ml-2 text-sm font-medium text-gray-700">Banco</label>
								</div>
								<div class="flex items-center">
									<input type="radio" id="aux_n" name="opt1" value="N" <?php if ($xauxiliar=="N" || $xauxiliar=="O") { echo "checked"; } ?> class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
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
								<!-- El contenido de la tabla se carga aquí dinámicamente -->
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>


		<?php include 'footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
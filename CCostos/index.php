<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$sw=0;

	if(isset($_POST['idccosto']) && $_POST['idccosto']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTCCosto WHERE id='".$_POST['idccosto']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$codigo=$registro["codigo"];
			$nombre=$registro["nombre"];
		}  
		$mysqli->close();
	}

	if (isset($_POST['idccostob']) && $_POST['idccostob']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTCCosto SET estado='B' WHERE id='".$_POST['idccostob']."'");
		$mysqli->close();
	}

	if (isset($_POST['idccostoa']) && $_POST['idccostoa']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTCCosto SET estado='A' WHERE id='".$_POST['idccostoa']."'");
		$mysqli->close();
	}

	if (isset($_POST['idccostoe']) && $_POST['idccostoe']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQL="SELECT * FROM CTRegLibroDiario WHERE ccosto='".$_POST['idccostoe']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("DELETE FROM CTCCosto WHERE id='".$_POST['idccostoe']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$NoElimina="N";
		}

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

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>


	<script type="text/javascript">
		function Modifi(valor){
			form1.idccosto.value=valor;
			form1.action="#";
			form1.submit();
		}

		function Baja(valor){
			form1.idccostob.value=valor;
			form1.action="#";
			form1.submit();
		}

		function Alta(valor){
			form1.idccostoa.value=valor;
			form1.action="#";
			form1.submit();
		}

		function Elimi(valor){
			form1.idccostoe.value=valor;
			form1.action="#";
			form1.submit();
		}

		function OrdeCli(){
			if(form1.orden.value==1){
				form1.orden.value=0;
			}else{
				form1.orden.value=1;
			}
			form1.action="";
			form1.submit();
		}

		function Volver(){
			form1.action="../frmMain.php";
			form1.submit();
		}

		function limpiarFormulario(){
			document.getElementById("codigo").value = "";
			document.getElementById("codigo").disabled = false;
			document.getElementById("codigo").readOnly = false;
			document.getElementById("nombre").value = "";
			document.getElementById("idccosto").value = "";
			document.getElementById("idccostob").value = "";
			document.getElementById("idccostoa").value = "";
			document.getElementById("idccostoe").value = "";

			document.getElementById("btnGrabar").className = "bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2";
			document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";
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

			<form action="xfrmCCostos.php" method="POST" name="form1" id="form1" class="space-y-8">
				
				<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2">
                    <button type="button" 
                            class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
                            onclick="limpiarFormulario()">
                        <i class="fa fa-plus mr-2"></i>Nuevo
                    </button>
                    <?php 
                        if ($sw==1) {
                    ?>
                    <button id="btnGrabar" type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fa fa-edit mr-2"></i>Modificar
                    </button>
                    <?php 
                    }else{
                    ?>
                    <button id="btnGrabar" type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fa fa-save mr-2"></i>Grabar
                    </button>
                    <?php 
                    }
                    ?>

					<button data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" type="button">
                        <i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
                    </button>

                    <button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
                        <i class="fa fa-times mr-2"></i>Cancelar
                    </button>
                </div>

				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
                            <i class="fas fa-file-alt text-lg text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Centro de costos
                            </h3>
                            <!-- <p class="text-sm text-gray-600">Datos para ingresar un tipo de documento</p>      -->
                        </div>
					</div>
					<div class="p-6 pt-1 space-y-6">
						
						<div class="grid grid-cols-2 gap-6 mt-5">
							<div>
								<label for="codigo" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
									<i class="fa fa-file-text mr-1"></i>Codigo
								</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="codigo" name="codigo" autocomplete="off" onChange="javascript:this.value=this.value.toUpperCase();" placeholder="05-ADMIN-MADRID" value="<?php echo $codigo; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>
							</div>
							<input type="hidden" name="idccosto" id="idccosto" value="<?php echo $_POST['idccosto']; ?>">
							<input type="hidden" name="idccostob" id="idccostob">
							<input type="hidden" name="idccostoa" id="idccostoa">
							<input type="hidden" name="idccostoe" id="idccostoe">
							<div>
								<label for="codigo" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
									<i class="fa fa-file-text mr-1"></i>Nombre
								</label>
								<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $nombre; ?>"  autocomplete="off" required>
							</div>
						</div>

							<!-- <div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Codigo</span>
									<input type="text" class="form-control" id="codigo" name="codigo" autocomplete="off" onChange="javascript:this.value=this.value.toUpperCase();" placeholder="05-ADMIN-MADRID" value="<?php echo $codigo; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>
								</div>

								<input type="hidden" name="idccosto" id="idccosto" value="<?php echo $_POST['idccosto']; ?>">
								<input type="hidden" name="idccostob" id="idccostob">
								<input type="hidden" name="idccostoa" id="idccostoa">
								<input type="hidden" name="idccostoe" id="idccostoe">
							</div> 

							<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">Nombre</span>
								<input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $nombre; ?>"  autocomplete="off" required>
							</div> -->
					</div>
				</div>

				
					
					
			</form>
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
									Costos ingresados
								</h3>	
								<p class="text-sm text-gray-600">Lista de costos ingresados</p>
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
							<div class="relative">
								<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
									<i class="fa fa-search text-gray-400"></i>
								</div>
								<input class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="myInput" type="text" placeholder="Buscar...">
							</div>
						</div>
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codigo</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
									</tr>
								</thead>
								<tbody id="myTable">
									<?php 
										$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
										$SQL="SELECT * FROM CTCCosto WHERE estado<>'X' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY nombre";

										$resultados = $mysqli->query($SQL);
										while ($registro = $resultados->fetch_assoc()) {
											echo '
												<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["codigo"].'</td>
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["nombre"].'</td>
											';

												echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Modifi('.$registro["id"].')">Modificar</button></td>';

												if($registro["estado"]=="B"){
													echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-success-700 bg-success-100 hover:bg-success-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 transition duration-200" onclick="Alta('.$registro["id"].')">Alta</button></td>';
												}else{
													echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="Baja('.$registro["id"].')">Baja</button></td>';
												}

												echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-danger-700 bg-danger-100 hover:bg-danger-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-danger-500 transition duration-200" onclick="Elimi('.$registro["id"].')">Eliminar</button></td>';


											echo '
												</tr>
											';
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

		
		
		<?php
			if(isset($_GET['Err']) && $_GET['Err']==1){
				echo '<script> alert("Este Codigo ya esta ingresado"); </script>';
			}

			if ($NoElimina=="N") {
				echo '<script> alert("Este Centro de Costo tiene movimientos, no se puede eliminar"); </script>';
			}

		?>
	</div>
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


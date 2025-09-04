<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
	
	$sw=0;

	if(isset($_POST['idemp']) && $_POST['idemp']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTContadores WHERE id='".$_POST['idemp']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$nombre=$registro["nombre"];
			$correo=$registro["correo"];
			$rol=$registro["tipo"];
		}  
		$mysqli->close();
	}

	if (isset($_POST['idempb']) && $_POST['idempb']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTContadores SET estado='B' WHERE id='".$_POST['idempb']."'");
		$mysqli->query("UPDATE CTEmpresas SET user='0' WHERE user='".$_POST['idempb']."'");
		// if($registro["correo"]=="admin@mastecno.cl"){
		// 	$mysqli->query("UPDATE CTContadores SET estado='A' WHERE id='".$_POST['idempb']."'");
		// }
		// echo "UPDATE CTContadores SET estado='A' WHERE id='".$_POST['idempb']."'";
		$mysqli->close();
	}

	if (isset($_POST['idempa']) && $_POST['idempa']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTContadores SET estado='A' WHERE id='".$_POST['idempa']."'");
		$mysqli->close();
	}

	if (isset($_POST['idrol']) && $_POST['idrol']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTContadores WHERE id='".$_POST['idrol']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			if($registro["tipo"]=="U"){
				$rol="A";
			}else{
				$rol="U";
			}
		}
		$mysqli->query("UPDATE CTContadores SET tipo='$rol' WHERE id='".$_POST['idrol']."'");
		$mysqli->close();
	}


	if (isset($_POST['ideliusu']) && $_POST['ideliusu']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTEmpresas SET user='0' WHERE user='".$_POST['ideliusu']."'");
		$mysqli->query("DELETE FROM CTContadores WHERE id='".$_POST['ideliusu']."'");
		$mysqli->close();
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
		<script src="../js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">

			function Baja(valor){
				form1.idempb.value=valor;
				form1.action="#";
				form1.submit();
			}

			function Eliminar(valor){
				form1.ideliusu.value=valor;
				form1.action="#";
				form1.submit();
			}

			function Alta(valor){
				form1.idempa.value=valor;
				form1.action="#";
				form1.submit();
			}
			function Reset(valor){
				form1C.idmod.value=valor;
			}
			function GBt(){
				form1C.submit();
			}

			function Rol(r1){
				form1.idrol.value=r1;
				form1.action="#";
				form1.submit();
			}
			function Volver(){
				form1.action="frmMain.php";
				form1.submit();
			}    
		</script>  

	</head>
	<body>
		<?php 
			include 'nav.php';
		?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

			<div class="mb-5">
				<!-- <div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Cambiar Clave</h4>
						</div>
						<div class="modal-body">
							<form action="xfrmUsuarios.php" method="POST" name="form1C" id="form1C">
								<label for="claveX" class="p-1">Nueva Clave</label>
								<input type="text" class="form-control" id="claveX" name="claveX" maxlength="50" value="">
								<input type="hidden" name="idmod" id="idmod">
							</form>
						</div>
						<div class="modal-footer">
							<div class="btn-group btn-group-justified" role="group" aria-label="group button">
								<div class="btn-group" role="group">
									<button type="button" id="saveImage" class="btn btn-grabar" data-action="save" role="button" onclick="GBt()">Grabar</button>
								</div>
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-cancelar" data-dismiss="modal" role="button" id="CMOD">Cancelar</button>
								</div>			
							</div>
						</div>
					</div>
				</div>
				</div> -->

				<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
					<div class="relative p-4 w-full max-w-2xl max-h-full">
						<!-- Modal content -->
						<div class="relative bg-white rounded-lg shadow-sm">
							<!-- Modal header -->
							<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
								<h3 class="text-xl font-semibold text-gray-900">
									Cambiar Clave
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
								<form action="xfrmUsuarios.php" method="POST" name="form1C" id="form1C">
									<label for="claveX" class="p-1">
										<i class="fa fa-key mr-1"></i>Nueva Clave
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="claveX" name="claveX" maxlength="50" value="">
									<input type="hidden" name="idmod" id="idmod">
								</form>
							</div>
							<!-- Modal footer -->
							<div class="flex items-center gap-2 p-4 border-t border-gray-200 rounded-b dark:border-gray-600">
								<button type="button" id="saveImage" class="py-2.5 px-5 ms-1 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100" data-action="save" role="button" onclick="GBt()">Grabar</button>
								
								<button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-1 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Cancelar</button>
							</div>
						</div>
					</div>
				</div>

				<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
					<?php 
						if ($sw==1) {
					?>
						<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-edit mr-2"></i> Modificar
						</button>

					<?php 
						}else{
					?>
						<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
							<i class="fa fa-save mr-2"></i> Grabar
						</button>

					<?php 
						}
					?>
						<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
							<i class="fa fa-times mr-2"></i> Cancelar
						</button>  
				</div>


				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-users text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">Registro de Usuarios</h3>
							<p class="text-sm text-gray-600">Ingresa los parametros</p>
						</div>
					</div>
					<div class="p-6 pt-1 space-y-6">				

						<form action="xfrmUsuarios.php" method="POST" name="form1" id="form1">

							<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-2 mt-3">


								<div class="input-group">
									<label for="tnombre" class="p-1">
										<i class="fa fa-user mr-1"></i>Nombre
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off" id="tnombre" name="tnombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $razonsocial; ?>" required>
									<input type="hidden" name="idempa" id="idempa">
									<input type="hidden" name="idempb" id="idempb">
									<input type="hidden" name="ideliusu" id="ideliusu">
									<input type="hidden" name="idrol" id="idrol">
								</div>


								<div class="input-group">
									<label for="correo" class="p-1">
										<i class="fa fa-envelope mr-1"></i>Correo
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="correo" autocomplete="off" name="correo" required>
								</div>

								<div class="input-group">
									<label for="clave" class="p-1">
										<i class="fa fa-key mr-1"></i>Clave
									</label>
									<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="clave" autocomplete="off" name="clave" required>
								</div>

							</div>

						</form>
					</div>
				</div>

				<div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-5">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-list text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">Usuarios Registrados</h3>
							<p class="text-sm text-gray-600">Lista de usuarios</p>
						</div>
					</div>

					<div class="p-6 pt-1 space-y-6">
						<form name="form2" action="#" method="POST">
							<table class="min-w-full divide-y divide-gray-200 mt-5">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">Nombre</th>
										<th class="px-6 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">Correo</th>
										<th class="px-6 py-2 text-left text-xs font-medium text-gray-500 tracking-wider"></th>
										<th class="px-6 py-2 text-left text-xs font-medium text-gray-500 tracking-wider"></th>
										<th class="px-6 py-2 text-left text-xs font-medium text-gray-500 tracking-wider"></th>
										<th class="px-6 py-2 text-left text-xs font-medium text-gray-500 tracking-wider"></th>
										<!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider"></th> -->
									</tr>
								</thead>

								<tbody>
									<?php 
									$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
									$SQL="SELECT * FROM CTContadores WHERE estado<>'X'";

									$resultados = $mysqli->query($SQL);
									while ($registro = $resultados->fetch_assoc()) {
										echo '
										<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
											<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["nombre"].'</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["correo"].'</td>
										';

										if($registro["correo"]=="admin@mastecno.cl"){
											echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>';
										}else{
											echo '<td data-modal-target="default-modal" data-modal-toggle="default-modal" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Reset('.$registro["id"].')"><i class="fa-solid fa-key mr-1"></i>Clave</button></td>';
										}
										if($registro["estado"]=="B"){
											echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Alta('.$registro["id"].')"><i class="fa-solid fa-check mr-1"></i>Alta</button></td>';
										}else{								
											echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="Baja('.$registro["id"].')"><i class="fa-solid fa-ban mr-1"></i>Baja</button></td>';
										}
										if($registro["tipo"]=="U"){
											echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Eliminar('.$registro["id"].')"><i class="fa-solid fa-trash mr-1"></i>Eliminar</button></td>';
										}else{
											echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>';
										}
										if($_SESSION["CORREO"]=="admin@mastecno.cl"){
											if($registro["tipo"]=="U"){
												echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Rol('.$registro["id"].')"><i class="fa-solid fa-user mr-1"></i>Usuario</button></td>';
											}else{
												echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Rol('.$registro["id"].')"><i class="fa-solid fa-user-shield mr-1"></i>Administrador</button></td>';
											}
										}

										echo '
										</tr>
										';
									}       
									$mysqli->close();
								?>
								</tbody>
							</table>      
						</form>
					</div>
				</div>


				<?php
					if(isset($_GET['Err']) && $_GET['Err']==1){
						echo '<script>
							alert("Usuarios ya esta ingresado");
						</script>';
					}
				?>
			</div>
		</div>
		</div>
		<?php include 'footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
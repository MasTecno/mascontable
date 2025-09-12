<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	include '../vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\IOFactory;

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}
	$SwMes="";
	if ($_POST['swImport']=="S") {
		if (isset($_POST['action'])) {
			$action=$_POST['action'];
		}

		$Msj="";
		if (isset($action)== "upload" && $_FILES['excel']['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
			// echo "rrr";
			//cargamos el fichero
			$archivo = $_FILES['excel']['name'];
			$tipo = $_FILES['excel']['type'];

			// echo $archivo . "<br>";
			// print_r($_FILES);
			// exit;
			$destino = "Temp_".$archivo;//Le agregamos un prefijo para identificarlo el archivo cargado
			if (copy($_FILES['excel']['tmp_name'],$destino)){
				// echo "Archivo Cargado Con Éxito<br>";
			}else{
				$Msj="Error Al Cargar el Archivo";
			}

			if (file_exists ("Temp_".$archivo)){
				$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

				$objPHPExcel = IOFactory::load($destino);
				$objPHPExcel->setActiveSheetIndex(0);
				$columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
				$filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

				// echo "columnas: ".$columnas."<br>";
				// echo "filas: ".$filas."<br>";

				$STRInsert = ""; // Initialize the variable
				for ($i = 2; $i <= $filas; $i++){
					$_DATOS_EXCEL[$i]['RutImp'] = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Numero'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
					
					$ccosto = "";
					$estado = "A";

					//* Crear cuenta
					if ($_DATOS_EXCEL[$i]['Numero'] != 0) {
						$STRInsert = $STRInsert . "INSERT INTO CTCliProCuenta VALUES('','$RutEmpresa','".$_DATOS_EXCEL[$i]['RutImp']."','".$_DATOS_EXCEL[$i]['Numero']."','$ccosto','".$_POST['SelCliPro']."','A'); ";
						
					}
				}

				if ($Msj=="") {
					$sql = "DELETE FROM CTCliProCuenta WHERE rutempresa = ? AND tipo = ?";
					$stmt = $mysqli->prepare($sql);

					$stmt->bind_param("ss", $RutEmpresa, $_POST['SelCliPro']);
					$stmt->execute();
					$stmt->close();


					$mysqli->multi_query($STRInsert);
					// $mysqli->query($STRInseert);
					$SwMes="S";
				}

				// exit;
				// require_once('../Classes/PHPExcel.php');
				// require_once('../Classes/PHPExcel/Reader/Excel2007.php');                  
				// // Cargando la hoja de excel
				// $objReader = new PHPExcel_Reader_Excel2007();
				// $objPHPExcel = $objReader->load("Temp_".$archivo);
				// $objFecha = new PHPExcel_Shared_Date();       
				// // Asignamon la hoja de excel activa
				// $objPHPExcel->setActiveSheetIndex(0);

				// $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
				// $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

				// // //* Obtengo rut y numero, A y F
				// for ($i=2;$i<=$filas;$i++){
				// 	$_DATOS_EXCEL[$i]['RutImp'] = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
				// 	$_DATOS_EXCEL[$i]['Numero'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
					
				// 	//* Crear cuenta
				// 	if ($_DATOS_EXCEL[$i]['Numero']!=0) {
				// 		$STRInseert=$STRInseert."INSERT INTO CTCliProCuenta VALUES('','$RutEmpresa','".$_DATOS_EXCEL[$i]['RutImp']."','".$_DATOS_EXCEL[$i]['Numero']."','".$_POST['SelCliPro']."','A'); ";
				// 	}
				// }
				// // $errores=0;

				// if ($Msj=="") {
				// 	$mysqli->query("DELETE FROM CTCliProCuenta WHERE rutempresa='$RutEmpresa' AND tipo='".$_POST['SelCliPro']."'");

				// 	// $mysqli->query("UPDATE CTEmpresas SET ccosto='S', plan='S' WHERE rut='$RutEmpresa'");
				// 	// echo $STRInseert;
				// 	// exit;
				// 	// mysqli_multi_query($mysqliX, $StrSql)
				// 	$mysqli->multi_query($STRInseert);
				// 	// $mysqli->query($STRInseert);
				// 	$SwMes="S";
				// }

				unlink($destino);
				$mysqli->close();
			}else{
				$Msj="Primero debes cargar el archivo con extencion .xlsx";
			}
		}else{
			$Msj="Primero debes cargar el archivo con extencion .xlsx";
		}
	}
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
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">
		
		<script type="text/javascript">
			function ActivaBtn(){
				if (document.getElementById("BtnVisual").style.visibility == "hidden") {
					document.getElementById("BtnVisual").style.visibility = "visible";
				}else{
					document.getElementById("BtnVisual").style.visibility = "hidden";
				}
			}
			// function ClonaPlan(){
			// 	form1.swImport.value="S";
			// 	form1.submit();
			// }
		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
			<div class="space-y-8">
				<form name="form1" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" class="space-y-6">
					
					<!-- Action Buttons -->
					<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
						<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="history.back()">
							<i class="fa fa-arrow-left mr-2"></i> Volver
						</button>
					</div>

					<!-- Main Card -->
					<div class="bg-white rounded-lg shadow-sm border border-gray-200">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fa-solid fa-upload text-lg text-blue-600"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-800">
									Importar Cuenta Clientes/Proveedores
								</h3>
								<p class="text-sm text-gray-600">Seleccione el tipo de entidad y cargue el archivo Excel</p>
							</div>
						</div>
						
						<div class="p-6 pt-1 space-y-6">
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
								
								<div>
									<label for="SelCliPro" class="block text-sm font-medium text-gray-700 mb-2">Entidades</label>
									<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="SelCliPro" name="SelCliPro" required>
										<option value="">Seleccione</option>
										<option value="C">Clientes</option>
										<option value="P">Proveedores</option>
									</select>
								</div>

								<div>
									<label for="excel" class="block text-sm font-medium text-gray-700 mb-2">Adjuntar Archivo</label>
									<div class="relative">
										<input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200" id="excel" name="excel" accept=".xlsx" required>
									</div>
									<input type="hidden" value="upload" name="action">
									<input type="hidden" name="swImport" id="swImport" value="S">
								</div>
							</div>

							<?php if ($Msj!="") { ?>
								<div class="bg-red-50 border border-red-200 rounded-md p-4">
									<div class="flex">
										<div class="flex-shrink-0">
											<i class="fa fa-exclamation-circle text-red-400"></i>
										</div>
										<div class="ml-3">
											<h3 class="text-sm font-medium text-red-800">Error</h3>
											<div class="mt-2 text-sm text-red-700">
												<p><?php echo $Msj; ?></p>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>

							<!-- Confirmation -->
							<div class="bg-blue-50 border border-blue-200 rounded-md p-2.5">
								<div class="flex items-center">
									<div class="flex-shrink-0">
										<i class="fa fa-info-circle text-blue-400 text-sm"></i>
									</div>
									<div class="ml-3">
										<!-- <h3 class="text-sm font-medium text-blue-800">Confirmación</h3> -->
										<div class="text-sm text-blue-700">
											<p>¿Está seguro de realizar la carga del nuevo plan de cuenta para Clientes/Proveedores?</p>
										</div>
									</div>
								</div>
							</div>

							<!-- Checkbox and Submit Button -->
							<div class="flex items-center justify-between">
								<div class="flex items-center">
									<input type="checkbox" id="SwPago" name="SwPago" value="" onclick="ActivaBtn()" class="h-4 w-4 border-2 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
									<label for="SwPago" class="ml-2 block text-sm text-gray-900">Aceptar</label>
								</div>
								
								<button type="submit" name="BtnVisual" id="BtnVisual" style="visibility:hidden;" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
									<i class="fa fa-upload mr-2"></i> Importar
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		</div>
		<script type="text/javascript">
		<?php
			if ($SwMes=="N") {
				echo 'alert("A ocurrido un error, favor contactar con soporte.")';
			}
			if ($SwMes=="S") {
				echo 'alert("Se a completado la operaci\u00F3n con exito.")';
			}
		?>
		</script>

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>

</html>


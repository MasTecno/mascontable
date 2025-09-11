<?php
    include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	include '../vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Shared\Date;


	$Periodo=$_SESSION['PERIODO'];
    if($Periodo==""){
		header("location:../frmMain.php");
		exit;
    }

	$dmes = substr($Periodo,0,2);
	$dano = substr($Periodo,3,4);

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    extract($_POST);

    if ($action == "upload") {

		if ($messelect<=9) {
			$messelect="0".$messelect;
		}

		$LPeriodo=$messelect."-".$anoselect;

		$dmes = substr($LPeriodo,0,2);
		$dano = substr($LPeriodo,3,4);

		$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$LCuentaDef=$registro['L1'];
		}
		$NewRSocial=0;
		$ListError="";
		if ($_POST['optradio']=="R"){
			$Inifor=7;
		}

		if ($_POST['optradio']=="T"){
			$Inifor=9;
		}

		// print_r($_FILES['excel']['type']);
		if (isset($action)== "upload" && $_FILES['excel']['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
			
			//cargamos el fichero
			$archivo = $_FILES['excel']['name'];
			$tipo = $_FILES['excel']['type'];
			$destino = "Temp_".$archivo;//Le agregamos un prefijo para identificarlo el archivo cargado
			if (copy($_FILES['excel']['tmp_name'],$destino)){
				// echo "Archivo Cargado Con Éxito<br>";
			}else{
				$ListError="Error Al Cargar el Archivo";
			}

			if (file_exists ("Temp_".$archivo)){
				// echo "ok";
				
				$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
 
				$spreadsheet = IOFactory::load("Temp_".$archivo);
				
				//* Obtener primera hoja
				$spreadsheet->setActiveSheetIndex(0);
				$sheet = $spreadsheet->getActiveSheet(); //* Acceder a las celdas
				
				$columnas = $sheet->getHighestColumn(); //* Ultima columna
				$filas = $sheet->getHighestRow(); //* Ultima fila

				for ($i = $Inifor; $i <= ($filas-2); $i++){

					if ($_POST['optradio'] == "R"){
						// $_DATOS_EXCEL[$i]['Numero'] = $spreadsheet->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Numero'] = $sheet->getCell('A'.$i)->getCalculatedValue();

						
						// print_r($_DATOS_EXCEL[$i]['Numero']);

						$format = "Y-m-d";     
						$cell = $sheet->getCell('B'. $i);	
						$InvDateRaw = $cell->getValue();
						// print_r($InvDateRaw);
						// exit;

						//* Verificar si las celdas contienen una fecha
						if(Date::isDateTime($cell)) {
							
							//* Formatear la fecha
							$datetime = Date::excelToDateTimeObject($InvDateRaw);
							$InvDate = $datetime->format($format);
				
							$FDocumento = $datetime->modify('+1 day')->format('Y-m-d');
						}

						//* Obtener el valor de las celdas
						$_DATOS_EXCEL[$i]['Estado'] = $sheet->getCell('C'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Rut'] = $sheet->getCell('E'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Razon'] = $sheet->getCell('F'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Brutos'] = $sheet->getCell('H'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Retenido'] = $sheet->getCell('I'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Pagado'] = $sheet->getCell('J'.$i)->getCalculatedValue();
					}

					if ($_POST['optradio']=="T"){
						// echo "4444";
						$ListErrorArchivo="";

						// $_DATOS_EXCEL[$i]['RutEmpresa'] = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['RutEmpresa'] = $sheet->getCell('D'.$i)->getCalculatedValue();
						if($_DATOS_EXCEL[$i]['RutEmpresa']!=$_SESSION['RUTEMPRESA']){
							$ListErrorArchivo="Error Archivo no corresponde";
						}

						// $_DATOS_EXCEL[$i]['Numero'] = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Numero'] = $sheet->getCell('A'.$i)->getCalculatedValue();
						$format = "Y-m-d";     
						$cell = $sheet->getCell('F'. $i);	
						$InvDateRaw = $cell->getValue();
						
						if(Date::isDateTime($cell)) {
							$datetime = Date::excelToDateTimeObject($InvDateRaw);
							$InvDate = $datetime->format($format);
							$FDocumento = $datetime->modify('+1 day')->format('Y-m-d');
						}

						// $InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
						// $FDocumento =date("Y-m-d",strtotime($InvDate."+ 1 days")); 

						$_DATOS_EXCEL[$i]['Estado'] = $sheet->getCell('B'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Rut'] = $sheet->getCell('G'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Razon'] = $sheet->getCell('H'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Brutos'] = $sheet->getCell('I'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Retenido'] = $sheet->getCell('J'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Pagado'] = $sheet->getCell('K'.$i)->getCalculatedValue();
					}

					if (str_replace(" ","",$_DATOS_EXCEL[$i]['Estado'])=="VIGENTE" || str_replace(" ","",$_DATOS_EXCEL[$i]['Estado'])=="Vigente") {
						$SQL="SELECT * FROM CTHonorarios WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='".$_DATOS_EXCEL[$i]['Rut']."' AND tdocumento='R' AND numero='".$_DATOS_EXCEL[$i]['Numero']."' AND liquido='".$_DATOS_EXCEL[$i]['Pagado']."'";
						$resultado = $mysqli->query($SQL);
						$numero = $resultado->num_rows;
						if ($numero==0){
							$STRInseert=$STRInseert."INSERT INTO CTHonorarios VALUES('','$LPeriodo','".$_SESSION['RUTEMPRESA']."','$FDocumento','".$_DATOS_EXCEL[$i]['Rut']."','".$_DATOS_EXCEL[$i]['Numero']."','$LCuentaDef','','".$_DATOS_EXCEL[$i]['Brutos']."','".$_DATOS_EXCEL[$i]['Retenido']."','".$_DATOS_EXCEL[$i]['Pagado']."','T','".date('Y-m-d')."','','A',''); ";
						}
					}

					$SQL="SELECT * FROM CTCliPro WHERE rut='".$_DATOS_EXCEL[$i]['Rut']."' AND tipo='P'";
					$resultados = $mysqli->query($SQL);
					$row_cnt = $resultados->num_rows;
					if ($row_cnt==0) {
						$mysqli->query("INSERT INTO CTCliPro VALUES('','".$_DATOS_EXCEL[$i]['Rut']."','".strtoupper($_DATOS_EXCEL[$i]['Razon'])."','','','','','','P','A')");
						$NewRSocial=$NewRSocial+1;
					}					
				}

				// echo $STRInseert;
				// exit;

				if ($ListError=="") {
					// echo $STRInseert;

					$mysqli->multi_query($STRInseert);
					$SwMes="S";
				}
				if($STRInseert==""){
					$ListError='<div class="alert alert-danger"><strong>Informativo</strong> El archivo NO Procesado...</div><br>';
				}
				unlink($destino);
				// $mysqli->close();
			}else{
				$ListError="Primero debes cargar el archivo con extencion .xlsx";
			}
		}else{
			$ListError="Primero debes cargar el archivo con extencion .xlsx";
		}

		if ($ListError=="") {
			$ListError='<div class="alert alert-success"><strong>Informativo</strong> El archivo fue procesado con Exito. <br> Se han cargado '.$NewRSocial.', Razon(es) Social(es) nueva(s)...</div><br>';
		}

    }

	$mysqli->close();
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
		<script src="../js/propio.js"></script>

		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script type="text/javascript">
			function CargaArc(){
				var r = confirm("El proceso puede tomar tiempo");
				if (r == true) {
					importar.action="";
					//importar.submit();
				}else{
					alert("Operacion Cancelada");
					importar.action.value="";
				}

			}
			function Descar(){
				window.open("CargaMasiva.csv", "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=500, left=500, width=400, height=400");
			}
		</script>

	</head>

	<body>

	<?php include '../nav.php'; ?>

	<div class="min-h-screen bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

		<div class="space-y-8">
		<form name="importar" method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data">

			<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
				<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
					<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
						<i class="fa-solid fa-file-import text-lg text-blue-600"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-800">
							Importar Honorarios
						</h3>
						<p class="text-sm text-gray-600">Cargue el archivo de honorarios desde el SII</p>
					</div>
				</div> 
					
				<div class="p-6 pt-1 space-y-6">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
						<div>
							<label for="messelect" class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
							<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="messelect" name="messelect" required>
							<?php 
								$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
								$i=1;
								$dmes=$dmes*1;
								while($i<=12){

									if ($i==$dmes) {
										echo "<option value ='".$i."' selected>".$Meses[($i-1)]."</option>";
									}else{
										echo "<option value ='".$i."'>".$Meses[($i-1)]."</option>";
									}
									$i++;
								}
							?>
							</select>
						</div>

						<div>
							<label for="anoselect" class="block text-sm font-medium text-gray-700 mb-2">A&ntilde;o</label>
							<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="anoselect" name="anoselect" required>
							<?php 
								$yoano=date('Y');
								$tano="2010";

								while($tano<=($yoano+1)){
									if ($dano==$tano) {
										echo "<option value ='".$tano."' selected>".$tano."</option>";
									}else{
										echo "<option value ='".$tano."'>".$tano."</option>";
									}
									$tano=$tano+1;
								}
							?>
							</select>
						</div>
					</div>

					<div class="mt-6">
						<label for="file" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Archivo</label>
						<div class="flex items-center gap-2">
							<input type="file" name="excel" id="excel" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-l-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200">
						</div>
						<input type="hidden" value="upload" name="action" />
					</div>

					<div class="mt-6">
						<fieldset>
							<legend class="text-sm font-medium text-gray-700 mb-3">Tipo de Honorarios</legend>
							<div class="flex justify-start items-center gap-5 mb-3">
								<div class="flex items-center">
									<input id="optradio-r" name="optradio" type="radio" value="R" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
									<label for="optradio-r" class="ml-3 block text-sm font-medium text-gray-700">
										Honorarios Recibidos
									</label>
								</div>
								<div class="flex items-center">
									<input id="optradio-t" name="optradio" type="radio" value="T" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
									<label for="optradio-t" class="ml-3 block text-sm font-medium text-gray-700">
										Honorarios a Terceros
									</label>
								</div>
							</div>
						</fieldset>
					</div>

					<div class="border border-gray-200 rounded-lg bg-gray-50 flex justify-center items-center">
						<img src="../images/Honorario.JPG" class="rounded-md shadow-sm" alt="Ejemplo de formato de honorarios">
					</div>

					<div>
							<h4 class="text-sm font-medium text-gray-900 mb-3">Pasos a Seguir</h4>
							<div class="space-y-3">
								<div class="flex items-start">
									<div class="flex-shrink-0">
										<div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
											<span class="text-xs font-medium text-blue-600">1</span>
										</div>
									</div>
									<div class="ml-3">
										<p class="text-sm text-gray-700">Descargar directamente el resumen desde el SII. "Ver informe como planilla electr&oacute;nica"</p>
									</div>
								</div>
								
								<div class="flex items-start">
									<div class="flex-shrink-0">
										<div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
											<span class="text-xs font-medium text-blue-600">2</span>
										</div>
									</div>
									<div class="ml-3">
										<p class="text-sm text-gray-700">Abrir archivo descargado</p>
									</div>
								</div>
								
								<div class="flex items-start">
									<div class="flex-shrink-0">
										<div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
											<span class="text-xs font-medium text-blue-600">3</span>
										</div>
									</div>
									<div class="ml-3">
										<p class="text-sm text-gray-700">Guardar como un archivo XLSX (Libro de Excel)</p>
									</div>
								</div>
								
								<div class="flex items-start">
									<div class="flex-shrink-0">
										<div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
											<span class="text-xs font-medium text-blue-600">4</span>
										</div>
									</div>
									<div class="ml-3">
										<p class="text-sm text-gray-700">Procesar archivo guardado usando este formulario</p>
									</div>
								</div>
							</div>
						</div>

					<div class="mt-6">
						<button type="submit" class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
							<i class="fa-solid fa-upload mr-2"></i>
							Procesar Archivo
						</button>
					</div>

					<?PHP
						if ($ListError!="") {
							echo '<div class="mt-6">' . $ListError . '</div>';
						}
						
						if ($ListErrorArchivo!="") {
							echo '<div class="mt-6"><div class="bg-red-50 border border-red-200 rounded-md p-4"><div class="flex"><div class="flex-shrink-0"><i class="fa-solid fa-exclamation-triangle text-red-400"></i></div><div class="ml-3"><p class="text-sm text-red-800">' . $ListErrorArchivo . '</p></div></div></div></div>';
						}
					?>

				</div>
			</div>

		</form>

	</div>
	</div>

	<div class="clearfix"> </div>

	<?php include '../footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
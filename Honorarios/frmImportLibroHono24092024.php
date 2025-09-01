<?php
    include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

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
				
				// $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
				/** Llamamos las clases necesarias PHPEcel */
				require_once('../Classes/PHPExcel.php');
				require_once('../Classes/PHPExcel/Reader/Excel2007.php');                  
				// Cargando la hoja de excel
				$objReader = new PHPExcel_Reader_Excel2007();
				$objPHPExcel = $objReader->load("Temp_".$archivo);
				$objFecha = new PHPExcel_Shared_Date();       
				// Asignamon la hoja de excel activa
				$objPHPExcel->setActiveSheetIndex(0);

				$columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
				$filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

				for ($i=$Inifor;$i<=($filas-2);$i++){
					// echo "aaaaXXXX";

					if ($_POST['optradio']=="R"){
						// echo "rrr";
						$_DATOS_EXCEL[$i]['Numero'] = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
						$format = "Y-m-d";     
						$cell = $objPHPExcel->getActiveSheet()->getCell('B'. $i);	
						$InvDate= $cell->getValue();
						if(PHPExcel_Shared_Date::isDateTime($cell)) {
							$InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
							$FDocumento =date("Y-m-d",strtotime($InvDate."+ 1 days")); 
						}

						$_DATOS_EXCEL[$i]['Estado'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Rut'] = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Razon'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Brutos'] = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Retenido'] = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Pagado'] = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
					}

					if ($_POST['optradio']=="T"){
						// echo "4444";
						$ListErrorArchivo="";

						$_DATOS_EXCEL[$i]['RutEmpresa'] = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
						if($_DATOS_EXCEL[$i]['RutEmpresa']!=$_SESSION['RUTEMPRESA']){
							$ListErrorArchivo="Error Archivo no corresponde";
						}

						$_DATOS_EXCEL[$i]['Numero'] = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
						$format = "Y-m-d";     
						$cell = $objPHPExcel->getActiveSheet()->getCell('F'. $i);	
						$InvDate= $cell->getValue();
						if(PHPExcel_Shared_Date::isDateTime($cell)) {
							$InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
							$FDocumento =date("Y-m-d",strtotime($InvDate."+ 1 days")); 
						}

						$_DATOS_EXCEL[$i]['Estado'] = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Rut'] = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Razon'] = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Brutos'] = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Retenido'] = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
						$_DATOS_EXCEL[$i]['Pagado'] = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
					}

					if (str_replace(" ","",$_DATOS_EXCEL[$i]['Estado'])=="VIGENTE") {
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

				if ($ListError=="") {
					// echo $STRInseert;

					$mysqli->multi_query($STRInseert);
					$SwMes="S";
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
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

	<div class="container-fluid text-left">
	<div class="row content">
		<!-- <h3 class="text-center">Importar Libros</h3> -->
		<form name="importar" method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" class="form-horizontal">

			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading text-center">Importar Honorarios</div>
				<div class="panel-body">
					<div class="col-md-6 text-right">
					<div class="input-group">
						<span class="input-group-addon">Mes</span>
						<select class="form-control" id="messelect" name="messelect" required>
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
					</div>

					<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">A&ntilde;o</span>
						<select class="form-control" id="anoselect" name="anoselect" required>
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

					<div class="clearfix"></div>
					<br>

					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Seleccionar Archivo</span>
							<input type="file" class="form-control-file" id="excel" name="excel" aria-describedby="fileHelp" required>
								
							<input type="hidden" value="upload" name="action" />
						</div>
					</div> 

					<div class="clearfix"></div>
					<br>

					<div class="col-md-12">
					<div class="input-group">

						<label class="radio-inline"><input type="radio" name="optradio" id="optradio" value="R" checked>Honorarios Recibidos</label>
						<label class="radio-inline"><input type="radio" name="optradio" id="optradio" value="T">Honorarios a Terceros</label>					
						<br>
						<br>
						<button type="submit" class="btn btn-grabar btn-block">Procesar</button>
					</div>
					</div>

					<div class="clearfix"></div>
					<br>

					<div class="form-group">
						<label class="control-label col-md-12" for="file"></label>
						<div class="col-md-12">
						<?PHP
							if ($ListError!="") {
								echo $ListError;
							}
							
							if ($ListErrorArchivo!="") {
								echo "<br>".$ListErrorArchivo;
							}
							
						?>
						</div>
					</div> 

					<div class="clearfix"></div>
					<br>

					<div class="col-md-10">
						<div class="col-md-12">
							* Imagen Referencial
						</div>
						<img src="../images/Honorario.JPG" class="img-rounded" alt="Honorario">

						<br>
						<h3>Paso a Paso</h3>
						1. Descargar directamente el resumen desde el SII. "Ver informe como planilla electr&oacute;nica"<br>
						2. Abrir archivo descargado.<br>
						3. Guardar como un archivo XLSX (Libro de Excel).<br>
						4. Procesar archivo guardado.<br>
						<br>
					</div>

				</div>
				</div>
			</div>

		</form>

	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include '../footer.php'; ?>

	</body>
</html>
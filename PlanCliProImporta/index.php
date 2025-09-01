<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

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
			// exit;
			$destino = "Temp_".$archivo;//Le agregamos un prefijo para identificarlo el archivo cargado
			if (copy($_FILES['excel']['tmp_name'],$destino)){
				// echo "Archivo Cargado Con Éxito<br>";
			}else{
				$Msj="Error Al Cargar el Archivo";
			}

			if (file_exists ("Temp_".$archivo)){
				$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
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

				for ($i=2;$i<=$filas;$i++){
					$_DATOS_EXCEL[$i]['RutImp'] = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Numero'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
					
					if ($_DATOS_EXCEL[$i]['Numero']!=0) {
						$STRInseert=$STRInseert."INSERT INTO CTCliProCuenta VALUES('','$RutEmpresa','".$_DATOS_EXCEL[$i]['RutImp']."','".$_DATOS_EXCEL[$i]['Numero']."','".$_POST['SelCliPro']."','A'); ";
					}
				}
				// $errores=0;

				if ($Msj=="") {
					$mysqli->query("DELETE FROM CTCliProCuenta WHERE rutempresa='$RutEmpresa' AND tipo='".$_POST['SelCliPro']."'");

					// $mysqli->query("UPDATE CTEmpresas SET ccosto='S', plan='S' WHERE rut='$RutEmpresa'");
					// echo $STRInseert;
					// exit;
					// mysqli_multi_query($mysqliX, $StrSql)
					$mysqli->multi_query($STRInseert);
					// $mysqli->query($STRInseert);
					$SwMes="S";
				}

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

		<div class="container-fluid">
		<div class="row content">
		<form name="form1" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
			<br>

			
			<div class="col-sm-4">
			</div>
			<div class="col-sm-4">



			</div>
			<div class="clearfix"></div>	
			<br>

			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">


				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading">IMPORTAR CUENTA CLIENTES/PROVEEDORES</div>
					<div class="panel-body">
						<!-- <div class="col-md-12"> -->
							<br><br>
							<div class="col-md-1"></div>
							<div class="col-md-10">
							<div class="input-group">
								<span class="input-group-addon">Entidades</span>
								<select class="form-control" id="SelCliPro" name="SelCliPro" required>
									<option value="">Seleccione</option>
									<option value="C">Clientes</option>
									<option value="P">Proveedores</option>
								</select>
							</div>
							</div>
							<div class="clearfix"></div>
							<br>


							<div class="col-md-1"></div>
							<div class="col-md-10">
								<div class="form-group">
									<input type="file" class="filestyle" data-buttonText="Seleccione archivo" name="excel">
								</div>
								<input type="hidden" value="upload" name="action">
								<input type="hidden" name="swImport" id="swImport" value="S">

								<?php
									if ($Msj!="") {
										echo "<h2>".$Msj."</h2>";
									}
								?>

								<p>
									Esta seguro de realiza la carga del nuevo plan de cuenta para Clientes/Proveedores?
								</p>

								<div class="checkbox">
									<label><input type="checkbox" id="SwPago" name="SwPago" value="" onclick="ActivaBtn()"> Aceptar</label>
								</div>
								<div class="clearfix"></div>
								<br><br>
							</div>

							<input class="btn btn-default btn-file btn-block" type='submit' name='BtnVisual' id="BtnVisual" style="visibility:hidden;" value="Importar"  />
						<!-- </div> -->

					</div>
				</div>

				<div class="clearfix"></div>
				<br>				
			</div>
		</form>
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

	</body>

</html>


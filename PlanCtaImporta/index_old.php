<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		echo ('<script> window.location.href="../?Msj=95";</script>');
		exit;
	}

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
		// $STRInseert="INSERT INTO CTCuentasEmpresa VALUES ";
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
					$_DATOS_EXCEL[$i]['Codigo'] = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Nombre']= $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Tipo'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
					$_DATOS_EXCEL[$i]['Categoria'] = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
					
					$idCat="";

					$resultado = $mysqli->query("SELECT * FROM CTCategoria WHERE nombre='".utf8_decode($_DATOS_EXCEL[$i]['Categoria'])."' AND estado='A'");
					while ($registro = $resultado->fetch_assoc()) {
						$idCat=$registro["id"];
					}

					if ($idCat=="") {
						echo "W".$_DATOS_EXCEL[$i]['Codigo']."W";
						$Msj="Error en las Categorias";
					}

		 			$STRInseert=$STRInseert."INSERT INTO CTCuentasEmpresa VALUES('','$RutEmpresa','".$_DATOS_EXCEL[$i]['Codigo']."','".utf8_decode($_DATOS_EXCEL[$i]['Nombre'])."','".$idCat."','N','N','A'); ";
		 			// $STRInseert=$STRInseert."('','$RutEmpresa','".$_DATOS_EXCEL[$i]['Codigo']."','".utf8_decode($_DATOS_EXCEL[$i]['Nombre'])."','".$idCat."','N','A'), ";
				}
				// $errores=0;

				if ($Msj=="") {
					$mysqli->query("DELETE FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa'");
					$mysqli->query("UPDATE CTEmpresas SET ccosto='S', plan='S' WHERE rut='$RutEmpresa'");
					// echo $Msj;
					// echo "<br>";
					// echo $STRInseert;
					// echo "<br>";
					// echo $STRInseert;
					// echo "<br>";
					// mysqli_multi_query($mysqliX, $StrSql)
					$mysqli->multi_query($STRInseert);
					// $mysqli->query($STRInseert);
					$SwMes="S";
					// exit;
					$_SESSION["PLAN"]="S";
				}

				unlink($destino);
				$mysqli->close();
			}else{
				$Msj="Primero debes cargar el archivo con extencion .xlsx";
			}
		}else{
			$Msj="Primero debes cargar el archivo con extencion .xlsx";
		}

		// $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		// $CtaRegistros=0;
		// $SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_POST['SelRutEmp']."' ORDER BY numero";
		// $resultado = $mysqli->query("$SQL");
		// $CtaRegistros = $resultado->num_rows;
		// if ($CtaRegistros>0) {
		// 	$mysqli->query("DELETE FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa'");

		// 	$mysqli->query("UPDATE CTEmpresas SET ccosto='S', plan='S' WHERE rut='$RutEmpresa'");

		// 	$resultados = $mysqli->query($SQL);
		// 	while ($registro = $resultados->fetch_assoc()) {

		// 		if ($registro['auxiliar']=="") {
		// 			$laxu="N";
		// 		}else{
		// 			$laxu=$registro['auxiliar'];
		// 		}
		//  		$mysqli->query("INSERT INTO CTCuentasEmpresa VALUES('','$RutEmpresa','".$registro['numero']."','".$registro['detalle']."','".$registro['id_categoria']."','$laxu','A')");
		// 	}
		// 	$SwMes="S";
		// }else{
		// 	$SwMes="N";
		// }
		// $mysqli->close();
	}
?>

<?php 
	// extract($_POST);
	// $mysqli=ConCobranza();


?>


<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
		<script src="../js/jquery.dataTables.min.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="../css/StConta.css">

		<style>
			/* Remove the navbar's default margin-bottom and rounded borders */
			.navbar {
			margin-bottom: 0;
			border-radius: 0;
			}

			/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
			.row.content {height: 450px}

			/* Set gray background color and 100% height */
			.sidenav {
			padding-top: 20px;
			background-color: #f1f1f1;
			height: 100%;
			}

			/* Set black background color, white text and some padding */
			footer {
			background-color: #555;
			color: white;
			padding: 15px;
			}

			/* On small screens, set height to 'auto' for sidenav and grid */
			@media screen and (max-width: 767px) {
			.sidenav {
			height: auto;
			padding: 15px;
			}
			.row.content {height:auto;}
			}

		</style>
		<script type="text/javascript">
			function ActivaBtn(){
				if (document.getElementById("BtnVisual").style.visibility == "hidden") {
					document.getElementById("BtnVisual").style.visibility = "visible";
				}else{
					document.getElementById("BtnVisual").style.visibility = "hidden";
				}
			}
			function ClonaPlan(){
				form1.swImport.value="S";
				form1.submit();
			}
		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form name="form1" method="post" action="" enctype="multipart/form-data">
			<br>

			
			<div class="col-sm-4">
			</div>
			<div class="col-sm-4">
				<?php
					$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

					$SQL="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa'";
					$resultado = $mysqli->query("$SQL");
					$CtaRegistros = $resultado->num_rows;
					if ($CtaRegistros>0) {
						echo '
							<div class="panel panel-default">
							<div class="panel-heading">&#33;OPERACI&Oacute;N NO PERMITIDA!</div>
								<div class="panel-body">
									Movimientos contables ya registrados en esta empresa.<br>
									utilizar esta opci&oacute;n puede provocar inconsistencia en cuentas contables y en la consolidaci&oacute;n de la informaci&oacute;n disponible.<br>
									Para m&aacute;s Informaci&oacute;n cont&aacute;ctese con su asesor de soporte asignado.
								</div>
							</div>
						';
					}					
					$mysqli->close();
					// $CtaRegistros=0;
				?>

			</div>
			<div class="clearfix"></div>	
			<br>

			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<?php
					if ($CtaRegistros==0) {
				?>

				<div class="panel panel-default">
				<div class="panel-heading">IMPORTAR PLAN DE CUENTA</div>
					<div class="panel-body">


						<div class="col-md-4">
							<table class="table table-condensed">
								<thead>
									<tr>
										<th width="30%">Tipo</th>
										<th>Categoria</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

										$SQL="SELECT * FROM CTCategoria WHERE estado='A' ";
										$resultado = $mysqli->query("$SQL");
										while ($registro = $resultado->fetch_assoc()) {
											echo "
												<tr>
													<td>".$registro["tipo"]."</td>
													<td>".$registro["nombre"]."</td>
												</tr>
											";
										}
										$mysqli->close();
									?>
								</tbody>
							</table>
							<br>
							<a href="PlanEJEMPLO.xlsx" class="btn btn-info btn-block" role="button">Descargar Ejemplo</a>
						</div>


						<div class="col-md-8">
							

							<br><br>
							<div class="text-center">


								<div class="form-group">
									<input type="file" class="filestyle" data-buttonText="Seleccione archivo" name="excel">
								</div>
								<input type="hidden" value="upload" name="action">
								<input type="hidden" name="swImport" id="swImport">



								<?php
									if ($Msj!="") {
										echo "<h2>".$Msj."</h2>";
									}
								?>

								<p>
									Esta seguro de realiza la carga del nuevo plan de cuenta?
								</p>

								<div class="checkbox">
									<label><input type="checkbox" id="SwPago" name="SwPago" value="" onclick="ActivaBtn()"> Aceptar</label>
								</div>
								<div class="clearfix"></div>
								<br><br>
							</div>

							<input class="btn btn-default btn-file btn-block" type='button' name='BtnVisual' onclick="ClonaPlan()" id="BtnVisual" style="visibility:hidden;" value="Importar"  />
						</div>

					</div>
				</div>

				<div class="clearfix"></div>
				<br>				
				<?php
					}
				?>

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


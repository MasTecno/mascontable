<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	if(isset($_GET['OK'])){
		$SwMes="S";
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
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">
			function ActivaBtn(){
				if (document.getElementById("BtnVisual").style.visibility == "hidden") {
					document.getElementById("BtnVisual").style.visibility = "visible";
				}else{
					document.getElementById("BtnVisual").style.visibility = "hidden";
				}
			}
			function ClonaPlan(){

				if (confirm("Está a punto de realizar una carga masiva de voucher. Realizo la carga del archivo según las instrucciones. \n\nSi tiene dudas, cancele la operación, y contacte a soporte para guiar en este proceso. \n\nDesea continuar?") == true) {
					form1.swImport.value="S";
					form1.submit();
				}
			}
		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form name="form1" method="post" action="procesar.php" enctype="multipart/form-data">
			<br>

			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<div class="panel panel-default">
				<div class="panel-heading">Importador de Voucher Masivos</div>
					<div class="panel-body">


						<div class="col-md-4">
							<br>
							<a href="PlantillaVoucher.xlsx" class="btn btn-info btn-block" role="button">Descargar Ejemplo</a>
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
									Esta seguro de realiza la carga del archivo?
								</p>

								<div class="checkbox">
									<label><input type="checkbox" id="SwPago" name="SwPago" value="" onclick="ActivaBtn()"> Aceptar</label>
								</div>
								<div class="clearfix"></div>
								<br><br>
							</div>

							<input class="btn btn-default btn-file btn-block" type='button' name='BtnVisual' onclick="ClonaPlan()" id="BtnVisual" style="visibility:hidden;" value="Importar"  />


							<p>


							<div class="input-group" style="width: 100%;">
								<button type="button" class="btn btn-grabar btn-block" data-toggle="modal" data-target="#myModal">Instrucciones</button>
							</div>

							<!-- Modal -->
							<div class="modal fade" id="myModal" role="dialog" style="text-align: initial;">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Instrucciones Importación de Voucher</h4>
									</div>
									<div class="modal-body" style="font-size: 14px;">
										<strong>Importante</strong><br><br>
										1.	Debe tener un orden cronológico por fecha ascendente.<br><br>
										2.	La cantidad de líneas del voucher deben tener la misma glosa, con esto el sistema identifica la cantidad de líneas para el cierra del voucher.<br><br>
										3.	El siguiente voucher debe tener una glosa diferente a la anterior, para identificar que es un nuevo voucher.<br><br>

										<strong>Nota Importante:</strong>
										Asegúrese de revisar cada paso para evitar errores en la configuración y procesamiento.<br>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
							</div>



							</p>
						</div>

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


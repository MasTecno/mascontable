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

	$Ldmes = substr($Periodo,0,2);
	$Ldano = substr($Periodo,3,4);
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">
			function ckSi(){
				form1.mescon.value="S";
				TempGrilla();
			}

			function anual(){
				form1.mescon.value="";
				TempGrilla();
			}


			function Proce(){
				document.getElementById("BtrProce").style.display = 'none';
				document.getElementById("Mensa").style.display = 'inline';
				var url= "xfrmProcesar.php";

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						document.getElementById("BtrProce").style.display = 'inline';
						document.getElementById("Mensa").style.display = 'none';
						TempGrilla();
					}
				});
			}

			function TempGrilla() {
				var url= "ReportCaja.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grillaX').html(resp);
					}
				});
			}


			function Conf(l1){
				form1.Id14D.value=l1;
			}
			
			function Btr(X1){
				$Urt="ReportPDF.php";
				if(document.getElementById("excel").checked == true){
					$Urt="ReportXLS.php";
				}

				if (X1==1) {
					// $Urt="ReportCaja.php";	
					// $Urt="ReportPDF.php";
					form1.Report.value="ReportCaja.php";
				}
				if (X1==2) {
					// $Urt="ReportIngEgr.php?Movi=Ing";	
					// $Urt="ReportPDF.php";
					form1.Report.value="ReportIngEgr.php";
					form1.Movi.value="Ing";
				}
				if (X1==3) {
					// $Urt="ReportPDF.php";
					form1.Report.value="ReportIngEgr.php";
					form1.Movi.value="Egr";
					// $Urt="ReportIngEgr.php?Movi=Egr";	
				}

				form1.method="POST";
				form1.target="_blank";
				form1.action=$Urt;
				form1.submit();
				form1.target="";
				form1.action="#"; 
			}

		</script>
	</head>
	<body onload="TempGrilla()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<form action="#" method="POST" name="form1" id="form1">
				<input type="hidden" name="mescon" id="mescon">
				<input type="hidden" name="Report" id="Report">
				<input type="hidden" name="Movi" id="Movi">
			<br>

			<div class="col-md-1"></div>
			<div class="col-md-10">


				<div class="col-md-2 text-center">
					<h4>Genera 14D</h4>

					<div class="col-md-12">
					<div class="input-group" style="width: 100%;">
						<button type="button" class="btn btn-danger btn-block" id="BtrProce" onclick="Proce()">Procesar</button>
					</div>
					</div>
				</div>
				
				<div class="col-md-6 text-center">
					<h4>Genera reporte por mes</h4>
					<div class="col-md-6 text-right">
						<div class="input-group">
							<span class="input-group-addon">Mes</span>

							<select class="form-control" id="messelect" name="messelect">
							<?php 
								$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
								$i=1;
								
								$Ldmes=$Ldmes*1;
								while($i<=12){

									if ($i==$Ldmes) {
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
							<select class="form-control" id="anoselect" name="anoselect">
							<?php 
								$yoano=date('Y');
								$tano="2010";

								while($tano<=($yoano+1)){
									if ($Ldano==$tano) {
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

					<div class="col-md-6">
					<div class="input-group" style="width: 100%;">
						<button type="button" class="btn btn-success" onclick="ckSi()">Genera Informe Mensual</button>
					</div>
					</div>					
					<div class="col-md-6">
					<div class="input-group" style="width: 100%;">
						<button type="button" class="btn btn-success" onclick="anual()">Informe Anual</button>
					</div>
					</div>					
				</div>
				<div class="col-md-4">
					<h4>Reporteria</h4>

					<div class="checkbox">
						<input type="checkbox" name="ConMem" id="ConMem" value="">Membrete
					</div>						

					<div class="checkbox">
						<input type="checkbox" name="ConRep" id="ConRep" value="">Insertar Representante Legal en Membrete
					</div>						

					<div class="checkbox">
						<input type="checkbox" name="MarSup" id="MarSup" value="">Margen Superior
						<input class="text-right" type="text" name="nlines" id="nlines" value="4" maxlength="2" size="3">
					</div>

					<div class="checkbox">
						<input type="checkbox" name="MarFol" id="MarFol" value="" checked>Folio Inicial PDF
						<input class="text-right" type="text" name="folio" id="folio" value="1" maxlength="20" size="3">
					</div>

					<div class="clearfix"></div>
					<br>

					<div class="col-md-6">
					<div class="input-group">
						<input type="checkbox" name="tos" value="accepted" <?php if(isset($_POST['tos']) && $_POST['tos']==='accepted'){ echo "checked"; } ?> onclick="ckSi()"> Aplicar Neto a Facturas
					</div>
					</div>

					<div class="col-md-6">
					<div class="input-group">
						<input type="checkbox" name="excel" value="excel" id="excel"> Generar Excel
					</div>
					</div>
					
					<div class="clearfix"></div>
					<br>

					<div class="col-md-4">
					<div class="input-group">
						<button type="button" class="btn btn-warning" onclick="Btr('1')"><span class="glyphicon glyphicon-book"></span> Reporte Caja</button>
					</div>
					</div>

					<div class="col-md-4">
					<div class="input-group">
						<button type="button" class="btn btn-warning" onclick="Btr('2')"><span class="glyphicon glyphicon-book"></span> Reporte Ingreso</button>
					</div>
					</div>

					<div class="col-md-4">
					<div class="input-group">
						<button type="button" class="btn btn-warning" onclick="Btr('3')"><span class="glyphicon glyphicon-book"></span> Reporte Egreso</button>
					</div>
					</div>					
				</div>


				<div class="clearfix"></div>
				<br>

				<div class="col-md-12">
				<div class="input-group" style="width: 100%;">
					<div class="alert alert-warning" id="Mensa" style="background-color: #fbc7c7; border-color: #b35c5c; display:none; text-align: center;">
						<strong>Generando!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
					</div>					
				</div>
				</div>
				
				<hr>

			</div>

			<div class="clearfix"></div>
			<br>

			<div class="col-md-1"></div>
			<div class="col-md-10">
				<l id="grillaX"></l>
			</div>

			<div class="modal fade" id="Autoriza" role="dialog">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Autorizaci&oacute;n Manual</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="pwd">Afecta Base Imponible</label>

								<select class="form-control" id="AfeImp" name="AfeImp">
								<?php 
									echo '<option value="N">NO</option>';
									echo '<option value="S">SI</option>';
								?>
								</select>
						</div>

						<input type="hidden" name="Id14D" id="Id14D">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" onclick="TempGrilla()">Confirmar</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
			</div>

		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>
</html>
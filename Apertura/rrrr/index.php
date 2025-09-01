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

	$PerInsert = substr($Periodo,3,4);
	$PerInsert = $Periodo;
	// $PerInsert = "12-".($PerInsert-0);
	// $PerInsert = $_POST['PApertura1'];

	$CantHono=0;
	$CantCoVe=0;

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT count(*) as CantHono FROM CTHonorarios WHERE origen='Z' AND periodo='$PerInsert' AND movimiento='' AND rutempresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) { 
		$CantHono=$registro["CantHono"];
	}
	
	$SQL="SELECT count(*) as CantCoVe FROM CTRegDocumentos WHERE origen='Z' AND periodo='$PerInsert' AND keyas='' AND rutempresa='$RutEmpresa'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) { 
		$CantCoVe=$registro["CantCoVe"];
	}
	
	$mysqli->close();

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

			function data(valor){
				ProBal.cuenta.value=valor;
				document.getElementById("cmodel").click();
			}

			function acept(){
				sw = document.getElementById("ace").checked;

				if (sw==false) {
					document.getElementById("bt").classList.remove("active");
					document.getElementById("bt").classList.add("disabled");
				}else{
					document.getElementById("bt").classList.remove("disabled");
					document.getElementById("bt").classList.add("active");
				}
			}

			function acept1(){
				sw = document.getElementById("ace1").checked;

				if (sw==false) {
					document.getElementById("bt1").classList.remove("active");
					document.getElementById("bt1").classList.add("disabled");
				}else{
					document.getElementById("bt1").classList.remove("disabled");
					document.getElementById("bt1").classList.add("active");
				}
			}

			// function GenAsiApe(){
			// 	GenAsiApertura.action="CrearAsientoApertura.php";
			// 	GenAsiApertura.submit();
			// }

			function GenAsiApe(){
				GenAsiApertura.action="PlantillaXLS.php";
				GenAsiApertura.submit();
			}

			function CarAsiApe(){
				if(GenAsiApertura.CsvCuentas.value==""){
					alert("No a seleccionado el archivo para la carga");
				}else{
					GenAsiApertura.action="XPlantillaXLS.php";
					GenAsiApertura.submit();
				}
			}

			function UpAsiento(){
				var url= "DatosAsiento.php";
				$.ajax({
					type: "POST",
					dataType: 'json',
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$("#TDebe").html(resp.dato1);
						$("#THaber").html(resp.dato2);
					}
				});	
			}
			function Ref(){
				UpAsiento();
			}
			function GrHono(){
				if (GenAsiApertura.CsvHonorario.value=="") {
					alert("Seleccione el archivo a procesar");
				}else{				
					GenAsiApertura.action="xProcesaHonorario.php";
					GenAsiApertura.submit();
				}
			}
			function GrComVen(){
				if (GenAsiApertura.CsvCompraVenta.value=="") {
					alert("Seleccione el archivo a procesar");
				}else{
					GenAsiApertura.action="xProcesaCompraVenta.php";
					GenAsiApertura.submit();					
				}
			}
			function DescHono(){
					GenAsiApertura.action="CargaMasivaH.csv";
					GenAsiApertura.submit();
			}
			function DescVenCom(){
				GenAsiApertura.action="CargaMasivaCV.csv";
				GenAsiApertura.submit();
			}
			jQuery(document).ready(function(e) {
				$('#myModal').on('shown.bs.modal', function() {
					$('input[name="BCodigo"]').focus();
				});
			});
		</script>
	</head>

	<body onload="Ref()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-sm-6">
				<form action="ProcesaApertura.php" method="POST" id="ProBal" name="ProBal">
					
					<div class="panel panel-default">
						<div class="panel-heading">Apertura a partir de Balance Periodo Anterior</div>
						<div class="panel-body">



							<!-- <div class="clearfix"></div>
							<br> -->
							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">A&ntilde;o Balance</span>
									<select class="form-control" id="anoselect" name="anoselect" required>
									<?php 
										$yoano=date('Y');
										$tano="2010";

										while($tano<=($yoano+1)){
											if (($yoano-1)==$tano) {
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

							<div class="col-md-6">
								<div class="input-group">
										<span class="input-group-addon">A&ntilde;o Apertura</span>
										<input type="text" class="form-control" id="PApertura" name="PApertura" readonly value="<?php echo $Periodo; ?>">
								</div>
							</div>

							<div class="clearfix"></div>
							<br>
							<div class="col-md-6">
								<div class="input-group"> 
									<span class="input-group-addon">Cuenta Contable</span>
									<input type="text" class="form-control text-right" id="cuenta" name="cuenta" required value="<?php echo $cuenta; ?>"> 
									<div class="input-group-btn"> 
										<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
											<span class="glyphicon glyphicon-search"></span> 
										</button>
									</div> 
								</div>
							</div>

							<div class="clearfix"></div>
							<br>

							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Glosa</span>
									<input type="text" class="form-control" id="glosa" name="glosa" autocomplete="off" required onChange="javascript:this.value=this.value.toUpperCase();">
								</div>            
							</div>

							<div class="clearfix"></div>
							<br>

							<div class="col-sm-12">
								<label class="checkbox-inline"><input type="checkbox" onclick="acept()" id="ace" name="ace">Aceptar</label> 
								<p>* Considero que el Balance que estoy seleccionado esta correcto y traspasare su informaci&oacute;n.</p>
								<p>** Este Proceso puede ser ejecutado en cualquier momento, si ser&aacute; insertado en Enero del A&ntilde;o de Apertura.</p>
								<p>*** La cuenta seleccionada corresponde para el proceso de Apertura.</p>
								<p>**** Este proceso no cierra periodos.</p>         	
							</div>

							<div class="clearfix"></div>
							<br>

							<div class="col-sm-12 ">
								<button type="submit" class="btn btn-default disabled" onclick="Porce()" id="bt" name="bt">Procesar</button>
							</div>
						</div>
					</div>

					<!-- Modal  buscar codigo-->
					<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Listado de Cuentas</h4>
						</div>

						<div class="modal-body">
							<div class="col-md-12">
								<input class="form-control" id="BCodigo" name="BCodigo" type="text" placeholder="Buscar...">
							</div>
							<div class="col-md-12">

								<table class="table table-condensed table-hover">
									<thead>
										<tr>
											<th>Codigo</th>
											<th>Detalle</th>
											<th>Tipo de Cuenta</th>
										</tr>
									</thead>

									<tbody id="TableCod">
										<?php 
											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											if ($_SESSION["PLAN"]=="S"){
												$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
											}else{
												$SQL="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
											}
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) { 

												$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) { 
													$tcuenta=$registro1["nombre"];
												}

												echo '
													<tr onclick="data(\''.$registro["numero"].'\')">
													<td>'.$registro["numero"].'</td>
													<td>'.strtoupper($registro["detalle"]).'</td>
													<td>'.$tcuenta.'</td>
													</tr>
												';
											}
											$mysqli->close();
										?>
									</tbody>
								</table>
								<script>
									$(document).ready(function(){
										$("#BCodigo").on("keyup", function() {
										var value = $(this).val().toLowerCase();
											$("#TableCod tr").filter(function() {
											$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
										});
										});
									});
								</script>								
							</div>
						</div>

					<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" id="cmodel">Cerrar</button>
					</div>
					</div>
					</div>
					</div>
					<!-- fin buscar codigo -->   

				</form>				
			</div>

			<div class="col-md-6">

				<form action="ProcesaAperturaMigra.php" method="POST" id="GenAsiApertura" name="GenAsiApertura" enctype="multipart/form-data">
					<div class="panel panel-default">
						<div class="panel-heading">Apertura por Migraci&oacute;n</div>
						<div class="panel-body">
								<div class="well well-sm">Asiento de Apertura</div>


								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-sm-3">Seleccionar Archivo</label>
										<div class="col-sm-6">
											<input type="file" class="form-control-file" id="CsvCuentas" name="CsvCuentas" aria-describedby="fileHelp">
											<small id="fileHelp" class="form-text text-muted">* Solo archivo CSV.</small><br>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<br>

								<div class="col-md-12">
									<button type="button" class="btn btn-default" onclick="CarAsiApe()">Cargar Cuentas</button>
								</div>

								<div class="clearfix"></div>
								<br>

									<table class="table table-striped">
									<thead>
										<tr class="">
											<th>Debe</th>
											<th>Haber</th>
										</tr>
									</thead>
									<tbody>
										<tr class="">
											<td id="TDebe">0</td>
											<td id="THaber">0</td>
										</tr>
									</tbody>
									</table>

								<div class="clearfix"></div>
								<br>
								
								<div class="well well-sm">Libros</div>
								
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-sm-3">Archivo Compra-Venta</label>
										<div class="col-sm-6">
											<input type="file" class="form-control-file" id="CsvCompraVenta" name="CsvCompraVenta" aria-describedby="fileHelp">
											<small id="fileHelp" class="form-text text-muted">* Solo archivo CSV.</small><br>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<br>
								<div class="col-md-6">
									<button type="button" class="btn btn-default" onclick="GrComVen()">Cargar Compras y Ventas</button>
								</div>
								<div class="col-md-6">
									Documentos Cargados <?php echo $CantCoVe; ?>
								</div>
								<div class="clearfix"></div>
								<br>

								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label col-sm-3">Archivo Honorario</label>
										<div class="col-sm-6">
											<input type="file" class="form-control-file" id="CsvHonorario" name="CsvHonorario" aria-describedby="fileHelp">
											<small id="fileHelp" class="form-text text-muted">* Solo archivo CSV.</small><br>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<br>
								<div class="col-md-6">
									<button type="button" class="btn btn-default" onclick="GrHono()">Cargar Honorarios</button>
								</div>
								<div class="col-md-6">
									Documentos Cargados <?php echo $CantHono; ?>
								</div>
								<div class="clearfix"></div>
								<br>

								<div class="well well-sm">Descarga Archivos Tipo</div>

								<div class="col-md-4">
									<button type="button" class="btn btn-default" onclick="GenAsiApe()">Descarga Plan de Cuenta</button>
								</div>
								<div class="col-md-4">
									<button type="button" class="btn btn-default" onclick="DescVenCom()">Descarga Compra y Ventas</button>
								</div>
								<div class="col-md-4">
									<button type="button" class="btn btn-default" onclick="DescHono()">Descarga Honorarios</button>
								</div>
								<div class="clearfix"></div>
								<br>


								<div class="clearfix"></div>
								<br>
	
								<div class="col-md-6">
									<div class="input-group">
											<span class="input-group-addon">A&ntilde;o Apertura</span>
											<input type="text" class="form-control" id="PApertura1" name="PApertura1" readonly value="<?php echo $Periodo; ?>">
									</div>
								</div>

								<div class="clearfix"></div>
								<br>

								<div class="col-md-12">
									<div class="input-group">
										<span class="input-group-addon">Glosa</span>
										<input type="text" class="form-control" id="xglosa1" name="xglosa1" autocomplete="off" required onChange="javascript:this.value=this.value.toUpperCase();">
									</div>            
								</div>

								<div class="clearfix"></div>
								<br>

								<div class="col-sm-12">
									<label class="checkbox-inline"><input type="checkbox" onclick="acept1()" id="ace1" name="ace1">Aceptar</label> 
									<p>* Considero que la informaci&oacute;n, en el asinto de apertura esta correcta.</p>
									<p>** Los archivos de Compra, Venta y Honorarios, estan correctos.</p>
									<p>*** Este proceso no cierra periodos.</p>         	
								</div>

								<div class="clearfix"></div>
								<br>

								<div class="col-sm-12 ">
									<button type="submit" class="btn btn-default disabled" id="bt1" name="bt1">Procesar</button>
								</div>


						</div>
					</div>
				</form>
				
			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


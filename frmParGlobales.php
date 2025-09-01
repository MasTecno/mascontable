<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
	
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if($registro['tipo']=="IVA"){
			$DIVA=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];	
		}

		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];	
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];	
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];	
		}	

		if($registro['tipo']=="RETE_HONO"){
			$DPORC=$registro['valor'];	
		}	

		if($registro['tipo']=="RETE_FACT"){
			$DFACT=$registro['valor'];	
		}	
		if($registro['tipo']=="PPM"){
			$DPPM=$registro['valor'];	
		}	

		// if($registro['tipo']=="CUEN_REND"){
		// 	$CUENANT=$registro['valor'];	
		// }

		// if($registro['tipo']=="ANTI_PROV"){
		// 	$ANTIPRO=$registro['valor'];	
		// }
		// if($registro['tipo']=="ANTI_CLIE"){
		// 	$ANTICLI=$registro['valor'];	
		// }	 
		
		if($registro['tipo']=="CERO_FOLI"){
			$CFOLIO=$registro['valor'];	
		}
		if($registro['tipo']=="TEXT_FOLI"){
			$TFOLIO=$registro['valor'];	
		}
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
			function Volver(){
				form1.action="frmMain.php";
				form1.submit();
			}

			function data(valor){
				var cas=form1.casilla.value;
				var r=cas.substr(0,4);
				document.getElementById(cas).value=valor;
				document.getElementById("cmodel").click();
			}

			jQuery(document).ready(function(e) {
				$('#myModal').on('shown.bs.modal', function() {
					$('input[name="BCodigo"]').focus();
				});
			});

		</script>
	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="XfrmParGlobales.php" method="POST" name="form1" id="form1">
			<input type="hidden" name="casilla" id="casilla">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<br>
				<div class="well well-sm text-center" style="background-color: #e51c20; color: white;">	
					<h4>Parametros Globales</h4>
				</div>	

				<hr>
				<div class="well well-sm" style="background-color: #e51c20; color: white;">
					<strong>Datos da calculo y Moneda</strong>
				</div>				
				<form action="xfrmCuentas.php" method="POST" name="form1" id="form1">

					<div class="col-md-3">
						<label for="numero">IVA</label>
						<input type="text" class="form-control text-right" id="DIVA" name="DIVA" maxlength="2" value="<?php echo $DIVA; ?>" required>
					</div> 

					<div class="col-md-3">
						<label for="numero">S&iacute;mbolo Moneda</label>
						<input type="text" class="form-control" id="DMONE" name="DMONE" maxlength="3" value="<?php echo $DMONE; ?>" required>
					</div> 
					<div class="clearfix"> </div>

				<hr>
				<div class="well well-sm" style="background-color: #e51c20; color: white;">
					<strong>Datos de N&uacute;meros y Lista</strong>
				</div>		

					<div class="col-md-3">
						<label for="numero">Separador de Miles</label>
			            <select class="form-control" id="DMILE" name="DMILE" required>
			              <?php 
			              		if ($DMILE==",") {
			              			echo "<option value=',' selected> , Coma</option>";
			              			echo "<option value='.'> . Punto</option>";
			              		}else{
			              			echo "<option value=','> , Coma</option>";
			              			echo "<option value='.' selected> . Punto</option>";
			              		}
			               ?>
			           </select>
					</div> 

					<div class="col-md-3">
						<label for="numero">Decimal</label>
			            <select class="form-control" id="DDECI" name="DDECI" required>
			              <?php 
			              		if ($DDECI==",") {
			              			echo "<option value=',' selected> , Coma</option>";
			              			echo "<option value='.'> . Punto</option>";
			              		}else{
			              			echo "<option value=','> , Coma</option>";
			              			echo "<option value='.' selected> . Punto</option>";
			              		}
			               ?>
			           </select>
					</div> 			        

					<div class="col-md-3">
						<label for="numero">Separador de Lista</label>
			            <select class="form-control" id="DLIST" name="DLIST" required>
			              <?php 
			              		if ($DLIST==",") {
			              			echo "<option value=',' selected> , Coma</option>";
			              			echo "<option value=';'> ; Punto y coma</option>";
			              		}else{
			              			echo "<option value=','> , Coma</option>";
			              			echo "<option value=';' selected> ; Punto y coma</option>";
			              		}
			               ?>
			           </select>
					</div> 			        

					<div class="col-md-3">
						<label for="numero">Cantidad de Decimales</label>
			            <input type="text" class="form-control text-right" id="NDECI" name="NDECI" maxlength="3" value="<?php echo $NDECI; ?>" required>
					</div> 			        

					<div class="clearfix"> </div>

				<hr>
				<div class="well well-sm" style="background-color: #e51c20; color: white;">
					<strong>Parametros Honorarios</strong>
				</div>	

					<div class="col-md-3">
						<label for="numero">% Retenci&oacute;n</label>
						<input type="text" class="form-control text-right" id="DPORC" name="DPORC" maxlength="2" value="<?php echo $DPORC; ?>" readonly>
					</div> 

					<div class="col-md-3">
						<label for="numero">Factor Retenci&oacute;n</label>
						<input type="text" class="form-control text-right" id="DFACT" name="DFACT" maxlength="3" value="<?php echo $DFACT; ?>" readonly>
					</div> 

					<div class="clearfix"> </div>

				<hr>	
				<div class="well well-sm" style="background-color: #e51c20; color: white;">
					<strong>Parametros 14Ter</strong>
				</div>	
					<div class="col-md-3">
						<div class="input-group"> 
							<span class="input-group-addon">Cuenta PPM</span>
							<input type="text" class="form-control text-right" id="Comp1" name="Comp1" value="<?php echo $DPPM; ?>"> 
							<div class="input-group-btn"> 
								<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp1'" >
									<span class="glyphicon glyphicon-search"></span> 
								</button>
							</div> 
						</div>
					</div> 

					<div class="clearfix"> </div>

				<hr>	


				<div class="well well-sm" style="background-color: #e51c20; color: white;">
					<strong>Folio de Documentos (Largo de 3 caracteres: 001, 002, 003, etc. --- Texto: Folio: 001)</strong>
				</div>	
					<div class="col-md-3">
						<label for="numero">Largo de Folio</label>
						<input type="text" class="form-control text-right" id="CFOLIO" name="CFOLIO" maxlength="5" value="<?php echo $CFOLIO; ?>" required>
					</div> 

					<div class="col-md-3">
						<label for="numero">Texto folio</label>
						<select class="form-control" id="TFOLIO" name="TFOLIO" required>
							<?php 
								if ($TFOLIO=="NO") {
									echo "<option value='NO' selected>NO</option>";
									echo "<option value='SI'>SI</option>";
								}else{
									echo "<option value='NO'>NO</option>";
									echo "<option value='SI' selected>SI</option>";
								}
							?>
						</select>
					</div> 			        
					<div class="clearfix"> </div>

				<hr>	


					<!-- <div class="well well-sm">
						<strong>Configuraci&oacute;n de Anticipos y Rendiciones</strong>
					</div>	
					<div class="col-md-4">
						<div class="input-group"> 
							<span class="input-group-addon">Rendiciones</span>
							<input type="text" class="form-control text-right" id="Comp4" name="Comp4" value="<?php echo $CUENANT; ?>"> 
							<div class="input-group-btn"> 
								<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp4'" >
									<span class="glyphicon glyphicon-search"></span> 
								</button>
							</div> 
						</div>
					</div> 

					<div class="col-md-4">
						<div class="input-group"> 
							<span class="input-group-addon">Anticipo Proveedores</span>
							<input type="text" class="form-control text-right" id="Comp2" name="Comp2" value="<?php echo $ANTIPRO; ?>"> 
							<div class="input-group-btn"> 
								<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp2'" >
									<span class="glyphicon glyphicon-search"></span> 
								</button>
							</div> 
						</div>
					</div> 

					<div class="col-md-4">
						<div class="input-group"> 
							<span class="input-group-addon">Anticipo Clientes</span>
							<input type="text" class="form-control text-right" id="Comp3" name="Comp3" value="<?php echo $ANTICLI; ?>"> 
							<div class="input-group-btn"> 
								<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp3'" >
									<span class="glyphicon glyphicon-search"></span> 
								</button>
							</div> 
						</div>
					</div> 
					<br><br> -->

					<!-- Modal  buscar codigo-->
					<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
						<div class="modal-header">
							<!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
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
												while ($registro1= $resultados1->fetch_assoc()) {
													$tcuenta=$registro1["nombre"];
												}

												echo '<tr onclick="data(\''.$registro["numero"].'\')">
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

					<div class="clearfix"> </div>


					<hr>
					<div class="col-md-12">

						<button type="submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
						</button>

						<button type="button" class="btn btn-danger" onclick="Volver()">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</button>   
						<p></p>
					</div>

					<div class="clearfix"> </div>
				</form>				
			</div>
			<div class="col-sm-2">
			</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include 'footer.php'; ?>

	</body>
</html>
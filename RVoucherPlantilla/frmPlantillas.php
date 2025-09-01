<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';
	
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTAsientoPlantilla WHERE id='".$_POST['casilla']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$NombAsi=$registro['nombre'];
		$TipMov=$registro['tipo'];
	}

	$TIng="";
	$TEgr="";
	$TTra="";

	if($TipMov=="I"){
		$TIng="selected";
	}else{
		if($TipMov=="E"){
			$TEgr="selected";
		}else{
			if($TipMov=="T"){
				$TTra="selected";
			}					
		}
	}

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}
		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}


	$mysqli->close();

	$textfecha="01-".$_SESSION['PERIODO'];

?>
<!DOCTYPE html>
<html >
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
			.ui-widget.ui-widget-content {
				z-index: 9999 !important;
			}
		</style>
		<script type="text/javascript">
		
			function BuscaCuenta(vall){
			      var url= "../buscacuenta.php";
			      var x1=$('#'+vall).val();
			      $.ajax({
			        type: "POST",
			        url: url,
			        data: ('dat1='+x1),
			        success:function(resp)
			        {

			          var r=Number(vall.substr(4, 1));
			          var r='DComp'+r;

			          if(resp==""){
			            alert("No se encontro cuenta");
			            $('#'+vall).focus(); 
			            $('#'+vall).select();
			            document.getElementById(r).value="";
			          }else{
			            document.getElementById(r).value=resp;
			          }
			        }
			      }); 
			}


			function BuscaCuentaV(vall){
			      var url= "../buscacuenta.php";
			      var x1=$('#'+vall).val();
			      $.ajax({
			        type: "POST",
			        url: url,
			        data: ('dat1='+x1),
			        success:function(resp)
			        {

			          var r=Number(vall.substr(5, 1));
			          var r='DVenta'+r;

			          if(resp==""){
			            alert("No se encontro cuenta");
			            $('#'+vall).focus(); 
			            $('#'+vall).select();
			            document.getElementById(r).value="";
			          }else{
			            document.getElementById(r).value=resp;
			          }
			        }
			      }); 
			}

		function data(valor){
			var cas=form1.casilla.value;
			document.getElementById(cas).value=valor;
			BuscaCuenta(form1.casilla.value);
			document.getElementById("cmodel").click();
		}


		// function sumar (valor) {
		// 	if (valor=="") {
		// 		valor=0;
		// 	}
		// 	var total = 0;	
		// 	valor = parseInt(valor); // Convertir el valor a un entero (número).

		// 	total = document.getElementById('TDebe').value;

		// 	// Aquí valido si hay un valor previo, si no hay datos, le pongo un cero "0".
		// 	// if (parseInt(value)== NaN) {
		// 	// 	value=0;
		// 	// }
		// 	// valor === NaN ? 0 : valor;
		// 	// valor = (valor == null || valor == undefined || valor == NaN || valor == "") ? 0 : valor;
		// 	total = (total == null || total == undefined || total == NaN || total == "") ? 0 : total;

		// 	/* Esta es la suma. */
		// 	total = (parseInt(total) + parseInt(valor));

		// 	// Colocar el resultado de la suma en el control "span".
		// 	document.getElementById('TDebe').value = total;
		// }

		function Valida(r1){
			if (form1.TDebe.value!="" && form1.TDebe.value>0) {
				if (form1.THaber.value!="" && form1.THaber.value>0) {
					MDebe=form1.TDebe.value;
					MHaber=form1.THaber.value;
					if (MDebe!=MHaber) {
						alert("Error en el cuadra del Voucher");
					}else{
						if (form1.TFecha.value=="") {
							alert("Debe Asignar una Fecha");
						}else{
							if (form1.Glosa.value=="") {
								alert("Asignar Glosa");
							}else{
								if (form1.ttmovimiento.value=="") {
									alert("Definir Tipo de Movimiento");
								}else{
									form1.swupdate.value=r1;
									form1.action="xfrmPlantillas.php";
									form1.submit();
								}
							}
						}
					}
				}
			}
		}

		$( function() {
			$("#TFecha").datepicker();
		} );
		function Volver(){
			history.go(-1);
		}

		</script> 
	</head>

	<body>


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="#" method="POST" name="form1" id="form1">
			<input type="hidden" name="casilla" id="casilla">
			<input type="hidden" name="swupdate" id="swupdate">
			<input type="hidden" name="NomPlantilla" id="NomPlantilla" value="<?php echo $NombAsi; ?>">

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
													<th style="text-align: right;">Codigo</th>
													<th>&nbsp;&nbsp;</th>
													<th>Cuenta</th>
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
															<td style="text-align: right;">'.$registro["numero"].'</td>
															<td>&nbsp;&nbsp;</td>
															<td>'.$registro["detalle"].'</td>
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

											jQuery(document).ready(function(e) {
												$('#myModal').on('shown.bs.modal', function() {
													$('input[name="BCodigo"]').focus();
												});
											});

										</script>

									</div>
									<div class="clearfix"></div>
									<br>
								</div>

								<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal" id="cmodel">Cerrar</button>
								</div>
							</div>
						</div>
						</div>




			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">            
				<h3>Plantilla de Voucher</h3> 
				<input type="hidden" name="keyas" id="keyas" value="<?php echo $_POST['KeyMod']; ?>">

					<div class="col-md-4">
					<div class="input-group">
					<span class="input-group-addon">Tipo</span>
						<select class="form-control" id="ttmovimiento" name="ttmovimiento" required>
							<option value="">Seleccionar...</option>
							<option value="I" <?php echo $TIng; ?>>Ingreso</option>
							<option value="E" <?php echo $TEgr; ?>>Egreso</option>
							<option value="T" <?php echo $TTra; ?>>Traspaso</option>
						</select>
					</div>
					</div>

					<div class="clearfix"></div>
					<br>


					<div class="col-md-2">
						<label>Fecha</label>
						<input id="TFecha" name="TFecha" type="text" class="form-control" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
					</div> 

					<div class="clearfix"></div>
 
					<div class="col-md-2">
						<label>Cuenta</label>
						<?php

							$Nlinea=1;
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$SQL="SELECT * FROM CTAsientoPlantilla WHERE nombre='$NombAsi' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								if ($registro['glosa']=="") {
									echo '
										<div class="input-group"> 
											<input type="number" class="form-control text-right" id="Comp'.$Nlinea.'" name="Comp'.$Nlinea.'" required maxlength="50" value="'.$registro['cuenta'].'">
											<div class="input-group-btn"> 
													<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value=\'Comp'.$Nlinea.'\'">
													<span class="glyphicon glyphicon-search"></span>
												</a>
											</div>
										</div>
									';
									$Nlinea++;
								}else{
									$Xglosa=$registro['glosa'];
								}
							}
							$mysqli->close();
							$i=1;
							while($i<5){
								echo '
									<div class="input-group"> 
										<input type="number" class="form-control text-right" id="Comp'.$Nlinea.'" name="Comp'.$Nlinea.'" required maxlength="50" value="'.$registro['cuenta'].'">
										<div class="input-group-btn"> 
												<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value=\'Comp'.$Nlinea.'\'">
												<span class="glyphicon glyphicon-search"></span>
											</a>
										</div>
									</div>
								';
								$Nlinea++;
								$i++;						
							}


						?>
					</div>



					<div class="col-md-4">
						<label>Detalle</label>  

						<?php
							$Nlinea=1;
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$SQL="SELECT * FROM CTAsientoPlantilla WHERE nombre='$NombAsi' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
									if ($_SESSION["PLAN"]=="S"){
										$SqlStr="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro['cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}else{
										$SqlStr="SELECT * FROM CTCuentas WHERE numero='".$registro['cuenta']."'";
									}
									$Res = $mysqli->query($SqlStr);
									while ($Reg = $Res->fetch_assoc()) {
										$NomCue=$Reg['detalle'];
									}

									echo '
										<input type="text" class="form-control" id="DComp'.$Nlinea.'" name="DComp'.$Nlinea.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.strtoupper($NomCue).'" readonly="false" >
									';
									$Nlinea++;

							}
							$mysqli->close();

							$i=1;
							while($i<5){
									echo '
										<input type="text" class="form-control" id="DComp'.$Nlinea.'" name="DComp'.$Nlinea.'" onChange="javascript:this.value=this.value.toUpperCase();" value="" readonly="false" >
									';
								$Nlinea++;
								$i++;						
							}

						?>
					</div>

					<div class="col-md-2">
						<label>Debe</label>  

						<?php
							$Nlinea=1;
							$Valor="";
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$SQL="SELECT * FROM CTAsientoPlantilla WHERE nombre='$NombAsi' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								echo '
									<input type="text" class="form-control text-right" id="Debe'.$Nlinea.'" name="Debe'.$Nlinea.'" onchange="RefTotal()" oninput="MilesManu(this.value, this.id)" value="'.number_format((float)$Valor, $NDECI, $DDECI, $DMILE).'" >
								';
								$Nlinea++;
							}
							$mysqli->close();
							$i=1;
							while($i<5){
									echo '
										<input type="text" class="form-control text-right" id="Debe'.$Nlinea.'" name="Debe'.$Nlinea.'" onchange="RefTotal()" oninput="MilesManu(this.value, this.id)" value="" >
									';
								$Nlinea++;
								$i++;
							}


						?>
					</div>

					<div class="col-md-2">
						<label>Haber</label>  

						<?php
							$Nlinea=1;
							$Valor="";
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$SQL="SELECT * FROM CTAsientoPlantilla WHERE nombre='$NombAsi' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								echo '
									<input type="text" class="form-control text-right" id="Haber'.$Nlinea.'" name="Haber'.$Nlinea.'" onchange="RefTotal()" oninput="MilesManu(this.value, this.id)" value="'.number_format((float)$Valor, $NDECI, $DDECI, $DMILE).'" >
								';
								$Nlinea++;
							}
							$mysqli->close();
							$i=1;
							while($i<5){
									echo '
										<input type="text" class="form-control text-right" id="Haber'.$Nlinea.'" name="Haber'.$Nlinea.'" onchange="RefTotal()" oninput="MilesManu(this.value, this.id)" value="" >
									';
								$Nlinea++;
								$i++;						
							}

						?>
					</div>

					<div class="col-md-2">
						<label>Centro Costo</label>
						<?php
							$Nlinea=1;
							$Valor="";
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$SQL="SELECT * FROM CTAsientoPlantilla WHERE nombre='$NombAsi' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$opcCC="";
								$SqlCC="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."'";
								$SqlCCr = $mysqli->query($SqlCC);
								while ($RegCC = $SqlCCr->fetch_assoc()) {
									$opcCC= $opcCC.'<option value="'.$RegCC['id'].'">'.strtoupper($RegCC['nombre']).'</option>';
								}

								echo '
									<select class="form-control" id="SelCCosto'.$Nlinea.'" name="SelCCosto'.$Nlinea.'">
										<option value="0"></option>
									'.$opcCC.'
									</select>
								';

								$Nlinea++;
							}
							$mysqli->close();

							$i=1;
							while($i<5){
									echo '
										<select class="form-control" id="SelCCosto'.$Nlinea.'" name="SelCCosto'.$Nlinea.'">
											<option value="0"></option>
										'.$opcCC.'
										</select>
									';
								$Nlinea++;
								$i++;						
							}

						?>
					</div>

					<div class="clearfix"></div>
					<br>
					<div class="col-md-6"></div>
					<div class="col-md-2">
						<input type="Number" class="form-control text-right" id="TDebe" name="TDebe" value="<?php echo $SDebe; ?>" readonly="false">
					</div>
					<div class="col-md-2">
						<input type="Number" class="form-control text-right" id="THaber" name="THaber" value="<?php echo $SHaber; ?>" readonly="false">
					</div>
					<div class="col-md-2">
						<input type="Number" class="form-control text-right" id="Dif" name="Dif" value="<?php echo ($SDebe-$SHaber); ?>" readonly="false">
					</div>

					<div class="clearfix"></div>
					<br>
					<div class="col-md-12">
						<input type="text" class="form-control" id="Glosa" name="Glosa" value="<?php echo $NombAsi; ?>">
					</div>

					<div class="clearfix"></div>
					<br>

				<br>
				<div class="col-sm-12 text-right">
					<button type="button" class="btn btn-grabar" onclick="Valida()">
						<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
					</button>

					<button type="button" class="btn btn-grabar" onclick="Valida('U')">
						<span class="glyphicon glyphicon-floppy-saved"></span> Grabar y Actualizar Plantilla
					</button>


					<button type="button" class="btn btn-cancelar" onclick="Volver()">
						<span class="glyphicon glyphicon-remove"></span> Cancelar
					</button>
				</div>

			</div>
			<div class="col-sm-2">
			</div>
			<input type="hidden" name="Clineas" id="Clineas" value="<?php echo ($Nlinea-1); ?>">
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>

<br><br>
	<script type="text/javascript">
		function RefTotal(){
			SumDebe=0;
			SumHaber=0;

			<?php
				$i=1;
				while ($i< $Nlinea) {
					echo "
						VSDebe".$i."=(form1.Debe".$i.".value).replace(/\./g, '');
						VSDebe".$i."=parseFloat(VSDebe".$i.")*1;
						if (VSDebe".$i.">0) {
							SumDebe=SumDebe+VSDebe".$i.";
						}

						VSHaber".$i."=(form1.Haber".$i.".value).replace(/\./g, '');
						VSHaber".$i."=VSHaber".$i."*1;
						if (VSHaber".$i.">0) {
							SumHaber=SumHaber+VSHaber".$i.";
						}

					";
					$i++;
				}

			?>

			form1.TDebe.value=SumDebe;
			form1.THaber.value=SumHaber;
			form1.Dif.value=(SumDebe-SumHaber);
		}

		$("#TFecha").datepicker({
			// Formato de la fecha
			dateFormat: "dd-mm-yy",
			// Primer dia de la semana El lunes
			firstDay: 1,
			// Dias Largo en castellano
			dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
			// Dias cortos en castellano
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			// Nombres largos de los meses en castellano
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
			// Nombres de los meses en formato corto 
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
			// Cuando seleccionamos la fecha esta se pone en el campo Input 
			onSelect: function(dateText) { 
				$('#TFecha').val(dateText);
			}
		});

	</script>
	<?php include '../footer.php'; ?>

	</body>
</html>
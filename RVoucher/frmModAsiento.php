<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$dmes = substr($Periodo,0,2);
	$dano = substr($Periodo,3,4);

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$swb=0;

	$SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY id  DESC";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro["glosa"]=="" && $swb==0) {
			$mysqli->query("DELETE FROM CTRegLibroDiario WHERE id='".$registro["id"]."'");
		}else{
			$swb=1;
		}
	} 

	$mysqli->close();

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['KeyMod']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id ASC";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		if ($registro['glosa']!="") {
			$textfecha=date('d-m-Y',strtotime($registro['fecha']));
			$numecomp=$registro['ncomprobante'];
			$tipocomp=$registro['tipo'];
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
			.ui-datepicker{
				z-index: +6 !important;
			}			
		</style>

		<script type="text/javascript">
			$( function() {
				$("#TFecha").datepicker();
			} );
			
			function BuscaCuenta(vall){
				var url= "../buscacuenta.php";
				var x1=$('#'+vall).val();
				$.ajax({
				type: "POST",
				url: url,
				data: ('dat1='+x1),
				success:function(resp)
				{

					var r=Number(vall.substr(4, 3));
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

					var r=Number(vall.substr(5, 3));
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

			function sumar (valor) {
				if (valor=="") {
					valor=0;
				}
				var total = 0;	
				valor = parseInt(valor); // Convertir el valor a un entero (número).

				total = document.getElementById('TDebe').value;

				// Aquí valido si hay un valor previo, si no hay datos, le pongo un cero "0".
				// if (parseInt(value)== NaN) {
				// 	value=0;
				// }
				// valor === NaN ? 0 : valor;
				// valor = (valor == null || valor == undefined || valor == NaN || valor == "") ? 0 : valor;
				total = (total == null || total == undefined || total == NaN || total == "") ? 0 : total;

				/* Esta es la suma. */
				total = (parseInt(total) + parseInt(valor));

				// Colocar el resultado de la suma en el control "span".
				document.getElementById('TDebe').value = total;
			}

			function Valida(){
				if (form1.TDebe.value!="" && form1.TDebe.value>0) {
					if (form1.THaber.value!="" && form1.THaber.value>0) {
						MDebe=form1.TDebe.value;
						MHaber=form1.THaber.value;
						if (MDebe!=MHaber) {
							alert("Error en el cuadra del Voucher");
						}else{
							form1.action="xfrmModAsiento.php";
							form1.submit();
						}
					}
				}
			}
			function Borra(){

				var r = confirm("Esta Seguro de Eliminar este Voucher");
				if (r == true) {
					form1.SwEli.value="S";
					form1.action="xfrmModAsiento.php";
					form1.submit();
				}

			}
			
			function Volver(){
				history.go(-1);
			}

			jQuery(document).ready(function(e) {
				$('#myModal').on('shown.bs.modal', function() {
					$('input[name="BCodigo"]').focus();
				});
			});

		</script> 
	</head>

	<body>


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="#" method="POST" name="form1" id="form1">
			<input type="hidden" name="casilla" id="casilla">


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
								</script>

							</div>
							<div class="clearfix"></div>
							<br>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-cancelar" data-dismiss="modal" id="cmodel">Cerrar</button>
						</div>
				      </div>
				    </div>
				  </div>
				  <!-- fin buscar codigo -->

			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">            
				<h3>Modificar Voucher</h3> 
				<input type="hidden" name="keyas" id="keyas" value="<?php echo $_POST['KeyMod']; ?>">
				<input type="hidden" name="SwEli" id="SwEli" value="">



					<div class="col-md-2">
						<label>Fecha</label>
						<input id="TFecha" name="TFecha" type="text" class="form-control" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
					</div> 
					<div class="col-md-4 text-center">						
						<div class="input-group" style="display: block;">
							<label class="radio-inline"><input type="radio" name="Tipo" <?php if ($tipocomp=="I") { echo "checked"; } ?> onclick="javascript:form1.tmovi.value='I'">Ingreso</label>
							<label class="radio-inline"><input type="radio" name="Tipo" <?php if ($tipocomp=="E") { echo "checked"; } ?> onclick="javascript:form1.tmovi.value='E'">Egreso</label>
							<label class="radio-inline"><input type="radio" name="Tipo" <?php if ($tipocomp=="T") { echo "checked"; } ?> onclick="javascript:form1.tmovi.value='T'">Traspaso</label>
						</div>
						<input type="hidden" name="tmovi" id="tmovi" value="<?php echo $tipocomp; ?>">
					</div>
					<div class="col-md-4 text-center">						

					</div>								

					<div class="clearfix"></div>
 
					<div class="col-md-2">
						<label>Cuenta</label>
						<?php

							$Nlinea=1;
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['KeyMod']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id ASC";
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
							while($i<20){
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
							$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['KeyMod']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								if ($registro['glosa']=="") {
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
								}else{
									$Xglosa=$registro['glosa'];
								}
							}
							$mysqli->close();


							$i=1;
							while($i<20){
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
							$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['KeyMod']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								if ($registro['glosa']=="") {

									if ($_SESSION["PLAN"]=="S"){
										$SqlStr="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro['cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}else{
										$SqlStr="SELECT * FROM CTCuentas WHERE numero='".$registro['cuenta']."'";
									}
									$Res = $mysqli->query($SqlStr);
									while ($Reg = $Res->fetch_assoc()) {
										$NomCue=$Reg['detalle'];
									}
									$Valor=$registro['debe'];

									echo '
										<input type="text" class="form-control text-right" id="Debe'.$Nlinea.'" name="Debe'.$Nlinea.'" onchange="RefTotal()" oninput="MilesManu(this.value, this.id)" value="'.number_format($Valor, $NDECI, $DDECI, $DMILE).'" >
									';
									$SDebe=$SDebe+$registro['debe'];
									$Nlinea++;
								}else{
									$Xglosa=$registro['glosa'];
								}
							}
							$mysqli->close();

							$i=1;
							while($i<20){
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
							$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['KeyMod']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								if ($registro['glosa']=="") {

									if ($_SESSION["PLAN"]=="S"){
										$SqlStr="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro['cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}else{
										$SqlStr="SELECT * FROM CTCuentas WHERE numero='".$registro['cuenta']."'";
									}
									$Res = $mysqli->query($SqlStr);
									while ($Reg = $Res->fetch_assoc()) {
										$NomCue=$Reg['detalle'];
									}

									$Valor=$registro['haber'];

									echo '
										<input type="text" class="form-control text-right" id="Haber'.$Nlinea.'" name="Haber'.$Nlinea.'" onchange="RefTotal()" oninput="MilesManu(this.value, this.id)" value="'.number_format($Valor, $NDECI, $DDECI, $DMILE).'" >
									';
									$SHaber=$SHaber+$registro['haber'];
									$Nlinea++;
								}else{
									$Xglosa=$registro['glosa'];
								}
								
							}
							$mysqli->close();

							$i=1;
							while($i<20){
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
							$SQL="SELECT * FROM CTRegLibroDiario WHERE keyas='".$_POST['KeyMod']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id ASC";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								if ($registro['glosa']=="") {

									if ($_SESSION["PLAN"]=="S"){
										$SqlStr="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro['cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
									}else{
										$SqlStr="SELECT * FROM CTCuentas WHERE numero='".$registro['cuenta']."'";
									}
									$Res = $mysqli->query($SqlStr);
									while ($Reg = $Res->fetch_assoc()) {
										$NomCue=$Reg['detalle'];
									}
	
									$opcCC="";
									$SqlCC="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."'";
									$SqlCCr = $mysqli->query($SqlCC);
									while ($RegCC = $SqlCCr->fetch_assoc()) {
										if ($registro["ccosto"]==$RegCC['id']) {
											$opcCC= $opcCC.'<option value="'.$RegCC['id'].'" selected>'.strtoupper($RegCC['nombre']).'</option>';
										}else{
											$opcCC= $opcCC.'<option value="'.$RegCC['id'].'">'.strtoupper($RegCC['nombre']).'</option>';
										}
										
									}
								
									echo '
										<select class="form-control" id="SelCCosto'.$Nlinea.'" name="SelCCosto'.$Nlinea.'">
											<option value="0"></option>
										'.$opcCC.'
										</select>
									';

									$Nlinea++;
								}else{
									$Xglosa=$registro['glosa'];
								}
								
							}
							$mysqli->close();

							$i=1;
							while($i<20){
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
						<input type="text" class="form-control" id="Glosa" name="Glosa" value="<?php echo $Xglosa; ?>">
					</div>

					<div class="clearfix"></div>
					<br>

 			<br>

			<button type="button" class="btn btn-grabar" onclick="Valida()">
				<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
			</button>

			<button type="button" class="btn btn-mastecno" onclick="Volver()">
				<span class="glyphicon glyphicon-share"></span> Volver
			</button>

			<button type="button" class="btn btn-cancelar" onclick="Borra()">
				<span class="glyphicon glyphicon-remove"></span> Eliminar
			</button>

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
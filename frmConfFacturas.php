<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:index.php?Msj=95");
		exit;
	}
	
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTAsiento WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		$SQL1="SELECT * FROM CTAsiento WHERE tipo='C' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XC1=$registro1["L1"];
			$XC2=$registro1["L2"];
			$XC3=$registro1["L3"];
			$XC4=$registro1["L4"];
			$XC5=$registro1["L5"];
		}

		$SQL1="SELECT * FROM CTAsiento WHERE tipo='V' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XV1=$registro1["L1"];
			$XV2=$registro1["L2"];
			$XV3=$registro1["L3"];
			$XV4=$registro1["L4"];
			$XV5=$registro1["L5"];
		}

	}else{

		$SQL1="SELECT * FROM CTAsiento WHERE tipo='C' AND rut_empresa=''";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XC1=$registro1["L1"];
			$XC2=$registro1["L2"];
			$XC3=$registro1["L3"];
			$XC4=$registro1["L4"];
			$XC5=$registro1["L5"];
		}

		$SQL1="SELECT * FROM CTAsiento WHERE tipo='V' AND rut_empresa=''";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XV1=$registro1["L1"];
			$XV2=$registro1["L2"];
			$XV3=$registro1["L3"];
			$XV4=$registro1["L4"];
			$XV5=$registro1["L5"];
		}

	}

	if ($XC1!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC1' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XC1' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnC1=$registro1["detalle"];
		}
	}
	if ($XC2!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC2' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XC2' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnC2=$registro1["detalle"];
		}
	}
	if ($XC3!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC3' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XC3' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnC3=$registro1["detalle"];
		}
	}
	if ($XC4!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC4' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XC4' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnC4=$registro1["detalle"];
		}
	}
	if ($XC5!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC5' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XC5' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnC5=$registro1["detalle"];
		}
	}


	if ($XV1!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV1' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XV1' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnV1=$registro1["detalle"];
		}
	}
	if ($XV2!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV2' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XV2' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnV2=$registro1["detalle"];
		}
	}
	if ($XV3!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV3' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XV3' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnV3=$registro1["detalle"];
		}
	}
	if ($XV4!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV4' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XV4' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnV4=$registro1["detalle"];
		}
	}
	if ($XV5!="") {
		if ($_SESSION["PLAN"]=="S") {
			$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV5' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";;
		}else{
			$SQL1="SELECT * FROM CTCuentas WHERE numero='$XV5' ORDER BY detalle";
		}

		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {
			$XnV5=$registro1["detalle"];
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

		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/select2.css">

		<script type="text/javascript">

			function BuscaCuenta(vall){
			      var url= "buscacuenta.php";
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
			      var url= "buscacuenta.php";
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

			$(document).ready(function (eOuter) {

				$('input').bind('keypress', function (eInner) {
				//alert(eInner.keyCode);
					if (eInner.keyCode == 13){

						var idinput = $(this).attr('id');

						<?php 

							$i = 1;
							while ($i <= 5) {
								echo "
									if(idinput==\"Comp".$i."\"){
										BuscaCuenta(this.id);
										$('#Comp".($i+1)."').focus();
										$('#Comp".($i+1)."').select();
									}
									";

								$i++; 
							}

						?>

						<?php 

							$i = 1;
							while ($i <= 5) {
								echo "
									if(idinput==\"Venta".$i."\"){
										BuscaCuentaV(this.id);
										$('#Venta".($i+1)."').focus();
										$('#Venta".($i+1)."').select();
									}
									";

								$i++; 
							}

						?>

						return false;
					}
				});
			});			


		function Volver(){
			form1.action="frmMain.php";
			form1.submit();
		}

		function data(valor){
			var cas=form1.casilla.value;
			var r=cas.substr(0,4);
			document.getElementById(cas).value=valor;

			if (r=='Comp') {
				BuscaCuenta(form1.casilla.value);
			}else{
				BuscaCuentaV(form1.casilla.value);
			}

			document.getElementById("cmodel").click();
		}


		</script> 
	</head>

	<body>


	<?php include 'nav.php'; ?>

<div class="container-fluid text-left">
<div class="row content">

		<form action="xfrmConfFacturas.php" method="POST" name="form1" id="form1">
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
								<input class="form-control" id="BCuenta" name="BCuenta" type="text" placeholder="Buscar...">
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
									<tbody id="TableCta">
										<?php 
											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


											if ($_SESSION["PLAN"]=="S") {
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
										$("#BCuenta").on("keyup", function() {
										var value = $(this).val().toLowerCase();
											$("#TableCta tr").filter(function() {
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

			<br>
			<div class="col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading">Centralizaci&oacute;n de Compras</div>
					<div class="panel-body">

						<div class="col-md-12">
							<label>Cuenta Utilizada para IVA Credito Fiscal</label>
							<div class="input-group"> 
								<input type="text" class="form-control text-right" id="Comp2" name="Comp2" maxlength="50" value="<?php echo $XC2; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp2'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div>
								<input type="text" class="form-control" id="DComp2" name="DComp2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC2); ?>"  readonly="false" >
							</div>
							<br>

							<label>Cuenta Utilizada para Retenci&oacute;n</label>
							<div class="input-group"> 
								<input type="text" class="form-control text-right" id="Comp3" name="Comp3" maxlength="50" value="<?php echo $XC3; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp3'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div>
								<input type="text" class="form-control" id="DComp3" name="DComp3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC3); ?>"  readonly="false" >
							</div>
							<br>

							<label>Cuenta Utilizada para Auxiliar</label>
							<div class="input-group"> 
								<input type="text" class="form-control text-right" id="Comp4" name="Comp4" maxlength="50" value="<?php echo $XC4; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp4'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div>
								<input type="text" class="form-control" id="DComp4" name="DComp4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC4); ?>"  readonly="false" > 
							</div>
							<br>
						</div>

					</div>
				</div>
			</div>


			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">Centralizaci&oacute;n de Ventas</div>
					<div class="panel-body">

						<div class="col-md-12">


							<!-- <div class="input-group"> 
								<input type="text" class="form-control text-right" id="Venta2" name="Venta2" maxlength="50" value="<?php echo $XV2; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Venta2'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div> 
							</div>  -->
							<label>Cuenta Utilizada para IVA Debito Fiscal</label>
							<div class="input-group"> 
								<input type="text" class="form-control text-right" id="Venta3" name="Venta3" maxlength="50" value="<?php echo $XV3; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Venta3'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div>
								<input type="text" class="form-control" id="DVenta3" name="DVenta3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnV3); ?>"  readonly="false" >
							</div>
							<br>

							<label>Cuenta Utilizada para Retenci&oacute;n</label>
							<div class="input-group"> 
								<input type="text" class="form-control text-right" id="Venta4" name="Venta4" maxlength="50" value="<?php echo $XV4; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Venta4'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div>
								<input type="text" class="form-control" id="DVenta4" name="DVenta4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnV4); ?>"  readonly="false" > 
							</div>
							<br> 

<!-- 							<label>Cuenta Utilizada para Retenci&oacute;n</label>
							<div class="input-group"> 
								<input type="text" class="form-control text-right" id="Venta5" name="Venta5" maxlength="50" value="<?php echo $XV5; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Venta5'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div> 
							</div>
							<br> -->


							<label>Cuenta Utilizada para Auxiliar</label>
							<div class="input-group"> 
								<input type="text" class="form-control text-right" id="Venta1" name="Venta1" maxlength="50" value="<?php echo $XV1; ?>">
								<div class="input-group-btn"> 
									<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Venta1'">
										<span class="glyphicon glyphicon-search"></span>
									</a>
								</div>
								<input type="text" class="form-control" id="DVenta1" name="DVenta1" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnV1); ?>"  readonly="false" >
							</div>
							<br>


						</div>

					</div>
				</div>


			</div>

			<div class="clearfix"></div>
			<br>
			<br>
			<div class="col-md-12 text-center">


				<?php
					if ($_SESSION["PLAN"]!="S") {
				?>
					<div class="checkbox">
						<label><input type="checkbox" id="DefeAsie" name="DefeAsie">Dejar esta Configuraci&oacute;n por defecto</label>
					</div>
				<?php
					}
				?>
				<!-- <button type="submit" class="btn btn-warning btn-xs">Grabar</button>
				<button type="button" class="btn btn-warning btn-xs" onclick="Volver()">Grabar</button> -->

					<button type="submit" class="btn">
						<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
					</button>

					<button type="button" class="btn btn-default" onclick="Volver()">
						<span class="glyphicon glyphicon-remove"></span> Cancelar
					</button>  

				<!-- <button type="submit" class="btn btn-success" tabindex="15"></button>
				<input type="" tabindex="8" value="Volver" class="btn btn-danger"> -->
			</div>


		</form>

</div>
</div>

	<div class="clearfix"> </div>

<br><br>

	<?php include 'footer.php'; ?>

	</body>
</html>
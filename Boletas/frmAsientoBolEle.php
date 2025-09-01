<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTAsientoBolEle WHERE tipo='V'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
		$XC1=$registro["L1"];
		$XC2=$registro["L2"];
		$XC3=$registro["L3"];
		$XPago=$registro["pago"];
    }

  //   $SQL="SELECT * FROM CTAsientoHono WHERE tipo='E'";
  //   $resultados = $mysqli->query($SQL);
  //   while ($registro = $resultados->fetch_assoc()) {
		// $XV1=$registro["L1"];
		// $XV2=$registro["L2"];
		// $XV3=$registro["L3"];
  //   }

    if ($XC1!="") {
    	if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC1' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XC1'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnC1=$registro["detalle"];
		}
	}

    if ($XC2!="") {
    	if ($_SESSION["PLAN"]=="S"){
 			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC2' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	   	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XC2'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnC2=$registro["detalle"];
		}
	}

    if ($XC3!="") {
    	if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC3' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XC3'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnC3=$registro["detalle"];
		}
	}


    if ($XV1!="") {
    	if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV1' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XV1'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnV1=$registro["detalle"];
		}
	}

    if ($XV2!="") {
    	if ($_SESSION["PLAN"]=="S"){
 			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV2' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	   	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XV2'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnV2=$registro["detalle"];
		}
	}

	if ($XV3!="") {
		if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XV3' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XV3'";
		}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnV3=$registro["detalle"];
		}
	}

	if ($XPago!="") {
		if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XPago' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XPago'";
		}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnPago=$registro["detalle"];
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
							while ($i <= 3) {
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
							while ($i <= 3) {
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
				form1.action="../frmMain.php";
				form1.submit();
			}
			
			function data(valor){
				var cas=form1.casilla.value;
				document.getElementById(cas).value=valor;

				//$('#'+cas).val()=valor;
				BuscaCuenta(form1.casilla.value);
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


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="xfrmAsientoBolEle.php" method="POST" name="form1" id="form1">
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
													$res = $mysqli->query($SQL1);
													while ($reg = $res->fetch_assoc()) {
														$tcuenta=$reg["nombre"];
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

			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">            
				<h3>Centralizaci&oacute;n Boletas Electronicas</h3> 
					<div class="clearfix"></div>
 
					<div class="col-md-2">

						<label for="mcuenta">Cuenta</label>
						<div class="input-group"> 
							<input type="text" class="form-control text-right" id="Comp1" name="Comp1" required maxlength="50" value="<?php echo $XC1; ?>">
							<div class="input-group-btn"> 
								<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp1'">
									<span class="glyphicon glyphicon-search"></span>
								</a>
							</div> 
						</div> 
						<div class="input-group"> 
							<input type="text" class="form-control text-right" id="Comp2" name="Comp2" required maxlength="50" value="<?php echo $XC2; ?>">
							<div class="input-group-btn"> 
								<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp2'">
									<span class="glyphicon glyphicon-search"></span>
								</a>
							</div> 
						</div> 
						<div class="input-group"> 
							<input type="text" class="form-control text-right" id="Comp3" name="Comp3" required maxlength="50" value="<?php echo $XC3; ?>">
							<div class="input-group-btn"> 
								<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp3'">
									<span class="glyphicon glyphicon-search"></span>
								</a>
							</div> 
						</div> 

					</div>
					<div class="col-md-10">
						<label for="mdetalle">Detalle</label>  
						<input type="text" class="form-control" id="DComp1" name="DComp1" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC1); ?>, * Total"  readonly="false" >
						<input type="text" class="form-control" id="DComp2" name="DComp2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC2); ?>, Monto Neto"  readonly="false" >
						<input type="text" class="form-control" id="DComp3" name="DComp3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC3); ?>, Impuesto"  readonly="false" >
					</div>
					<div class="clearfix"></div>

				<h4>Cuenta de Pago</h4> 
					<div class="clearfix"></div>

					<div class="col-md-2">
						<label for="mcuenta">Cuenta</label>
						<div class="input-group"> 
							<input type="text" class="form-control text-right" id="Comp4" name="Comp4" required maxlength="50" value="<?php echo $XPago; ?>">
							<div class="input-group-btn"> 
								<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp4'">
									<span class="glyphicon glyphicon-search"></span>
								</a>
							</div> 
						</div> 
					</div>

					<div class="col-md-10">
						<label for="mdetalle">Detalle</label>  
						<input type="text" class="form-control" id="DComp4" name="DComp4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnPago); ?>"  readonly="false" >
					</div>


 			<br>

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
					<button type="submit" class="btn">
						<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
					</button>

					<button type="button" class="btn btn-default" onclick="Volver()">
						<span class="glyphicon glyphicon-remove"></span> Cancelar
					</button>  

			</div>


		</form>

	</div>
	</div>

	<div class="clearfix"> </div>

<br><br>

	<?php include '../footer.php'; ?>

	</body>
</html>
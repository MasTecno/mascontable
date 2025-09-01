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

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<script>
			function CGrilla(){
				var url= "DetGrilla.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#Grilla').html(resp);
					}
				});
			}

			function Procesar(v1,v2){
				if (confirm("Esta Nota de Cr\u00e9dito quedar\u00e1 asociada a la Factura de referencia.\n\r \u00bfConfirma?") == true) {
					form1.D1.value=v1;
					form1.D2.value=v2;

					var url= "xfrmprocesa.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							CGrilla();
							form1.D1.value="";
							form1.D2.value="";
						}
					});
				}
			}

			function ProcesarX(v1,v2,v3,v4,v5,v6,v7,v8,v9,v10){
				form1.Mod01.value=v1;
				form1.Mod02.value=v2;
				form1.Mod03.value=v3;
				
				form1.rsocial.value=v4;

				form1.ModNNC.value=v5;
				form1.ModFNC.value=v6;
				form1.ModMNC.value=v7;
				
				form1.ModNFC.value=v8;
				form1.ModFFC.value=v9;
				form1.ModMFC.value=v10;

				form1.ModFCen.value=v9;

				form1.MontoCen.value=parseFloat(v10)-parseFloat(v7);

				if(form1.MontoCen.value==0){
					var Ex1 = document.getElementById("DivGlosaNC");
					Ex1.style.display = "none";
					var Ex1 = document.getElementById("DivMontoCen");
					Ex1.style.display = "none";
					var Ex1 = document.getElementById("DivSelCta");
					Ex1.style.display = "none";
					var Ex1 = document.getElementById("DivTutCen");
					Ex1.style.display = "none";
					var Ex1 = document.getElementById("DivFecCen");
					Ex1.style.display = "none";
				}else{
					var Ex1 = document.getElementById("DivGlosaNC");
					Ex1.style.display = "block";
					var Ex1 = document.getElementById("DivMontoCen");
					Ex1.style.display = "block";
					var Ex1 = document.getElementById("DivSelCta");
					Ex1.style.display = "block";
					var Ex1 = document.getElementById("DivTutCen");
					Ex1.style.display = "block";
					var Ex1 = document.getElementById("DivFecCen");
					Ex1.style.display = "block";
				}
			}

			function t() {
				if (form1.MontoCen.value > 0 && (form1.SelCta.value === "0" || form1.GlosaNC.value === "")) {
					alert("Debe seleccionar cuenta e ingresar glosa");
					return;
				}
				
				if (confirm("Esta Nota de Crédito quedará asociada a la Factura de referencia.\n\r ¿Confirma?")) {
					var url = "xfrmprocesav2.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success: function (resp) {
							$('#myModal').modal('hide');
							CGrilla();
							resetForm();
						}
					});
				}
			}

			function resetForm() {
				form1.Mod01.value = "";
				form1.Mod02.value = "";
				form1.Mod03.value = "";
				form1.rsocial.value = "";
				form1.ModNNC.value = "";
				form1.ModFNC.value = "";
				form1.ModMNC.value = "";
				form1.ModNFC.value = "";
				form1.ModFFC.value = "";
				form1.ModMFC.value = "";
				form1.ModFCen.value = "";
				form1.MontoCen.value = "";
			}

			$(window).load(function(){
				$('#SelCta').select2();
			});
			
            $( function() {
                $("#ModFCen").datepicker();
            });

			function Ir(){
				if(form1.frm.value!=""){
					form1.action="AsociarDoc.php";
					form1.submit();
				}
			}
		</script>
		<style>
			.ui-widget.ui-widget-content {
				z-index: 9999 !important;
			}			
		</style>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<input type="hidden" name="D1" id="D1" value="">
			<input type="hidden" name="D2" id="D2" value="">
			<br>

			<div class="col-sm-12">
				<div class="col-md-4">
					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Registros</span>
							<select class="form-control" id="frm" name="frm" onchange="CGrilla()" required>
								<option value="">Seleccione</option>
								<option value="C">Documentos de Compra</option>
								<option value="V">Documentos de Venta</option>
							</select>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<button type="button" class="btn btn-modificar" onclick="Ir()">Asociar Documentos sin referenciar</button>
				</div>

				<div class="clearfix"></div>
				<br>

				<div class="col-md-12" id="Mensa" style=" display:none;">
					<div class="alert alert-warning alert-dismissible" style="text-align: center; background-color: #fbc7c7;">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Generando!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
					</div>
				</div>

				<div class="clearfix"></div>
				<br>

				<div class="col-md-12" id="Grilla">
				</div>

				<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Generar Voucher</h4>
						</div>
						<div class="modal-body">

							<input id="Mod01" name="Mod01" type="hidden" class="form-control" value="">
							<input id="Mod02" name="Mod02" type="hidden" class="form-control" value="">
							<input id="Mod03" name="Mod03" type="hidden" class="form-control" value="">

							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Raz&oacute;n Social</span>
									<input id="rsocial" name="rsocial" type="text" class="form-control" readonly value="">
								</div>							
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">N&reg; NC</span>
									<input id="ModNNC" name="ModNNC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Fecha NC</span>
									<input id="ModFNC" name="ModFNC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Monto NC</span>
									<input id="ModMNC" name="ModMNC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>							
							<div class="clearfix"></div>
							<br>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">N&reg; Factura</span>
									<input id="ModNFC" name="ModNFC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Fecha Factura</span>
									<input id="ModFFC" name="ModFFC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Monto Factura</span>
									<input id="ModMFC" name="ModMFC" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>
							<div class="clearfix"></div>
							<br>

							<hr>
							<div class="clearfix"></div>

							<div class="col-md-12 text-center" id="DivTutCen">
								<hhx style="font-size: 18px;">Centralizar Documento</hhx>
							</div>

							<div class="clearfix"></div>
							<br>


							<div class="col-md-6" id="DivFecCen">
								<div class="input-group">
									<span class="input-group-addon">Fecha Centralizaci&oacute;n</span>
									<input id="ModFCen" name="ModFCen" type="text" class="form-control text-right" value="">
								</div>							
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-6" id="DivSelCta">
								<div class="input-group" id="SelPag">
								<span class="input-group-addon">Cuenta</span>
									<select id="SelCta" name="SelCta" class="form-control">
									<option value="0">Seleccione...</option>
									<?php
										$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

										if ($_SESSION["PLAN"]=="S"){
											$SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
										}else{
											$SQL="SELECT * FROM CTCuentas WHERE 1=1 ORDER BY detalle";
										}
										$resultado = $mysqli->query("$SQL");
										while ($registro = $resultado->fetch_assoc()) {
											echo "<option value ='".$registro["numero"]."'>".$registro["numero"]." ".$registro["detalle"]."</option>";
										}
										$mysqli->close();
									?>
									</select>
								</div>
							</div>

							<div class="col-md-6" id="DivMontoCen">
								<div class="input-group">
									<span class="input-group-addon">Monto Centralizaci&oacute;n</span>
									<input id="MontoCen" name="MontoCen" type="text" class="form-control text-right" readonly value="">
								</div>							
							</div>

							<div class="clearfix"></div>
							<br>

							<div class="col-md-12" id="DivGlosaNC">
								<div class="input-group">
									<span class="input-group-addon">Glosa</span>
									<input id="GlosaNC" name="GlosaNC" type="text" class="form-control text-right" value="" onchange="javascript:this.value=this.value.toUpperCase();">
								</div>							
							</div>
							<div class="clearfix"></div>
							<br>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-grabar" onclick="t()">Procesar</button>
							<button type="button" class="btn btn-cancelar" data-dismiss="modal">Cerrar</button>
						</div>
					</div>
				</div>
				</div>

				<div class="clearfix"></div>
				<br>
			</div>

			<script type="text/javascript">
				$( "#ModFCen" ).datepicker({
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
					// $('#d1').val(dateText);
					// $('#d2').focus();
					// $('#d2').select();
					}
				});
			</script>
		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>
	</body>

</html>


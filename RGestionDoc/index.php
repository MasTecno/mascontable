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
	
		</style>
		<script type="text/javascript">
			function CargaGrilla(r1){
				if (r1=="B" && form1.SelCta.value==0) {
					alert("Debe selecionar una cuenta para continuar");
				}else{
					form1.SwMov.value=r1;
					Proce();
					if(form1.frm.value=="H"){
						var url= "DetGrillaHono.php";
					}else{
						var url= "DetGrilla.php";
					}

					$.ajax({
					type: "POST",
					url: url,

					data: $('#form1').serialize(),
					success:function(resp){
						$('#Grilla').html(resp);

						$('#ModCta').modal('hide');
						$('#ModMon').modal('hide');
						Fin();
						form1.SwMov.value="";
						$('#SelCtaMas').val('0').trigger('change.select2');
						$('#SelCCMas').val('0').trigger('change.select2');
					}
					});
				}
			}

			function Proce(){
				document.getElementById("Mensa").style.display = 'inline';
			}

			function Fin(){
				document.getElementById("Mensa").style.display = 'none';
			}

			function PMonto(vr1){
				form1.KeyMov.value=vr1;

				var url="ProceMonto.php";
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					success:function(resp){
						$("#Exento").val(resp.dato1);
						$("#Neto").val(resp.dato2);
						$("#IVA").val(resp.dato3);
						$("#Reten").val(resp.dato4);
						$("#Total").val(resp.dato10);
						// $("#SQ").val(resp.dato11);

						$("#D1").val(resp.dato5);
						$("#D2").val(resp.dato6);
						$("#D3").val(resp.dato7);
						$("#D4").val(resp.dato8);
						$("#D5").val(resp.dato9);
					}
				});	
			}

			function AsigCta(ct1){
				form1.KeyMov.value=ct1;

				var url="ProceMonto.php";
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					success:function(resp){
						$("#CtaRut").val(resp.dato5);
						$("#CtaRaz").val(resp.dato6);
						$("#ctaOri").val(resp.dato12);
						$("#CCOri").val(resp.dato13);
						var y= resp.dato12;
						var f= resp.dato13;

						$('#SelCta').val(y).trigger('change.select2');
						$('#SelCC').val(f).trigger('change.select2');

						$("#SwCta").prop('checked', false);
						$("#SwCC").prop('checked', false);
					}
				});	
			}

			function limpia(){
				form1.Exento.value="";
				form1.Neto.value="";
				form1.IVA.value="";
				form1.Reten.value="";
				form1.Total.value="";
				// form1.SQ.value="";
				form1.D1.value="";
				form1.D2.value="";
				form1.D3.value="";
				form1.D4.value="";
				form1.D5.value="";
			}

			function limpiacte(){
				form1.CtaRut.value="";
				form1.CtaRaz.value="";
			}

			function ProceDiv(zt1){

				form1.KeyMov.value=zt1;
				document.getElementById("btrEli").style.display = 'none';

				var url="ProceMonto.php";
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					success:function(resp){

						if (resp.dato1=="Exit") {
							window.location.href="../?Msj=95";
						}else{
							net1=(resp.dato1);
							net2=(resp.dato2);
							form1.NetoDiv.value=parseInt(net1)+parseInt(net2);
							$("#RutDiv").val(resp.dato5);
							$("#RSocialDiv").val(resp.dato6);
							$("#FechaDiv").val(resp.dato7);
							$("#DocumentoDiv").val(resp.dato8);
							$("#NumeroDiv").val(resp.dato9);
							InsDivMon();							
						}
					}
				});	
			}

			function InsDivMon(){
				form1.SwInsert.value=1;
				var url= "DivMonto.php";

				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#LisCtaDiv').html(resp);
					form1.SwInsert.value="";
					form1.MontDiv.value=0;

					swmonto=parseInt(form1.Mtotal.value);
					if (swmonto>0) {
						document.getElementById("btrEli").style.display = 'inline';
					}
				}
				});
			}

			function Elimi(v1){
				form1.SwInsert.value="";
				form1.SwEliDiv.value=v1;

				var url= "DivMonto.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#LisCtaDiv').html(resp);
						form1.SwEliDiv.value="";
					}
				});	
			}

			function CalDifDiv() {
				RD1=parseInt(form1.NetoDiv.value);
				RD2=parseInt(form1.Mtotal.value);
				ResDiv=RD1-RD2;
				if (ResDiv<0) {
					alert("La sumatorio no coincide con el total a distribuir");
				}else{
					form1.MontDiv.value=ResDiv;
				}
			}

			function GrabDiv(){
				RD1=parseInt(form1.NetoDiv.value);
				RD2=parseInt(form1.Mtotal.value);
				ResDiv=RD1-RD2;
				if (ResDiv<0 || RD1!=RD2) {
					alert("La sumatorio no coincide con el total a distribuir");
				}else{
					var url= "GraDivMot.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							form1.KeyMov.value="";
							// document.getElementById("ModDiv").style.display = "none";
							$('#ModDiv').modal('hide');
							CargaGrilla();
						}
					});	
				}
			}

			function EliDivMot(){
				form1.SwMov.value="E";
					var url= "GraDivMot.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							form1.KeyMov.value="";
							form1.SwMov.value="";
							$('#ModDiv').modal('hide');
							CargaGrilla();
						}
					});
			}



			function ProceDivRete(zt1){

				form1.KeyMov.value=zt1;
				document.getElementById("btrEliR").style.display = 'none';

				var url="ProceMontoRete.php";
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					success:function(resp){

						if (resp.dato1=="Exit") {
							window.location.href="../?Msj=95";
						}else{
							// net1=0;//(resp.dato1);
							net2=(resp.dato4);
							form1.NetoDivR.value=parseInt(net2);
							$("#RutDivR").val(resp.dato5);
							$("#RSocialDivR").val(resp.dato6);
							$("#FechaDivR").val(resp.dato7);
							$("#DocumentoDivR").val(resp.dato8);
							$("#NumeroDivR").val(resp.dato9);
							InsDivMonRete();							
						}
					}
				});	
			}

			function InsDivMonRete(){
				form1.SwInsertR.value=1;
				var url= "DivMontoRete.php";
				SwInsert
				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#LisCtaDivRet').html(resp);
					form1.SwInsertR.value="";
					form1.MontDivR.value=0;
					swmonto=parseInt(form1.MtotalR.value);
					if (swmonto>0) {
						document.getElementById("btrEliR").style.display = 'inline';
					}
				}
				});
			}

			function ElimiRete(v1){
				form1.SwInsertR.value="";
				form1.SwEliDivR.value=v1;

				var url= "DivMontoRete.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#LisCtaDivRet').html(resp);
						form1.SwEliDivR.value="";
					}
				});	
			}

			function CalDifDivRete() {
				RD1=parseInt(form1.NetoDivR.value);
				RD2=parseInt(form1.MtotalR.value);
				ResDiv=RD1-RD2;
				if (ResDiv<0) {
					alert("La sumatorio no coincide con el total a distribuir");
				}else{
					form1.MontDivR.value=ResDiv;
				}
			}

			function GrabDivRete(){
				RD1=parseInt(form1.NetoDivR.value);
				RD2=parseInt(form1.MtotalR.value);
				ResDiv=RD1-RD2;
				if (ResDiv<0 || RD1!=RD2) {
					alert("La sumatorio no coincide con el total a distribuir");
				}else{
					var url= "GraDivMotRete.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							form1.KeyMov.value="";
							// document.getElementById("ModDiv").style.display = "none";
							$('#ModDivRete').modal('hide');
							CargaGrilla();
						}
					});	
				}
			}

			function EliDivMotRete(){
				form1.SwMov.value="E";
					var url= "GraDivMotRete.php";
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							form1.KeyMov.value="";
							form1.SwMov.value="";
							$('#ModDivRete').modal('hide');
							CargaGrilla();
						}
					});
			}

			function Historial(){
				if(form1.filtro.value=="T"){
					form1.action="Historial.php";
					form1.submit();
				}
				if(form1.filtro.value=="H"){
					form1.action="Historial.php";
					form1.submit();
				}
			}

			$(window).load(function(){
				$('#SelCta').select2();
				$('#SelCC').select2();
				$('#SelCtaMas').select2();
				$('#SelCCMas').select2();
				$('#SelCtaDiv').select2();
				$('#SelCCDiv').select2();
				$('#SelCtaDivR').select2();
				$('#SelCCDivR').select2();
			});


			function Centrali() {
				si=0;
				for (i=0;i<document.form1.elements.length;i++){
					if(document.form1.elements[i].type == "checkbox"){
						if (document.form1.elements[i].checked==1) {
							si++;
						}
					}
				}

				if (si>0) {
					form1.action="ProceCentra.php";
					form1.submit();
				}else{
					alert("Debe selecionar al menos 1 documentos para continuar.");
				}
			}

			function seleccionar_todo(){
				for (i=0;i<document.form1.elements.length;i++)
					if(document.form1.elements[i].type == "checkbox"){
						if (document.form1.elements[i].checked==0) {
							document.form1.elements[i].checked=1;
						}else{
							document.form1.elements[i].checked=0;
						}
					}
			}

			function EliDocu(){

				const checkboxes = document.querySelectorAll('input[name="check_list[]"]');
				let isSelected = false;

				checkboxes.forEach(checkbox => {
					if (checkbox.checked) {
						isSelected = true;
					}
				});

				if (isSelected) {
					// alert("1");
					form1.EliRegi.value="I";
					CargaGrilla();
					form1.EliRegi.value="";


				} else {

					var r = confirm("Esta seguro de Eliminar los Documentos!, Solo afectara a aquellos que no estan centralizados...");
					if (r == true) {
						form1.EliRegi.value="S";
						CargaGrilla();
						form1.EliRegi.value="";
					} 
				}


			}

			function GrMov(){
				si=0;
				for (i=0;i<document.form1.elements.length;i++){
					if(document.form1.elements[i].type == "checkbox"){
						if (document.form1.elements[i].checked==1) {
							si++;
						}
					}
				}

				if (si>0) {
					form1.IdMovDoc.value="S";
					// form1.action="ProceCentra.php";
					// form1.submit();
					CargaGrilla();
					form1.IdMovDoc.value="";
				}else{
					alert("Debe selecionar al menos 1 documentos para continuar.");
				}
				// form1.IdMovDoc.value=form1.ModReg.value;
				// form1.ModReg.value="";
				// form1.submit();
			}



		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<input type="hidden" name="SwMov" id="SwMov">
			<input type="hidden" name="KeyMov" id="KeyMov">
			<input type="hidden" name="EliRegi" id="EliRegi">
			<input type="hidden" name="IdMovDoc" id="IdMovDoc">

			<br>
			<div class="col-sm-12">

				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading text-center">Selecci&oacute;n de Documentos</l></div>
						<div class="panel-body">
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Registros</span>
									<select class="form-control" id="frm" name="frm" onchange="CargaGrilla()" required>
										<option value="">Seleccione</option>
										<option value="C" <?php if(isset($_GET['C'])){ echo "selected";}?> >Documentos de Compra</option>
										<option value="V" <?php if(isset($_GET['V'])){ echo "selected";}?> >Documentos de Venta</option>
										<option value="H" <?php if(isset($_GET['H'])){ echo "selected";}?> >Documentos Honorarios</option>
									</select>
								</div>
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Mes</span>
									<select class="form-control" id="messelect" name="messelect" onchange="CargaGrilla()" required>
									<?php 
										$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
										$i=1;
										$dmes=$dmes*1;

										if(isset($_GET['Me']) && $_GET['Me']!=""){
											$dmes=$_GET['Me']*1;
										}
										
										while($i<=12){
											if ($i==$dmes) {
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
									<select class="form-control" id="anoselect" name="anoselect" onchange="CargaGrilla()" required>
									<?php 
										$yoano=date('Y');
										$tano="2010";

										if(isset($_GET['Pe']) && $_GET['Pe']!=""){
											$dano=$_GET['Pe'];
										}

										while($tano<=($yoano+1)){
											if ($dano==$tano) {
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
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading text-center">Asignaci&oacute;n Rapida</l></div>
						<div class="panel-body">
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Cuenta</span>
										<select id="SelCtaMas" name="SelCtaMas" class="form-control">
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
							<div class="clearfix"></div>
							<br>
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">C. Costo</span>
										<select id="SelCCMas" name="SelCCMas" class="form-control">
										<option value="0">- No Aplica -</option>
										<?php 
											$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

											$SQL="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."'";
											$resultado = $mysqli->query("$SQL");
											while ($registro = $resultado->fetch_assoc()) {
												echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
											}
											$mysqli->close();
										?>
										</select>
								</div>
							</div>
							<div class="clearfix"></div>
							<br>
							<div class="col-md-12">
								<button type="button" class="btn btn-mastecno btn-block" id="BtnVisual" onclick="CargaGrilla('C')">Asignaci&oacute;n R&aacute;pida</button>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading text-center">Centralizar</l></div>
						<div class="panel-body">
							<div class="col-md-12">
								<button type="button" class="btn btn-grabar btn-block" id="BtnVisual" onclick="Centrali()">Procesar Documentos</button>
							</div>

							<div class="clearfix"></div>
							<br>
							<div class="col-md-12">
								<button type="button" onclick="seleccionar_todo()" class="btn btn-block btn-exportar">Marcar Todos</button>	
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="col-md-12" id="Mensa" style=" display:none;">
				<div class="alert alert-warning alert-dismissible" style="text-align: center; background-color: #fbc7c7;">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<strong>Generando!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
				</div>
			</div>

			<div class="col-md-12" id="Grilla">
			</div>



				<!-- Div Documento-->
				<div class="modal fade" id="ModDiv" role="dialog">
				<div class="modal-dialog modal-lg" style="width: 1200px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Distribuir Monto</h4>
					</div>
					<div class="modal-body">
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Rut</span>
									<input type="text" class="form-control" id="RutDiv" name="RutDiv" readonly="false">
								</div>
							</div> 
							<div class="col-md-8">
								<div class="input-group">
									<span class="input-group-addon">R. Social</span>
									<input type="text" class="form-control" id="RSocialDiv" name="RSocialDiv" readonly="false">
								</div>
							</div> 

							<div class="clearfix"></div>
							<br>
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Fecha</span>
									<input type="text" class="form-control" id="FechaDiv" name="FechaDiv" readonly="false">
								</div>
							</div> 

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Documento</span>
									<input type="text" class="form-control text-right" id="DocumentoDiv" name="DocumentoDiv" readonly="false">
								</div>
							</div> 

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Numero</span>
									<input type="text" class="form-control text-right" id="NumeroDiv" name="NumeroDiv" readonly="false">
								</div>
							</div> 


							<div class="clearfix"></div>
							<br>
							<div class="col-md-4">
							</div> 

							<div class="col-md-4">
							</div>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Neto</span>
									<input type="text" class="form-control text-right" id="NetoDiv" name="NetoDiv" readonly="false">
								</div>
							</div> 


							<div class="clearfix"></div>
							<br>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Cuenta</span>
										<select id="SelCtaDiv" name="SelCtaDiv" class="form-control">
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

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">C. Costo</span>
										<select id="SelCCDiv" name="SelCCDiv" class="form-control">
										<option value="0">- No Aplica -</option>
										<?php 
											$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

											$SQL="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."'";
											$resultado = $mysqli->query("$SQL");
											while ($registro = $resultado->fetch_assoc()) {
												echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
											}
											$mysqli->close();
										?>
										</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="input-group">
									<input type="hidden" name="SwInsert" id="SwInsert">
									<input type="hidden" name="SwEliDiv" id="SwEliDiv">
									<input type="number" class="form-control text-right" id="MontDiv" name="MontDiv" onclick="CalDifDiv()">
									<div class="input-group-btn">
										<button class="btn btn-default" type="button" onclick="InsDivMon()">
											<i class="glyphicon glyphicon-saved"></i>
										</button>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<br>

							<table class="table table-condensed">
								<thead>
									<tr>
										<th width="1%">N</th>
										<th>Cuenta</th>
										<th>C. Costo</th>
										<th style="text-align: right;">Monto</th>
										<th width="1%"></th>
									</tr>
								</thead>
								<tbody id="LisCtaDiv">

								</tbody>
							</table>

							<div class="clearfix"></div>
							<br>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-cancelar" style=" display:none;" id="btrEli" name="btrEli" onclick="EliDivMot()">Eliminar</button>
						<button type="button" class="btn btn-grabar" onclick="GrabDiv()">Grabar</button>
						<button type="button" class="btn btn-mofificar" data-dismiss="modal" onclick="limpia()">Cerrar</button>
					</div>
				</div>
				</div>
				</div>
				<!-- fin Div Documento-->


				<!-- Div Retencion-->
				<div class="modal fade" id="ModDivRete" role="dialog">
				<div class="modal-dialog modal-lg" style="width: 1200px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Distribuir Monto Retención X</h4>
					</div>
					<div class="modal-body">
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Rut</span>
									<input type="text" class="form-control" id="RutDivR" name="RutDivR" readonly="false">
								</div>
							</div> 
							<div class="col-md-8">
								<div class="input-group">
									<span class="input-group-addon">R. Social</span>
									<input type="text" class="form-control" id="RSocialDivR" name="RSocialDivR" readonly="false">
								</div>
							</div> 
							<div class="clearfix"></div>
							<br>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Fecha</span>
									<input type="text" class="form-control" id="FechaDivR" name="FechaDivR" readonly="false">
								</div>
							</div> 
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Documento</span>
									<input type="text" class="form-control text-right" id="DocumentoDivR" name="DocumentoDivR" readonly="false">
								</div>
							</div> 
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Numero</span>
									<input type="text" class="form-control text-right" id="NumeroDivR" name="NumeroDivR" readonly="false">
								</div>
							</div> 
							<div class="clearfix"></div>
							<br>

							<div class="col-md-4">
							</div> 
							<div class="col-md-4">
							</div>
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Neto</span>
									<input type="text" class="form-control text-right" id="NetoDivR" name="NetoDivR" readonly="false">
								</div>
							</div> 
							<div class="clearfix"></div>
							<br>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Cuenta</span>
										<select id="SelCtaDivR" name="SelCtaDivR" class="form-control">
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

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">C. Costo</span>
										<select id="SelCCDivR" name="SelCCDivR" class="form-control">
										<option value="0">- No Aplica -</option>
										<?php 
											$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

											$SQL="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."'";
											$resultado = $mysqli->query("$SQL");
											while ($registro = $resultado->fetch_assoc()) {
												echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
											}
											$mysqli->close();
										?>
										</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="input-group">
									<input type="hidden" name="SwInsertR" id="SwInsertR">
									<input type="hidden" name="SwEliDivR" id="SwEliDivR">
									<input type="number" class="form-control text-right" id="MontDivR" name="MontDivR" onclick="CalDifDivRete()">
									<div class="input-group-btn">
										<button class="btn btn-default" type="button" onclick="InsDivMonRete()">
											<i class="glyphicon glyphicon-saved"></i>
										</button>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<br>

							<table class="table table-condensed">
								<thead>
									<tr>
										<th width="1%">N</th>
										<th>Cuenta</th>
										<th>C. Costo</th>
										<th style="text-align: right;">Monto</th>
										<th width="1%"></th>
									</tr>
								</thead>
								<tbody id="LisCtaDivRet">

								</tbody>
							</table>

							<div class="clearfix"></div>
							<br>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-cancelar" style=" display:none;" id="btrEliR" name="btrEliR" onclick="EliDivMotRete()">Eliminar</button>
						<button type="button" class="btn btn-grabar" onclick="GrabDivRete()">Grabar</button>
						<button type="button" class="btn btn-mofificar" data-dismiss="modal" onclick="limpiaRete()">Cerrar</button>
					</div>
				</div>
				</div>
				</div>
				<!-- fin Div Retencion-->



				<!-- pREDETERMINANR cTA  Y cc-->
				<div class="modal fade" id="ModCta" role="dialog">
				<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Distribuci&oacute;n de Cuentas</h4>
					</div>
					<div class="modal-body">
						<p>Definir Cuenta</p>

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Rut</span>
									<input type="text" class="form-control" id="CtaRut" name="CtaRut" readonly="false">
								</div>
							</div> 
							<div class="col-md-8">
								<div class="input-group">
									<span class="input-group-addon">R. Social</span>
									<input type="text" class="form-control" id="CtaRaz" name="CtaRaz" readonly="false">
								</div>
							</div> 

							<div class="clearfix"></div>
							<br>


							<div class="col-md-6">
								<div class="input-group">
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
												if ($IdCliente==$registro["numero"]) {
													echo "<option value ='".$registro["numero"]."' selected>".$registro["numero"]." ".$registro["detalle"]."</option>";
												}else{
													echo "<option value ='".$registro["numero"]."'>".$registro["numero"]." ".$registro["detalle"]."</option>";
												}
											}
											$mysqli->close();
										?>
										</select>
										<input type="hidden" name="ctaOri" id="ctaOri">
										<input type="hidden" name="CCOri" id="CCOri">
								</div>
									<div class="checkbox">
										<label><input type="checkbox" id="SwCta" name="SwCta" value="">Dejar predeterminada a todos los documentos de esta Raz&oacute;n Social, que no esten Centralizados</label>
									</div>
							</div>

							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">C. Costo</span>
										<select id="SelCC" name="SelCC" class="form-control">
										<option value="0">- No Aplica -</option>
										<?php 
											$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

											$SQL="SELECT * FROM CTCCosto WHERE rutempresa='".$_SESSION['RUTEMPRESA']."'";
											$resultado = $mysqli->query("$SQL");
											while ($registro = $resultado->fetch_assoc()) {
												echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
											}
											$mysqli->close();
										?>
										</select>
								</div>
									<div class="checkbox">
										<label><input type="checkbox" id="SwCC" name="SwCC" value="">Dejar predeterminado el C. Costo a todos los documentos de esta Raz&oacute;n Social, que no esten Centralizados</label>
									</div>
							</div>
						<div class="clearfix"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-grabar" data-dismiss="modal" onclick="CargaGrilla('B')">Procesar</button>
						<button type="button" class="btn btn-cancelar" data-dismiss="modal" onclick="limpiacte()">Close</button>
					</div>
				</div>
				</div>
				</div>
				<!-- fin pREDETERMINANR cTA  Y cc-->

				<!-- MOFIFICA dOCUMENTO-->
				<div class="modal fade" id="ModMon" role="dialog">
				<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Modificar Montos</h4>
					</div>
					<div class="modal-body">
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Rut</span>
									<input type="text" class="form-control" id="D1" name="D1" readonly="false">
								</div>
							</div> 
							<div class="col-md-8">
								<div class="input-group">
									<span class="input-group-addon">R. Social</span>
									<input type="text" class="form-control" id="D2" name="D2" readonly="false">
								</div>
							</div> 

							<div class="clearfix"></div>
							<br>
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Fecha</span>
									<input type="text" class="form-control" id="D3" name="D3" readonly="false">
								</div>
							</div> 

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Documento</span>
									<input type="text" class="form-control text-right" id="D4" name="D4" readonly="false">
								</div>
							</div> 

							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">Numero</span>
									<input type="text" class="form-control text-right" id="D5" name="D5" readonly="false">
								</div>
							</div> 


							<div class="clearfix"></div>
							<br>

							<table class="table table-condensed">
								<thead>
									<tr>
										<th style="text-align: center;">Exento</th>
										<th style="text-align: center;">Neto</th>
										<th style="text-align: center;">IVA</th>
										<th style="text-align: center;">Reten/Imp.Esp.</th>
										<th style="text-align: center;">Total</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="input-group">
												<input type="number" class="form-control text-right" id="Exento" name="Exento" required>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="number" class="form-control text-right" id="Neto" name="Neto" required>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="number" class="form-control text-right" id="IVA" name="IVA" required>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="number" class="form-control text-right" id="Reten" name="Reten" required>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="number" class="form-control text-right" id="Total" name="Total" readonly>
											</div>
										</td>
									</tr>
								</tbody>
							</table>

							

							<!-- <div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">SQ</span>
									<input type="text" class="form-control" id="SQ" name="SQ">
								</div>
							</div> --> 


							<div class="clearfix"></div>
							<br>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-grabar" data-dismiss="modal" onclick="CargaGrilla('A')">Actualizar</button>
						<button type="button" class="btn btn-cancelar" data-dismiss="modal" onclick="limpia()">Cerrar</button>
					</div>
				</div>
				</div>
				</div>
				<!-- fin MOFIFICA dOCUMENTO-->


				<div class="modal fade" id="MovDocumento" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Mover Documento de Periodo</h4>
						</div>
						<div class="modal-body">
							
							<div class="col-md-6 text-right">
							<div class="input-group">
								<span class="input-group-addon">Mes</span>
								<select class="form-control" id="messelectM" name="messelectM" required>
								<?php 
									$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
									$i=1;
									$dmes=$dmes*1;
									while($i<=12){

										if ($i==$dmes) {
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
								<select class="form-control" id="anoselectM" name="anoselectM" required>
								<?php 
									$yoano=date('Y');
									$tano="2010";

									while($tano<=($yoano+1)){
										if ($dano==$tano) {
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

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-grabar" data-dismiss="modal" onclick="GrMov()">Mover</button>
							<button type="button" class="btn btn-cancelar" data-dismiss="modal">Cerrar</button>
						</div>
					</div>
				</div>
				</div>



		</form>
		</div>
		</div>

		<script>
			<?php 
				if(isset($_GET['C']) || (isset($_GET['V'])) || (isset($_GET['H'])) ){ 
					echo "CargaGrilla();";
				}		
			?>
		</script>

		<?php include '../footer.php'; ?>

	</body>

</html>


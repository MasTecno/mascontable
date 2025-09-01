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

	$_SESSION['PERIODOPC']=$Periodo;

	$dmes = substr($_SESSION['PERIODOPC'],0,2);
    $dano = substr($_SESSION['PERIODOPC'],3,4);

	if(isset($_POST['Fecha'])){
		if ($_POST['Fecha']!="") {
			$textfecha=$_POST['Fecha'];
		}else{
			$textfecha=date("d")."-".$dmes."-".$dano;
		}      
	}else{
		$textfecha=date("d")."-".$dmes."-".$dano;
	}

	if(!isset($_GET['page'])) {
		$current_page = 1;
	}else{
		$current_page = $_GET['page'];
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

			$( function() {
				$( "#Fecha" ).datepicker();
			} );

			function GrabaVoucher(){
				Dif=parseInt(form1.ColDebe.value)-parseInt(form1.ColHaber.value);
				if (Dif!=0) {
					alert("La sumatoria de la columna Debe es Diferente a la de la Columna Haber, Favor Revisar");
				}else{

					if (form1.Glosa.value=="") {
						alert("Ingrese Glosa para cerrar asiento");
					}else{
						if (form1.tmovi.value=="") {
							alert("No esta definito el tipo de comprobante");
						}else{
							document.getElementById("BtnGrabarGlosa").style.visibility = "hidden";
							
							var url= "xVoucher.php";
							form1.SwGrabar.value="S";
							$.ajax({
								type: "POST",
								url: url,
								data: $('#form1').serialize(),
								success:function(resp){
									$('#grilla').html(resp);
									RefreshTemp();
									document.getElementById("BtnGrabarGlosa").style.visibility = "visible";
								}
							});					
						}
					}
				}
			}

			function Refresh(){
				var url= "xVoucher.php?page=<?php echo $current_page; ?>";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
						form1.Codigo.focus();
						form1.MarApe.value="";
						form1.NoBase.value="";
						RefreshTemp();
					}
				});									
			}

			function RefreshTemp(){
				var url= "xTemp.php";

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#GTemp').html(resp);
						Blanqueo();
						form1.Codigo.focus();
					}
				});
			}

			function GTemp(){
				BuscaCuenta();

				if (form1.d3.value!="") {
					if (parseInt(form1.Debe.value)>0 && parseInt(form1.Haber.value)>0) {
						alert("Solo monto en Debe o Haber");
					}else{
						var url= "xTemp.php";

						$.ajax({
							type: "POST",
							url: url,
							data: $('#form1').serialize(),
							success:function(resp){

								Erro= resp.substr(0,10);

								if (Erro=="DocExiste*") {
									alert("El Documento referenciado ya esta Pagado");
									return false;
								}else{
									$('#GTemp').html(resp);
									Blanqueo();
									form1.Codigo.focus();									
								}

							}
						});
					}
				}
			}

			function VCalorCue(valor){
				form1.Codigo.value=valor;
				BuscaCuenta();
				document.getElementById("cmodel").click();
			}

			function BuscaCuenta(){
				var url= "../buscacuenta.php";
				var x1=$('#Codigo').val();
				$.ajax({
				type: "POST",
				url: url,
				data: ('dat1='+x1),
				success:function(resp){
					if(resp==""){
						alert("No se encontro cuenta");
						$('#Codigo').focus(); 
						$('#Codigo').select();
						form1.d3.value="";
					}else{
						form1.d3.value=resp;
						Auxiliar();
					}
				}
				});
			}

			function Auxiliar(){
				var url= "buscaauxiliar.php";

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						// if (resp=="SI") {
						// 	document.getElementById("Colap").click();
						// }
					}
				});
			}

			function BorreTemp(valor){
				form1.SwElimTemp.value=valor;
				var url= "xTemp.php";

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#GTemp').html(resp);
						Blanqueo();
						form1.Codigo.focus();
					}
				});
			}

			function Blanqueo(){
				form1.Codigo.value="";
				form1.d3.value="";
				form1.Debe.value="";
				form1.Haber.value="";
				form1.SwGrabar.value="";
				form1.Glosa.value="";
				form1.RutUno.value="";
				form1.RSocial.value="";
				form1.NDocumento.value="";
				$("#TDocumento")[0].selectedIndex = 0;
				$("#SelCliPro")[0].selectedIndex = 0;
				$("#SelCCosto")[0].selectedIndex = 0;
				form1.d3.value="";
			}

			function Diferencia(){
				if (form1.Debe.value=="" || form1.Debe.value==0) {
					if (form1.ColDebe.value!="" || form1.ColHaber.value!="") {
						Dif=parseInt(form1.ColDebe.value)-parseInt(form1.ColHaber.value);	
						if (Dif>0) {
							form1.Haber.value=Dif;
						}
						if (Dif<0) {
							form1.Debe.value=Dif*-1;
						}
					}
				}
			}

			function EliRegA(valor,Tstring){
				var r = confirm("Esta Seguro de eliminar el Voucher\r\n"+Tstring+"\r\n");
				if (r == true) {
					var url= "xVoucher.php";
					form1.dat2.value=valor;
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$('#grilla').html(resp);
							form1.dat2.value="";
						}
					});					

				}
			}

			$(document).ready(function (eOuter) {
				$('input').bind('keypress', function (eInner) {
					//alert(eInner.keyCode);
					if (eInner.keyCode == 13){

						var idinput = $(this).attr('id');

						if(idinput=="Codigo"){
							if($(this).val()==0){
								form1.Codigo.value="";
								$('#Glosa').focus();
								$('#Glosa').select();
							}else{
								BuscaCuenta();
								$('#Debe').focus();
								$('#Debe').select();
							}
						}

						if(idinput=="Debe"){
							if($(this).val()=="" || $(this).val()==0){
								Diferencia();
							}
							$('#Haber').focus();
							$('#Haber').select();
						}

						if(idinput=="Haber"){
							$('#SelCliPro').focus();
							$('#SelCliPro').select();
						}

						if(idinput=="SelCliPro"){
							$('#RutUno').focus();
							$('#RutUno').select();
						}

						if(idinput=="RutUno"){
							$('#NDocumento').focus();
							$('#NDocumento').select();
						}

						if(idinput=="NDocumento"){
							GTemp();
							$('#Codigo').focus();
							$('#Codigo').select();
						}

						if(idinput=="Glosa"){
							GrabaVoucher();
						}
						return false;
					}
				});
			});

			function Refresh1(){
				var url= "xListo.php";			
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#Listo').html(resp);
					}
				});									
			}

			function Refresh2(){
				var url= "xReferencia.php";			
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#TDocumento').html(resp);
					}
				});									
			}

			function Buscar(valor){
				form1.RutUno.value=valor;
				BuscaRut();
				document.getElementById("cmodel1").click();
			} 			

			function BuscaRut(){
				var url= "../buscadatos.php";
				var x1=$('#RutUno').val();
					
				$.ajax({
					type: "POST",
					url: url,
					data: ('dat1='+x1+'&dat2=X'),
					success:function(resp){
						if(resp==""){
							alert("Rut no encontrado");
							$('#RutUno').focus();
							$('#RutUno').select();
						}else{
							form1.RSocial.value=resp;
							Refresh2();
						}
					}
				});
			}


			function Proce(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmImpComprobante.php";
				form1.submit();
				form1.target="";
				form1.action="#";       
			}


			function ImpCom(valor){
				form2.KeyCom.value=valor;
				form1.Keyimp.value=valor;
				CargaInfCom();    
			}

			function CargaInfCom(){
				var url= "xInfComent.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form2').serialize(),
					success:function(resp){
						$('#commentcomp').val(resp);
					}
				});
			}

			function ModAsiento(valor){
				form1.KeyMod.value=valor;
				form1.action="frmModAsiento.php";
				form1.submit();
			}


			function GraCome(valor){
				var url= "xGrComent.php";

				$.ajax({
				type: "POST",
				url: url,
				data: $('#form2').serialize(),
				success:function(resp){
					document.getElementById("CerrCome").click();
					if (resp!="") {
					  alert(resp);
					}else{
						if (valor==2) {
							Proce();
						}
					}

					form1.Keyimp.value="";
					form2.KeyCom.value="";
					form2.commentcomp.value="";
				}

				});
			}
			function GuaPlan(valor){
				form3.TGuaPlan.value=valor;
			}
			function GraPlan(valor){
				var url= "xGrPlant.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form3').serialize(),
					success:function(resp){
					document.getElementById("CerrPlan").click();
						if (resp!="") {
							alert(resp);
						}
						form3.TGuaPlan.value="";
						form3.NomPlan.value="";
					}
				});
			}

			function RefRefDoc(){
				var url= "xRefDocumentos.php";			
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form4').serialize(),
					success:function(resp){
						$('#RefDocum').html(resp);
					}
				});									
			}

			function Info(r1,r2){
				form4.R1.value=r1;
				form4.R2.value=r2;
				RefRefDoc();
			}

			function GuaAper(valor){
				form1.MarApe.value=valor;
				Refresh();
			}
			function NoBase(valor){
				form1.NoBase.value=valor;
				Refresh();
			}

			jQuery(document).ready(function(e) {
				$('#myModal').on('shown.bs.modal', function() {
					$('input[name="BCodigo"]').focus();
				});

				$('#myModal1').on('shown.bs.modal', function() {
					$('input[name="BRutRaz"]').focus();
				});
			});

		</script>


	</head>
	<body onload="Refresh()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">

			<!-- Modal Referenciade documentos -->
			<div class="modal fade" id="RefDocumentos" role="dialog">
			<div class="modal-dialog modal-lg" style="width: 1200px;">
				<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Referencias</h4>
				</div>
				<div class="modal-body">
					<form action="#" method="POST" name="form4" id="form4">
						<input type="hidden" name="R1" id="R1">
						<input type="hidden" name="R2" id="R2">
						

						<div class="col-md-12" id="RefDocum">

						</div>

						<div class="clearfix"></div>
						<br>
					</form>
				</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" id="CerrPlan">Cerrar</button>
					</div>
				</div>
			</div>
			</div>
			<!-- Modal Referenciade documentos -->



			<!-- Modal comentario -->
			<div class="modal fade" id="ModalComent" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Nota Voucher - Comprobante</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<form action="#" method="POST" name="form2" id="form2">
								<label for="comment">Comentario:</label>
								<textarea class="form-control" rows="5" id="commentcomp" name="commentcomp"></textarea>
								<input type="hidden" name="KeyCom" id="KeyCom" value="">
							</form>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" id="CerrCome">Cerrar</button>
						<button type="button" class="btn btn-default" data-dismiss="modal" onclick="GraCome(1)">Grabar</button>
						<button type="button" class="btn btn-default" data-dismiss="modal" onclick="GraCome(2)">Grabar e Imprimir</button>
					</div>
				</div>
			</div>
			</div>
			<!-- Modal comentario -->

			<!-- Modal Graba Plantilla -->
			<div class="modal fade" id="ModalPlantilla" role="dialog">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Guardar Plantilla de Voucher</h4>
				</div>
				<div class="modal-body">
					<form action="#" method="POST" name="form3" id="form3">
						<input type="hidden" name="TGuaPlan" id="TGuaPlan">
						<div class="col-md-12">
							<input type="text" class="form-control" name="NomPlan" id="NomPlan" onChange="javascript:this.value=this.value.toUpperCase();">  
						</div>
						<div class="clearfix"></div>
						<br>
					</form>
				</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" id="CerrPlan">Cerrar</button>
						<button type="button" class="btn btn-default" data-dismiss="modal" onclick="GraPlan()">Grabar</button>
					</div>
				</div>
			</div>
			</div>
			<!-- Modal Graba Plantilla -->

		<form action="#" name="form1" id="form1" method="POST">
			<br>
			<input type="hidden" name="VCalorCue" id="VCalorCue">
			<input type="hidden" name="SwElimTemp" id="SwElimTemp">
			<input type="hidden" name="SwGrabar" id="SwGrabar">
			<input type="hidden" name="dat2" id="dat2">
			<input type="hidden" name="KeyAs" id="KeyAs">
			<input type="hidden" name="Keyimp" id="Keyimp">
			<input type="hidden" name="KeyMod" id="KeyMod">
			<input type="hidden" name="MarApe" id="MarApe">
			<input type="hidden" name="NoBase" id="NoBase">

			<div class="col-sm-12">

				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading text-center">Registro</div>
					<div class="panel-body">
						<div class="col-md-4">						
							<div class="input-group">
								<span class="input-group-addon">Fecha</span>
								<input type="text" class="form-control text-right" id="Fecha" name="Fecha" size="10" maxlength="10" onblur="Refresh()" value="<?php echo $textfecha; ?>"><!--  -->
							</div>
						</div>

						<div class="col-md-8 text-center">						
							<div class="input-group" style="display: block;">
								<div class="col-md-2">
									<label class="radio-inline"><input type="radio" name="Tipo" onclick="javascript:form1.tmovi.value='I'">Ingreso</label>
								</div>
								<div class="col-md-2">
									<label class="radio-inline"><input type="radio" name="Tipo" onclick="javascript:form1.tmovi.value='E'">Egreso</label>
								</div>
								<div class="col-md-2">
									<label class="radio-inline"><input type="radio" name="Tipo" onclick="javascript:form1.tmovi.value='T'">Traspaso</label>
								</div>
							</div>
							<input type="hidden" name="tmovi" id="tmovi" value="<?php echo $_POST['tipovou']; ?>">
						</div>
						<div class="clearfix"></div>
						<br>

						<hr>

						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Codigo</span> 
								<input type="number" class="form-control text-right" id="Codigo" name="Codigo" value="<?php echo $LCuenta;?>">
								<div class="input-group-btn"> 
									<button type="button" class="btn btn-mastecno" data-toggle="modal" data-target="#myModal">
										<span class="glyphicon glyphicon-search"></span> 
									</button>
								</div> 
							</div> 
						</div>

						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Cuenta</span> 
								<input type="text" class="form-control" id="d3" name="d3" onChange="javascript:this.value=this.value.toUpperCase();" value="" readonly="false" >
							</div>
						</div>
						<div class="col-md-4" style="margin-top: 10px;">
							<div class="input-group">
								<span class="input-group-addon">Debe</span> 
								<input type="text" class="form-control text-right" oninput="MilesManu(this.value, this.id)" id="Debe" name="Debe" maxlength="50" onclick="Diferencia()" value="<?php echo $LDebe;?>">
							</div>
						</div>
						<div class="col-md-4" style="margin-top: 10px;">
							<div class="input-group">
								<span class="input-group-addon">Haber</span> 
								<input type="text" class="form-control text-right" oninput="MilesManu(this.value, this.id)" id="Haber" name="Haber" maxlength="50" onclick="Diferencia()" value="<?php echo $LHaber;?>">
							</div>
						</div>

						<div class="col-md-4" style="margin-top: 10px;">
							<div class="input-group">
								<span class="input-group-addon">Centro Costo</span>
								<select class="form-control" id="SelCCosto" name="SelCCosto">
									<option value="0"></option>
									<?php 
										// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
										$mysqli = xconectar("root", "", "mastecno_server08");
										$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
										$resultados = $mysqli->query($SQL);
										while ($registro = $resultados->fetch_assoc()) {
											echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
										}
										$mysqli->close();
									?>
								</select>
							</div>
						</div>

						<div class="clearfix"></div>
						<br>

						<div class="col-md-12">
							<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading" style="padding: 1px 15px; color: #333; background-color: #eee;">
									<h4 class="panel-title" data-toggle="collapse" href="#collapse1" id="Colap">
										<l style="font-size: 12px;">Atributos</l>
									</h4>
								</div>
								<div id="collapse1" class="panel-collapse collapse">
									<div class="panel-body">

										<div class="col-md-6">
										<div class="input-group">
											<span class="input-group-addon">Asociar</span> 
											<select class="form-control" id="SelCliPro" name="SelCliPro">
												<option value=""></option>
												<option value="C">Cliente</option>
												<option value="P">Proveedor</option>
											</select>
										</div>
										</div>



										<div class="col-md-6">
										<div class="input-group">
											<span class="input-group-addon">Rut</span> 
											<input type="text" class="form-control text-right" id="RutUno" name="RutUno" readonly>
											<div class="input-group-btn"> 
												<button type="button" class="btn btn-cancelar" data-toggle="modal" data-target="#myModal1" onclick="Refresh1()">
													<span class="glyphicon glyphicon-search"></span> 
												</button>
											</div> 
										</div> 
										</div>
													<!-- Modal  buscar rut-->
													<div class="modal fade" id="myModal1" role="dialog">
													<div class="modal-dialog modal-lg">
													<div class="modal-content">
														<div class="modal-header">
															<h4 class="modal-title">Listado</h4>
														</div>

														<div class="modal-body">

															<div class="col-md-12">
																<input class="form-control" id="BRutRaz" name="BRutRaz" type="text" placeholder="Buscar...">
															</div>


															<div class="col-md-12" id="Listo">
															</div>
														</div>

														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal" id="cmodel1">Cerrar</button>
														</div>
													</div>
													</div>
													</div>
													<!-- fin buscar rut --> 

										<div class="col-md-4" style="margin-top: 10px;">
										<div class="input-group">
											<span class="input-group-addon">Raz&oacute;n Social</span> 
											<input type="text" class="form-control text-right" id="RSocial" name="RSocial" disabled>
										</div>
										</div>

										<div class="col-md-4" style="margin-top: 10px;">
										<div class="input-group">
											<span class="input-group-addon">Referencia</span> 
											<select class="form-control" id="TDocumento" name="TDocumento">
												<option value=""></option>
											</select>
										</div>
										</div>

										<div class="col-md-4" style="margin-top: 10px;">
										<div class="input-group">
											<span class="input-group-addon">Documento</span> 
											<input type="number" class="form-control text-right" id="NDocumento" name="NDocumento" maxlength="30">
										</div>
										</div>

									</div>
									<!-- <div class="panel-footer">Panel Footer</div> -->
								</div>
							</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>
						<div class="col-md-8">
						</div>
						<div class="col-md-4">
							<button type="button" class="btn btn-mastecno btn-block" id="btinsert" name="btinsert" onclick="GTemp()">Insertar Linea</button>
						</div>


						<div class="clearfix"></div>
						<hr>
						<div class="col-md-2"></div>
						<div class="col-md-8">
							<table class="table table-condensed">
								<thead>
								<tr>
									<th width="10%">Codigo</th>
									<th>Cuenta</th>
									<th width="10%" style="text-align: right;">Debe</th>
									<th width="10%" style="text-align: right;">Haber</th>
									<th width="10%" style="text-align: center;">C. Costo</th>
									<th width="1%">&nbsp;</th>
								</tr>
								</thead>
								<tbody id="GTemp">

								</tbody>
							</table>

							<div class="input-group">
								<span class="input-group-addon">Glosa</span>
								<input type="text" class="form-control text-right" id="Glosa" name="Glosa" size="50" maxlength="50" value="" onChange="javascript:this.value=this.value.toUpperCase();">
								<button type="button" class="btn btn-grabar form-control" id="BtnGrabarGlosa" name="BtnGrabarGlosa" onclick="GrabaVoucher()">
									<span class="glyphicon glyphicon-floppy-saved"></span> Cierra Voucher
								</button>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

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
													// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
													$mysqli = xconectar("root", "", "mastecno_server08");
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
															<tr onclick="VCalorCue(\''.$registro["numero"].'\')">
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



					</div>				
				</div>

			</div>
			
			<div class="clearfix"></div>
			<div class="col-sm-12" id="grilla">

			</div>

		</form>
		</div>
		</div>

		<script type="text/javascript">
			$( "#Fecha" ).datepicker({
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
					Refresh();
					// $('#d1').val(dateText);
					// $('#d2').focus();
					// $('#d2').select();
				}
			});			
		</script>

		<?php include '../footer.php'; ?>
	</body>
</html>
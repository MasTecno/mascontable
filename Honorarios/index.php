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

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	if(isset($_GET['Doc'])){
		if($_GET['Doc']==1){
			$frm="C"; 
		}
		if($_GET['Doc']==2){
			$frm="V";
		}
	}

	$dmes = substr($Periodo,0,2);
	$dano = substr($Periodo,3,4);

	if(isset($_POST['d1'])){
		if ($_POST['d1']!="") {
			$textfecha=$_POST['d1'];
		}else{
			$textfecha="01-".$dmes."-".$dano;
		}      
	}else{
		$textfecha="01-".$dmes."-".$dano;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}
		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}
		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}
	}

	$SQL="SELECT * FROM CTParametros";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro['tipo']=='RETE_HONO') {
			$Val_Ret=$registro['valor'];
		}
	}

	if ($dano=="2020") {
		$Val_Ret=10.75;
	}

	if ($dano=="2021") {
		$Val_Ret=11.5;
	}

	if ($dano=="2022") {
		$Val_Ret=12.25;
	}

	if ($dano=="2023") {
		$Val_Ret=13;
	}

	if ($dano=="2024") {
		$Val_Ret=13.75;
	}

	if ($dano=="2025") {
		$Val_Ret=14.5;
	}

	if ($dano=="2026") {
		$Val_Ret=15.25;
	}

	if ($dano=="2027") {
		$Val_Ret=16;
	}

	if ($dano=="2028") {
		$Val_Ret=17;
	}

	$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	}else{
		$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa=''";      
	}

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro['tipo']=="R") { ///honorarios Recibido
			$Rec1=$registro['L1'];
			$Rec2=$registro['L2'];
			$Rec3=$registro['L3'];
			$Rec4=$registro['L4'];

			if ($_SESSION["PLAN"]=="S"){
				$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$Rec1' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL1="SELECT * FROM CTCuentas WHERE numero='$Rec1'";
			}
			$res = $mysqli->query($SQL1);
			while ($reg = $res->fetch_assoc()) {
				$XnL1=$reg['detalle'];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$Rec2' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL1="SELECT * FROM CTCuentas WHERE numero='$Rec2'";
			}
			$res = $mysqli->query($SQL1);
			while ($reg = $res->fetch_assoc()) {
				$XnL2=$reg['detalle'];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$Rec3' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL1="SELECT * FROM CTCuentas WHERE numero='$Rec3'";
			}

			$res = $mysqli->query($SQL1);
			while ($reg = $res->fetch_assoc()) {
				$XnL3=$reg['detalle'];
			}

			if ($_SESSION["PLAN"]=="S"){
				$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='$Rec4' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
			}else{
				$SQL1="SELECT * FROM CTCuentas WHERE numero='$Rec4'";
			}

			$res = $mysqli->query($SQL1);
			while ($reg = $res->fetch_assoc()) {
				$XnL4=$reg['detalle'];
			}

		}

		if ($registro['tipo']=="E") { ///Honorarios emitidos
			$Emi1=$registro['L1'];
			$Emi2=$registro['L2'];
			$Emi3=$registro['L3'];
		}
	}

	if($Rec4==""){
		$Rec4=0;
	}
	$mysqli->close();
	// echo $Rec4;
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

		<script>
			function BuscaRut(){
				var url= "../buscadatos.php";
				var x1=$('#d2').val();
				var x2=$('#frm').val();
				$.ajax({
					type: "POST",
					url: url,
					data: ('dat1='+x1+'&dat2='+x2),
					success:function(resp){
						if(resp==""){
							form1.d3.value="";
							alert("Rut no encontrado");
							$('#d3').focus();
							$('#d3').select();
						}else{
							form1.d3.value=resp;
						}
					}
				});
			}
			
			function EliReg(valor){

				var r = confirm("Esta seguro de Eliminar el Honorios...");
				if (r == true) {
					var url= "grillahonorarios.php";
					$.ajax({
					type: "POST",
					url: url,
					data: ('dat1='+valor),
						success:function(resp){
							CargGrilla();
						}
					});
				}
			}

			function BuscaCuenta(){
				var url= "../buscacuenta.php";
				var x1=$('#d7').val();
				$.ajax({
					type: "POST",
					url: url,
					data: ('dat1='+x1),
					success:function(resp){
						if(resp==""){
						alert("No se encontro cuenta");
							$('#d7').focus(); 
							$('#d7').select();
						}else{           
							form1.d8.value=resp;
							$('#d9').focus(); 
							$('#d9').select();
						}
					}
				});
			}

			function CargGrilla(){
				var url= "grillahonorarios.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
					}
				});
			}

			function GBDocum(){
				var url= "gbhonorario.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){       
						if(resp!=""){
							$('#msj1').html(resp);
						}else{
							form1.d2.value="";
							form1.d3.value="";
							form1.d6.value="";
							// form1.d10.value="";
							form1.d11.value=0;
							form1.d12.value=0;
							form1.d13.value=0;

							$('#msj1').html(resp);
							CargGrilla(); 
						}
					}
				});
			}

			$(document).ready(function (eOuter) {

				$('input').bind('keypress', function (eInner) {
					//alert(eInner.keyCode);
					if (eInner.keyCode == 13) {

						var idinput = $(this).attr('id');

						if(idinput=="d1"){
							$('#d2').focus();
							$('#d2').select();
						}

						if(idinput=="d2"){
							BuscaRut();
							var str =form1.d1.value;
							var mes= Number(str.slice(3, 5));
							var ano= Number(str.slice(6, 10));

							var sumfe=(ano*12)+mes;

							var mesc=<?php echo $dmes; ?>;
							var anoc=<?php echo $dano; ?>;

							var sumpe=(anoc*12)+mesc;
							var res =sumpe-sumfe;

							if (res>=0 && res<=3) {
							}else{
								alert("Este documento tiene diferencias de fecha con respecto Periodo que trabajas");
							}

							$('#d5').focus();
							$('#d5').select();
						}

						if(idinput=="d5"){
							$('#d6').focus();
							$('#d6').select();
						}

						if(idinput=="d6"){
							$('#d11').focus();
							$('#d11').select();
						}

						// if(idinput=="d7"){
						//   BuscaCuenta();
						//   $('#d10').focus();
						//   $('#d10').select();
						// }

						// if(idinput=="d10"){
						// 	RETEN();
						// 	$('#d11').focus();
						// 	$('#d11').select();
						// }

						if(idinput=="mfecha"){
							$('#mcuenta1').focus();
							$('#mcuenta1').select();
						}

						//linea 1 model

						<?php 

						$i = 1;
							while ($i <= 4) {
								echo "
								if(idinput==\"mcuenta".$i."\"){
								BuscaCuentaMod(this.id);
								$('#mdebe".$i."').focus();
								$('#mdebe".$i."').select();
								}

								if(idinput==\"mdebe".$i."\"){
								$('#mhaber".$i."').focus();
								$('#mhaber".$i."').select();
								}

								if(idinput==\"mhaber".$i."\"){
								$('#mcuenta".($i+1)."').focus();
								$('#mcuenta".($i+1)."').select();
								}
								";
								$i++; 
							}

						?>

						return false;
					}
					});
			});

			function BuscaCuentaMod(vall){
				var url= "../buscacuentafact.php";
				var x1=$('#'+vall).val();
				$.ajax({
					type: "POST",
					url: url,
					data: ('dat1='+x1),
					success:function(resp){
						var r=Number(vall.substr(7, 1));
						var r='mdetalle'+r;

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

			function Lala(iddoc,canBruto,canRete,canRete3,canLiqui,NDoc,NCuenta,TDoc,Cadena,RutHono,FecDoc){
				fmodal.iddoc.value=iddoc;
				fmodal.canBruto.value=canBruto;
				fmodal.canRete.value=canRete;
				fmodal.canRete3.value=canRete3;
				fmodal.canLiqui.value=canLiqui;
				fmodal.NDoc.value=NDoc;
				fmodal.NCuenta.value=NCuenta;
				fmodal.TDoc.value=TDoc;
				fmodal.Cadena.value=Cadena;
				fmodal.RutHono.value=RutHono;
				fmodal.mfecha.value=FecDoc;

				if (TDoc=="R" || TDoc=="T") {
					Cod1=<?php echo $Rec1; ?>;
					Cod2=<?php echo $Rec2; ?>;
					Cod3=<?php echo $Rec3; ?>;
					Cod4=<?php echo $Rec4; ?>;

					fmodal.mcuenta1.value=Cod1;
					fmodal.mcuenta2.value=Cod2;
					fmodal.mcuenta3.value=Cod3;
					fmodal.mcuenta4.value=Cod4;

					fmodal.mdebe1.value=canBruto;
					fmodal.mhaber2.value=canRete;
					fmodal.mhaber3.value=canLiqui;
					fmodal.mhaber4.value=canRete3;
					fmodal.Glosa.value="BOLETA DE HONORARIO(S) "+NDoc;
				}
			}

			function soloNumeros(e){
				var key = window.Event ? e.which : e.keyCode
				return (key >= 48 && key <= 57)
			}

			$( function() {
				$( "#d1" ).datepicker();
				$( "#mfecha" ).datepicker();
			});
			function data(valor){
				form1.d7.value=valor;
				BuscaCuenta();
				document.getElementById("cmodel").click();
			}

			function data1(valor){
				form1.d2.value=valor;
				BuscaRut();
				document.getElementById("cmodel1").click();
				document.getElementById("d6").focus();
				document.getElementById("d6").select();
			} 

			function RETEN(){
				if (form1.d10.value!="" && form1.d10.value>0) {
					form1.BolExe.checked=false;
					if(document.getElementById("retinc").checked==true){
						if (form1.CTRETEF.value>0) {
							form1.d13.value=parseInt(form1.d10.value);
							form1.d11.value=Math.round((form1.d10.value/form1.CTRETEF.value));
							form1.d12.value= parseInt(form1.d11.value)- parseInt(form1.d13.value);
						}
					}else{
						if (form1.CTRETE.value>0) {
							form1.d11.value=parseInt(form1.d10.value);
							form1.d12.value=Math.round((form1.d11.value*form1.CTRETE.value)/100);
							form1.d13.value= parseInt(form1.d11.value)- parseInt(form1.d12.value);
						}
					}
				}
			}

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


			$(document).ready(function (eOuter) {
				$('input').bind('keypress', function (eInner) {
				//alert(eInner.keyCode);
					if (eInner.keyCode == 13){
						var idinput = $(this).attr('id');
						<?php 
							$i = 1;
							while ($i <= 5) {
								echo "
									if(idinput==\"mdetalle".$i."\"){
										BuscaCuenta(this.id);
										$('#mdetalle".($i+1)."').focus();
										$('#mdetalle".($i+1)."').select();
									}
								";
								$i++; 
							}

						?>
						return false;
					}
				});
			});     

			function BuscaCuenta(vall){
				var url= "../buscacuenta.php";
				var x1=$('#'+vall).val();
				$.ajax({
					type: "POST",
					url: url,
					data: ('dat1='+x1),
					success:function(resp){
						var r=Number(vall.substr(7, 1));
						var r='mdetalle'+r;
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

			function GBDocumCent(){
				var url= "xfrmRegHonorariosModal.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#fmodal').serialize(),
					success:function(resp){
						if(resp!=""){
							alert(resp);
							// $('#msjx').html(resp);
						}else{
							// $('#msjx').html(resp);
							CargGrilla();
							$("#CMOD").click();
						}
					}
				});
			}

			function Exce(){
				form1.d11.value=form1.d10.value;
				form1.d12.value=0;
				form1.d13.value=form1.d10.value;
				form1.retinc.checked=false;
			}

			function RetAdc(){
				if (form1.RetAdi.checked==false) {
					RETEN();
				}else{
					R=parseInt(form1.d12.value);
					form1.d12.value=((parseInt(form1.d11.value)*3)/100)+R;//+parseInt(form1.d12.value);	
				}
			}
			function calc() {
				form1.d13.value=(parseInt(form1.d11.value)-parseInt(form1.d12.value));
			}

			function EliDocu(){
				var r = confirm("Esta seguro de Eliminar los Documentos!, Solo afectara a aquellos que no estan centralizados...");
				if (r == true) {
					form1.EliRegi.value="S";
					CargGrilla();
					form1.EliRegi.value="";
				}     
			}

			jQuery(document).ready(function(e) {
				$('#myModal1').on('shown.bs.modal', function() {
					$('input[name="BRut"]').focus();
				});
			});
		</script>

	</head>
	<body onload="CargGrilla()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">

			<?php
				include 'frmRegHonorariosModal.php';
			?>


			<div class="col-sm-12 text-left">
				<br>

				<form action="#" method="POST" name="form1" id="form1">
					<div class="col-md-10">
						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading text-center"><strong>Registro de Honorarios</strong></div>
							<div class="panel-body">

								<div class="col-md-2">
									<label>Fecha</label>
									<input id="d1" name="d1" type="text" class="form-control" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
									<input type="hidden" name="CTEmpre" id="CTEmpre">
									<input type="hidden" name="CTRETE" id="CTRETE" value="<?php echo $Val_Ret;?>">
									<input type="hidden" name="CTRETEF" id="CTRETEF" value="<?php echo $Val_FRet;?>">
									<input type="hidden" name="EliRegi" id="EliRegi">
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
															<input class="form-control" id="BRut" name="BRut" type="text" placeholder="Buscar...">
														</div>
														<div class="col-md-12">

															<table class="table table-condensed table-hover">
																<thead>
																	<tr>
																	<th>Rut</th>
																	<th>Raz&oacute;n Social</th>
																	</tr>
																</thead>
																<tbody id="TableRut">
																	<?php 
																		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
																		$SQL="SELECT * FROM CTCliPro WHERE tipo='P' AND estado='A' ORDER BY razonsocial";
																		$resultados = $mysqli->query($SQL);
																		while ($registro = $resultados->fetch_assoc()) {

																			echo '
																				<tr onclick="data1(\''.$registro["rut"].'\')">
																				<td>'.$registro["rut"].'</td>
																				<td>'.$registro["razonsocial"].'</td>
																				</tr>
																			';
																		}
																		$mysqli->close();
																	?>
																</tbody>
															</table>

															<script>
																$(document).ready(function(){
																	$("#BRut").on("keyup", function() {
																	var value = $(this).val().toLowerCase();
																		$("#TableRut tr").filter(function() {
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
													<button type="button" class="btn btn-danger" data-dismiss="modal" id="cmodel1">Cerrar</button>
													</div>
												</div>
											</div>
										</div>
									<!-- fin buscar rut -->      

								<div class="col-md-2">
									<label>Rut</label>
									<div class="input-group"> 
										<input type="text" class="form-control" id="d2" name="d2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $LRut;?>" required>
										<div class="input-group-btn"> 
											<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal1" onfocus="javascritp:document.getElementById('d6').focus();">
												<span class="glyphicon glyphicon-search"></span> 
											</button>
										</div> 
									</div> 
								</div>

								<div class="col-md-6">
									<label>Razon Social</label>  
									<input type="text" class="form-control" id="d3" name="d3"  value="<?php echo $LRSocial; ?>" onChange="javascript:this.value=this.value.toUpperCase();">
								</div>

								<div class="clearfix"> </div>

								<div class="col-md-2">
									<label>N&deg; Documento </label>
									<input type="text" class="form-control text-right" id="d6" name="d6" maxlength="50" value="<?php echo $LNumero; ?>" required>
								</div>          
		
								<div class="clearfix"> </div>

								<div class="col-md-2">
									<label>Bruto</label>  
									<input type="text" class="form-control text-right" id="d11" name="d11" value="0" onchange="calc()">
								</div>

								<div class="col-md-2">
									<label>Retenci&oacute;n</label>  
									<input type="text" class="form-control text-right" id="d12" name="d12" value="0" onchange="calc()">
								</div>

								<div class="col-md-2">
									<label>Liquido</label>  
									<input type="text" class="form-control text-right" id="d13" name="d13" value="0" onchange="calc()">
								</div>  

								<div class="col-md-2">
									<label>Periodo</label>  
									<input type="text" class="form-control text-right" id="PERD" name="PERD" readonly="false" value="<?php echo $Periodo; ?>">
								</div> 

								<div class="clearfix"></div>
								<br>

								<div class="col-md-10" id="msj1">
								</div>
								<div class="clearfix"></div>
								<hr>

								<button type="button" class="btn btn-grabar" onclick="GBDocum()">
									<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
								</button>

								<button type="button" class="btn btn-cancelar" onclick="Volver()">
									<span class="glyphicon glyphicon-remove"></span> Cancelar
								</button>  


							</div>
						</div>
					</div>

					<div class="col-md-2">
						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading text-center"><strong>Utilidades</strong></div>
							<div class="panel-body text-center">
								<a href="frmCentraHonorarios.php" class="btn btn-success btn-block" role="button">Centralizaci&oacute;n Masiva</a>

								<button type="button" class="btn btn-block btn-mastecno" onclick="EliDocu()" title="Eliminar&aacute; todos los documentos que no est&aacute;n procesados">
									<span class="glyphicon glyphicon-remove"></span> Eliminar Documentos
								</button>
							</div>
						</div>
					</div>
				</form>

				<div class="clearfix"> </div>
				<hr>



				<div class="col-md-12">       
					<div class="col-md-4"><h4>Documentos del Periodo <?php echo $Periodo; ?> ( Retenci&oacute;n <?php echo $Val_Ret; ?>%)</h4></div>
					<div class="col-md-8"><input class="form-control" id="myInput" type="text" placeholder="Buscar..."></div>
				</div>
				<div class="clearfix"></div>
				<br>


				<table class="table table-hover table-condensed" id="grilla">

				</table>
			</div>

		</div>
		</div> 

		<?php include '../footer.php'; ?>

	</body>
	<script type="text/javascript">

		$( "#d1" ).datepicker({
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
				$('#d1').val(dateText);
				$('#d2').focus();
				$('#d2').select();
			}
		});  
		$( "#mfecha" ).datepicker({
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
			// $('#d2').val(dateText);
			}
		});  
		<?php 
			if($Rec4==0){
				echo 'alert("Hemos realizado una actualizaci\u00F3n de la retenci\u00F3n para el 3% correspondiente al prestamos solidario. Para ello debe crear una cuenta para este concepto y asignala en la configuraci\u00F3n de Centralización de Honorarios. \n\nSi necesita ayuda, contactar a su ejecutivo.");';
			}

		?>
		$(document).ready(function(){
			$("#myInput").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#ListDoc tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});
		});

	</script>
</html>
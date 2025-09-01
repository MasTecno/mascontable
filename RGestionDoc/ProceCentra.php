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

	if ($_POST['messelect']<=9) {
		$PerCen="0".$_POST['messelect']."-".$_POST['anoselect'];
	}else{
		$PerCen=$_POST['messelect']."-".$_POST['anoselect'];
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

		</style>

		<script type="text/javascript">
			$(window).load(function(){
				$('#SelCta').select2();
			});
			function Centrali() {
				swProce="N";
				if (form1.SwPago.checked==true && form1.SelCta.value==0) {
					alert("Debe selecionar una cuenta para el pago automatico");
				}else{
					swProce="S";
				}

				if (swProce=="S") {
					document.getElementById("BtnVisual").style.visibility = "hidden";
					document.getElementById("Mensa").style.display = 'inline';


					if (form1.Op2.checked==true) {
						form1.action="xProceCentra.php";
						form1.submit();
					}else{
						if (form1.Op1.checked==true) {
							form1.action="xProceCentraMensual.php";
							form1.submit();
						}						
					}
				}

			}
			function ActivaPago(){
				if (document.getElementById("SelPag").style.visibility == "hidden") {
					document.getElementById("SelPag").style.visibility = "visible";
				}else{
					document.getElementById("SelPag").style.visibility = "hidden";
				}
			}
			function ProcesarCentra(){
				if (document.getElementById("BtnVisual").style.visibility == "hidden") {
					document.getElementById("BtnVisual").style.visibility = "visible";
				}else{
					document.getElementById("BtnVisual").style.visibility = "hidden";
				}
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

			<input type="hidden" name="frm" id="frm" value="<?php echo $_POST['frm']; ?>">
			<input type="hidden" name="messelect" id="messelect" value="<?php echo $_POST['messelect']; ?>">
			<input type="hidden" name="anoselect" id="anoselect" value="<?php echo $_POST['anoselect']; ?>">
			
			<br>
			<div class="col-sm-12">

				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div class="col-md-4">

						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading text-center">1 - Tipo de Centralizaci&oacute;n</div>
							<div class="panel-body">
								<?php
									if (count($_POST['check_list'])>1) {  
										echo '
											<div class="radio">
												<label><input type="radio" name="optradio" id="Op1" name="Op1" checked> Mensual</label>
											</div>

											<div class="radio">
												<label><input type="radio" name="optradio" id="Op2" name="Op2"> Individual</label>
											</div>
										';

										if($_POST['frm']=="H"){
											echo '
												<div class="checkbox">
													&nbsp&nbsp&nbsp&nbsp&nbsp<label><input type="checkbox" name="SwNombreHono" id="SwNombreHono" value="">Insertar Nombre en la glosa, solo centralización individual</label>
												</div>
											';
										}

									}else{
										echo '
											<div class="radio">
												<label><input type="radio" name="optradio" id="Op2" name="Op2" checked> Individual</label>
											</div>
										';
										if($_POST['frm']=="H"){
											echo '
												<div class="checkbox">
													&nbsp&nbsp&nbsp&nbsp&nbsp<label><input type="checkbox" name="SwNombreHono" id="SwNombreHono" value="">Insertar Nombre en la glosa, solo centralización individual</label>
												</div>
											';
										}


									}
								?>
							</div>
						</div>
					</div>
					<div class="col-md-4">

						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading text-center">2 - Paga <l style="font-size: 10px;">* Opcional</l></div>
							<div class="panel-body">
								<div class="checkbox">
									<label><input type="checkbox" id="SwPago" name="SwPago" value="" onclick="ActivaPago()"> Deseo registrar como pagado los documentos seleccionados. <br>Debe definir una cuenta para el pago.</label>
								</div>

								<div class="input-group" id="SelPag" style="visibility:hidden;">
									<span class="input-group-addon">Cuenta</span>
										<select id="SelCta" name="SelCta" class="form-control">
										<option value="0">Seleccione Cuenta...</option>
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
						</div>
					</div>
					<div class="col-md-4">
						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading text-center">3 - Procesar</div>
							<div class="panel-body">
								<button type="button" class="btn btn-grabar btn-block" id="BtnVisual" onclick="Centrali()">Procesar Documento(s)</button>
								<span style="font-size: 10px;">El tiempo del proceso dependerá de la cantidad de Documento(s)</span>

								<div class="col-md-12" id="Mensa" style="display:none;">
									<div class="alert alert-warning alert-dismissible" style="text-align: center; background-color: #fbc7c7;">
										<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
										<strong>Generando!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
				<div class="clearfix"></div>
				<br>

				<?php

					$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
					unset($_SESSION['ARRCENTRA']);
					$Lasientos=array(
						'IdDoc'=>'xxxx',
						'FDocu'=>'xxxx',
						'Peri'=>'xxxx',
						'Cta'=>'xxxx',
						'CC'=>'xxxx',
						'MDebe'=>'xxxx',
						'MHaber'=>'xxxx',
						'AKeyAs'=>'xxxx'
					);
					$_SESSION['ARRCENTRA'][0]=$Lasientos;


					$SQL="SELECT * FROM CTParametros WHERE estado='A'";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						if($registro['tipo']=="SEPA_MILE"){
							$DMILE=$registro['valor'];  
						}

						if($registro['tipo']=="SEPA_DECI"){
							$DDECI=$registro['valor'];  
						}

						if($registro['tipo']=="TIPO_MONE"){
							$DMONE=$registro['valor'];  
						}

						if($registro['tipo']=="NUME_DECI"){
							$NDECI=$registro['valor'];  
						} 
					}

					$SQL="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa='$RutEmpresa'";
					$resultados = $mysqli->query($SQL);
					$row_cnt = $resultados->num_rows;
					if ($row_cnt==0) {
						$SQL="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa=''";
					}

					if ($_POST['frm']=="C") {
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							$AUX=$registro["L4"];// AUXILIAR PROVEEDORES
							$IVA=$registro["L2"];// IVA CREDITO
							$OTR=$registro["L3"];// IVA NO RECUPERABLE
						}
					}

					if ($_POST['frm']=="V") {
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							$AUX=$registro["L1"];// AUXILIAR CLIENTES
							$IVA=$registro["L3"];// IVA DEBITO
							$OTR=$registro["L4"];// OTRA RETENCIÓN
						}
					}

					if ($_POST['frm']=="H") {
						$dano = substr($PerCen,3,4);

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

						$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa='$RutEmpresa'";
						$resultados = $mysqli->query($SQL);
						$row_cnt = $resultados->num_rows;
						if ($row_cnt==0) {
							$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa=''";
						}

						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							$AUX=$registro["L1"];// AUXILIAR HONORARIOS
							$RET=$registro["L2"];// RETENCION
							$HXP=$registro["L3"];// HONORARIOS POR PAGAR
							$RE3=$registro["L4"];// RETENCION 3%
						}
					}

					$GKeyAs=date("YmdHis")."1";

					$TT=0;
					if(isset($_SESSION['DOCUCENTRA'])){
						unset($_SESSION['DOCUCENTRA']);
					}					

					foreach($_POST['check_list'] as $selected) {

						$RIdDocX=array(
							'IdDocX'=>descript($selected),
							'Peri'=>$PerCen
						);

						$_SESSION['DOCUCENTRA'][$TT]=$RIdDocX;
						$TT++;

						if($_POST['frm']=="H"){
							$SQL="SELECT * FROM CTHonorarios WHERE estado='A' AND id='".descript($selected)."' AND rutempresa='$RutEmpresa' AND movimiento=''";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$RutHon=$registro["rut"];
								$calbruto=$registro["bruto"];
								$calrete=$registro["retencion"];
								$calliqui=$registro["liquido"];
								$calreteCal=round(($registro["bruto"]*$Val_Ret)/100);
								$calreteCal3=round(($registro["bruto"]*3)/100);

								$CCosto=$registro['ccosto'];

								$calrete3=$calrete-$calreteCal;
								$calrete=$calreteCal;
						
								$color='';
								if($calrete3<0){
									$calrete3=0;
								}
						
								if($registro["retencion"]==0){
									$calrete=0;
								}

								if($registro["cuenta"]<>$AUX){
									$AUX=$registro["cuenta"];
								}

								$SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='$RutEmpresa' AND rut='$RutHon' AND tipo='H'";
								$Resul = $mysqli->query($SqlCP);
								$row_cnt = $Resul->num_rows;
								if ($row_cnt>0) {
									$SqlCP="SELECT * FROM CTCliProCuenta WHERE rutempresa='$RutEmpresa' AND rut='$RutHon' AND tipo='H'";
									$Resul = $mysqli->query($SqlCP);
									while ($Reg = $Resul->fetch_assoc()) {
										if ($Reg['cuenta']!=0) {
											$AUX=$Reg['cuenta'];
										}
									}
								}

								/////Total
								$NProductos=count($_SESSION['ARRCENTRA']);
								$Lasientos=array(
										'IdDoc'=>$registro['id'],
										'FDocu'=>$registro['fecha'],
										'Peri'=>$PerCen,
										'Cta'=>$AUX,
										'CC'=>$CCosto,
										'MDebe'=>$registro['bruto'],
										'MHaber'=>"0",
										'Glosa'=>"",
										'AKeyAs'=>$GKeyAs
								);
								$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
								
								if($calrete>0){
									$NProductos=count($_SESSION['ARRCENTRA']);
									$Lasientos=array(
											'IdDoc'=>$registro['id'],
											'FDocu'=>$registro['fecha'],
											'Peri'=>$PerCen,
											'Cta'=>$RET,
											'CC'=>"0",
											'MDebe'=>"0",
											'MHaber'=>$calrete,
											'Glosa'=>"",
											'AKeyAs'=>$GKeyAs
									);
									$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
								}

								$NProductos=count($_SESSION['ARRCENTRA']);
								$Lasientos=array(
										'IdDoc'=>$registro['id'],
										'FDocu'=>$registro['fecha'],
										'Peri'=>$PerCen,
										'Cta'=>$HXP,
										'CC'=>"0",
										'MDebe'=>"0",
										'MHaber'=>$calliqui,
										'Glosa'=>"",
										'AKeyAs'=>$GKeyAs
								);
								$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;

								if($calrete3>0){
									$NProductos=count($_SESSION['ARRCENTRA']);
									$Lasientos=array(
											'IdDoc'=>$registro['id'],
											'FDocu'=>$registro['fecha'],
											'Peri'=>$PerCen,
											'Cta'=>$RE3,
											'CC'=>"0",
											'MDebe'=>"0",
											'MHaber'=>$calrete3,
											'Glosa'=>"",
											'AKeyAs'=>$GKeyAs
									);
									$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
								}

								$Glosa="CENTRALIZACIÓN DE HONORARIOS, N:".$registro['numero'];
								$NProductos=count($_SESSION['ARRCENTRA']);
								$Lasientos=array(
										'IdDoc'=>$registro['id'],
										'FDocu'=>$registro['fecha'],
										'Peri'=>$PerCen,
										'Cta'=>"0",
										'CC'=>"0",//$CCosto,
										'MDebe'=>"0",
										'MHaber'=>"0",
										'Glosa'=>$Glosa,
										'AKeyAs'=>$GKeyAs
								);
								$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;

								$GKeyAs=$GKeyAs+1;
							}
						}else{
							$SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND id='".descript($selected)."' AND rutempresa='$RutEmpresa' AND lote=''";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								$operador=1;
								$SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
								$resultados1 = $mysqli->query($SQL1);
								while ($registro1 = $resultados1->fetch_assoc()) {
									$NomDoc=strtoupper($registro1["nombre"]);
									$TipDocSii=$registro1["tiposii"];
									if($registro1["operador"]=="R"){
										$operador=-1;
									}
								}

								if ($operador==1) {

									if ($_POST['frm']=="V") {
										$SQL1="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".$registro["id"]."'";
										$ConResu = $mysqli->query($SQL1);
										$row_cnt = $ConResu->num_rows;
										if ($row_cnt>0) {
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$CCosto=$registro1['CCosto'];
												$NProductos=count($_SESSION['ARRCENTRA']);
												$Lasientos=array(
														'IdDoc'=>$registro['id'],
														'FDocu'=>$registro['fecha'],
														'Peri'=>$PerCen,
														'Cta'=>$registro1['Cuenta'],
														'CC'=>$CCosto,
														'MDebe'=>"0",
														'MHaber'=>$registro1['Monto'],
														'Glosa'=>"",
														'AKeyAs'=>$GKeyAs
												);
												$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
												$CtaResp=$registro1['Cuenta'];
											}
										}else{


											$CCosto=$registro['ccosto'];
											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$registro['cuenta'],
													'CC'=>$CCosto,
													'MDebe'=>"0",
													'MHaber'=>$registro['exento']+$registro['neto'],
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
											$CtaResp=$registro['cuenta'];
										}

										///IVA
										if ($registro['iva']!=0) {

											if ($TipDocSii==46 && $registro['retencion']<0) {
												$MonRete=($registro['retencion']*-1);
											}else{
												$MonRete=0;
											}

											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$IVA,
													'CC'=>"0",//$CCosto,
													'MDebe'=>$MonRete,
													'MHaber'=>$registro['iva'],
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
										}

										////RETE OTROS
										if ($registro['retencion']!=0 && $TipDocSii!=46) {
											$NProductos=count($_SESSION['ARRCENTRA']);

											if ($TipDocSii!=46) {
												if ($registro['retencion']<0) {
													$RetDebe=0;
													$RetHaber=$registro['retencion']*-1;
													$OTR=$CtaResp; ///Impuesto negativo caso de copec con especifico
												}else{
													$RetDebe=$registro['retencion'];
													$RetHaber=0;
												}			
											}

											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$OTR,
													'CC'=>$CCosto,
													'MDebe'=>$RetHaber,
													'MHaber'=>$RetDebe,
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
										}

										/////Total
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>$AUX,
												'CC'=>"0",//$CCosto,
												'MDebe'=>$registro['total'],
												'MHaber'=>"0",
												'Glosa'=>"",
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;

										$Glosa="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDoc." N:".$registro['numero'];
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>"0",
												'CC'=>"0",//$CCosto,
												'MDebe'=>"0",
												'MHaber'=>"0",
												'Glosa'=>$Glosa,
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
									}

									if ($_POST['frm']=="C"){
										$SQL1="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".$registro["id"]."'";
										$ConResu = $mysqli->query($SQL1);
										$row_cnt = $ConResu->num_rows;
										if ($row_cnt>0) {
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$CCosto=$registro1['CCosto'];
												$NProductos=count($_SESSION['ARRCENTRA']);
												$Lasientos=array(
														'IdDoc'=>$registro['id'],
														'FDocu'=>$registro['fecha'],
														'Peri'=>$PerCen,
														'Cta'=>$registro1['Cuenta'],
														'CC'=>$CCosto,
														'MDebe'=>$registro1['Monto'],
														'MHaber'=>"0",
														'Glosa'=>"",
														'AKeyAs'=>$GKeyAs
												);
												$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
												$CtaResp=$registro1['Cuenta'];
											}
										}else{
											$CCosto=$registro['ccosto'];
											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$registro['cuenta'],
													'CC'=>$CCosto,
													'MDebe'=>$registro['exento']+$registro['neto'],
													'MHaber'=>"0",
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
											$CtaResp=$registro['cuenta'];
										}

										///IVA
										if ($registro['iva']!=0) {
											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$IVA,
													'CC'=>"0",//$CCosto,
													'MDebe'=>$registro['iva'],
													'MHaber'=>"0",
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
										}

										////RETE OTROS
										if ($registro['retencion']!=0) {
											// $NProductos=count($_SESSION['ARRCENTRA']);
												if ($registro['retencion']<0) {
													if ($registro['id_tipodocumento']=="26") {
														// FACTURAS TIPO COMPRA RETENCIAÖN IVA
														$RetDebe=0;
														$RetHaber=$registro['retencion']*-1;
														//$OTR=$CtaResp; ///Impuesto negativo caso de copec con especifico													
													}else{
														$RetDebe=0;
														$RetHaber=$registro['retencion']*-1;
														$OTR=$CtaResp; ///Impuesto negativo caso de copec con especifico													
													}

												}else{
													$RetDebe=$registro['retencion'];
													$RetHaber=0;
												}

												
											// $Lasientos=array(
											// 		'IdDoc'=>$registro['id'],
											// 		'FDocu'=>$registro['fecha'],
											// 		'Peri'=>$PerCen,
											// 		'Cta'=>$OTR,
											// 		'CC'=>$CCosto,
											// 		'MDebe'=>$RetDebe,
											// 		'MHaber'=>$RetHaber,
											// 		'Glosa'=>"",
											// 		'AKeyAs'=>$GKeyAs
											// );
											// $_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;


											$SQL1="SELECT * FROM CTRegDocumentosDivRete WHERE Id_Doc='".$registro["id"]."'";
											$ConResu = $mysqli->query($SQL1);
											$row_cnt = $ConResu->num_rows;
											if ($row_cnt>0) {
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$CCosto=$registro1['CCosto'];
													$NProductos=count($_SESSION['ARRCENTRA']);

													if ($registro['retencion']<0) {
														if ($registro['id_tipodocumento']=="26") {
															// FACTURAS TIPO COMPRA RETENCIAÖN IVA
															$RetDebe=0;
															$RetHaber=$registro1['Monto']*-1;
															//$OTR=$CtaResp; ///Impuesto negativo caso de copec con especifico													
														}else{
															$RetDebe=0;
															$RetHaber=$registro1['Monto']*-1;
															// $OTR=$CtaResp; ///Impuesto negativo caso de copec con especifico													
														}
		
													}else{
														$RetDebe=$registro1['Monto'];
														$RetHaber=0;
													}

													$Lasientos=array(
															'IdDoc'=>$registro['id'],
															'FDocu'=>$registro['fecha'],
															'Peri'=>$PerCen,
															'Cta'=>$registro1['Cuenta'],
															'CC'=>$CCosto,
															'MDebe'=>$RetDebe,
															'MHaber'=>$RetHaber,
															'Glosa'=>"",
															'AKeyAs'=>$GKeyAs
													);
													$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
													$CtaResp=$registro1['Cuenta'];
												}
											}else{
												$NProductos=count($_SESSION['ARRCENTRA']);
												$CCosto=$registro['ccosto'];
												$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$OTR,
													'CC'=>$CCosto,
													'MDebe'=>$RetDebe,
													'MHaber'=>$RetHaber,
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
												);
												$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
											}
										}

										/////Total
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>$AUX,
												'CC'=>"0",//$CCosto,
												'MDebe'=>"0",
												'MHaber'=>$registro['total'],
												'Glosa'=>"",
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;


										$Glosa="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDoc." N:".$registro['numero'];
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>"0",
												'CC'=>"0",//$CCosto,
												'MDebe'=>"0",
												'MHaber'=>"0",
												'Glosa'=>$Glosa,
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
									}
								}

								if ($operador<1) {
									//echo "negativo";

									if ($_POST['frm']=="C") {
										$SQL1="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".$registro["id"]."'";
										$ConResu = $mysqli->query($SQL1);
										$row_cnt = $ConResu->num_rows;
										if ($row_cnt>0) {
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$CCosto=$registro1['CCosto'];
												$NProductos=count($_SESSION['ARRCENTRA']);
												$Lasientos=array(
														'IdDoc'=>$registro['id'],
														'FDocu'=>$registro['fecha'],
														'Peri'=>$PerCen,
														'Cta'=>$registro1['Cuenta'],
														'CC'=>$CCosto,
														'MDebe'=>"0",
														'MHaber'=>$registro1['Monto'],
														'Glosa'=>"",
														'AKeyAs'=>$GKeyAs
												);
												$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
												$CtaResp=$registro1['Cuenta'];
											}
										}else{

											$CCosto=$registro['ccosto'];
											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$registro['cuenta'],
													'CC'=>$CCosto,
													'MDebe'=>"0",
													'MHaber'=>$registro['exento']+$registro['neto'],
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
											$CtaResp=$registro['cuenta'];
										}

										///IVA
										if ($registro['iva']!=0) {
											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$IVA,
													'CC'=>"0",//$CCosto,
													'MDebe'=>"0",
													'MHaber'=>$registro['iva'],
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
										}

										////RETE OTROS
										if ($registro['retencion']!=0) {
											$NProductos=count($_SESSION['ARRCENTRA']);
												if ($registro['retencion']<0) {
													$RetDebe=0;
													$RetHaber=$registro['retencion']*-1;
													$OTR=$CtaResp; ///Impuesto negativo caso de copec con especifico
												}else{
													$RetDebe=$registro['retencion'];
													$RetHaber=0;
												}			
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$OTR,
													'CC'=>$CCosto,
													'MDebe'=>$RetHaber,
													'MHaber'=>$RetDebe,
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
										}

										/////Total
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>$AUX,
												'CC'=>"0",//$CCosto,
												'MDebe'=>$registro['total'],
												'MHaber'=>"0",
												'Glosa'=>"",
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;

										$Glosa="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDoc." N:".$registro['numero'];
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>"0",
												'CC'=>"0",//$CCosto,
												'MDebe'=>"0",
												'MHaber'=>"0",
												'Glosa'=>$Glosa,
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
									}

									if ($_POST['frm']=="V"){
										$SQL1="SELECT * FROM CTRegDocumentosDiv WHERE Id_Doc='".$registro["id"]."'";
										$ConResu = $mysqli->query($SQL1);
										$row_cnt = $ConResu->num_rows;
										if ($row_cnt>0) {
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$CCosto=$registro1['CCosto'];
												$NProductos=count($_SESSION['ARRCENTRA']);
												$Lasientos=array(
														'IdDoc'=>$registro['id'],
														'FDocu'=>$registro['fecha'],
														'Peri'=>$PerCen,
														'Cta'=>$registro1['Cuenta'],
														'CC'=>$CCosto,
														'MDebe'=>$registro1['Monto'],
														'MHaber'=>"0",
														'Glosa'=>"",
														'AKeyAs'=>$GKeyAs
												);
												$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
												$CtaResp=$registro1['Cuenta'];
											}
										}else{
											$CCosto=$registro['ccosto'];
											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$registro['cuenta'],
													'CC'=>$CCosto,
													'MDebe'=>$registro['exento']+$registro['neto'],
													'MHaber'=>"0",
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
											$CtaResp=$registro['cuenta'];
										}

										///IVA
										if ($registro['iva']!=0) {
											$NProductos=count($_SESSION['ARRCENTRA']);
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$IVA,
													'CC'=>"0",//$CCosto,
													'MDebe'=>$registro['iva'],
													'MHaber'=>"0",
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
										}

										////RETE OTROS
										if ($registro['retencion']!=0) {
											$NProductos=count($_SESSION['ARRCENTRA']);
												if ($registro['retencion']<0) {
													$RetDebe=0;
													$RetHaber=$registro['retencion']*-1;
													$OTR=$CtaResp; ///Impuesto negativo caso de copec con especifico
												}else{
													$RetDebe=$registro['retencion'];
													$RetHaber=0;
												}			
											$Lasientos=array(
													'IdDoc'=>$registro['id'],
													'FDocu'=>$registro['fecha'],
													'Peri'=>$PerCen,
													'Cta'=>$OTR,
													'CC'=>$CCosto,
													'MDebe'=>$RetDebe,
													'MHaber'=>$RetHaber,
													'Glosa'=>"",
													'AKeyAs'=>$GKeyAs
											);
											$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
										}

										/////Total
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>$AUX,
												'CC'=>"0",//$CCosto,
												'MDebe'=>"0",
												'MHaber'=>$registro['total'],
												'Glosa'=>"",
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;


										$Glosa="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDoc." N:".$registro['numero'];
										$NProductos=count($_SESSION['ARRCENTRA']);
										$Lasientos=array(
												'IdDoc'=>$registro['id'],
												'FDocu'=>$registro['fecha'],
												'Peri'=>$PerCen,
												'Cta'=>"0",
												'CC'=>"0",//$CCosto,
												'MDebe'=>"0",
												'MHaber'=>"0",
												'Glosa'=>$Glosa,
												'AKeyAs'=>$GKeyAs
										);
										$_SESSION['ARRCENTRA'][$NProductos]=$Lasientos;
									}
								}

								$GKeyAs=$GKeyAs+1;
							}
						}
					}

					$mysqli->close();
				?>

				<div class="col-md-1"></div>
				<div class="col-md-10">
					<table width="100%">
						<tr>
							<td align="center" style="font-size: 18px;"><strong>Cantidad de Documentos a Procesar <?php echo isset($_POST['check_list']) && is_array($_POST['check_list']) ? count($_POST['check_list']) : 0; ?></strong></td>
						</tr>
					</table>

					<table class="table table-hover table-condensed" width="100%" style="font-size: 12px;">
						<thead>
							<tr style="background-color: #d9d9d9;">
								<th>Cuenta</th>
								<th>Nombre</th>
								<th width="10%" style="text-align:center;">Documento</th>
								<th style="text-align:right;">Debe</th>
								<th style="text-align:right;">Haber</th>
							</tr>
						</thead>
						<tbody>
							<?php

								unset($_SESSION['WhileCta']);
								$Lasientos=array(
									'numero'=>'xxxx'
								);
								$_SESSION['WhileCta'][0]=$Lasientos;


								foreach($_SESSION['ARRCENTRA'] as $indice=>$LAsiento){
									$ListCta=array_column($_SESSION['WhileCta'],"numero");

									if (in_array($LAsiento['Cta'],$ListCta)) {
										//echo "<script> alert('Produto ya Ingresado'); </script>";
									}else{
										if ($LAsiento['Cta']!="xxxx" && $LAsiento['Cta']!="0") {

											$NProductos=count($_SESSION['WhileCta']);
											$productos=array(
												'numero'=>$LAsiento['Cta']
											);

											$_SESSION['WhileCta'][$NProductos]=$productos;
										}
									}
								}

								if ($_SESSION["PLAN"]=="S"){
									$ConPlaCta="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
								}else{
									$ConPlaCta="SELECT * FROM CTCuentas WHERE 1=1";
								}

								$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
								foreach($_SESSION['WhileCta'] as $indice=>$LAs){

									$SqlCta=$ConPlaCta." AND numero='".$LAs['numero']."'";
									$Resultado = $mysqli->query($SqlCta);
									while ($Registro = $Resultado->fetch_assoc()) {

										$XnumCta=$Registro['numero'];
										$XnomCta=$Registro['detalle'];
									}
									$XcanDoc=0;
									$XtotDeb=0;
									$XtotHab=0;

									foreach($_SESSION['ARRCENTRA'] as $indice=>$LAsiento){
										if ($LAsiento['Cta']==$XnumCta) {
											$XtotDeb=$XtotDeb+$LAsiento['MDebe'];
											$XtotHab=$XtotHab+$LAsiento['MHaber'];
											$XcanDoc++;
										}
									}

									if ($XcanDoc>0) {
										echo '
											<tr>
												<td>'.$XnumCta.'</td>
												<td>'.$XnomCta.'</td>
												<td align="center">'.number_format($XcanDoc, $NDECI, $DDECI, $DMILE).'</td>
												<td align="right">'.number_format($XtotDeb, $NDECI, $DDECI, $DMILE).'</td>
												<td align="right">'.number_format($XtotHab, $NDECI, $DDECI, $DMILE).'</td>
											</tr>
										';
										$Tot1=$Tot1+$XtotDeb;
										$Tot2=$Tot2+$XtotHab;
									}
								}

								$mysqli->close();

								echo '
									<tr style="background-color: #d9d9d9;">
										<th></th>
										<th></th>
										<th width="10%" style="text-align:center;">Totales</th>
										<th style="text-align:right;">'.number_format($Tot1, $NDECI, $DDECI, $DMILE).'</th>
										<th style="text-align:right;">'.number_format($Tot2, $NDECI, $DDECI, $DMILE).'</th>
									</tr>
								';
							?>				
						</tbody>
					</table>
				</div>
				<div class="col-md-1"></div>
				<div class="clearfix"></div>
				<br>

			</div>
		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


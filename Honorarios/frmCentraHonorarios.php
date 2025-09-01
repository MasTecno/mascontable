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

	$dmes = substr($Periodo,0,2);
	$dano = substr($Periodo,3,4);

	function UltimoDiaMesD($periodo) { 
		$month = substr($periodo,0,2);
		$year = substr($periodo,3,4);
		$day = date("d", mktime(0,0,0, $month+1, 0, $year));

		return date('d', mktime(0,0,0, $month, $day, $year));
	};

	$textfecha=UltimoDiaMesD($Periodo)."-".$dmes."-".$dano;

	if ($dmes=="01") {
		$xMes="Enero";
	}
	if ($dmes=="02") {
		$xMes="Febrero";
	}
	if ($dmes=="03") {
		$xMes="Marzo";
	}
	if ($dmes=="04") {
		$xMes="Abril";
	}
	if ($dmes=="05") {
		$xMes="Mayo";
	}
	if ($dmes=="06") {
		$xMes="Junio";
	}
	if ($dmes=="07") {
		$xMes="Julio";
	}
	if ($dmes=="08") {
		$xMes="Agosto";
	}
	if ($dmes=="09") {
		$xMes="Septiembre";
	}
	if ($dmes=="10") {
		$xMes="Octubre";
	}
	if ($dmes=="11") {
		$xMes="Noviembre";
	}
	if ($dmes=="12") {
		$xMes="Diciembre";
	}
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
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

	$mysqli->close();

	$GlosaAsi="CENTRALIZACI&Oacute;N HONORARIOS ".strtoupper($xMes)." ".$dano;

	$GlosaPag="EGRESO DE HONORARIOS ".strtoupper($xMes)." ".$dano;
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
			function Porce(){
				sw = document.getElementById("ace").checked;
				sw1 = document.getElementById("PAuto").checked;

				if (form1.tglosa.value=="") {
					alert("Ingrese Glosa");
				}else{
					if (sw1== true && (form1.Comp4.value=="" || form1.tglosap.value=="") ) {
						alert("Debe asignar cuanta para el Pago y/o no cuenta con una Glosa");
					}else{
						if (sw==true) {				
							form1.submit();
						}
					}
				}
			}

			function acept(){
				sw = document.getElementById("ace").checked;

				if (sw==false) {
					document.getElementById("bt").classList.remove("active");
					document.getElementById("bt").classList.add("disabled");
				}else{
					document.getElementById("bt").classList.remove("disabled");
					document.getElementById("bt").classList.add("active");
				}
			}

			function Volver(){
				form1.action="../frmMain.php";
				form1.submit();
			}

			jQuery(document).ready(function(e) {
				$('#myModal').on('shown.bs.modal', function() {
					$('input[name="BCodigo"]').focus();
				});
			});
		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<form action="xfrmRegHonorariosMasivo.php" method="POST" name="form1" id="form1">
				<br>
				<div class="col-sm-2"></div>
				<div class="col-sm-8 text-left">
					<table class="table table-condensed">
						<thead style="background-color: #d9d9d9;">
							<tr>
								<th style="text-align: center;">N&deg; Cuenta</th>
								<th>Nombre Cuenta</th>
								<th style="text-align: center;">Cantidad</th>
								<th style="text-align: right;">Bruto</th>
								<th style="text-align: right;">Retenci&oacute;n</th>
								<th style="text-align: right;">3% Prestamo</th>
								<th style="text-align: right;">Liquido</th>
							</tr>
						</thead>
						<?php 

							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

							// $SQL="SELECT periodo, cuenta, COUNT(cuenta) as ccuentas, SUM(bruto) as sbruto, SUM(retencion) as sretencion, SUM(liquido) as sliquido FROM CTHonorarios WHERE estado='A' and origen<>'Z' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND movimiento='' GROUP BY cuenta";
							// $resultados = $mysqli->query($SQL);

							// while ($registro = $resultados->fetch_assoc()) {

							// 	$nomcuenta="";
							// 	if ($_SESSION["PLAN"]=="S"){
							// 		$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
							// 	}else{
							// 		$SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
							// 	}

							// 	$resultados1 = $mysqli->query($SQL1);
							// 	while ($registro1 = $resultados1->fetch_assoc()) {
							// 		$nomcuenta=$registro1["detalle"];
							// 	}

							// 	$calreteCal=0;
							// 	$calrete3=0;
								
							// 	$calbruto=$registro["sbruto"];
							// 	$calrete=$registro["sretencion"];
							// 	$calliqui=$registro["sliquido"];
							// 	$calreteCal=round(($registro["sbruto"]*$Val_Ret)/100);
							// 	$calreteCal3=round(($registro["sbruto"]*3)/100);
						  
							// 	$calrete3=$calrete-$calreteCal;
							// 	$calrete=$calreteCal;
						  
							// 	$color='';
							// 	if($calrete3<0){
							// 	  $calrete3=0;
							// 	}
						  
							// 	if($registro["sretencion"]==0){
							// 	  $calrete=0;
							// 	}



								
							// 	echo '
							// 		<tr>
							// 			<td align="center">'.$registro["cuenta"].'</td>
							// 			<td>'.strtoupper($nomcuenta).'</td>
							// 			<td align="center">'.$registro["ccuentas"].'</td>
							// 			<td align="right">$'.number_format(($registro["sbruto"]), $NDECI, $DDECI, $DMILE).'</td>
							// 			<td align="right">$'.number_format(($calreteCal), $NDECI, $DDECI, $DMILE).'</td>
							// 			<td align="right">$'.number_format(($calrete3), $NDECI, $DDECI, $DMILE).'</td>
							// 			<td align="right">$'.number_format(($registro["sliquido"]), $NDECI, $DDECI, $DMILE).'</td>
							// 		</tr>
							// 	';


							// 	$tbruto=$tbruto+($registro["sbruto"]);
							// 	$tretencion=$tretencion+($calreteCal);
							// 	$tretencion3=$tretencion3+($calrete3);
							// 	$tliquido=$tliquido+($registro["sliquido"]);
							// }


							$SQL="SELECT * FROM CTHonorarios WHERE estado='A' and origen<>'Z' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND movimiento=''";
							$resultados = $mysqli->query($SQL);
						
							while ($registro = $resultados->fetch_assoc()) {
						
							  $calbruto=$registro["bruto"];
							  $calrete=$registro["retencion"];
							  $calliqui=$registro["liquido"];
							  $calreteCal=round(($registro["bruto"]*$Val_Ret)/100);
							  $calreteCal3=round(($registro["bruto"]*3)/100);
						
							  $calrete3=$calrete-$calreteCal;
							  $calrete=$calreteCal;
						
							  $color='';
							  if($calrete3<0){
								$calrete3=0;
							  }
						
							  if($registro["retencion"]==0){
								$calrete=0;
							  }

						
						
							// echo '
							// 			<tr '.$color.'>
							// 				<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
							// 				<td>'.$registro["rut"].'</td>
							// 				<td>'.$rsocial.'</td>
							// 				<td>'.$registro["cuenta"]." - ".strtoupper($nomcuenta).'</td>
							// 				<td align="center">'.$registro["numero"].'</td>
							// 				<td align="right">$'.number_format(($calbruto), $NDECI, $DDECI, $DMILE).'</td>
							// 				<td align="right">$'.number_format(($calrete), $NDECI, $DDECI, $DMILE).'</td>
							// 				<td align="right">$'.number_format(($calrete3), $NDECI, $DDECI, $DMILE).'</td>
							// 				<td align="right">$'.number_format(($calliqui), $NDECI, $DDECI, $DMILE).'</td>
							// ';
						

						
							//   $tbruto=$tbruto+($calbruto);
							//   $tretencion=$tretencion+($calrete);
							//   $tretencion3=$tretencion3+($calrete3);
							//   $tliquido=$tliquido+($calliqui);


							  $tbruto=$tbruto+($calbruto);
							  $tretencion=$tretencion+($calrete);
							  $tretencion3=$tretencion3+($calrete3);
							  $tliquido=$tliquido+($calliqui);
							  $CTA=$registro["cuenta"];
							  $NCTA=strtoupper($nomcuenta);
							  $CONTA++;
							}






								echo '
									<tr>
										<td align="center">'.$CTA.'</td>
										<td>'.$NCTA.'</td>
										<td align="center">'.$CONTA.'</td>
										<td align="right"><strong>$'.number_format($tbruto, $NDECI, $DDECI, $DMILE).'</strong></td>
										<td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
										<td align="right"><strong>$'.number_format($tretencion3, $NDECI, $DDECI, $DMILE).'</strong></td>
										<td align="right"><strong>$'.number_format($tliquido, $NDECI, $DDECI, $DMILE).'</strong></td>
									</tr>
								';


								// $tbruto=$tbruto+($registro["sbruto"]);
								// $tretencion=$tretencion+($calreteCal);
								// $tretencion3=$tretencion3+($calrete3);
								// $tliquido=$tliquido+($registro["sliquido"]);






							$mysqli->close();

							echo'
								<tr style="background-color: #d9d9d9;">
									<td></td>
									<td></td>
									<td align="right"><strong>Totales</strong></td>
									<td align="right"><strong>$'.number_format($tbruto, $NDECI, $DDECI, $DMILE).'</strong></td>
									<td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
									<td align="right"><strong>$'.number_format($tretencion3, $NDECI, $DDECI, $DMILE).'</strong></td>
									<td align="right"><strong>$'.number_format($tliquido, $NDECI, $DDECI, $DMILE).'</strong></td>
								</tr>
							';

						?>
					</table>
					<br>

					<div class="clearfix"></div>

					<div class="col-md-2">
						<label>Fecha Centralizaci&oacute;n</label>
						<input id="d1" name="d1" type="text" class="form-control text-right" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
					</div> 
					<div class="clearfix"></div>

					<?php
						echo "<br>";
						$contlin=1;
						$totalizador=0;

						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

						$SQL="SELECT periodo, cuenta, COUNT(cuenta) as ccuentas, SUM(bruto) as sbruto, SUM(retencion) as sretencion, SUM(liquido) as sliquido FROM CTHonorarios WHERE estado='A' and origen<>'Z' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND movimiento='' GROUP BY cuenta";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {

							$nomcuenta="";
							if ($_SESSION["PLAN"]=="S"){
								$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
							}else{
								$SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
							}

							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$nomcuenta=$registro1["detalle"];
							}

							echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$registro["cuenta"].'"></div>';
							echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$nomcuenta.'"></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="'.$registro["sbruto"].'" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="clearfix"></div>';    	

							$contlin++;
						}

						$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' AND tipo='R'";
						$resultados = $mysqli->query($SQL);
						$row_cnt = $resultados->num_rows;
						if ($row_cnt>0) {
							$resultados1 = $mysqli->query($SQL);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$xL2=$registro1["L2"];
								$xL3=$registro1["L3"];
								$xL4=$registro1["L4"];
							}
						}else{
							$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='' AND tipo='R'";
							$resultados1 = $mysqli->query($SQL);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$xL2=$registro1["L2"];
								$xL3=$registro1["L3"];
								$xL4=$registro1["L4"];
							}
						}


						$NomL2="";
						$NomL3="";
						if ($_SESSION["PLAN"]=="S"){
							$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$xL2."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$NomL2=$registro1["detalle"];
							}

							$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$xL3."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$NomL3=$registro1["detalle"];
							}

							$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$xL4."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$NomL4=$registro1["detalle"];
							}
						}else{
							$SQL1="SELECT * FROM CTCuentas WHERE numero='".$xL2."'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$NomL2=$registro1["detalle"];
							}

							$SQL1="SELECT * FROM CTCuentas WHERE numero='".$xL3."'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$NomL3=$registro1["detalle"];
							}

							$SQL1="SELECT * FROM CTCuentas WHERE numero='".$xL4."'";
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) {
								$NomL4=$registro1["detalle"];
							}
						}

						$mysqli->close();

						echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$xL2.'"></div>';
						echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$NomL2.'"></div>';
						echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
						echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="'.$tretencion.'" onKeyPress="return soloNumeros(event)" ></div>';
						echo '<div class="clearfix"></div>';    	

						$contlin++;
						echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$xL3.'"></div>';
						echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$NomL3.'"></div>';
						echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
						echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="'.$tliquido.'" onKeyPress="return soloNumeros(event)" ></div>';
						echo '<div class="clearfix"></div>';    	
						if($tretencion3>0){
							$contlin++;
							echo '<div class="col-md-3"><input type="text" class="form-control" readonly id="mcuenta'.$contlin.'" name="mcuenta'.$contlin.'" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$xL4.'"></div>';
							echo '<div class="col-md-5"><input type="text" class="form-control" readonly id="" name="" onChange="javascript:this.value=this.value.toUpperCase();" value="'.$NomL4.'"></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mdebe'.$contlin.'" name="mdebe'.$contlin.'" maxlength="50" value="" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="col-md-2"><input type="text" class="form-control text-right" readonly id="mhaber'.$contlin.'" name="mhaber'.$contlin.'" maxlength="50" value="'.$tretencion3.'" onKeyPress="return soloNumeros(event)" ></div>';
							echo '<div class="clearfix"></div>';    	
						}
					?>

					<br>

					<div class="col-sm-12 text-center">
						<div class="input-group">
							<span class="input-group-addon">Glosa</span>
							<input type="text" class="form-control" id="tglosa" name="tglosa" value="<?php echo $GlosaAsi; ?>" onChange="javascript:this.value=this.value.toUpperCase();" style="z-index: 1;" required>
							<input type="hidden" name="nlineas" id="nlineas" value="<?php echo $contlin; ?>">
						</div>			      	
					</div>

				<div class="col-sm-12">
					<!-- Modal  buscar codigo-->


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

						function data(valor){
							var cas=form1.casilla.value;
							document.getElementById(cas).value=valor;

							//$('#'+cas).val()=valor;
							BuscaCuenta(form1.casilla.value);
							document.getElementById("cmodel").click();
						}

					</script>

					<input type="hidden" name="casilla" id="casilla">
					<div class="clearfix"></div>
					<br>

					<div class="clearfix"></div>
					<br>
					<h4>Generar Pago Inmediato</h4>
					<div class="col-md-3">
						<label>Cuenta</label>
						<div class="input-group"> 
							<input type="text" class="form-control text-right" id="Comp4" name="Comp4" required maxlength="50" value="<?php echo $XPago; ?>">
							<div class="input-group-btn"> 
								<a href="#" class="btn btn-default" role="button" data-toggle="modal" data-target="#myModal" onclick="form1.casilla.value='Comp4'">
									<span class="glyphicon glyphicon-search"></span>
								</a>
							</div> 
						</div> 
					</div>

					<div class="col-md-9">
						<label>Detalle</label>  
						<input type="text" class="form-control" id="DComp4" name="DComp4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnPago); ?>"  readonly="false" >
					</div>
					<div class="clearfix"></div>
					<br>

					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon">Glosa</span>
							<input type="text" class="form-control" id="tglosap" name="tglosap" value="<?php echo $GlosaPag; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
						</div>			      	
					</div>
				</div>

				<div class="col-sm-12 text-center">
					<label class="checkbox-inline">
						<input type="checkbox" id="PAuto" name="PAuto">Generar Pago Inmediato</label>

					<br><br>				
					<label class="checkbox-inline"><input type="checkbox" onclick="acept()" id="ace" name="ace">Aceptar</label> 
					<p>* Este proceso Genera La centralizaci&oacute;n masiva de todas los honorarios pendientes.</p>         	
					<p>** La Centralizaci&oacute;n dependera de las cuentas asigandas anteriormente.</p>         	
				</div>

				<div class="col-sm-12 text-center">
					<button type="button" class="btn btn-grabar btn-md disabled" onclick="Porce()" id="bt" name="bt">Centralizar todas los Honorarios Pendientes</button>   
				</div>
				<div class="clearfix"></div>
				<br>
				</div>
			</form>
		</div>
		</div>
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
			}
		});  
	</script>

	<?php include '../footer.php'; ?>

	</body>
</html>
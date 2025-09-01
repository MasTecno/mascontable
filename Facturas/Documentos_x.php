<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	// include '../conexion/secciones.php';
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$PeriodoX=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if (isset($_POST["IdFactura"]) && isset($_POST["IdTrans"]) && $_POST["IdFactura"]!="" && $_POST["IdTrans"]!="") {
		$mysqli=ConCobranza();

		$MonFac=0;
		$SQL="SELECT * FROM Facturas WHERE Id='".$_POST["IdFactura"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$MonFac=$registro["Total"];
			$NFactura=$registro["Folio"];
		}

		$MonTrans=0;
		$SQL="SELECT * FROM Transferencias WHERE Id='".$_POST["IdTrans"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$MonTrans=$registro["Monto"];
			$NOperacion=$registro["NOperacion"];
		}

		$SumTrans=0;
		$SQL="SELECT sum(MontoTrans) as SumTrans FROM FactTrans WHERE IdTrans='".$_POST["IdTrans"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$SumTrans=$registro["SumTrans"];
		}

		$swAbono=0;
		$SQL="SELECT sum(MontoTrans) as SumTrans FROM FactTrans WHERE IdFactura='".$_POST["IdFactura"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$SumTrans=$SumTrans+$registro["SumTrans"];
			if ($registro["SumTrans"]>0) {
				$swAbono=1;
			}
		}

		if ($swAbono==1) {
			$MonTrans=($MonFac-$SumTrans);
		}else{
			if (($MonTrans-$SumTrans)>0) {
				if ($MonFac<=($MonTrans-$SumTrans)) {
					$MonTrans=$MonFac;
				}else{
					$MonTrans=($MonTrans-$SumTrans);
				}
			}		
		}

		$mysqli->query("INSERT INTO FactTrans VALUES('','".$_POST['IdFactura']."','$NFactura','$MonFac','".$_POST['IdTrans']."','$NOperacion','$MonTrans','".date('Y-m-d H:i:s')."')");
		$mysqli->close();
	}

	if (isset($_POST['IdFactran']) && $_POST['IdFactran']!="" ) { /////elimina
		$mysqli=ConCobranza();
		$SQL="SELECT * FROM FactTrans WHERE Id='".$_POST['IdFactran']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$LIdTYran=$registro['IdTrans'];
		}

		$SQL="SELECT * FROM Transferencias WHERE Id='$LIdTYran' AND Banco='BANCO MASTECNO'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$mysqli->query("DELETE FROM Transferencias WHERE Id='$LIdTYran' AND Banco='BANCO MASTECNO'");
		}

		$mysqli->query("DELETE FROM FactTrans WHERE Id='".$_POST['IdFactran']."'");
		$mysqli->close();
	}

	if ($_POST["l1"]!="" && $_POST["l2"]!="" && $_POST["l3"]!="" && $_POST['pwd']=="@Adminssv") {   //// insert excepciones

		$mysqli=ConCobranza();
		$SQL="SELECT * FROM Facturas WHERE Id='".$_POST["l3"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$MonFac=$registro["Total"];
			$NFactura=$registro["Folio"];
			$RFactura=$registro["Rut"];
		}
		$nOpera="D-".date('YmdHis');

		$mysqli->query("INSERT INTO Transferencias VALUES('','".date('Y-m-d')."','$nOpera','$MonFac','BANCO MASTECNO','$RFactura','$RFactura','A','".date('Y-m-d')."','".date("H:i:s")."');");

		$SQL="SELECT max(Id) as FId FROM Transferencias WHERE Id>0";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$IdTrans=$registro["FId"];
		}

		$mysqli->query("INSERT INTO FactTrans VALUES('','".$_POST["l3"]."','$NFactura','$MonFac','$IdTrans','$nOpera','$MonFac','".date('Y-m-d')."');");
		$mysqli->close();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; ">
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

		<script type="text/javascript">
			function Asociar(v1,v2,v3){
				form1.FecAsociado.value=v1;
				form1.DocAsociado.value=v2;
				form1.IdFactura.value=v3;
			}
			function Confirmar(x1,x2){

				form1.IdTrans.value=x1;
				form1.NOperacion.value=x2;
				var r = confirm("Asociar la Factura N: "+form1.DocAsociado.value+", con las transferencia N: "+form1.NOperacion.value);
				if (r == true) {
					//alert("You pressed OK!");
					form1.submit();
				} else {
					alert("Operacion Cancelada");
				}
			}

			function ConfirmarX(l1,l2, l3){
				form1.l1.value=l1;
				form1.l2.value=l2;
				form1.l3.value=l3;
			}
			function Autor(){
				form1.FecAsociado.value="";
				form1.DocAsociado.value="";
				form1.IdFactura.value="";
				form1.submit();		
			}
			function EliReg(r1) {
				form1.IdFactran.value=r1;
				form1.submit();
			}

		</script>

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<!-- <div class="col-md-2"></div> -->
			<div class="col-md-12 text-left">
				<form action="#" name="form1" id="form1" method="POST">
				<input type="hidden" name="IdTrans" id="IdTrans">
				<input type="hidden" name="NOperacion" id="NOperacion">
				<input type="hidden" name="IdFactura" id="IdFactura">
				<input type="hidden" name="IdFactran" id="IdFactran">

				<a href="../Facturas" class="btn btn-warning">
					<span class="glyphicon glyphicon-new-window"></span> Volver
				</a>
				<br>

				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr style="background-color: #e51c20; color: #FFF;">
							<th style="text-align: center;" width="1%"></th>
							<th style="text-align: center;" width="10%">Rut</th>
							<th style="text-align: center;">Raz&oacute;n Social</th>
							<th style="text-align: center;" width="10%">Tipo</th>
							<th style="text-align: center;" width="10%">Folio</th>
							<th style="text-align: center;" width="10%">Fecha</th>
							<th style="text-align: center;" width="10%">Monto</th>
							<th style="text-align: center;" width="10%">Adeudado</th>
						</tr>
					</thead>
					<tbody id="Empresas">
						<?php
							$mysqli=ConCobranza();

							$SQL="SELECT * FROM Maestra WHERE IdServer='".$_SESSION['xIdServer']."'";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$RutFactura=$registro['RutFactura'];  
							}

							$Cadera="";
							$SQL="SELECT * FROM FacturasRut WHERE IdServer='".$_SESSION['xIdServer']."'";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$Cadera=$Cadera."OR Rut='".$registro['RutFactura']."'";  
							}

							$SQL="SELECT * FROM Facturas WHERE Rut='$RutFactura'"; 
							$SQL=$SQL.$Cadera;
							$SQL=$SQL." ORDER BY Fecha DESC";

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								$SumTrans=0;

								$SQL1="SELECT sum(MontoTrans) AS SumTrans FROM FactTrans WHERE IdFactura='".$registro["Id"]."'";
								$resultados1 = $mysqli->query($SQL1);
								while ($registro1 = $resultados1->fetch_assoc()) {
									$SumTrans=$registro1["SumTrans"];
								}

								$Info="";
								$SQL1="SELECT * FROM FactTrans WHERE IdFactura='".$registro["Id"]."'";
								$resultados1 = $mysqli->query($SQL1);
								while ($registro1 = $resultados1->fetch_assoc()) {
									$SQL2="SELECT * FROM Transferencias WHERE Id='".$registro1["IdTrans"]."'";
									$resultados2 = $mysqli->query($SQL2);
									while ($registro2 = $resultados2->fetch_assoc()) {
										$Info=$Info."Operaci&oacute;n: ".$registro2["NOperacion"]."<br>Fecha: ".date('d-m-Y',strtotime($registro2["Fecha"]))."<br>Monto: ".$registro2["Monto"]."<br>";

										if ($_SESSION['NOMBRE']=="Admini") {
											$Info=$Info.'
												<button type="button" class="btn btn-xs btn-danger" onclick="EliReg('.$registro1["Id"].')">
													<span class="glyphicon glyphicon-trash"></span>
												</button>
											';
										}
									}
								}

								$XTipo="";
								if ($registro["IdDocumento"]=="34") {
									$XTipo="FacExe";
								}
								if ($registro["IdDocumento"]=="33") {
									$XTipo="FacAfe";
								}
								if ($registro["IdDocumento"]=="61") {
									$XTipo="NotCre";
								}

								$NC = 0;

								if ($registro["CnNuRefe"]>0 && ($registro["CnRefe"]=="34" || $registro["CnRefe"]=="33") ) {
									$NC = 1;
								}else{
									$SQL1="SELECT * FROM Facturas WHERE CnNuRefe='".$registro["Folio"]."' AND Rut='".$registro["Rut"]."' AND (CnRefe='34' || CnRefe='33')";
									$Res = $mysqli->query($SQL1);
									$NC = $Res->num_rows;
								}

								if ($NC>0) {
									$ColorX="#caf3ff";
								}else{
									$ColorX="#ffcaca";
								}

								if ($SumTrans>=$registro["Total"]) {
									echo '
									<tr style="background-color:#dfffca;">
										<td>

										</td>
										<td style="text-align: center;">'.$registro["Rut"].'</td>
										<td>'.$registro["RSocial"].'</td>
										<td style="text-align: center;">'.$XTipo.'</td>
										<td style="text-align: center;">'.$registro["Folio"].'</td>
										<td style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
										<td style="text-align: center;">'.number_format($registro["Total"],0,",",".").'</td>
										<!--<td style="text-align: center;">'.number_format($registro["Total"]-$SumTrans,0,",",".").'</td>-->
										<td>'.$Info.'</td>
									</tr>
									';
								}else{
									echo '
									<tr style="background-color:'.$ColorX.';" >
										<td>';

										if ($NC==0) {
											echo '
											<button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#myModal" onclick="Asociar(\''.date('d-m-Y',strtotime($registro["Fecha"])).'\',\''.$registro["Folio"].'\',\''.$registro["Id"].'\')">
												<span class="glyphicon glyphicon-pushpin"></span>
											</button>
											';
										}
									echo '
										</td>
										<td style="text-align: center;">'.$registro["Rut"].'</td>
										<td data-toggle="modal" data-target="#Autoriza" onclick="ConfirmarX(\''.date('d-m-Y',strtotime($registro["Fecha"])).'\',\''.$registro["Folio"].'\',\''.$registro["Id"].'\')">'.$registro["RSocial"].'</td>
										<td style="text-align: center;">'.$XTipo.'</td>
										<td style="text-align: center;">'.$registro["Folio"].'</td>
										<td style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
										<td style="text-align: center;">'.number_format($registro["Total"],0,",",".").'</td>
										<td><strong>'.number_format($registro["Total"]-$SumTrans,0,",",".").'</strong><br>'.$Info.'</td>
									</tr>
									';									
								}
							}
							$mysqli->close();
						?>
					</tbody>
				</table>
 <!-- ondblclick="ConfirmarX(\''.date('d-m-Y',strtotime($registro["Fecha"])).'\',\''.$registro["Folio"].'\',\''.$registro["Id"].'\')" -->


					<div class="modal fade" id="Autoriza" role="dialog">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Autorizaci&oacute;n Manual</h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="pwd">Password:</label>
									<input type="password" class="form-control" id="pwd" name="pwd">
								</div>

								<input type="hidden" name="l1" id="l1">
								<input type="hidden" name="l2" id="l2">
								<input type="hidden" name="l3" id="l3">
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" onclick="Autor()">Confirmar</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
					</div>


					<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
								<?php
									$mysqli=ConCobranza();
									$SQL="SELECT max(Fecha) As UFecha FROM Transferencias";
									$resultados = $mysqli->query($SQL);
									while ($registro = $resultados->fetch_assoc()) {
										$UFecha=date('d-m-Y',strtotime($registro["UFecha"]));
									}
									$mysqli->close();
								?>							
							<h4 class="modal-title">Transferencias Recibidas (Cartola <?php echo $UFecha; ?>)</h4>
						</div>
						<div class="modal-body">

							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Fecha</span>
								<input type="text" class="form-control" id="FecAsociado" name="FecAsociado" value="" disabled="true">
							</div>
							</div> 
							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Documentos</span>
								<input type="text" class="form-control" id="DocAsociado" name="DocAsociado" value="" disabled="true">
							</div>
							</div> 

							<div class="clearfix"> </div>
							<br>							

							<table class="table table-bordered table-striped table-hover">
								<thead>
									<tr style="background-color: #e51c20; color: #FFF;">
										<th style="text-align: center;" width="10%">Fecha</th>
										<th style="text-align: center;">Banco</th>
										<th style="text-align: center;" width="">N. Operaci&oacute;n</th>
										<th style="text-align: center;" width="">N. Cuenta</th>
										<th style="text-align: center;" width="10%">Monto</th>
										<th style="text-align: center;" width="10%">Saldo</th>
									</tr>
								</thead>
								<tbody id="Empresas">
									<?php
										$mysqli=ConCobranza();

										$SQL='SELECT Transferencias.Id, TransferenciasRut.IdServer, TransferenciasRut.Rut, Transferencias.Fecha, Transferencias.Banco, Transferencias.NOperacion, Transferencias.Cta, Transferencias.Monto, Transferencias.Estado
										FROM TransferenciasRut LEFT JOIN Transferencias ON TransferenciasRut.Rut = Transferencias.Rut
										WHERE (((TransferenciasRut.IdServer)="'.$_SESSION['xIdServer'].'")
										AND ((Transferencias.Estado)="A") AND (Transferencias.Fecha >= "2022-08-01 00:00:00"))
										ORDER BY Transferencias.Fecha DESC;';
										
										$resultados = $mysqli->query($SQL);
										while ($registro = $resultados->fetch_assoc()) {
											$SumTrans=0;
											$SQL1="SELECT sum(MontoTrans) AS SumTrans FROM FactTrans WHERE idTrans='".$registro["Id"]."'";
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$SumTrans=$registro1["SumTrans"];
											}

											if ($SumTrans<$registro["Monto"]) {
												echo '
												<tr onclick="Confirmar(\''.$registro["Id"].'\',\''.$registro["NOperacion"].'\')">
													<td style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
													<td>'.$registro["Banco"].'</td>
													<td style="text-align: center;">'.$registro["NOperacion"].'</td>
													<td style="text-align: center;">'.$registro["Cta"].'</td>
													<td style="text-align: center;">'.number_format($registro["Monto"],0,",",".").'</td>
													<td style="text-align: center;">'.number_format(($registro["Monto"]-$SumTrans),0,",",".").'</td>
												</tr>
												';
											}

										}
										$mysqli->close();
									?>
								</tbody>
							</table>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						</div>
					</div>
					</div>
					</div>




				</form>
			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


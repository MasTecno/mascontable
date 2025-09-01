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

	if (isset($_POST['messelect'])) {
		if ($_POST['messelect']<=9) {
			$_SESSION['PERIODOPC']="0".$_POST['messelect']."-".$_POST['anoselect'];
		}else{
			$_SESSION['PERIODOPC']=$_POST['messelect']."-".$_POST['anoselect'];     
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
		<script src="../js/jquery.dataTables.min.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="../css/StConta.css">

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
			.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    			padding: 1px;
    		}
		</style>
		<script type="text/javascript">
			function EliRegA(valor,Tstring){
				var r = confirm("Esta Seguro de eliminar el Voucher\r\n"+Tstring+"\r\n");
				if (r == true) {
					form1.dat2.value=valor;
					form1.action="Procesar.php";
					form1.submit();
				}
			}

			function ModAsiento(valor){
				form1.KeyMod.value=valor;
				form1.action="frmModAsiento.php";
				form1.submit();
			}

		</script>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" method="POST" id="form1" name="form1">
			<input type="hidden" name="KeyMod" id="KeyMod">
			<input type="hidden" name="Origen" id="Origen" value="Visualizar">
			<input type="hidden" name="dat2" id="dat2">
			<br>
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="col-sm-12 text-center">
					<h2>Libro Diario</h2>
					<h3>Periodo <?php echo $_SESSION['PERIODOPC']; ?></h3>
				</div>
				<div class="clearfix"></div>
				<br>

				<table class="table table-hover" id="grilla">
					<thead>
						<tr>
							<th width="10%">Fecha</th>

							<?php if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"): ?>
								<th width="5%" style="text-align: center;">Comprobante</th>
								<th width="5%" style="text-align: center;">Tipo</th>
							<?php endif ?>

							<th width="10%">Codigo</th>
							<th>Cuenta</th>
							<th width="10%" style="text-align: right;">Debe</th>
							<th width="10%" style="text-align: right;">Haber</th>
							<th width="1%"> </th>
						</tr>
					</thead> 
					<tbody>
					<?php

						$NomCont=$_SESSION['NOMBRE'];
						$Periodo=$_SESSION['PERIODOPC'];
						$RazonSocial=$_SESSION['RAZONSOCIAL'];
						$RutEmpresa=$_SESSION['RUTEMPRESA'];

						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']); 

						$SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' GROUP BY id,keyas ORDER BY fecha, id, debe ASC";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) { 

							if ($_SESSION["PLAN"]=="S"){
								$SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
							}else{
								$SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
							}
							$resultados1 = $mysqli->query($SQL1);
							while ($registro1 = $resultados1->fetch_assoc()) { 
								$ncuenta=strtoupper($registro1["detalle"]);
							}

							$ndoc="";
							if($registro["iddocref"]!="0"){

								$SQL1="SELECT * FROM CTRegDocumentos WHERE id='".$registro["iddocref"]."'";
								$resultados1 = $mysqli->query($SQL1);
								while ($registro1 = $resultados1->fetch_assoc()) { 
									$ndoc=", N&deg; DOCUMENTO ASOCIADO: ".strtoupper($registro1["numero"]);
								}

							}

							$SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$registro["keyas"]."' AND glosa<>''";
							$resultado1 = $mysqli->query($SQL1);
							$row_cnt = $resultado1->num_rows;
							if ($row_cnt>0) {
								$BtsEli=1;
							}

							if($registro["glosa"]==""){
								echo '
								<tr>
									<td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>';

								if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"){
									echo '
										<td></td>
										<td></td>';
								}

								echo '
									<td>'.$registro["cuenta"].'</td>
									<td>'.$ncuenta.$ndoc.'</td>
									<td align="right">'.number_format($registro["debe"], $NDECI, $DDECI, $DMILE).'</td>
									<td align="right">'.number_format($registro["haber"], $NDECI, $DDECI, $DMILE).'</td>
									<td align="center"></td>
								</tr>
								';
								$tgdebe=$tgdebe+$registro["debe"];
								$tghaber=$tghaber+$registro["haber"];
							}

							if($registro["glosa"]!=""){

								if ($registro["tipo"]=="E") {
									$xMen="Egreso";
								}
								if ($registro["tipo"]=="I") {
									$xMen="Ingreso";	
								}
								if ($registro["tipo"]=="T") {
									$xMen="Traspaso";
								}

								echo '
								<tr class="info"> 
									<td>

										<div class="btn-group">
											<button type="button" class="btn btn-Verde btn-xs">Opciones</button>
											<button type="button" class="btn btn-Verde btn-xs dropdown-toggle" data-toggle="dropdown">
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu" role="menu">
												<li><a href="#" style="font-size: 12px;" data-toggle="modal" data-target="#ModalComent" onclick="ImpCom(\''.$registro["keyas"].'\')">Anotaci&oacute;n y/o Imprimir</a></li>
												<li><a href="javascript:ModAsiento('.$registro["keyas"].');" style="font-size: 12px;" onclick="">Modificar</a></li>
												<li><a href="#" style="font-size: 12px;" data-toggle="modal" data-target="#ModalPlantilla" onclick="GuaPlan('.$registro["keyas"].')">Guardar como Plantilla</a></li>
											</ul>
										</div>

									</td>';

								if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"){
									echo'
									<td align="center">'.number_format($registro["ncomprobante"], $NDECI, $DDECI, $DMILE).'</td>
									<td align="center">'.$xMen.'</td>';
								}

								echo '	
									<td></td>
									<td><strong>'.strtoupper($registro["glosa"]).'</strong></td>
									<td align="right">'.number_format($tgdebe, $NDECI, $DDECI, $DMILE).'</td>
									<td align="right">'.number_format($tghaber, $NDECI, $DDECI, $DMILE).'</td>
									<td align="center" ><button type="button" class="btn btn-Rojo btn-xs" onclick="EliRegA('.$registro["keyas"].',\''.strtoupper($registro["glosa"]).'\')">X</button></td>
								</tr>
								';
								
								$tgdebe=0;
								$tghaber=0;
								$BtsEli=0;
							}
						}

						$mysqli->close();
					?>
					</tbody>
				</table>

			</div>

		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


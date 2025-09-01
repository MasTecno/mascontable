<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

    $Ano=substr($_SESSION['PERIODO'],3);

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if(isset($_POST['EliCon']) && $_POST['EliCon']!=""  && $_POST['EliCon']>0){
		$mysqli->query("DELETE FROM CTConciliacionCab WHERE Id='".$_POST['EliCon']."'");
		$mysqli->query("DELETE FROM CTConciliacionDet WHERE IdCab='".$_POST['EliCon']."'");
	}

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

	$mysqli->close();

	if(isset($_SESSION['EdiCon']) ){
		$_SESSION['EdiCon']="";
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

			function RepXLS(v1){
				form1.EdiCon.value=v1;
				form1.action="frmCartolaPDF.php";
				form1.submit();
			}

			function EliCar(v1){
				if (confirm("Esta seguro de eliminar la Cartola.") == true) {
					form1.EliCon.value=v1;
					form1.submit();
				}
			}

			function ModCar(v1){
				form1.EdiCon.value=v1;
				form1.action="frmRepConciliacion.php";
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
			<form name="form1" method="post" action="" enctype="multipart/form-data">
				<input type="hidden" id="EliCon" name="EliCon" value="">
				<input type="hidden" id="EdiCon" name="EdiCon" value="">

				<br>

				<div class="col-sm-2">
				</div>
				<div class="col-sm-8">
					<div class="col-md-3">
						<a href="frmImportaCartola.php" class="btn btn-exportar btn-block" role="button">Importar Cartola</a>
					</div>
					<div class="col-md-3">
						<button type="button" class="btn btn-grabar btn-block" data-toggle="modal" data-target="#myModal">Instrucciones</button>
						<div class="modal fade" id="myModal" role="dialog">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">Instrucciones Uso Conciliación Bancaria</h4>
								</div>
								<div class="modal-body" style="font-size: 14px;;">
									<p>
										1. Evalua si existe el pago o cobro del Documento en la Cartola, con Rut y Numero de Documento, luego evalua si el Voucher del (P/C) contiene la Cta Banco.<br><br>
										2. Evalua Fecha y Glosa en Libro Diario, luego evalua si existe la Cta en el Voucher.<br><br>
										3. Evalua Fecha, Cta y Monto, si existe el Voucher.<br><br>
										** Para la cuenta Bancaria los Cargos Son "INGRESOS" y los Abonos "EGRESOS".<br><br><br>
									</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
						</div>
					</div>


					<div class="clearfix"></div>
					<br>

					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Cartolas Importadas</div>
						<div class="panel-body">
							<table class="table table-hover">
							<thead>
								<tr>
									<th>N°</th>
									<th>Cartola</th>
									<th style="text-align: center;">Fecha Desde</th>
									<th style="text-align: center;">Fecha Hasta</th>
									<th style="text-align: right;">Abono</th>
									<th style="text-align: right;">Cargo</th>
									<th style="text-align: right;">%</th>
									<th style="text-align: center;" widht="1%">Editar</th>
									<th style="text-align: center;" widht="1%">Reporte</th>
									<th style="text-align: center;" widht="1%">Eliminar</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$Cont=1;
									$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
									$SqlStr="SELECT * FROM CTConciliacionCab WHERE RutEmpresa='$RutEmpresa' ORDER BY FDesde DESC, Id DESC";
									$Resultado = $mysqli->query($SqlStr);
									while ($Registro = $Resultado->fetch_assoc()) {

										$TCargos=0;
										$TAbonos=0;
										$SStr="SELECT sum(Cargos) AS SCargos, sum(Abonos) AS SAbonos FROM CTConciliacionDet WHERE IdCab='".$Registro['Id']."' AND RutEmpresa='$RutEmpresa'";
										$Res = $mysqli->query($SStr);
										while ($Reg = $Res->fetch_assoc()) {
											$TCargos=$Reg['SCargos'];
											$TAbonos=$Reg['SAbonos'];											
										}

										$TCartola=0;
										$SStr="SELECT count(Id) AS TCartola FROM CTConciliacionDet WHERE IdCab='".$Registro['Id']."' AND RutEmpresa='$RutEmpresa'";
										$Res = $mysqli->query($SStr);
										while ($Reg = $Res->fetch_assoc()) {
											$TCartola=$Reg['TCartola'];									
										
										}
										$TLog=0;
										$SStr="SELECT count(Id) AS TLog FROM CTConciliacionLog WHERE IdCab='".$Registro['Id']."' AND RutEmpresa='$RutEmpresa'";
										$Res = $mysqli->query($SStr);
										while ($Reg = $Res->fetch_assoc()) {
											$TLog=$Reg['TLog'];									
										}

										echo '
											<tr>
												<td>'.$Cont.'</td>
												<td>'.$Registro['Nombre'].'</td>
												<td style="text-align: center;">'.date('d-m-Y',strtotime($Registro['FDesde'])).'</td>
												<td style="text-align: center;">'.date('d-m-Y',strtotime($Registro['FHasta'])).'</td>
												<td style="text-align: right;">'.number_format($TAbonos, $NDECI, $DDECI, $DMILE).'</td>
												<td style="text-align: right;">'.number_format($TCargos, $NDECI, $DDECI, $DMILE).'</td>
												<td style="text-align: right;">'.$TLog."/".$TCartola.'</td>
												<td style="text-align: center;"><button type="button" title="Modificar Cartola" class="btn btn-modificar btn-xs" onclick="ModCar('.$Registro['Id'].')"><span class="glyphicon glyphicon-edit"></span>  </button></td>
												<td style="text-align: center;"><button type="button" title="Descargar Reporte XLS" class="btn btn-grabar btn-xs" onclick="RepXLS('.$Registro['Id'].')"><span class="glyphicon glyphicon-file"></span>  </button></td>
												<td style="text-align: center;"><button type="button" title="Eliminar Cartola" class="btn btn-cancelar btn-xs" onclick="EliCar('.$Registro['Id'].')"><span class="glyphicon glyphicon-trash"></span>  </button></td>
											</tr>
										';
										$Cont++;
									}
									$mysqli->close();
								?>
							</tbody>
							</table>
						</div>
					</div>

					<div class="clearfix"></div>
					<br>				

				</div>
			</form>
		</div>
		</div>
		<?php include '../footer.php'; ?>
	</body>
</html>
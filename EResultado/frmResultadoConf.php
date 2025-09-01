<?php

	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	if (isset($_POST['idcab']) && $_POST['idcab']!="") {
		if ($_POST['idcuenta']!="") {
			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


			$SQL="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SQL1="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa=''";
				$resultados1 = $mysqli->query($SQL1);
				while ($registro1 = $resultados1->fetch_assoc()) {
      				$mysqli->query("INSERT INTO CTEstResultadoDet VALUES('','".$registro1['IdCab']."','".$_SESSION['RUTEMPRESA']."','".$registro1['Cuenta']."')");

				}
			}

			$SQL="SELECT * FROM CTEstResultadoDet WHERE Cuenta='".$_POST['idcuenta']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
      			$mysqli->query("INSERT INTO CTEstResultadoDet VALUES('','".$_POST['idcab']."','".$_SESSION['RUTEMPRESA']."','".$_POST['idcuenta']."')");
			}

			$mysqli->close();
		}
	}
	
	if (isset($_POST['DefeAsie'])) {

		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$mysqli->query("DELETE FROM CTEstResultadoDet WHERE RutEmpresa=''");

		$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' ORDER BY Id";
		$resultadosint = $mysqli->query($SQLint);
		while ($registroint = $resultadosint->fetch_assoc()) {
			$mysqli->query("INSERT INTO CTEstResultadoDet VALUES('','".$registroint['IdCab']."','','".$registroint['Cuenta']."')");	
		}

		$mysqli->close();
	}


	if (isset($_POST['ridcuenta']) && $_POST['ridcuenta']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
      	$mysqli->query("DELETE FROM CTEstResultadoDet WHERE Id='".$_POST['ridcuenta']."'");
		$mysqli->close();
	}

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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">

			$(document).ready(function() {
				$('#example').DataTable();
			} );

			function BuscaCuentas(){
				var url= "../buscaitems.php";

				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#items').html(resp);
				}

				});
			}


			function NewCuenta(value){
				form1.idcab.value=value;
				form1.idcuenta.value="";
			}

			function remov(valor){
				form1.idcab.value="";
				form1.idcuenta.value="";
				form1.ridcuenta.value=valor;
				form1.submit();
			}

			function data(valor){
				form1.idcuenta.value=valor;
				document.getElementById("cmodel").click();
				if (form1.idcab.value!="" && form1.idcuenta.value!="") {
					form1.submit();
				}
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
		<br>

		<form action="#" method="POST" name="form1" id="form1">
			<div class="col-md-2">
				<input type="hidden" name="idcab" id="idcab">
				<input type="hidden" name="idcuenta" id="idcuenta">
				<input type="hidden" name="ridcuenta" id="ridcuenta">
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
											
											$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."' AND tipo='RESULTADO'";
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$tcuenta=$registro1["nombre"];

												echo '
													<tr onclick="data(\''.$registro["numero"].'\')">
														<td>'.$registro["numero"].'</td>
														<td>'.strtoupper($registro["detalle"]).'</td>
														<td>'.$tcuenta.'</td>
													</tr>
												';

											}


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




			</div>
			<div class="col-md-8">

				<div class="col-md-12 text-right">
					<a href="frmResultadoConfCat.php" class="btn btn-warning">
						<span class="glyphicon glyphicon-cog"></span> Genera Niveles
					</a>
				</div>
				<div class="clearfix"></div>
				<br>

				<div class="col-md-6">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Ingreso</div>
						<div class="panel-body">
							<?php
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$swnivel=1;
								$SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='I' ORDER BY Id";
								$resultados = $mysqli->query($SQL);
								$cont=1;
								while ($registro = $resultados->fetch_assoc()) {

									echo '
										<div class="col-md-6">'.$cont.' - '.$registro['Nombre'].'</div>
										<div class="col-md-6 text-center"><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" onclick="NewCuenta('.$registro['Id'].')" >Agregar</button></div>
										<div class="clearfix"></div>
									';

									$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
									$resultados1 = $mysqli->query($SQLint);
									$row_cnt = $resultados1->num_rows;
									if ($row_cnt==0) {
										$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='' ORDER BY Cuenta";
									}else{
										$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' ORDER BY Cuenta";
									}

									$resultadosint = $mysqli->query($SQLint);
									while ($registroint = $resultadosint->fetch_assoc()) {
										if ($_SESSION["PLAN"]=="S"){
											$SQLint2="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registroint['Cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
										}else{
											$SQLint2="SELECT * FROM CTCuentas WHERE numero='".$registroint['Cuenta']."'";
										}
										$resultados2 = $mysqli->query($SQLint2);
										while ($registroint2 = $resultados2->fetch_assoc()) {
											$Xoper=$registroint2['detalle'];
										}

										echo '
											<div class="col-md-2"></div>
											<div class="col-md-10"><span class="glyphicon glyphicon-remove" onclick="remov('.$registroint['Id'].')"></span> '.$registroint['Cuenta']." - ".$Xoper.'</div>
											<div class="clearfix"></div>
										';
									}
									$cont++;
								}
								$mysqli->close();
							?>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Egreso</div>
						<div class="panel-body">
							<?php
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$swnivel=1;
								$SQL="SELECT * FROM CTEstResultadoCab WHERE Estado='A' AND Tipo='E' ORDER BY Id";
								$resultados = $mysqli->query($SQL);
								$cont=1;
								while ($registro = $resultados->fetch_assoc()) {

									echo '
										<div class="col-md-6">'.$cont.' - '.$registro['Nombre'].'</div>
										<div class="col-md-6 text-center"><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" onclick="NewCuenta('.$registro['Id'].')" >Agregar</button></div>
										<div class="clearfix"></div>
									';

									$SQLint="SELECT * FROM CTEstResultadoDet WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
									$resultados1 = $mysqli->query($SQLint);
									$row_cnt = $resultados1->num_rows;
									if ($row_cnt==0) {
										$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='' ORDER BY Cuenta";
									}else{
										$SQLint="SELECT * FROM CTEstResultadoDet WHERE IdCab='".$registro['Id']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."' ORDER BY Cuenta";
									}

									$resultadosint = $mysqli->query($SQLint);
									while ($registroint = $resultadosint->fetch_assoc()) {
										if ($_SESSION["PLAN"]=="S"){
											$SQLint2="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registroint['Cuenta']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
										}else{
											$SQLint2="SELECT * FROM CTCuentas WHERE numero='".$registroint['Cuenta']."'";
										}
										$resultados2 = $mysqli->query($SQLint2);
										while ($registroint2 = $resultados2->fetch_assoc()) {
											$Xoper=$registroint2['detalle'];
										}

										echo '
											<div class="col-md-2"></div>
											<div class="col-md-10"><span class="glyphicon glyphicon-remove" onclick="remov('.$registroint['Id'].')"></span> '.$registroint['Cuenta']." - ".$Xoper.'</div>
											<div class="clearfix"></div>
										';
									}
									$cont++;
								}
								$mysqli->close();
							?>
						</div>
					</div>
				</div>

				<div class="clearfix"></div>
				<br>

				<div class="col-md-12 text-center">
					<div class="checkbox">
						<label><input type="checkbox" id="DefeAsie" name="DefeAsie" onclick="javascript:form1.submit();">Dejar esta Configuraci&oacute;n por defecto</label>
					</div>
				</div>


			</div>

			<div class="col-md-2">
			</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include '../footer.php'; ?>

	</body>
</html>
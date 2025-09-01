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
	$SwMes="";
	if ($_POST['swClona']=="S") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$CtaRegistros=0;
		$SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_POST['SelRutEmp']."' ORDER BY numero";
		$resultado = $mysqli->query("$SQL");
		$CtaRegistros = $resultado->num_rows;
		if ($CtaRegistros>0) {
			$mysqli->query("DELETE FROM CTCuentasEmpresa WHERE rut_empresa='$RutEmpresa'");

			$mysqli->query("UPDATE CTEmpresas SET ccosto='S', plan='S' WHERE rut='$RutEmpresa'");

			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {

				if ($registro['auxiliar']=="") {
					$laxu="N";
				}else{
					$laxu=$registro['auxiliar'];
				}
		 		$mysqli->query("INSERT INTO CTCuentasEmpresa VALUES('','$RutEmpresa','".$registro['numero']."','".$registro['detalle']."','".$registro['id_categoria']."','$laxu','".$registro['ingreso']."','A')");
			}
			$SwMes="S";
		}else{
			$SwMes="N";
		}
		$mysqli->close();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />

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
			function ActivaBtn(){
				if (document.getElementById("BtnVisual").style.visibility == "hidden") {
					document.getElementById("BtnVisual").style.visibility = "visible";
				}else{
					document.getElementById("BtnVisual").style.visibility = "hidden";
				}
			}
			function ClonaPlan(){
				form1.swClona.value="S";
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
		<form action="#" name="form1" id="form1" method="POST">
			<br>

			
			<div class="col-sm-4">
			</div>
			<div class="col-sm-4">
				<?php
					$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

					$SQL="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa'";
					$resultado = $mysqli->query("$SQL");
					$CtaRegistros = $resultado->num_rows;
					if ($CtaRegistros>0) {
						echo '
							<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading">&#33;OPERACI&Oacute;N NO PERMITIDA!</div>
								<div class="panel-body">
									Movimientos contables ya registrados en esta empresa.<br>
									utilizar esta opci&oacute;n puede provocar inconsistencia en cuentas contables y en la consolidaci&oacute;n de la informaci&oacute;n disponible.<br>
									Para m&aacute;s Informaci&oacute;n cont&aacute;ctese con su asesor de soporte asignado.
								</div>
							</div>
						';
					}					
					$mysqli->close();
					// $CtaRegistros=0;
				?>



			</div>
			<div class="clearfix"></div>	
			<br>

			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<?php
					if ($CtaRegistros==0) {
				?>
				<div class="col-md-12">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">CLONAR PLAN DE CUENTA</div>
						<div class="panel-body">
							<input type="hidden" name="swClona" id="swClona">
							<div class="input-group">
								<span class="input-group-addon">Empresa con Plan de Cuenta Individual</span>
									<select id="SelRutEmp" name="SelRutEmp" class="form-control">
									<option value="0">Seleccione...</option>
									<?php
										if ($CtaRegistros==0) {
											$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

											$SQL="SELECT * FROM CTEmpresas WHERE estado='A' ORDER BY razonsocial";
											$resultado = $mysqli->query("$SQL");
											while ($registro = $resultado->fetch_assoc()) {
												$SQL1="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$registro['rut']."'";
												$resultados1 = $mysqli->query($SQL1);
												$row_cnt = $resultados1->num_rows;
												if ($row_cnt>0) {									
													echo "<option value ='".$registro["rut"]."'>".$registro["rut"]." - ".$registro["razonsocial"]."</option>";
												}
											}
											$mysqli->close();
										}
									?>
									</select>
							</div>
							<br><br>
							<div class="text-center">	
								<p>
									Proceder a clonar el plan de cuenta, de la empresa seleccionada?
								</p>

								<div class="checkbox">
									<label><input type="checkbox" id="SwPago" name="SwPago" value="" onclick="ActivaBtn()"> Aceptar</label>
								</div>
								<div class="clearfix"></div>
								<br><br>
							</div>
							
							<button type="button" class="btn btn-default btn-block" id="BtnVisual" onclick="ClonaPlan()" style="visibility:hidden;">Clonar</button>
						</div>
					</div>
				</div>

				<div class="col-md-4">

				</div>

				<div class="clearfix"></div>
				<br>				
				<?php
					}
				?>

			</div>
		</form>
		</div>
		</div>
		<script type="text/javascript">
		<?php
			if ($SwMes=="N") {
				echo 'alert("A ocurrido un error, favor contactar con soporte.")';
			}
			if ($SwMes=="S") {
				echo 'alert("Se a completado la operaci\u00F3n con exito.")';
			}
		?>
		</script>

		<?php include '../footer.php'; ?>
	</body>
</html>
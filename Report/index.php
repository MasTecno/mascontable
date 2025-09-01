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

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTCliPro WHERE tipo='P' ORDER BY id ASC;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$PRut=$registro['rut'];

		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P'";
		$Resul = $mysqli->query($SQL1);
		$row_cnt = $Resul->num_rows;
		if($row_cnt>1){
			$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='P' ORDER BY id ASC LIMIT 1;";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$IdReg=$registro1['id'];
			}			
			$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='P'");
			// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
		}

	}

	$SQL="SELECT * FROM CTCliPro WHERE tipo='C' ORDER BY id ASC;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$PRut=$registro['rut'];

		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C'";
		$Resul = $mysqli->query($SQL1);
		$row_cnt = $Resul->num_rows;
		if($row_cnt>1){
			$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='C' ORDER BY id ASC LIMIT 1;";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$IdReg=$registro1['id'];
			}			
			$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='C'");
			// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
		}

	}

	$SQL="SELECT * FROM CTCliPro WHERE tipo='2' ORDER BY id ASC;";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$PRut=$registro['rut'];

		$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2'";
		$Resul = $mysqli->query($SQL1);
		$row_cnt = $Resul->num_rows;
		if($row_cnt>1){
			$SQL1="SELECT * FROM CTCliPro WHERE rut LIKE '$PRut' AND tipo='2' ORDER BY id ASC LIMIT 1;";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$IdReg=$registro1['id'];
			}			
			$mysqli->query("DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut' AND tipo='2'");
			// echo "DELETE FROM CTCliPro WHERE id> $IdReg AND rut='$PRut'";
		}

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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-md-12">
				<div class="col-md-2"></div>

				<div class="col-md-8">
					<table class="table table-striped">
						<thead>
							<tr>
							<th>Descripci&oacute;n</th>
							<th>Acci&oacute;n</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Analiticos Transacciones en Compras, Ventas y Honorarios</td>
								<td><a href="frmNComVenHon.php" class="btn btn-modificar" role="button">Ingresar</a></td>
							</tr>
							<!-- <tr>
								<td>Transacciones de Movimiento de Cuentas - Customizable (Mayor V2)</td>
								<td><a href="frmNCuentas.php" class="btn btn-modificar" role="button">Ingresar</a></td>
							</tr> -->
							<tr>
								<td>Documentos por Cobrar - Pagar</td>
								<td><a href="frmNDocumentos.php" class="btn btn-modificar" role="button">Ingresar</a></td>
							</tr>
							<tr>
								<td>Documentos por Centro de Costo</td>
								<td><a href="frmDocCCosto.php" class="btn btn-modificar" role="button">Ingresar</a></td>
							</tr>
						</tbody>
					</table>
				</div>

			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


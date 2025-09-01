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

	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
			<?php
				if (isset($_GET['pageno'])) {
					$pageno = $_GET['pageno'];
				} else {
					$pageno = 1;
				}
			
				$no_of_records_per_page = 50;
				$offset = ($pageno-1) * $no_of_records_per_page;
			?>
			<br>
			<div class="col-sm-12 text-center">
				<h3>Registros Boletas Electronicas</h3>
				<h5>Periodo <?php echo $_SESSION['PERIODO']; ?></h5>
			</div>
			<br>
			<div class="col-sm-12">
					<div class="col-sm-3"></div>
					<div class="col-sm-6">
						<table class="table table-condensed table-hover">
							<thead>
							<tr>
								<th style="text-align: right;" width="20%">Folio</th>
								<th style="text-align: right;" width="20%">Fecha</th>
								<th style="text-align: right;" width="20%">Neto</th>
								<th style="text-align: right;" width="20%">IVA</th>
								<th style="text-align: right;" width="20%">Total</th>
							</tr>
							</thead>
							<tbody>
							<?php


								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

								$total_pages_sql = "SELECT COUNT(*) FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Periodo='".$_SESSION['PERIODO']."'";

								$result = mysqli_query($mysqli,$total_pages_sql);
								$total_rows = mysqli_fetch_array($result)[0];
								$total_pages = ceil($total_rows / $no_of_records_per_page);

								$sql = "SELECT * FROM CTBoletasDTE WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Periodo='".$_SESSION['PERIODO']."' LIMIT $offset, $no_of_records_per_page";
								$res_data = mysqli_query($mysqli,$sql);
								while($row = mysqli_fetch_array($res_data)){
									echo "<tr><td class='text-right'>".$row[3]."</td><td class='text-right'>".date('d-m-Y',strtotime($row[8]))."</td><td class='text-right'>".$row[4]."</td><td class='text-right'>".$row[5]."</td><td class='text-right'>".$row[6]."</td><tr>";
								}
								mysqli_close($mysqli);
							?>
							</tbody>
						</table>
					</div>	

				<div class="clearfix"></div>
				<br>


				<div class="col-sm-12 text-center">
					<ul class="pagination">
						<li>
							<a href="?pageno=1">Inicio</a>
						</li>
						<li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
							<a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Anterior</a>
						</li>
						<li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
							<a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Siguiente</a>
						</li>
						<li>
							<a href="?pageno=<?php echo $total_pages; ?>">Fin</a>
						</li>
					</ul>
				</div>

			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


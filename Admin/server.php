<?php
// global $mysqli;

	include 'conexionserver.php';
	include 'conexion.php';
	// include 'js/funciones.php';
	// session_start();

	// if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
	// 	header("location:index.php?Msj=95");
	// 	exit;
	// }

 //    $Periodo=$_SESSION['PERIODO'];
 //    $RazonSocial=$_SESSION['RAZONSOCIAL'];
 //    $RutEmpresa=$_SESSION['RUTEMPRESA'];

 //    if($Periodo==""){
 //      header("location:frmMain.php");
 //      exit;
 //    }
	
?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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

	</head>

	<body>

		<!-- <?php //include 'nav.php'; ?> -->

		<div class="container-fluid text-left">
		<div class="row content">

		<form action="" method="POST" name="form1" id="form1">

			<div class="col-md-12">
				<br>

				<?php 
					$mysqli=conectarServer();

					$sql = "SELECT * FROM UnionServer ORDER BY Server ASC";
					if (!$resultado = $mysqli->query($sql)) {
						echo "Lo sentimos, este sitio web está experimentando problemas.";
						exit;
					}

					while ($registro = $resultado->fetch_assoc()) {


						$mysqliX=xconectar($registro["Usuario"],$registro["Clave"],$registro["Base"]);

						$sqlin = "SELECT * FROM CTContadores";
						if (!$resultadoin = $mysqliX->query($sqlin)) {
							echo "Lo sentimos, este sitio web está experimentando problemas.";
							exit;
						}

						echo '
							<div class="col-md-4">
								<div class="panel panel-default">
									<div class="panel-heading">'.strtoupper($registro["Server"]).' - Alias: '.strtoupper($registro["Alias"]).'</div>
									<div class="panel-body">

										<div class="col-md-6">Base: </div>
										<div class="col-md-6">'.$registro["Base"].'</div>

										<div class="col-md-6">Usuario: </div>
										<div class="col-md-6">'.$registro["Usuario"].'</div>

										<div class="clearfix"></div><br>

										<button type="button" class="btn btn-primary btn-block" data-toggle="collapse" data-target="#usu'.$registro["id"].'">Usuarios</button>
											<div id="usu'.$registro["id"].'" class="collapse">
						';
												while ($registroin = $resultadoin->fetch_assoc()) {

													// echo '
													// <div class="col-md-6>"'.$registroin["nombre"].'</div>
													// <div class="col-md-6">Acceso: '.date('d-m-Y',strtotime($registroin["ingreso"])).'</div> 
													// <div class="clearfix"></div><br>
													// ';
													echo $registroin["Nombre"].', Fecha de Ingreso: '.date('d-m-Y',strtotime($registroin["Ingreso"])).'<br>';


												}

						echo '
											</div>
									</div>
								</div>
							</div>

						';
					}
   
					$mysqli->close();
				?>

			</div>


		</form>

		</div>
		</div>

		<div class="clearfix"> </div>

<!-- 		<script>
			$(document).ready(function(){
				$("#myInput").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#myTable tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
				});
			});
		</script>
 -->
		<?php 
		// include 'footer.php'; 
		?>

	</body>
</html>
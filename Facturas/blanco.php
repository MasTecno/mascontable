<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$PeriodoX=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	// if($Periodo==""){
	// 	header("location:../frmMain.php");
	// 	exit;
	// }


	///ConCobranza();


	// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	// $SQL="SELECT * FROM CTParametros WHERE estado='A'";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	if($registro['tipo']=="SEPA_MILE"){
	// 		$DMILE=$registro['valor'];  
	// 	}

	// 	if($registro['tipo']=="SEPA_DECI"){
	// 		$DDECI=$registro['valor'];  
	// 	}

	// 	if($registro['tipo']=="TIPO_MONE"){
	// 		$DMONE=$registro['valor'];  
	// 	}

	// 	if($registro['tipo']=="NUME_DECI"){
	// 		$NDECI=$registro['valor'];  
	// 	} 
	// }
	// $mysqli->close();

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

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">
			<br>
			<div class="col-md-2"></div>
			<div class="col-md-8 text-left">
				<form action="#" name="form1" id="form1" method="POST">


				</form>
			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


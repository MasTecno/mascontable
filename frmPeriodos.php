<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	if ($_SESSION['ROL']!="A") {
		header("location:frmMain.php");
		exit;
	}

	if ($_POST['SwP']!="" && $_POST['Per']) {
	
		$mysqliX=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);


		if($_POST['SwP']=="0"){
			$mysqliX->query("DELETE FROM CTPeriodoEmpresa WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Periodo='".$_POST['Per']."'");
		}else{
			$mysqliX->query("INSERT INTO CTPeriodoEmpresa VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Per']."','A')");
		}

		$mysqliX->close();    	
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>MasRemuneraciones</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/StConta.css">
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
	<script type="text/javascript">
		function Procesar(d1,d2){
			form1.Per.value=d1;
			form1.SwP.value=d2;
			form1.submit();			
		}
	</script>

</head>
<body>


<?php 
	include 'nav.php';
?>

<div class="container-fluid text-left">
<div class="row content">
	<form action="#" method="POST" name="form1" id="form1">
		<input type="hidden" name="Per" id="Per">
		<input type="hidden" name="SwP" id="SwP">
	<div class="col-md-12">
				<br>

		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Periodos del A&ntilde;o</div>
				<div class="panel-body">

					<table class="table table-hover">
					<thead>
						<tr>
							<th>Periodo</th>
							<th>Acci&oacute;n</th>
						</tr>
					</thead>
					<tbody>
						<?php
						      $Mes=substr($_SESSION['PERIODO'],0,2);
						      $Ano=substr($_SESSION['PERIODO'],3);

							$mysqliX=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$sqlin = "SELECT * FROM CTPeriodo WHERE periodo LIKE '%-".$Ano."'";
							$resultadoin = $mysqliX->query($sqlin);

							while ($registro = $resultadoin->fetch_assoc()) {

								$sql = "SELECT * FROM CTPeriodoEmpresa WHERE RutEmpresa='".$_SESSION['RUTEMPRESA']."' AND Periodo='".$registro["periodo"]."'";
								// echo "<br>";
								$resul = $mysqliX->query($sql);
								$ContReg = $resul->num_rows;

								if ($ContReg>0) {
									$Bot='<a href="javascript:Procesar(\''.$registro["periodo"].'\',0)" class="btn btn-cancelar"><span class="glyphicon glyphicon-thumbs-down"></span> Cerrado</a>';
								}else{
									$Bot='<a href="javascript:Procesar(\''.$registro["periodo"].'\',1)" class="btn btn-grabar"><span class="glyphicon glyphicon-thumbs-up"></span> Abierto</a>';
								}
								echo '
									<tr>
										<td>'.$registro["periodo"].'</td>
										<td>'.$Bot.'</td>
									</tr>
								';
							}
							$mysqliX->close();
						?>
					</tbody>
					</table>


				</div>
			</div>
		</div> 
		
	</div>

	</form>

</div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>


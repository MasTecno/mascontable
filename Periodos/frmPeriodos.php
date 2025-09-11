<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if ($_SESSION['ROL']!="A") {
		header("location:../frmMain.php");
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
	<script src="../js/jquery.min.js"></script>

	<script src="https://cdn.tailwindcss.com"></script>
	<script src="../js/tailwind.js"></script>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="../css/StConta.css">
	<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>


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
	include '../nav.php';
?>

<div class="min-h-screen bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
	<div class="space-y-8">
		<form action="#" method="POST" name="form1" id="form1">
			<input type="hidden" name="Per" id="Per">
			<input type="hidden" name="SwP" id="SwP">
		<div class="col-md-12">
					<br>

			<div class="col-md-4">
				<div class="bg-white rounded-lg shadow-sm border border-gray-200">
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-calendar-days text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Periodos
							</h3>
							<p class="text-sm text-gray-600">Administración de periodos del año</p>     
						</div>
					</div>
					<div class="p-6 pt-1 space-y-6">

						<table class="min-w-full divide-y divide-gray-200 mt-3">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Periodo</th>
								<th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Acci&oacute;n</th>
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
										$Bot='<a href="javascript:Procesar(\''.$registro["periodo"].'\',0)" class="inline-flex border border-red-300 justify-evenly items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200"><i class="fa fa-thumbs-down mr-2"></i> Cerrado</a>';
									}else{
										$Bot='<a href="javascript:Procesar(\''.$registro["periodo"].'\',1)" class="inline-flex border border-green-300 justify-evenly items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"><i class="fa fa-thumbs-up mr-2"></i> Abierto</a>';
									}
									echo '
										<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
											<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">'.$registro["periodo"].'</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">'.$Bot.'</td>
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
</div>

<?php include '../footer.php'; ?>

</body>
</html>


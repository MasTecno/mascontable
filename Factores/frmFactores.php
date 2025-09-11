<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	if ($_POST['d01']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    	$SQL="SELECT * FROM CTFactores WHERE periodo='".$_POST['ano']."'";
        $resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt>0) {
			$mysqli->query("UPDATE CTFactores SET mes1='".$_POST['d01']."', mes2='".$_POST['d02']."', mes3='".$_POST['d03']."', mes4='".$_POST['d04']."', mes5='".$_POST['d05']."', mes6='".$_POST['d06']."', mes7='".$_POST['d07']."', mes8='".$_POST['d08']."', mes9='".$_POST['d09']."', mes10='".$_POST['d10']."', mes11='".$_POST['d11']."', mes12='".$_POST['d12']."' WHERE periodo='".$_POST['ano']."'");
			$mysqli->close();
			header("location:../frmMain.php");
			exit;
		}else{
			$mysqli->query("INSERT INTO CTFactores VALUE('','".$_POST['ano']."','".$_POST['d01']."','".$_POST['d02']."','".$_POST['d03']."','".$_POST['d04']."','".$_POST['d05']."','".$_POST['d06']."','".$_POST['d07']."','".$_POST['d08']."','".$_POST['d09']."','".$_POST['d10']."','".$_POST['d11']."','".$_POST['d12']."','A')");
			$mysqli->close();
			header("location:../frmMain.php");
			exit;
		}

	}
?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">

		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script type="text/javascript">
			function CargGrilla(){

				var url= "frmFactoresDet.php";

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
					}
				});
			}
			function Porce(){
				form1.submit();

			}

		</script>
	</head>

	<body onload="CargGrilla()">


	<?php include '../nav.php'; ?>

	<div class="min-h-screen bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

		<div class="space-y-8">
		<form action="" method="POST" name="form1" id="form1">

			<div class="sticky top-0 bg-white z-50 md:static flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-8">
				<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Porce()">
					<i class="fa fa-save mr-2"></i> Confirmar Configuraci&oacute;n
				</button>
			</div>

			<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
				<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
					<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
						<i class="fa-solid fa-percentage text-lg text-blue-600"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-800">
							Configuraci&oacute;n de Factores por Mes
						</h3>
						<p class="text-sm text-gray-600">Establecer factores de conversi&oacute;n para cada mes del a&ntilde;o</p>
					</div>
				</div> 
					
				<div class="p-6 pt-1 space-y-6">
					<div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-3">
						<div>
							<label for="ano" class="block text-sm font-medium text-gray-700 mb-2">Periodo</label>
							<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="ano" name="ano" required onchange="CargGrilla()">
								<option value="">Seleccione un a&ntilde;o</option>
								<?php              
									$i=2010;
									$dano=date("Y");

									while ( $i<= 2030){
										if($i==$dano){
											echo "<option value='".$i."' selected>".$i."</option>";
										}else{
											echo "<option value='".$i."'>".$i."</option>";
										}
										$i=$i+1;
									}
								?>
							</select>
						</div>
					</div>

					<div class="mt-6" id="grilla">
					</div>

				</div>
			</div>

		</form>
		</div>
	</div>
	</div>

	<div class="clearfix"> </div>


	<?php include '../footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
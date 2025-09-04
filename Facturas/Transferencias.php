<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	// include '../conexion/secciones.php';
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$PeriodoX=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if (isset($_POST['rut']) && $_POST['rut']!="") {
		$lMensaje="";
		$mysqli=ConCobranza();
		$SQL="SELECT * FROM TransferenciasRut WHERE Rut='".$_POST['rut']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {

			$mysqli->query("INSERT INTO TransferenciasRut VALUES('','".$_SESSION['xIdServer']."','".$_POST['rut']."','".date('Y-m-d')."','A')");

		}else{
			$lMensaje="<br>El Rut (".$_POST['rut'].") ya est&aacute; registro, Si el problema persiste contactar a MasTecno para ayudarle.<br>Saludos.";
		}
		$mysqli->close();
	}

	if (isset($_POST['EliRut']) && $_POST['EliRut']!="") {
		$Lrut=descript($_POST['EliRut']);
		$mysqli=ConCobranza();
		$mysqli->query("DELETE FROM TransferenciasRut WHERE IdServer='".$_SESSION['xIdServer']."' AND Id='$Lrut';");
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
		<!-- <link rel="stylesheet" href="../css/bootstrap.min.css"> -->
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
			$(document).ready(function(){
			$('#rut').Rut({ 
				on_error: function(){alert('Rut incorrecto'); $('#rut').val(""); $('#rut').focus();} 
			});
			});


			function NumYGuion(e){
			var key = window.Event ? e.which : e.keyCode
				return (key >= 48 && key <= 57 || key == 45 || key==75 || key==107)
			}
			function FEliRut(valor){
				form1.rut.value="";
				form1.EliRut.value=valor;
				form1.submit();
			}
		</script>
	</head>
	<body>
		<?php 
			include '../nav.php';
		?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
		<div class="space-y-6">
			<br>
			<form action="#" name="form1" id="form1" method="POST">
				<input type="hidden" name="EliRut" id="EliRut">

			<div class="grid grid-cols-1 md:grid-cols-5 gap-6">
				<div class="md:col-span-2">

					<div class="col-md-12">
						<div>
							<label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
								<i class="fa fa-id-card mr-1"></i>RUT
							</label>
							<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="rut" autocomplete="off" name="rut" onChange="javascript:this.value=this.value.toUpperCase();" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" required>
						</div>
						<br>
						<button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-sm text-white font-medium py-1 px-2 border-2 border-green-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
							<span class="glyphicon glyphicon-saved"></span>Agregar
						</button>


					</div> 

					<p><?php echo $lMensaje; ?></p>

					<div class="clearfix"></div>
					<br>


					<h4>Mis Rut</h4>
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr style="background-color: #e51c20; color: #FFF;">
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider" style="text-align: center;">Fecha</th>
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider" style="text-align: center;">Rut</th>
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider" style="text-align: center;" width="1%"></th>
							</tr>
						</thead>
						<tbody id="Empresas">
							<?php
								$mysqli=ConCobranza();

								$SQL="SELECT * FROM TransferenciasRut WHERE IdServer='".$_SESSION['xIdServer']."';";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									$Pref=randomText(35);
									$Suf=randomText(8);

									echo '
									<tr class="bg-white hover:bg-gray-50 border border-gray-300 transition duration-150 ease-in-out">
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["Rut"].'</td>
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">
											<button type="button" class="bg-red-500 hover:bg-red-600 text-sm text-white font-medium py-1 px-2 border-2 border-red-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="FEliRut(\''.$Pref.$registro["Id"].$Suf.'\')">
												<i class="fa fa-trash"></i>
											</button>
										</td>
									</tr>
									';
								}
								$mysqli->close();
							?>
						</tbody>
					</table>
		
				</div>

				<div class="md:col-span-3">
					<?php
						$mysqli=ConCobranza();
						$SQL="SELECT max(Fecha) As UFecha FROM Transferencias";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							$UFecha=date('d-m-Y',strtotime($registro["UFecha"]));
						}
					?>

					<div class="flex justify-start items-center gap-6">
						<a href="../Facturas" class="bg-orange-300 hover:bg-orange-400 text-sm text-white font-medium py-1 px-2 border-2 border-orange-300 shadow rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2">
							<i class="fa-solid fa-arrow-left mr-1"></i>Volver
						</a>
						<h3 class="text-sm font-semibold text-gray-900">Transferencias Recibidas (Cartola <?php echo $UFecha; ?>)</h3>
					</div>
					
					<br>
					
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr style="background-color: #e51c20; color: #FFF;">
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider" style="text-align: center;" width="10%">Fecha</th>
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider" style="text-align: center;">Banco</th>
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider" style="text-align: center;" width="10%">N. Operaci&oacute;n</th>
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider"style="text-align: center;" width="10%">N. Cuenta</th>
								<th class="px-6 py-2 text-left text-xs font-semibold text-white tracking-wider" style="text-align: center;" width="10%">Monto</th>
							</tr>
						</thead>
						<tbody id="Empresas">
							<?php
								$mysqli=ConCobranza();

								$SQL='SELECT Transferencias.Id, TransferenciasRut.IdServer, TransferenciasRut.Rut, Transferencias.Fecha, Transferencias.Banco, Transferencias.NOperacion, Transferencias.Cta, Transferencias.Monto, Transferencias.Estado
								FROM TransferenciasRut LEFT JOIN Transferencias ON TransferenciasRut.Rut = Transferencias.Rut
								WHERE (((TransferenciasRut.IdServer)="'.$_SESSION['xIdServer'].'")
								AND ((Transferencias.Estado)="A"))
								ORDER BY Transferencias.Fecha DESC;';
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {


									echo '
									<tr class="bg-white hover:bg-gray-50 border border-gray-300 transition duration-150 ease-in-out">
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["Banco"].'</td>
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["NOperacion"].'</td>
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["Cta"].'</td>
										<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.number_format($registro["Monto"],0,",",".").'</td>
									</tr>
									';
								}
								$mysqli->close();
							?>
						</tbody>
					</table>
					
				</div>
			</div>
			</form>
		</div>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>
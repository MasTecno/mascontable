<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);



	$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt==0) {
    	$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa=''";
	}else{
		$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	}

	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$XC1=$registro["L1"];
		$XC2=$registro["L2"];
		$XC3=$registro["L3"];
		$XC4=$registro["L4"];
	}

	// if ($_SESSION["PLAN"]=="S"){
    // 	$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	// }else{
    // 	$SQL="SELECT * FROM CTAsientoHono WHERE tipo='R' AND rut_empresa=''";
	// }
    // $resultados = $mysqli->query($SQL);
    // while ($registro = $resultados->fetch_assoc()) {
	// 	$XC1=$registro["L1"];
	// 	$XC2=$registro["L2"];
	// 	$XC3=$registro["L3"];
    // }


    if ($XC1!="") {
    	if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC1' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XC1'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnC1=$registro["detalle"];
		}
	}

    if ($XC2!="") {
    	if ($_SESSION["PLAN"]=="S"){
 			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC2' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
	   	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XC2'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnC2=$registro["detalle"];
		}
	}

    if ($XC3!="") {
    	if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC3' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XC3'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnC3=$registro["detalle"];
		}
	}
 
    if ($XC4!="") {
    	if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='$XC4' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    	}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='$XC4'";
    	}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$XnC4=$registro["detalle"];
		}
	}

    $mysqli->close();
?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
		<script src="js/jquery.min.js"></script>
		<!-- <script src="js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="js/tailwind.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type="text/javascript">

			function BuscaCuenta(vall){
			      var url= "buscacuenta.php";
			      var x1=$('#'+vall).val();
			      $.ajax({
			        type: "POST",
			        url: url,
			        data: ('dat1='+x1),
			        success:function(resp)
			        {

			          var r=Number(vall.substr(4, 1));
			          var r='DComp'+r;

			          if(resp==""){
			            alert("No se encontro cuenta");
			            $('#'+vall).focus(); 
			            $('#'+vall).select();
			            document.getElementById(r).value="";
			          }else{
			            document.getElementById(r).value=resp;
			          }
			        }
			      }); 
			}


			// function BuscaCuentaV(vall){
			//       var url= "buscacuenta.php";
			//       var x1=$('#'+vall).val();
			//       $.ajax({
			//         type: "POST",
			//         url: url,
			//         data: ('dat1='+x1),
			//         success:function(resp)
			//         {

			//           var r=Number(vall.substr(5, 1));
			//           var r='DVenta'+r;

			//           if(resp==""){
			//             alert("No se encontro cuenta");
			//             $('#'+vall).focus(); 
			//             $('#'+vall).select();
			//             document.getElementById(r).value="";
			//           }else{
			//             document.getElementById(r).value=resp;
			//           }
			//         }
			//       }); 
			// }

			// $(document).ready(function (eOuter) {

			// 	$('input').bind('keypress', function (eInner) {
			// 	//alert(eInner.keyCode);
			// 		if (eInner.keyCode == 13){

			// 			var idinput = $(this).attr('id');

			// 			<?php 

			// 				$i = 1;
			// 				while ($i <= 4) {
			// 					echo "
			// 						if(idinput==\"Comp".$i."\"){
			// 							BuscaCuenta(this.id);
			// 							$('#Comp".($i+1)."').focus();
			// 							$('#Comp".($i+1)."').select();
			// 						}
			// 						";

			// 					$i++; 
			// 				}

			// 			?>

			// 			<?php 

			// 				$i = 1;
			// 				while ($i <= 3) {
			// 					echo "
			// 						if(idinput==\"Venta".$i."\"){
			// 							BuscaCuentaV(this.id);
			// 							$('#Venta".($i+1)."').focus();
			// 							$('#Venta".($i+1)."').select();
			// 						}
			// 						";

			// 					$i++; 
			// 				}

			// 			?>

			// 			return false;
			// 		}
			// 	});
			// });			


		function Volver(){
			form1.action="frmMain.php";
			form1.submit();
		}

		// $(document).ready(function() {
		// 	$('#example').DataTable();
		// } );
		
		function data(valor){
			var cas=form1.casilla.value;
			document.getElementById(cas).value=valor;

			//$('#'+cas).val()=valor;
			BuscaCuenta(form1.casilla.value);
			const closeButton = document.querySelector('[data-modal-hide="default-modal"]');
			if (closeButton) {
				closeButton.click();
			}
		}

		jQuery(document).ready(function(e) {
			$('#default-modal').on('shown.bs.modal', function() {
				$('input[name="BCuenta"]').focus();
			});

			// Manejar el cierre del modal
			// $('[data-modal-hide="default-modal"]').on('click', function() {
			// 	const modal = document.getElementById('default-modal');
			// 	modal.classList.add('hidden');
				
			// 	// Remover el overlay
			// 	const overlay = document.getElementById('default-modalOverlay');
			// 	if (overlay) {
			// 		overlay.remove();
			// 	}
			// });
		});



		</script> 
	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="min-h-screen bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

		<div class="space-y-8">
		<form action="xfrmConfHonorario.php" method="POST" name="form1" id="form1">
			<input type="hidden" name="casilla" id="casilla">

				<div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
					<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
						<i class="fa fa-save mr-2"></i> Grabar
					</button>

					<button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
						<i class="fa fa-times mr-2"></i> Cancelar
					</button> 
				</div>

				<!-- Modal  buscar codigo-->

				<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
					<div class="relative p-4 w-full max-w-7xl max-h-full">
						<!-- Modal content -->
						<div class="relative bg-white rounded-lg shadow-sm">
							<!-- Modal header -->
							<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
								<h3 class="text-xl font-semibold text-gray-900">
									Listado de Cuentas
								</h3>
								<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
									<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
										<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
									</svg>
									<span class="sr-only">Close modal</span>
								</button>
							</div>
							<!-- Modal body -->
							<div class="p-4 md:p-5 space-y-4">
								
								<div class="block">
									<input class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="BCuenta" name="BCuenta" type="text" placeholder="Buscar...">
								</div>
								<div class="col-md-12">

									<table class="min-w-full divide-y divide-gray-200">
										<thead class="bg-gray-50">
											<tr>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Codigo</th>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Detalle</th>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Tipo de Cuenta</th>
											</tr>
										</thead>

										<tbody id="TableCta">
											<?php 

													$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
													if ($_SESSION["PLAN"]=="S"){
														$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
													}else{
														$SQL="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
													}
													$resultados = $mysqli->query($SQL);
													while ($registro = $resultados->fetch_assoc()) {

														$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
														$res = $mysqli->query($SQL1);
														while ($reg = $res->fetch_assoc()) {
															$tcuenta=$reg["nombre"];
														}

														echo '
															<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out" onclick="data(\''.$registro["numero"].'\')">
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["numero"].'</td>
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.strtoupper($registro["detalle"]).'</td>
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$tcuenta.'</td>
															</tr>
														';


													}
													$mysqli->close();

											?>
										</tbody>
									</table>
									<script>
										$(document).ready(function(){
											$("#BCuenta").on("keyup", function() {
											var value = $(this).val().toLowerCase();
												$("#TableCta tr").filter(function() {
												$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
											});
											});
										});
									</script>								

								</div>


							</div>
							<!-- Modal footer -->
							<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
								<button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Cancelar</button>
							</div>
						</div>
					</div>
				</div>

			<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
				<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
					<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
						<i class="fa-solid fa-address-book text-lg text-blue-600"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-800">
							Centralizaci&oacute;n de Honorario Recibido
						</h3>
						<!-- <p class="text-sm text-gray-600">Datos para ingresar un contacto</p>      -->
					</div>
								
							
				</div> 
					
				<div class="p-6 pt-1 space-y-6">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">

						<div>
							<span>Cuenta</span>
							
							<div class="flex items-start gap-2"> 
								<input type="text" class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="Comp1" name="Comp1" required maxlength="50" value="<?php echo $XC1; ?>">
								<a href="#" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" role="button" data-toggle="modal" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp1'">
									<i class="fa-solid fa-magnifying-glass"></i>
								</a>
							</div> 
							<div class="flex items-start gap-2"> 
								<input type="text" class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="Comp2" name="Comp2" required maxlength="50" value="<?php echo $XC2; ?>">
								<a href="#" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" role="button" data-toggle="modal" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp2'">
									<i class="fa-solid fa-magnifying-glass"></i>
								</a>
							</div> 
							<div class="flex items-start gap-2"> 
								<input type="text" class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="Comp3" name="Comp3" required maxlength="50" value="<?php echo $XC3; ?>">
								<a href="#" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" role="button" data-toggle="modal" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp3'">
									<i class="fa-solid fa-magnifying-glass"></i>
								</a>
							</div> 
							<div class="flex items-start gap-2"> 
								<input type="text" class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="Comp4" name="Comp4" required maxlength="50" value="<?php echo $XC4; ?>">
								<a href="#" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" role="button" data-toggle="modal" data-modal-target="default-modal" data-modal-toggle="default-modal" onclick="form1.casilla.value='Comp4'">
									<i class="fa-solid fa-magnifying-glass"></i>
								</a>
							</div> 
						</div>

						<div>
							<span class="block">Detalle</span>
							<div class="flex flex-col items-start">
								<input type="text" class="w-full bg-gray-100 mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DComp1" name="DComp1" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC1); ?>, * Total Documento"  readonly="false" >
								<input type="text" class="w-full bg-gray-100 mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DComp2" name="DComp2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC2); ?>, Retenci&oacute;n"  readonly="false" >
								<input type="text" class="w-full bg-gray-100 mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DComp3" name="DComp3" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC3); ?>, Total Liquido x Pagar"  readonly="false" >
								<input type="text" class="w-full bg-gray-100 mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DComp4" name="DComp4" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($XnC4); ?>, 3% Retenci&oacute;n"  readonly="false" >
							</div>
							
						</div>
					
					</div>
 
				

					

				</div>

			</div>

			<div class="block text-center mt-3">


				<?php
					if ($_SESSION["PLAN"]!="S") {
				?>
					<div class="checkbox">
						<label><input type="checkbox" id="DefeAsie" name="DefeAsie">Dejar esta Configuraci&oacute;n por defecto</label>
					</div>
				<?php
					}
				?>
				 

			</div>


		</form>
	</div>
	</div>
	</div>

	<div class="clearfix"> </div>
	<script>
		<?php 
			if(isset($_GET['Exito'])){
				echo 'alert("Configuraci\u00F3n grabado con Exito")';
			}
		?>
	</script>

<br><br>

	<?php include 'footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
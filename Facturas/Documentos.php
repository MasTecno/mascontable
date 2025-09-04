<?php


if ($_GET['Adm']!="" && isset($_GET['Adm'])) {

	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();


	// $_SESSION['PERIODO']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']

	$_SESSION['NOMBRE']="Admini";
	$_SESSION['RAZONSOCIAL']="Admini";
	$_SESSION['ROL']="A";

	$mysqli=ConCobranza();
	
	$SQL="SELECT * FROM Servidores WHERE Id='".descript($_GET['Adm'])."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$NServer=$registro['Nombre'];
		$_SESSION['xIdServer']=$registro['Id'];  
	}

	$mysqli->close();

	$mysqli=conectarUnion();

	$Pref=randomText(35);
	$Suf=randomText(8);

	$SQL="SELECT * FROM UnionServer WHERE Alias='".$NServer."' OR Server='".$NServer."' AND Estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$NumServer=$registro["Numero"];
		$_SESSION['NomServer']=$registro["Server"];
		$_SESSION['BaseSV']=$registro["Base"];
		$_SESSION['UsuariaSV']=$registro["Usuario"];
		$_SESSION['PassSV']=$Pref.$registro["Clave"].$Suf;
	}

	$mysqli->close();

}else{
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	// include '../conexion/secciones.php';
	session_start();
}




	// include '../conexion/conexionmysqli.php';
	// include '../js/funciones.php';
	// include '../conexion/secciones.php';
	

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}

	$NomCont=$_SESSION['NOMBRE'];
	$PeriodoX=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$anioActual = date('Y');
	$anioSeleccionado = isset($_POST['anioFiltro']) ? $_POST['anioFiltro'] : $anioActual;

	if (isset($_POST["IdFactura"]) && isset($_POST["IdTrans"]) && $_POST["IdFactura"]!="" && $_POST["IdTrans"]!="") {
		$mysqli=ConCobranza();

		$MonFac=0;
		$SQL="SELECT * FROM Facturas WHERE Id='".$_POST["IdFactura"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$MonFac=$registro["Total"];
			$NFactura=$registro["Folio"];
		}

		$MonTrans=0;
		$SQL="SELECT * FROM Transferencias WHERE Id='".$_POST["IdTrans"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$MonTrans=$registro["Monto"];
			$NOperacion=$registro["NOperacion"];
		}

		$SumTrans=0;
		$SQL="SELECT sum(MontoTrans) as SumTrans FROM FactTrans WHERE IdTrans='".$_POST["IdTrans"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$SumTrans=$registro["SumTrans"];
		}

		$swAbono=0;
		$SQL="SELECT sum(MontoTrans) as SumTrans FROM FactTrans WHERE IdFactura='".$_POST["IdFactura"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$SumTrans=$SumTrans+$registro["SumTrans"];
			if ($registro["SumTrans"]>0) {
				$swAbono=1;
			}
		}

		if ($swAbono==1) {
			$MonTrans=($MonFac-$SumTrans);
		}else{
			if (($MonTrans-$SumTrans)>0) {
				if ($MonFac<=($MonTrans-$SumTrans)) {
					$MonTrans=$MonFac;
				}else{
					$MonTrans=($MonTrans-$SumTrans);
				}
			}		
		}

		$mysqli->query("INSERT INTO FactTrans VALUES('','".$_POST['IdFactura']."','$NFactura','$MonFac','".$_POST['IdTrans']."','$NOperacion','$MonTrans','".date('Y-m-d H:i:s')."')");


		// $_SESSION['NomServer']


		// $SQL="SELECT * FROM Servidores WHERE Id='".descript($_POST['KeyServer'])."'";
		// $resultados = $mysqli->query($SQL);
		// while ($registro = $resultados->fetch_assoc()) {
		// 	$NServer=$registro['Nombre'];
		// }


		$SQL="SELECT * FROM Bloqueos WHERE Nombre='".$_SESSION['NomServer']."' AND Estado='A'";
		$resultados = $mysqli->query($SQL);
		$SwBloqueo = $resultados->num_rows;
		if ($SwBloqueo>0) {
			$mysqli->query("UPDATE Bloqueos SET Estado='X' WHERE Nombre='".$_SESSION['NomServer']."' AND Estado='A'");
		}
		// $SwBloqueo="";


		$mysqli->close();
	}

	if (isset($_POST['IdFactran']) && $_POST['IdFactran']!="" ) { /////elimina
		$mysqli=ConCobranza();
		$SQL="SELECT * FROM FactTrans WHERE Id='".$_POST['IdFactran']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$LIdTYran=$registro['IdTrans'];
		}

		$SQL="SELECT * FROM Transferencias WHERE Id='$LIdTYran' AND Banco='BANCO MASTECNO'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$mysqli->query("DELETE FROM Transferencias WHERE Id='$LIdTYran' AND Banco='BANCO MASTECNO'");
		}

		$mysqli->query("DELETE FROM FactTrans WHERE Id='".$_POST['IdFactran']."'");
		$mysqli->close();
	}

	if ($_POST["l1"]!="" && $_POST["l2"]!="" && $_POST["l3"]!="" && $_POST['pwd']=="@Adminssv") {   //// insert excepciones

		$mysqli=ConCobranza();
		$SQL="SELECT * FROM Facturas WHERE Id='".$_POST["l3"]."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$MonFac=$registro["Total"];
			$NFactura=$registro["Folio"];
			$RFactura=$registro["Rut"];
		}
		$nOpera="D-".date('YmdHis');

		$mysqli->query("INSERT INTO Transferencias VALUES('','".date('Y-m-d')."','$nOpera','$MonFac','BANCO MASTECNO','$RFactura','$RFactura','A','".date('Y-m-d')."','".date("H:i:s")."');");

		$SQL="SELECT max(Id) as FId FROM Transferencias WHERE Id>0";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$IdTrans=$registro["FId"];
		}

		$mysqli->query("INSERT INTO FactTrans VALUES('','".$_POST["l3"]."','$NFactura','$MonFac','$IdTrans','$nOpera','$MonFac','".date('Y-m-d')."');");
		$mysqli->close();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; ">
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

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script type="text/javascript">
			function Asociar(v1,v2,v3){
				form1.FecAsociado.value=v1;
				form1.DocAsociado.value=v2;
				form1.IdFactura.value=v3;
			}
			function Confirmar(x1,x2){

				form1.IdTrans.value=x1;
				form1.NOperacion.value=x2;
				var r = confirm("Asociar la Factura N: "+form1.DocAsociado.value+", con las transferencia N: "+form1.NOperacion.value);
				if (r == true) {
					//alert("You pressed OK!");
					form1.submit();
				} else {
					alert("Operacion Cancelada");
				}
			}

			function ConfirmarX(l1,l2, l3){
				form1.l1.value=l1;
				form1.l2.value=l2;
				form1.l3.value=l3;
			}
			function Autor(){
				form1.FecAsociado.value="";
				form1.DocAsociado.value="";
				form1.IdFactura.value="";
				form1.submit();		
			}
			function EliReg(r1) {
				form1.IdFactran.value=r1;
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
			<br>
			<!-- <div class="col-md-2"></div> -->
			<div class="col-md-12 text-left">
				<form action="#" name="form1" id="form1" method="POST">
				<input type="hidden" name="IdTrans" id="IdTrans">
				<input type="hidden" name="NOperacion" id="NOperacion">
				<input type="hidden" name="IdFactura" id="IdFactura">
				<input type="hidden" name="IdFactran" id="IdFactran">


				<br>

				<div class="flex justify-start items-start gap-6">
					<div class="w-2/12 flex items-end">
						<a href="../Facturas" class="bg-orange-300 hover:bg-orange-400 text-sm text-white font-medium py-1 px-2 border-2 border-orange-300 shadow rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2">
							<i class="fa-solid fa-arrow-left mr-1"></i>Volver
						</a>
					</div>
					<div class="w-10/12">
						<div> 
							<!-- <label for="anioFiltro" class="input-group-addon">Filtrar por Año:</label> -->
							<select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="anioFiltro" name="anioFiltro" onchange="this.form.submit()">
								<?php
									for($anio = 2018; $anio <= $anioActual; $anio++) {
										$selected = ($anio == $anioSeleccionado) ? 'selected' : '';
										echo '<option value="'.$anio.'" '.$selected.'>'.$anio.'</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div>
				<br>

				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr style="background-color: #e51c20; color: #FFF;">
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="1%"></th>
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Rut</th>
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;">Raz&oacute;n Social</th>
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Tipo</th>
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Folio</th>
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Fecha</th>
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Monto</th>
							<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Adeudado</th>
						</tr>
					</thead>
					<tbody id="Empresas">
						<?php
							$mysqli=ConCobranza();

							$SQL="SELECT * FROM Maestra WHERE IdServer='".$_SESSION['xIdServer']."'";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$RutFactura=$registro['RutFactura'];  
							}

							$Cadera="";
							$SQL="SELECT * FROM FacturasRut WHERE IdServer='".$_SESSION['xIdServer']."'";
							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								$Cadera=$Cadera."OR Rut='".$registro['RutFactura']."'";  
							}

							$SQL="SELECT * FROM Facturas WHERE Rut='$RutFactura'"; 
							$SQL=$SQL.$Cadera;

							$SQL=$SQL." AND (Fecha >= '".$anioSeleccionado."-01-01 00:00:00') AND (Fecha <= '".$anioSeleccionado."-12-31 23:59:59')";

							$SQL=$SQL." ORDER BY Fecha DESC";

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {

								$SumTrans=0;

								$SQL1="SELECT sum(MontoTrans) AS SumTrans FROM FactTrans WHERE IdFactura='".$registro["Id"]."'";
								$resultados1 = $mysqli->query($SQL1);
								while ($registro1 = $resultados1->fetch_assoc()) {
									$SumTrans=$registro1["SumTrans"];
								}

								$Info="";
								$SQL1="SELECT * FROM FactTrans WHERE IdFactura='".$registro["Id"]."'";
								$resultados1 = $mysqli->query($SQL1);
								while ($registro1 = $resultados1->fetch_assoc()) {
									$SQL2="SELECT * FROM Transferencias WHERE Id='".$registro1["IdTrans"]."'";
									$resultados2 = $mysqli->query($SQL2);
									while ($registro2 = $resultados2->fetch_assoc()) {
										$Info=$Info."Operaci&oacute;n: ".$registro2["NOperacion"]."<br>Fecha: ".date('d-m-Y',strtotime($registro2["Fecha"]))."<br>Monto: ".$registro2["Monto"]."<br>";

										if ($_SESSION['NOMBRE']=="Admini") {
											$Info=$Info.'
												<button type="button" class="btn btn-xs btn-danger" onclick="EliReg('.$registro1["Id"].')">
													<span class="glyphicon glyphicon-trash"></span>
												</button>
											';
										}
									}
								}

								$XTipo="";
								if ($registro["IdDocumento"]=="34") {
									$XTipo="FacExe";
								}
								if ($registro["IdDocumento"]=="33") {
									$XTipo="FacAfe";
								}
								if ($registro["IdDocumento"]=="61") {
									$XTipo="NotCre";
								}
								if ($registro["IdDocumento"]=="39") {
									$XTipo="BolEle";
								}

								$NC = 0;

								if ($registro["CnNuRefe"]>0 && ($registro["CnRefe"]=="34" || $registro["CnRefe"]=="33" || $registro["CnRefe"]=="39") ) {
									$NC = 1;
								}else{
									$SQL1="SELECT * FROM Facturas WHERE CnNuRefe='".$registro["Folio"]."' AND Rut='".$registro["Rut"]."' AND (CnRefe='34' || CnRefe='33' || CnRefe='39')";
									$Res = $mysqli->query($SQL1);
									$NC = $Res->num_rows;
								}

								if ($NC>0) {
									$ColorX="#caf3ff";
								}else{
									$ColorX="#ffcaca";
								}

								if ($SumTrans>=$registro["Total"]) {
									echo '
									<tr class="bg-white hover:bg-gray-50 border border-gray-300 transition duration-150 ease-in-out" style="background-color:#dfffca;">
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">

										</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["Rut"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">'.$registro["RSocial"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$XTipo.'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["Folio"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.number_format($registro["Total"],0,",",".").'</td>
										<!--<td style="text-align: center;">'.number_format($registro["Total"]-$SumTrans,0,",",".").'</td>-->
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$Info.'</td>
									</tr>
									';
								}else{
									echo '
									<tr class="bg-white hover:bg-gray-50 border border-gray-300 transition duration-150 ease-in-out" style="background-color:'.$ColorX.';" >
										<td class="flex justify-center items-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">';

										if ($NC==0) {
											echo '
											<button type="button" class="bg-orange-300 text-sm hover:bg-orange-400 text-gray-600 font-medium py-1 px-2 border-2 border-orange-300 rounded-md transition duration-200 focus:outline-none" data-modal-target="myModal" data-modal-toggle="myModal" onclick="Asociar(\''.date('d-m-Y',strtotime($registro["Fecha"])).'\',\''.$registro["Folio"].'\',\''.$registro["Id"].'\')">
												<i class="fa-solid fa-thumbtack text-orange-800"></i>
											</button>
											';
										}
									echo '
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["Rut"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center" data-modal-target="autorizaModal" data-modal-toggle="autorizaModal" onclick="ConfirmarX(\''.date('d-m-Y',strtotime($registro["Fecha"])).'\',\''.$registro["Folio"].'\',\''.$registro["Id"].'\')">'.$registro["RSocial"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$XTipo.'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["Folio"].'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.number_format($registro["Total"],0,",",".").'</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><strong>'.number_format($registro["Total"]-$SumTrans,0,",",".").'</strong><br>'.$Info.'</td>
									</tr>
									';									
								}
							}
							$mysqli->close();
						?>
					</tbody>
				</table>
 <!-- ondblclick="ConfirmarX(\''.date('d-m-Y',strtotime($registro["Fecha"])).'\',\''.$registro["Folio"].'\',\''.$registro["Id"].'\')" -->


					<div id="autorizaModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
						<div class="relative p-4 w-full max-w-2xl max-h-full">
							<!-- Modal content -->
							<div class="relative bg-white rounded-lg shadow-sm">
								<!-- Modal header -->
								<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
									<h3 class="text-xl font-semibold text-gray-900">
										Autorización Manual
									</h3>
									<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="autorizaModal">
										<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
											<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
										</svg>
										<span class="sr-only">Close modal</span>
									</button>
								</div>
								<!-- Modal body -->
								<div class="p-4 md:p-5 space-y-4">
									<div class="grid grid-cols-1 md:grid-cols-1 mb-2 mt-1">
										<label for="pwd" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
											<i class="fa-solid fa-key mr-1"></i>Password
										</label>
										<input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="pwd" name="pwd">
									</div>

									<input type="hidden" name="l1" id="l1">
									<input type="hidden" name="l2" id="l2">
									<input type="hidden" name="l3" id="l3">
								</div>
								<!-- Modal footer -->
								<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
									<button data-modal-hide="autorizaModal" type="button" onclick="Autor()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
										Confirmar
									</button>
									<button data-modal-hide="autorizaModal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
										Cancelar
									</button>
								</div>
							</div>
						</div>
					</div>


					<div id="myModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
						<div class="relative p-4 w-full max-w-5xl max-h-full">
							<!-- Modal content -->
							<div class="relative bg-white rounded-lg shadow-sm">
								<!-- Modal header -->
								<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
									<h3 class="text-xl font-semibold text-gray-900">
										<?php
											$mysqli=ConCobranza();
											$SQL="SELECT max(Fecha) As UFecha FROM Transferencias";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$UFecha=date('d-m-Y',strtotime($registro["UFecha"]));
											}
											$mysqli->close();
										?>							
										<h4 class="text-xl font-semibold text-gray-900">Transferencias Recibidas (Cartola <?php echo $UFecha; ?>)</h4>
									</h3>
									<button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="myModal">
										<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
											<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
										</svg>
										<span class="sr-only">Close modal</span>
									</button>
								</div>
								<!-- Modal body -->
								<div class="p-4 md:p-5 space-y-4">
									

									<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2 mt-1">
										<div>
											<label for="FecAsociado" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
												<i class="fa-solid fa-calendar mr-1"></i>Fecha
											</label>
											<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="FecAsociado" name="FecAsociado" value="" disabled="true">
										</div>

										<div>
											<label for="DocAsociado" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
											<i class="fa-solid fa-file-invoice mr-1"></i>Documentos
											</label>
											<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="DocAsociado" name="DocAsociado" value="" disabled="true">
										</div>
									</div>


									<table class="min-w-full divide-y divide-gray-200">
										<thead class="bg-gray-50">
											<tr style="background-color: #e51c20; color: #FFF;">
												<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Fecha</th>
												<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;">Banco</th>
												<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="">N. Operaci&oacute;n</th>
												<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="">N. Cuenta</th>
												<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Monto</th>
												<th class="px-6 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider" style="text-align: center;" width="10%">Saldo</th>
											</tr>
										</thead>
										<tbody id="Empresas">
											<?php
												$mysqli=ConCobranza();

												$SQL='SELECT Transferencias.Id, TransferenciasRut.IdServer, TransferenciasRut.Rut, Transferencias.Fecha, Transferencias.Banco, Transferencias.NOperacion, Transferencias.Cta, Transferencias.Monto, Transferencias.Estado
												FROM TransferenciasRut LEFT JOIN Transferencias ON TransferenciasRut.Rut = Transferencias.Rut
												WHERE (((TransferenciasRut.IdServer)="'.$_SESSION['xIdServer'].'")
												AND ((Transferencias.Estado)="A") AND (Transferencias.Fecha >= "2023-01-01 00:00:00"))
												ORDER BY Transferencias.Fecha DESC;';
												// AND ((Transferencias.Estado)="A") AND (Transferencias.Fecha >= "2022-08-01 00:00:00"))
												
												$resultados = $mysqli->query($SQL);
												while ($registro = $resultados->fetch_assoc()) {
													$SumTrans=0;
													$SQL1="SELECT sum(MontoTrans) AS SumTrans FROM FactTrans WHERE idTrans='".$registro["Id"]."'";
													$resultados1 = $mysqli->query($SQL1);
													while ($registro1 = $resultados1->fetch_assoc()) {
														$SumTrans=$registro1["SumTrans"];
													}

													if ($SumTrans<$registro["Monto"]) {
														echo '
														<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out" onclick="Confirmar(\''.$registro["Id"].'\',\''.$registro["NOperacion"].'\')">
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium" style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium">'.$registro["Banco"].'</td>
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium" style="text-align: center;">'.$registro["NOperacion"].'</td>
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium" style="text-align: center;">'.$registro["Cta"].'</td>
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium" style="text-align: center;">'.number_format($registro["Monto"],0,",",".").'</td>
															<td class="px-6 py-2 whitespace-nowrap text-sm font-medium" style="text-align: center;">'.number_format(($registro["Monto"]-$SumTrans),0,",",".").'</td>
														</tr>
														';
													}
												}
												$mysqli->close();
											?>
										</tbody>
									</table>
									

								</div>
								<!-- Modal footer -->
								<div class="flex justify-end items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
									<button data-modal-hide="myModal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
										Cancelar
									</button>
								</div>
							</div>
						</div>
					</div>


				</form>
			</div>

		</div>
		</div>

		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>

</html>


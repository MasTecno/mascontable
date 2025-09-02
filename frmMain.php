<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
	include 'clases/clasesCss.php';

	if (isset($_POST['swUp']) && $_POST['swUp']=="N" ) {
		$_SESSION['SWFACTURA']="N";
	}

	if(isset($_POST['CTEmpre']) && $_POST['CTEmpre']!=""){
		// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli = xconectar("root", "", "mastecno_server08");
		$SQL="SELECT * FROM CTEmpresas  WHERE id='".descript($_POST['CTEmpre'])."' AND user=0";
		// echo $SQL;
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==1) {
			
			$SQL="SELECT * FROM CTEmpresas WHERE id='".descript($_POST['CTEmpre'])."'";
			$resultados = $mysqli->query($SQL);
			while ($registro = $resultados->fetch_assoc()) {
				$_SESSION['PERIODO']=$registro["periodo"];
				$_SESSION['RAZONSOCIAL']=$registro["razonsocial"];              
				$_SESSION['RUTEMPRESA']=$registro["rut"];              
				$_SESSION['COMPROBANTE']=$registro["comprobante"];
				$_SESSION['CCOSTO']=$registro["ccosto"];
				$_SESSION['PLAN']=$registro["plan"];
			}
			$mysqli->query("UPDATE CTEmpresas SET  user='".$_SESSION['XId']."' WHERE id='".descript($_POST['CTEmpre'])."'");
		}
		$mysqli->close();
	}

	if(isset($_POST['sw']) && $_POST['sw']==3){
		// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli = xconectar("root", "", "mastecno_server08");
		$mysqli->query("UPDATE CTEmpresas SET user=0 WHERE user='".$_SESSION['XId']."'");
		$mysqli->close();

		$_SESSION['RAZONSOCIAL']="";
		$_SESSION['PERIODO']="";
		$_SESSION['RUTEMPRESA']="";
		$_SESSION['COMPROBANTE']="";
		$_SESSION['CCOSTO']="";
		$_SESSION['PLAN']="";
	}

	// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$mysqli = xconectar("root", "", "mastecno_server08");
	if ($_SESSION['XId']!=""){
		$SQL="SELECT * FROM CTEmpresas WHERE user='".$_SESSION['XId']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$_SESSION['PERIODO']=$registro["periodo"];
			$_SESSION['RAZONSOCIAL']=$registro["razonsocial"]; 
			$_SESSION['RUTEMPRESA']=$registro["rut"];  
			$_SESSION['COMPROBANTE']=$registro["comprobante"];
			$_SESSION['CCOSTO']=$registro["ccosto"];
			$_SESSION['PLAN']=$registro["plan"];
		}
	}

	$SQL="SELECT * FROM CTParametros WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if($registro['tipo']=="IVA"){
			$DIVA=$registro['valor']; 
		}

		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_LIST"){
			$DLIST=$registro['valor'];  
		}

		if($registro['tipo']=="TIPO_MONE"){
			$DMONE=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 

		if($registro['tipo']=="SALD_ACUM"){
			$SACUM=$registro['valor'];  
		} 
	}

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];

	$FECHA=date("Y-m-d");
	$Hora1="09:00:00";
	$Hora2="12:00:00";

	$HoraActual=date("H:i:s");

	$SwHora="N";
	// $mysqli=conectarUnion();
	$mysqli=ConCobranza();

	if ($HoraActual>=$Hora1 && $HoraActual<$Hora2) {
		$SQL="SELECT * FROM CTTablaIndicadores WHERE Fecha='$FECHA'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$SwHora="S";
		}
	}else{

		if ($HoraActual>=$Hora2) {
			$SQL="SELECT * FROM CTTablaIndicadores WHERE Fecha='$FECHA' AND hora>'$Hora2'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$SwHora="S";
			}
		}        
	}


	if ($SwHora=="S") {
		$apiUrl = 'https://mindicador.cl/api';

		if (ini_get('allow_url_fopen') ) {
			// $json = file_get_contents($apiUrl);

			$arrContextOptions=array(
				"ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);  
			$json = file_get_contents($apiUrl, false, stream_context_create($arrContextOptions));
			
			// echo $response;
		}else{
			//De otra forma utilizamos cURL
			$curl = curl_init($apiUrl);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($curl);
			curl_close($curl);
		}

		$dailyIndicators = json_decode($json);

		$XDolar=$dailyIndicators->dolar->valor;
		$XEuro=$dailyIndicators->euro->valor;
		$XUF=$dailyIndicators->uf->valor;
		$XUTM=$dailyIndicators->utm->valor;
		$XIPC=$dailyIndicators->ipc->valor;
		$XIVP=$dailyIndicators->ivp->valor;
		$XIMACEC=$dailyIndicators->imacec->valor;
		$mysqli->query("INSERT INTO CTTablaIndicadores VALUES('','$FECHA','".date("H:i:s")."','$XDolar','$XEuro','$XUF','$XUTM','$XIPC','$XIVP','$XIMACEC','A')");
	}
	
	$mysqli->close();

	if ($_SESSION['NomServer']!="") {
		$mysqli=ConCobranza();

		$ActuDatos=1;
		$SQL="SELECT * FROM Servidores WHERE Nombre='".$_SESSION['NomServer']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$_SESSION['xIdServer']=$registro['Id'];  

			$SQL1="SELECT * FROM Contacto WHERE IdServer='".$_SESSION['xIdServer']."' AND Estado='U'";
			$resultados1 = $mysqli->query($SQL1);
			$ActuDatos = $resultados1->num_rows;
		}
		
		$ConCon=1;
		$SQL1="SELECT * FROM Contacto WHERE IdServer='".$_SESSION['xIdServer']."'";
		$resultados1 = $mysqli->query($SQL1);
		$ConCon = $resultados1->num_rows;

		if ($ConCon==0) {
			$ActuDatos=1;
		}

		$mysqli->close();
	}

?>
<!DOCTYPE html>
<html>
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
		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>



		<link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
<!-- <script type="module">
	import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

	createChat({
		webhookUrl: 'https://samito2015.app.n8n.cloud/webhook/397ec2ac-fd9f-4cfd-8049-10ecf7b9482e/chat',
		target: '#n8n-chat',
		mode: 'window',
		showWelcomeScreen: true,
		defaultLanguage: 'es',
		initialMessages: [
			'¡Hola! 👋',
			'Mi nombre es MasTecnoAI. Soy una inteligencia artificial que te ayudará con las dudas que tengas dentro de los sistemas de MasTecno.'
		],
		i18n: {
			es: {
				title: '¡Bienvenido a AgenteMasTecno!',
				subtitle: 'Inicia una conversación. Estamos aquí para ayudarte 24/7.',
				footer: '',
				getStarted: 'Nueva conversación',
				inputPlaceholder: 'Escribe tu pregunta...'
			}
		}
	});
</script> -->

		<script type="text/javascript">
			function CargaEmp(valor){
				form1.CTEmpre.value=valor;
				form1.submit();
			}

			function CamEmpr(){
				form1.CTEmpre.value="";
				form1.sw.value="3";
				form1.submit();  
			}

			function Factura(){
				fEstado.action="Facturas/";
				fEstado.submit();
			}

			function ConsultaS(){
				var url= "ConSaldo.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#ConSaldoGrilla').html(resp);
					}
				});
			}

			function Hoy(val){
				var url= "Notifica.php";
				form1.swDoc.value=val;
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						form1.swDoc.value="";
					}
				});
			}

			function Acu(){
				form1.swacu.value="S";
				ConsultaS();
			}

			function TransBanck(){
				form1.action = "transbank/";
				form1.submit();
			}
			function TermiCon(){
				form1.action = "TerCondi/";
				form1.submit();
			}

			<?php
				if(isset($_GET['msg']) && $_GET['msg']=="ParametrosOK"){
					echo "alert('Parámetros Globales actualizados correctamente');";
				}
			?>

		</script>
	</head>
	<body>

		<?php 
			include 'nav.php';
		?>

		<div class="col-md-12">
			<form action="#" method="POST" name="form1" id="form1">
				<input type="hidden" name="CTEmpre" id="CTEmpre">
				<input type="hidden" name="sw" id="sw">
				<input type="hidden" name="swUp" id="swUp">
				<input type="hidden" name="UpdEmp" id="UpdEmp">
				<input type="hidden" name="ModUpdEmp" id="ModUpdEmp">

				<?php
					if($RazonSocial!=""){
				?>
				<br>
				<div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 pt-0">
					<!-- Primera columna: 25% (3/12) -->
					<div class="flex flex-col w-full md:col-span-3">
						<div class="w-12/12 p-3">
							<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
								<div class="panel-heading text-center p-2.5 border rounded-t-md">Acceso Directo</div>
								<div class="flex flex-col gap-2 p-5 border-1 border-gray-600 bg-white/30">
									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "RVoucher/";} ?>" class="inline-flex border border-blue-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-book"></span> Registro de Voucher
									</a>						

									<!-- <div class="flex items-center">
											<div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
												<i class="fas fa-book text-blue-600"></i>
											</div>
											<div class="ml-3">
												<p class="text-sm font-medium text-gray-900">Registro de Voucher</p>
												<p class="text-xs text-gray-500">Gestionar asientos contables</p>
											</div>
									</div> -->
									<br>
									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "SincSII/";} ?>" class="inline-flex border border-blue-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-check"></span> Sincronizar SII</a>
									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "RGestionDoc/";} ?>" class="w-full inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-folder-open"></span> Gesti&oacute;n Documentos Electronicos</a>
									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "NCredito/";} ?>" class="w-full inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-folder-open"></span> Gesti&oacute;n Nota de Cr&eacute;dito</a>
									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "RComVen/index.php?Doc=1";} ?>" class="w-full inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-import"></span> Registro de Compras</a>
									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "RComVen/index.php?Doc=2";} ?>" class="w-full inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-export"></span> Registro de Ventas</a>

									<?php
										if($_SESSION['NomServer']=="Server48" || $_SESSION['NomServer']=="Server99"){
									?>
										<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "F29/";} ?>" class="inline-flex border border-blue-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" role="button" style="text-align: center;">
											<span class="glyphicon glyphicon-export"></span> F29</a>
									<?php
									}
									?>

									<br>	

									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "Honorarios/frmImportLibroHono.php";} ?>" class="inline-flex border border-blue-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-check"></span> Importar Honorarios CSV</a>
										<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "frmImportLibro.php";} ?>" class="inline-flex border border-blue-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-check"></span> Importar Compra/Ventas CSV</a>
									<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "Honorarios/";} ?>" class="w-full inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" role="button" style="text-align: center;">
										<span class="glyphicon glyphicon-header"></span> Registro de Honorarios</a>
									<br>

									<?php if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"): ?>
										<a href="<?php if ($_SESSION['ESTADOPERIODO']==1) { echo "#"; }else{ echo "GVoucher/";} ?>" class="w-full inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" role="button" style="text-align: center;">
											<span class="glyphicon glyphicon-usd"></span> Generador de Pagos</a>
									<?php endif ?>

								</div>
							</div>
						</div>
						<div class="w-12/12 p-3">
							<div style="background-color: hsl(0, 0%, 0%, 0);">
								<div class="bg-red-600 text-white text-center p-2.5 border rounded-t-md">Acceso Remoto</div>
								<div class="flex justify-center items-center border-1 border-gray-600 bg-white/30">
									<a class="border bg-white rounded-md shadow mt-3 mb-3" href="https://anydesk.com/es/downloads/windows?dv=win_exe" target="_blank">
										<img src="images/AnyDesk.png" class="img-thumbnail img-responsive" width="180" height="100" alt="Cinque Terre">
									</a>
								</div>
							</div>
							<p class="text-center p-1">Cierre Sesi&oacute;n:<?php echo date('H:i',$_SESSION['time_off']); ?></p>
						</div>


					</div>
				
					<!-- Segunda columna: 58% (7/12) -->
					<div class="flex flex-col w-full md:col-span-5">
						<div class="w-12/12 p-3">
							<div class="" style="background-color: hsl(0, 0%, 0%, 0);">
								<div class="bg-red-600 text-white text-center p-2.5 border-1 rounded-t-md">Registros Compra/Venta</div>
								<div class="bg-white/30 border-1 border-gray-600 p-5">

								<table class="min-w-full divide-y divide-gray-200 mb-6">
									<thead>
										<tr>
											<th class="text-left">Ventas</th>
											<th class="text-right" width="20%">Cantidad</th>
											<th class="text-right" width="20%">Neto</th>
											<th class="text-right" width="20%">IVA</th>
										</tr>
									</thead>
									<tbody class="divide-y divide-gray-500">
										<?php 
											$RutEmpresa=$_SESSION['RUTEMPRESA'];
											$PeriodoX=$_SESSION['PERIODO'];
											$SumNet=0;
											$SumIva=0;

											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											// $mysqli = xconectar("root", "", "mastecno_server08");
											////Ventas
											$SQL="SELECT * FROM CTTipoDocumento WHERE estado='A' ORDER BY id";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];
												$Cont=0;
												$Sexento=0;
												$Sneto=0;
												$Siva=0;


												$SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='V' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$SQL2="SELECT * FROM CTTipoDocumento WHERE id='".$registro1["id_tipodocumento"]."'";
													$resultados2 = $mysqli->query($SQL2);
													while ($registro2 = $resultados2->fetch_assoc()) {
														$operador=$registro2["operador"];
													}

													if($operador=="R"){
														$operador=-1;
													}else{
														$operador=1;
													}

													$Cont=$Cont+1;
													$Sexento=$Sexento+($registro1["exento"]);
													$Sneto=$Sneto+($registro1["neto"]);
													$Siva=$Siva+($registro1["iva"]);
												}

												if ($Cont>0) {

												echo '
													<tr>
														<td>'.strtoupper($registro["nombre"]).'</td>
														<td style="text-align: right;">'.$Cont.'</td>
														<td style="text-align: right;">'.number_format($Sneto+$Sexento, $NDECI, $DDECI, $DMILE).'</td>
														<td style="text-align: right;">'.number_format($Siva, $NDECI, $DDECI, $DMILE).'</td>
													</tr>
												';            
													$SumNet=$SumNet+(($Sneto+$Sexento)*$operador);
													$SumIva=$SumIva+(($Siva)*$operador);
												}
											}

											$SQL="SELECT sum(Neto) as Snet, sum(IVA) as Siva, sum(Total) as Stot, Periodo, DTE, keyas FROM CTBoletasDTE WHERE RutEmpresa='$RutEmpresa' AND periodo='$PeriodoX' GROUP BY Periodo, DTE ";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {

												$SQL1="SELECT * FROM CTTipoDocumento WHERE tiposii='".$registro["DTE"]."'";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$nomdoc=$registro1["nombre"];
												}

												$CantLote =0;
												$SQL1="SELECT count(*) as T FROM CTBoletasDTE WHERE RutEmpresa='$RutEmpresa' AND DTE='".$registro["DTE"]."' AND periodo='$PeriodoX' GROUP BY Periodo, DTE ";
												$resultados1 = $mysqli->query($SQL1);
												while ($registro1= $resultados1->fetch_assoc()) {
													$CantLote = $registro1['T'];
												}

												$LExento=0;
												$LNeto=$registro["Snet"];
												$LIva=$registro["Siva"];
												$LRete=0;
												$LTotal=$registro["Stot"];

												if ($registro["DTE"]==38 || $registro["DTE"]==41) {
													$LExento=$registro["Snet"];
													$LNeto=0;
												}

												echo '
												<tr>
													<td>'.$nomdoc.'</td>
													<td style="text-align: right;">'.$CantLote.'</td>
													<td style="text-align: right;">'.number_format($LNeto, $NDECI, $DDECI, $DMILE).'</td>
													<td style="text-align: right;">'.number_format($LIva, $NDECI, $DDECI, $DMILE).'</td>
												</tr>
												';
												$SumNet=$SumNet+($LNeto*$operador);
												$SumIva=$SumIva+($LIva*$operador);

											}
												echo '
												<tr>
													<td><strong>Totales</strong></td>
													<td style="text-align: right;"></td>
													<td style="text-align: right;"><strong>'.number_format($SumNet, $NDECI, $DDECI, $DMILE).'</strong></td>
													<td style="text-align: right;"><strong>'.number_format($SumIva, $NDECI, $DDECI, $DMILE).'</strong></td>
												</tr>
												';   
										?>

									</tbody>
								</table>

								<table class="min-w-full divide-y divide-gray-200 ">
									<thead>
										<tr>
											<th class="text-left">Compras</th>
											<th class="text-right" width="20%">Cantidad</th>
											<th class="text-right" width="20%">Neto</th>
											<th class="text-right" width="20%">IVA</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											$SumNet=0;
											$SumIva=0;

											$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
											// $mysqli = xconectar("root", "", "mastecno_server08");
											////Compras
											$SQL="SELECT * FROM CTTipoDocumento WHERE estado='A' ORDER BY id";
											$resultados = $mysqli->query($SQL);
											while ($registro = $resultados->fetch_assoc()) {
												$IDDOC=$registro["id"];
												$Cont=0;
												$Sexento=0;
												$Sneto=0;
												$Siva=0;


												$SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='C' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY rut, fecha";

												$resultados1 = $mysqli->query($SQL1);
												while ($registro1 = $resultados1->fetch_assoc()) {
													$SQL2="SELECT * FROM CTTipoDocumento WHERE id='".$registro1["id_tipodocumento"]."'";
													$resultados2 = $mysqli->query($SQL2);
													while ($registro2 = $resultados2->fetch_assoc()) {
														$operador=$registro2["operador"];
													}

													if($operador=="R"){
														$operador=-1;
													}else{
														$operador=1;
													}

													$Cont=$Cont+1;
													$Sexento=$Sexento+($registro1["exento"]);
													$Sneto=$Sneto+($registro1["neto"]);
													$Siva=$Siva+($registro1["iva"]);
												}

												if ($Cont>0) {

												echo '
													<tr>
														<td>'.strtoupper($registro["nombre"]).'</td>
														<td style="text-align: right;">'.$Cont.'</td>
														<td style="text-align: right;">'.number_format($Sneto+$Sexento, $NDECI, $DDECI, $DMILE).'</td>
														<td style="text-align: right;">'.number_format($Siva, $NDECI, $DDECI, $DMILE).'</td>
													</tr>
												';            
													$SumNet=$SumNet+(($Sneto+$Sexento)*$operador);
													$SumIva=$SumIva+(($Siva)*$operador);
												}

											}

											echo '
											<tr>
												<td><strong>Totales</strong></td>
												<td style="text-align: right;"></td>
												<td style="text-align: right;"><strong>'.number_format($SumNet, $NDECI, $DDECI, $DMILE).'</strong></td>
												<td style="text-align: right;"><strong>'.number_format($SumIva, $NDECI, $DDECI, $DMILE).'</strong></td>
											</tr>
											';   

										?>
									</tbody>
								</table>

								</div>
							</div>
							<div class="panel panel-default mt-5" style="background-color: hsl(0, 0%, 0%, 0);">
								<div class="panel-heading text-center p-2.5 border-1 rounded-t-md">Observaciones a revisar</div>
								<div class="panel-body p-5 bg-white/30 border-1 border-gray-600">
									<script>
										function RevContab(){
											var url= "frmMainObservaciones.php";
											$.ajax({
												type: "POST",
												url: url,
												dataType: 'json',
												data: $('#form1').serialize(),
												success:function(resp){
													// $("#UFecha").val(resp.dato1);
													// $("#NFPago").val(resp.dato2);

													document.getElementById("sw1_OK").style.display = 'none';
													document.getElementById("sw1_ER").style.display = 'none';
													document.getElementById("sw2_OK").style.display = 'none';
													document.getElementById("sw2_ER").style.display = 'none';
													
								
													if(resp.dato1==""){
														document.getElementById("sw1_OK").style.display = 'inline';
													}else{
														document.getElementById("sw1_ER").style.display = 'inline';
														$("#msw1").html(resp.dato1);
													}

													
													if(resp.dato2==""){
														document.getElementById("sw2_OK").style.display = 'inline';
													}else{
														document.getElementById("sw2_ER").style.display = 'inline';
														$("#msw2").html(resp.dato2);
													}
												}
											});	
										}
										
										function MayorCta(valor){
											form1.CtaMayor.value=valor;
											form1.anual.value=1;
											form1.method="POST";
											form1.target="_blank";
											form1.action="../Mayor/";
											form1.submit();
											form1.target="";
											form1.action="#";
										}


									</script>


									<table class="min-w-full divide-y divide-gray-200">
										<thead>
											<tr>
												<th class="text-center" width="1%">Estado</th>
												<th class="text-left px-2">Detalle</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="text-align: center;">
													<span id="sw1_OK" class="glyphicon glyphicon-ok-sign" style="color: #24bb09; display:none;" title=""></span>
													
													<span id="sw1_ER" class="glyphicon glyphicon-remove-sign" style="color: #bb0909; display:none;"></span>
												</td>
												<td class="px-2 py-0.5 whitespace-nowrap text-xs font-medium text-gray-900">
													Saldos de Ctas de Activo y Pasivos.<br>
													<l id="msw1"></l>
													<input type="hidden" name="CtaMayor" id="CtaMayor">
													<input type="hidden" name="anual" id="anual">
												</td>
											</tr>
											<tr>
												<td style="text-align: center;">
													<span id="sw2_OK" class="glyphicon glyphicon-ok-sign" style="color: #24bb09; display:none;"></span>
													<span id="sw2_ER" class="glyphicon glyphicon-remove-sign" style="color: #bb0909; display:none;"></span>
												</td>
												<td class="px-2 py-0.5 whitespace-nowrap text-xs font-medium text-gray-900">
													Asientos a revisar.<br>
													<l id="msw2"></l>
												</td>
											</tr>
										</tbody>
									</table>
									<button type="button" class="inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200 mt-3" onclick="RevContab()"><i class="fa fa-refresh mr-1"></i> Revisar Contabilidad</button>
								</div>
							</div>


						</div>
					</div>

					<!-- Tercera columna: 17% (2/12) -->
					<div class="col-md-4 p-3 md:col-span-4">
						<button type="button" class="w-full inline-flex border border-blue-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" onclick="CamEmpr()"><i class="fa fa-power-off mr-1"></i> Cambiar Empresa</button>
						<br>

						<button data-modal-target="default-modal" data-modal-toggle="default-modal"  type="button" class="mt-2 mb-2 w-full inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
							<i class="fa-solid fa-pen-to-square mr-1 text-white"></i>Modificar Parametros
						</button>

						<?php 
							if($_SESSION['CONTRATO']=="N" && $_SESSION['ROL']=="A"){
								// echo '
								// 	<br>
								// 	<button type="button" class="btn btn-warning btn-block" onclick="TermiCon()"><i class="glyphicon glyphicon-pencil"></i> Firma Términos y Condiciones</button>
								// ';
							}
						?>
						<?php 
							if ($_SESSION['ESTADOPERIODO']==1) {
								echo '<br><p style="text-align: center; font-size: 16px;"><strong>PERIODO CERRADO</strong></p>';							
							}else{
								echo '<br><p style="text-align: center; font-size: 16px;"><strong>PERIODO ABIERTO</strong></p>';
							}

							include('frmMainModDatos.php');
						?>
						<br>

						<input type="hidden" name="swacu" id="swacu" value="">
						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading text-center p-2.5 border-1 rounded-t-md">Consulta de Saldos</div>
							<div class="panel-body p-3 border-1 border-gray-600 bg-white/30" id="ConSaldoGrilla">
							</div>
						</div>					
					</div>
				</div>

				<div class="clearfix"></div>
				<br>				

				<script type="text/javascript">
					ConsultaS();
				</script>

			<?php
				}else{ 
			?>
				<br>
				<div class="flex flex-col md:flex-row gap-3 p-5">

					<div class="w-full md:w-2/12">
						<div class="text-sm">
							<p><strong>Usuario:</strong> <?php echo $NomCont; ?></p>
							<p><strong>Server:</strong> <?php echo $_SESSION['NomServer']; ?></p>	
						</div>
							
						<?php 
							if($_SESSION['CONTRATO']=="N"){
								if($_SESSION['ROL']=="A"){
									echo "<script>
										alert('Para poder ingresar nuevamente a la plataforma, deber realizar la firma de los Términos y Condiciones de uso de la plataforma.');
										TermiCon();
									</script>";

									// echo '
									// 	<br>
									// 	<button type="button" class="btn btn-warning btn-block" onclick="TermiCon()"><i class="glyphicon glyphicon-pencil"></i> Firma Términos y Condiciones</button>
									// 	<br>
									// 	<strong>La fecha límite para la revisión y firma de los términos y condiciones es el 15 de abril de 2025.</strong>
									
									// <script>
									// 		alert(\'Te recordamos que debes firmar los Términos y Condiciones de uso de la plataforma.\\n⚠ En caso de no hacerlo, el sistema será inhabilitado, entendiendo que no aceptas las condiciones.\\n\\n🖱 Al ingresar a los sistemas, simplemente haz clic en el botón naranjo que dice:\\n👉 "Firmar Términos y Condiciones"\\ny completa el proceso en unos pocos pasos.\\n\\n🤝 Agradecemos tu comprensión y colaboración.\\nwww.mastecno.cl\');
									// </script>
									// ';
								}else{
									echo "<script>
										alert('Por el momento la plataforma no esta habilitada para el uso de los usuarios. \\nFavor contactar con la cuenta principal de la empresa, para realizar la firma de los Términos y Condiciones de uso de la plataforma.');
										window.location='../';
									</script>";
								}
							}
						?>

							
					</div>
					<div class="w-full md:w-10/12">
						<div class="relative">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<i class="fa fa-search text-gray-400"></i>
							</div>
							<input class="<?php input_css(); ?> pl-8" id="myInput" name="myInput" type="text" placeholder="Buscar...">
						</div>
						<div class="overflow-x-auto mt-3 border-2 border-gray-200 rounded-md">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr class="bg-red-500">
										<th class="px-6 py-2 text-xs font-medium text-white text-center uppercase tracking-wider" width="10%">Rut</th>
										<th class="px-6 py-2 text-xs font-medium text-white text-center uppercase tracking-wider" width="10%">Raz&oacute;n Social</th>
										<th class="px-6 py-2 text-xs font-medium text-white text-center uppercase tracking-wider" width="10%">Periodo</th>
										<th class="px-6 py-2 text-xs font-medium text-white text-center uppercase tracking-wider" width="15%">Uso</th>
									</tr>
								</thead>
								<tbody id="Empresas">
									<?php
										$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
										// $mysqli = xconectar("root", "", "mastecno_server08");

										$SQL1="SELECT * FROM CTContadoresAsignado WHERE idcontador='".$_SESSION['XId']."'";
										$resultados1 = $mysqli->query($SQL1);
										$row_cnt = $resultados1->num_rows;

										if ($row_cnt==0) {
											$SQL="SELECT * FROM  CTEmpresas WHERE estado='A' ORDER BY razonsocial";
										}else{
											$SQL="SELECT CTEmpresas.id, CTContadoresAsignado.idcontador, CTEmpresas.rut, CTEmpresas.razonsocial, CTEmpresas.periodo, CTEmpresas.estado, CTEmpresas.user
											FROM CTContadoresAsignado LEFT JOIN CTEmpresas ON CTContadoresAsignado.rutempresa = CTEmpresas.rut
											WHERE CTEmpresas.estado='A' AND idcontador='".$_SESSION['XId']."' ORDER BY CTEmpresas.razonsocial;";
										}

										$resultados = $mysqli->query($SQL);
										while ($registro = $resultados->fetch_assoc()) {

											$conta="";
											$Color="";
											$SQL1="SELECT * FROM  CTContadores WHERE id='".$registro["user"]."'";
											$resultados1 = $mysqli->query($SQL1);
											while ($registro1 = $resultados1->fetch_assoc()) {
												$conta=$registro1["nombre"];
												$Color="background-color: #fbe6e7;";
											}

											if ($registro["user"]>0) {
												echo '
												<tr style="'.$Color.'">
												';
											}else{
												echo '
												<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out" style="'.$Color.'" onclick="CargaEmp(\''.randomText(35).''.$registro["id"].''.randomText(8).'\')">
												';
											}
												echo '
													<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["rut"].'</td>
													<td class="px-1 py-1 text-xs font-medium text-gray-900 text-center">'.$registro["razonsocial"].'</td>
													<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$registro["periodo"].'</td>
													<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" style="text-align: center;">'.$conta.'</td>
												</tr>
												';
										}
										$mysqli->close();
									?>
								</tbody>
							</table>
						</div>
						
					</div>
				</div>
				<script>
					form1.myInput.focus();
				</script>
			<?php 

				if ($_SESSION['DocInpagos']>0) {
					foreach($_SESSION['DOCUMENTOS'] as $indice=>$LAsiento){
						if ($LAsiento['Docu']!="xxxx"){
							$DatosDoc=$DatosDoc.
							'
								<tr>
									<!--<td align="center">'.$LAsiento['RSocial'].'</td>
									<td>
										<input type="checkbox" name="check_list[]" value="\''.$LAsiento['TDoc'].''.$LAsiento['Docu'].'\'">
									</td>-->
									<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" align="center">'.$LAsiento['TDoc'].'</td>
									<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" align="center">'.$LAsiento['Docu'].'</td>
									<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" align="center">'.date('d-m-Y',strtotime($LAsiento['Fecha'])).'</td>
									<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" align="center">'.date('d-m-Y',strtotime($LAsiento['Fecha']."+ 10 days")).'</td>
									<td class="px-1 py-1 whitespace-nowrap text-sm font-medium text-gray-900" align="right">'.number_format($LAsiento['Monto'], $NDECI, $DDECI, $DMILE).'</td>
								</tr>
							';
						}
					}
					
					echo '
						<div class="modal fade" id="myModal" role="dialog">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Documentos Pendientes</h4>
							</div>
							<div class="modal-body">
								<table class="table table-bordered table-striped table-hover">
									<thead>
										<tr style="background-color: #e51c20; color: #FFF;">
											<!--<th>Empresa</th>
											<th></th>-->
											<th>Documento</th>
											<th>Folio</th>
											<th>Fecha</th>
											<th>Vencimiento</th>
											<th>Total</th>
										</tr>
									</thead>
									<tbody>
									'.$DatosDoc.'
									</tbody>
								</table>

								<strong>* Le recordamos que el sistema est&aacute; automatizado y se suspende al no registrar pago.</strong><br>
								Gracias por su atenci&oacute;n.

							</div>
							<div class="modal-footer">
								<input type="hidden" name="swDoc" id="swDoc">

								<div class="col-sm-4">
									<img src="./images/transbank.png" width="200px">
								</div>

								<div class="col-sm-4">
									<button type="button" class="btn btn-warning btn-block" style="background-color: #ff0c00; border-color: #ff0c00;" data-dismiss="modal" onclick="TransBanck()">Pago TransBank</button>
								</div>

								<div class="col-sm-4">
									<button type="button" class="btn btn-warning btn-block" style="background-color: #002bff; border-color: #002bff;" data-dismiss="modal" onclick="Hoy(\'P\')">Pago Realizado</button>
								</div>

								<!--<button type="button" class="btn btn-info" data-dismiss="modal" onclick="Hoy(\'H\')">Posponer Recordatorio por Hoy</button>-->
								
								

								<!--<button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>-->
							</div>
						</div>
						</div>
						<script>
							$(\'#myModal\').modal(\'show\');
						</script>	
					';
				}



				}
			
				// 'Docu'=>$registro["Folio"],
				// ''=>$XTipo,
				// 'Rut'=>$registro["Rut"],
				// 'RSocial'=>$registro["RSocial"],
				// ''=>$registro["Fecha"],
				// ''=>$registro["Total"]
				// print_r($_SESSION['DocInpagos']);

			?>

			

			</form>
		</div>

		<script>
			$(document).ready(function(){
				$("#myInput").on("keyup", function() {
					var value = $(this).val().toLowerCase();
					$("#Empresas tr").filter(function() {
						$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
					});
				});
			});

			<?php
				if ($ActuDatos==1 && $_SESSION['ROL']=="A" && $_SESSION['SWFACTURA']=='') {
					echo '
						var r = confirm("Con el fin de ir mejorando nuestro servicio, es que le solicitamos actualizar sus datos de Contacto y Facturaci\u00F3n \n\nDe Ante mano Muchas Gracias");
						if (r == true) {
							window.location="Facturas/Informacion.php";
						} else {
							form1.swUp.value="N";
							form1.submit();
						}
					';
				}

				if($RazonSocial!=""){
					echo '
						ConsultaS();
					';
				}
			?>

		</script>

		<div class="clearfix"></div>
		<?php include 'footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
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

		<script type="text/javascript">
			function GenLibro(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="ReportXLS.php";
				form1.submit();
				form1.target="";
				form1.action="#";
			}

			function GenLibroPDF(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="ReportPDF.php";
				form1.submit();
				form1.target="";
				form1.action="#";        
			}			

			function UpdtaMensual(){
				var url= "GrillaMensual.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
					}
				});
			}

			function UpdtaAnual(){
				var url= "GrillaAnual.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
					}
				});
			}

			function UpdtaBiAnual(){
				var url= "GrillaBiAnual.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
					}
				});
			}

			$( function() {
				$( "#fdesde" ).datepicker();
				$( "#fhasta" ).datepicker();
			} );

			function Rango(){
				form1.SwFecha.value="R";
				if (form1.TipoER.value=="A") {
					UpdtaAnual();
				}
			}

			function Visual(r1) {
				if(r1=="A"){
					form1.TipoER.value="A";
					UpdtaAnual();
				}
				if(r1=="B"){
					form1.TipoER.value="B";
					UpdtaBiAnual();
				}
				if(r1=="M"){
					form1.TipoER.value="M";
					UpdtaMensual();
				}
			}

			function Updta(){
				form1.fdesde.value="";
				form1.fhasta.value="";
				form1.SwFecha.value="";
				if (form1.TipoER.value=="A") {
					UpdtaAnual();
				}else{
					if (form1.TipoER.value=="B") {
						UpdtaBiAnual();
					}else{
						UpdtaMensual();
					}
				}
			}

			function Configu(){
				form1.action="frmResultadoConf.php";
				form1.submit();
			}
		</script>
	</head>
	<body onload="UpdtaAnual()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
			<form action="#" name="form1" id="form1" method="POST">

				<input type="hidden" name="anoselect" id="anoselect" value="<?php echo $_POST['anoselect']; ?>">
				<input type="hidden" name="tccosto" id="tccosto" value="<?php echo $_POST['tccosto']; ?>">
				<input type="hidden" name="SwFecha" id="SwFecha">
				<br>
					<div class="col-md-4">
						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
							<div class="panel-heading">Generar Libro</div>
							<div class="panel-body">

									<div class="col-md-6">
										<div class="input-group">
										<span class="input-group-addon">Desde</span>
										<input id="fdesde" name="fdesde" type="text" class="form-control text-right" size="10" maxlength="10">
										</div>
									</div>

									<div class="col-md-6">
										<div class="input-group">
										<span class="input-group-addon">Hasta</span>
										<input id="fhasta" name="fhasta" type="text" class="form-control text-right" size="10" maxlength="10">
										</div>
									</div>
									<div class="clearfix"></div>
									<br>
									<div class="col-md-12 text-right">
										<button type="button" class="btn btn-modificar" onclick="Rango()">Procesar</button><br>
										<l style="font-size: 10px;">* Solo aplica a Vista Resumen Anual</l>
									</div>
							</div>
						</div>
					</div>

					<div class="col-md-4 text-center">
						<h3>Estado de Resultado</h3>
						<div class="col-md-4">
							<div class="input-group">
								<span class="input-group-addon">A&ntilde;o</span>
								<select class="form-control" id="anoselect" name="anoselect" onchange="Updta()" required>
								<?php 
									$yoano=date('Y');
									$tano="2010";
									while($tano<=($yoano+1)){
										if ($dano==$tano) {
											echo "<option value ='".$tano."' selected>".$tano."</option>";
										}else{
											echo "<option value ='".$tano."'>".$tano."</option>";
										}
										$tano=$tano+1;
									}
								?>
								</select>
							</div>
						</div>


						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">Centro de Costo</span>
								<select class="form-control" id="SelCCosto" name="SelCCosto" onchange="Updta()">
								<option value=""></option>
								<?php 
									$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
									$StrCCosto="";

									$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
									$resultados = $mysqli->query($SQL);
									while ($registro = $resultados->fetch_assoc()) {
										if ($_POST['SelCCosto']==$registro['id']) {
											echo '<option value="'.$registro['id'].'" selected>'.$registro['nombre'].'</option>';
										}else{
											echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
										}
									}
									$mysqli->close();
								?>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12 text-center">
							<label class="radio-inline"><input type="radio" onclick="Visual('A')" name="optradio" checked>Resumen Anual</label>
							<label class="radio-inline"><input type="radio" onclick="Visual('B')" name="optradio">Resumen Bi-Anual</label>
							<label class="radio-inline"><input type="radio" onclick="Visual('M')" name="optradio">Comparativo Mensual</label>
							<input type="hidden" name="TipoER" id="TipoER" value="A">				
						</div>

						<br>
						<br>
						<l4 style="font-size: 12px; font-weight: 600;">Contador(es):</l4>

							<?php
								$Conta=0;
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

								$SQL="SELECT * FROM CTContadoresFirma WHERE Estado='A'";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									echo '
										<div class="checkbox">
											<label>
												<input type="checkbox" name="check_list[]" value="'.$registro["Id"].'">'.$registro['Rut'].', '.$registro['Nombre'].'
											</label>
										</div>
										';    
									$Conta=1;
								}
								if($Conta==0){
									echo '<p>Debe registrarse en menú mantenedor - contadores para aparecer en las opciones de firma.</p>';
								}
							?>

					</div>

					<div class="col-md-4">
						<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Generar Libro</div>
						<div class="panel-body">

							<div class="checkbox">
								<label><input type="checkbox" name="ConMem" id="ConMem" value="">Membrete Empresa</label>
							</div>						

							<div class="checkbox">
								<label><input type="checkbox" name="ConRep" id="ConRep" value="">Membrete Representante</label>
							</div>						

							<div class="checkbox">
								<label><input type="checkbox" name="MarSup" id="MarSup" value="">Margen Superior</label>
								<input class="text-right" type="text" name="nlines" id="nlines" value="4" maxlength="2" size="3">
							</div>

							<div class="checkbox">
								<label><input type="checkbox" name="MarFol" id="MarFol" value="" checked>Folio Inicial PDF</label>
								<input class="text-right" type="text" name="folio" id="folio" value="1" maxlength="20" size="3">
							</div>
							<br>
							<div class="col-md-4">
								<button type="button" class="btn btn-success btn-block" onclick="GenLibro()">Generar Excel</button>	
							</div>
							<div class="col-md-4">
								<button type="button" class="btn btn-danger btn-block" onclick="GenLibroPDF()">Generar PDF</button>
							</div>
							<div class="col-md-4">
								<button type="button" class="btn btn-warning btn-block" onclick="Configu()">Configuración</button>
							</div>



						</div>
						</div>
					</div>

				
				<div class="clearfix"></div>
				<br>
				<hr>
				<div class="container" style="width: 90%;">
					<div class="col-sm-12" style="font-size: 12px;" id="grilla">
						
					</div>
				</div>

			</form>
		</div>
		</div>

		<script type="text/javascript">
			$( "#fdesde" ).datepicker({
				dateFormat: "dd-mm-yy",
				firstDay: 1,
				dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
				dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
				monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
				monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
				onSelect: function(dateText) { 
				}
			});

			$( "#fhasta" ).datepicker({
				// Formato de la fecha
				dateFormat: "dd-mm-yy",
				firstDay: 1,
				dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
				dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
				monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
				monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
				onSelect: function(dateText) { 
				}
			});				
		</script>

		<?php include '../footer.php'; ?>
	</body>
</html>
<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script type="text/javascript">

		function CargaGrilla(){
			Proce();

			var url= "DetGrilla.php";

			$.ajax({
			type: "POST",
			url: url,
			data: $('#form1').serialize(),
			success:function(resp){
				$('#Grilla').html(resp);
				Fin();
			}

			});
		}
		function VisAnual() {
			if (form1.anual.value==0) {
				form1.anual.value=1;
			}else{
				form1.anual.value=0;
			}
			FbotonOn();
			CargaGrilla();
		}

		function FbotonOn() {
			var uno = document.getElementById('BtnVisual');
			if (uno.textContent == 'Visualizar Anual'){
				uno.textContent = 'Visualizar Mensual';
			}else{
				uno.textContent = 'Visualizar Anual'; 
			}
		}

		function GenLibro(){
			form1.method="POST";
			form1.target="_blank";
			form1.action="frmLibComVenXLS.php";
			form1.submit();
			form1.target="";
			form1.action="#";
		}

		function GenLibroPDF(){
			form1.method="POST";
			form1.target="_blank";
			form1.action="frmLibComVenPDF.php";
			form1.submit();
			form1.target="";
			form1.action="#";        
		}
		function ComVen() {

			if (form1.frm.value=="C") {
				form1.frm.value="V";
			}else{
				form1.frm.value="C";
			}
			Titulo();
			CargaGrilla();
		}

		function Titulo() {
			var uno = document.getElementById('titu');
			if (uno.textContent == 'Libro Compras'){
				uno.textContent = 'Libro Ventas';
			}else{
				uno.textContent = 'Libro Compras'; 
			}
		}

		function Proce(){
			document.getElementById("Mensa").style.display = 'inline';
		}

		function Fin(){
			document.getElementById("Mensa").style.display = 'none';
		}


	</script>

</head>
	<body onload="CargaGrilla()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" method="POST" name="form1" id="form1">

			<br>
			<div class="col-md-4">
			</div>
			<div class="col-md-4 text-center">
				<h3 id="titu">Libro Compras</h3>

				<div class="col-md-12 text-center">
					<label class="radio-inline"><input type="radio" onclick="ComVen()" name="optradio" checked>Compras</label>
					<label class="radio-inline"><input type="radio" onclick="ComVen()" name="optradio">Ventas</label>
					<input type="hidden" name="frm" id="frm" value="C">				
				</div>
				<div class="clearfix"></div>
				<br>

				<div class="col-md-6">
				<div class="input-group">
					<span class="input-group-addon">Mes</span>
					<select class="form-control" id="messelect" name="messelect" onchange="CargaGrilla()" required>
					<?php 
						$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
						$i=1;
						echo $dmes=$dmes*1;
						while($i<=12){
							if ($i==$dmes) {
								echo "<option value ='".$i."' selected>".$Meses[($i-1)]."</option>";
							}else{
								echo "<option value ='".$i."'>".$Meses[($i-1)]."</option>";
							}
							$i++;
						}
					?>
					</select>
				</div>
				</div>

				<div class="col-md-6">
				<div class="input-group">
					<span class="input-group-addon">A&ntilde;o</span>
					<select class="form-control" id="anoselect" name="anoselect" onchange="CargaGrilla()" required>
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

				<div class="clearfix"></div>
				<br>

				<button type="button" class="btn btn-modificar btn-block" id="BtnVisual" onclick="VisAnual()">Visualizar Anual</button>
				<input type="hidden" name="anual" id="anual" value="0">

			</div>
			<div class="col-md-4">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">
						<h3 class="panel-title">Opciones de Generaci&oacute;n</h3>
					</div>
					<div class="panel-body">
							<div class="checkbox">
								<label><input type="checkbox" name="ConMem" id="ConMem" value="">Membrete</label>
							</div>						

							<div class="checkbox">
								<label><input type="checkbox" name="ConRep" id="ConRep" value="">Insertar Representante Legal en Membrete</label>
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
							<div class="col-md-6">
								<button type="button" class="btn btn-success btn-block" onclick="GenLibro()">Generar Excel</button>	
							</div>
							<div class="col-md-6">
								<button type="button" class="btn btn-success btn-block" onclick="GenLibroPDF()">Generar PDF</button>
							</div>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>
			<br>

			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div class="input-group">
					<div class="alert alert-warning" id="Mensa" style="background-color: #fbc7c7; border-color: #b35c5c; display:none;">
						<strong>Generando!</strong> El proceso tomara un tiempo, dependiendo de la cantidad de registro.
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<br>

			<div class="col-md-12" id="Grilla">


				

			</div>
		</form>
		</div>
		</div>

	</body>
</html>
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

	if ($_SESSION['COMPROBANTE']=="S") {
		include('../ReparaDiario.php');
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">

			function Lista(){
				if (form1.SelLibro.value=="E") {
					var url= "ListaEgreso.php";
				}else{
					var url= "ListaIngreso.php";
				}

				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#DatosLista').html(resp);
					}
				});					
			}

			function printDiv(nombreDiv) {
				var contenido= document.getElementById(nombreDiv).innerHTML;
				var contenidoOriginal= document.body.innerHTML;
				document.body.innerHTML = contenido;
				window.print();
				document.body.innerHTML = contenidoOriginal;
			} 		
			function Upfrom(){
				form1.submit();
			}

			function RefMen(){
				form1.submit();
			}

			function GenLibro(){
				if (form1.SelPeriodo.value=="") {
					alert("No a selecionado un Periodo");
				}else{
					form1.method="POST";
					form1.target="_blank";
					if (form1.SelLibro.value=="E") {
						form1.action="ListaEgresoExcel.php";
					}else{
						form1.action="ListaIngresoExcel.php";
					}

					form1.submit();
					form1.target="";
					form1.action="#";
				}
			}
	</script>

	</head>
	<body onload="Lista()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<br>
			<div class="col-md-12 text-center">
				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Visualizar</div>
						<div class="panel-body">
							<?php
								//echo "----".$_POST['SelLibro']."----";
							?>
							<div class="col-md-12">
								<div class="input-group">
								<span class="input-group-addon">Libro</span>
								<select class="form-control" id="SelLibro" name="SelLibro" required>
									<option value="">Seleccionar</option>
									<option value="E" <?php if ($_POST['SelLibro']=="E") { echo "selected"; } ?>>Egreso</option>
									<option value="I" <?php if ($_POST['SelLibro']=="I") { echo "selected"; } ?>>Ingreso</option>
								</select>
								</div>
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-12">
								<div class="input-group">
								<span class="input-group-addon">Periodo</span>
									<select class="form-control" id="SelPeriodo" name="SelPeriodo" required>
										<option value="">Seleccionar</option>
										<option value="C">A&ntilde;o Completo</option>
										<?php
											$dmes = substr($Periodo,0,2);
											$dano = substr($Periodo,3,4);
											$dmes = 1;

											while ($dmes <= 12) {
												if ($dmes<=9) {
													$Xper="0".$dmes."-".$dano;
													if ($_POST['SelPeriodo']==$Xper) {
														echo '<option value="'.$Xper.'" selected>'.$Xper.'</option>';
													}else{
														echo '<option value="'.$Xper.'">'.$Xper.'</option>';
													}
													
												}else{
													$Xper=$dmes."-".$dano;
													if ($_POST['SelPeriodo']==$Xper) {
														echo '<option value="'.$Xper.'" selected>'.$Xper.'</option>';
													}else{
														echo '<option value="'.$Xper.'">'.$Xper.'</option>';
													}
												}
												$dmes++;
											}
										?>
									</select>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Visualizar</div>
						<div class="panel-body">
							<div class="col-md-12">

								<div class="col-md-12 text-center">
								<div class="checkbox" style="font-size: 12px;">
									<label class="checkbox-inline"><input type="checkbox" value="MMenbrete" name="MMenbrete" <?php if (isset($_POST['MMenbrete']) && $_POST['MMenbrete']!="") { echo "checked"; } ?> >Visualizar Membrete</label>

									<label class="checkbox-inline"><input type="checkbox" value="ApliNeto" name="ApliNeto" <?php if (isset($_POST['ApliNeto']) && $_POST['ApliNeto']!="") { echo "checked"; } ?> >Visualizar Valores Netos</label>

									
								</div>
								</div>

								<button type="button" onclick="Lista()"><i class='fas fa-sync'></i> Consultar</button>
								<button type="button" onclick="GenLibro()"><i class='far fa-file-excel'></i> Descargar</button>
								<button type="button" onclick="printDiv('DivImp')" ><i class='fas fa-print'></i> Imprimir</button>

							</div>
						</div>
					</div>
				</div>


				<div class="clearfix"></div>
				<br>

			</div>
			<div class="clearfix"></div>
			<br>
			<div class="col-md-12" id="DivImp">


				<div class="col-md-12" id="DatosLista">

				</div>
			</div>
		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


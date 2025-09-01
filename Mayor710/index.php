<?php
	// echo "rrr";
	header('Cache-Control: no cache');

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

	if (isset($_POST['anoselect'])) {
		if ($_POST['anoselect']!=""){
			$danol=$_POST['anoselect'];
			$Xfdesde="01-01-".$danol;
			$Xfhasta="31-12-".$danol;
		}else{
			$dmes = substr($Periodo,0,2);
			$danol = substr($Periodo,3,4);
			$Xfdesde="01-01-".$danol;
			$Xfhasta="31-12-".$danol;
		}
	}else{
		$dmes = substr($Periodo,0,2);
		$danol = substr($Periodo,3,4);
		$Xfdesde="01-01-".$danol;
		$Xfhasta="31-12-".$danol;
	} 

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// $swb=0;
	// $SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY id DESC";
	// $resultados = $mysqli->query($SQL);
	// while ($registro = $resultados->fetch_assoc()) {
	// 	if ($registro["glosa"]=="" && $swb==0) {
	// 		$mysqli->query("DELETE FROM CTRegLibroDiario WHERE id='".$registro["id"]."'");
	// 	}else{
	// 		$swb=1;
	// 	}
	// } 

	$StrCCosto="";

	$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$StrCCosto=$StrCCosto.'<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
	}

	$SQL="SELECT * FROM CTPeriodoEmpresa WHERE RutEmpresa='$RutEmpresa' AND Periodo='$Periodo'";
	$resultados = $mysqli->query($SQL);
	$EstPeriodo = $resultados->num_rows;

	$PeriodoX=$Periodo; 
	$sdebe=0;
	$shaber=0;

	if (isset($_POST['messelect'])){
		if ($_POST['messelect']!=""){
			$dmes = $_POST['messelect'];
			$dano = $_POST['anoselect'];
			$PeriodoX=$_POST['messelect'].'-'.$_POST['anoselect'];
		}else{
			$dmes = substr($PeriodoX,0,2);
			$dano = substr($PeriodoX,3,4);
		} 
	}else{
		$dmes = substr($PeriodoX,0,2);
		$dano = substr($PeriodoX,3,4);
	}

	$sqlin = "SELECT * FROM CTParametros WHERE estado='A'";
	$resultadoin = $mysqli->query($sqlin);

	while ($registro = $resultadoin->fetch_assoc()) {
		if($registro['tipo']=="SEPA_MILE"){
			$DMILE=$registro['valor'];  
		}

		if($registro['tipo']=="SEPA_DECI"){
			$DDECI=$registro['valor'];  
		}

		if($registro['tipo']=="NUME_DECI"){
			$NDECI=$registro['valor'];  
		} 
	}

	$mysqli->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->

		<script type="text/javascript">
			// $(window).load(function(){
			// 	$('#seleccue').select2();
			// });

            $( function() {
                $("#fdesde").datepicker();
                $("#fhasta").datepicker();
            });

			$(document).ready(function() {
				if ($.fn.select2) {
					$('#seleccue').select2();
				} else {
					console.error('Select2 plugin is not loaded.');
				}
				Updta();
			});

			function CamEmpr(){
				form1.CTEmpre.value="";
				form1.action="../frmMain.php";
				form1.submit();
			}
			function Updta(){
				var url= "Grilla.php";
				$.ajax({
					type: "POST",
					url: url,
					data: $('#form1').serialize(),
					success:function(resp){
						$('#grilla').html(resp);
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
				Updta();
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
				form1.action="frmLibMayorXLS.php";
				form1.submit();
				form1.target="";
				form1.action="#";        
			}

			function GenLibroPDF(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmLibMayorPDF.php";
				form1.submit();
				form1.target="";
				form1.action="#";        
			}

			function ModAsiento(valor){
				<?php
					if($EstPeriodo>0){
						echo "alert('Periodo Cerrado, no se puede ingresar.');";
					}else{
				?>
				form1.KeyMod.value=valor;
				form1.action="../RVoucher/frmModAsiento.php";
				form1.submit();

				<?php
					}
				?>
			}

		</script>
	</head>
	<body onload="Updta()">
	<?php 
		include '../nav.php';
	?>

	<div class="container-fluid text-left">
	<div class="row content">

		<div class="col-sm-12">
			<br>
			<form action="#" method="POST" name="form1" id="form1">

				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Generar Rango de Fecha</div>
						<div class="panel-body">

							<h4>Generar por rango de Fecha</h4>
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Desde</span>
									<input id="fdesde" name="fdesde" type="text" class="form-control text-right" value="<?php echo $Xfdesde; ?>" size="10" maxlength="10">
								</div>
							</div>
							<div class="clearfix"></div>
							<br>

							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Hasta</span>
									<input id="fhasta" name="fhasta" type="text" class="form-control text-right" value="<?php echo $Xfhasta; ?>" size="10" maxlength="10">
								</div>
							</div>
							<div class="clearfix"></div>
							<br>
							<div class="col-md-12">
								<button type="button" class="btn btn-modificar" onclick="Updta(form1.rfecha.value='1')">Generar</button>
							</div>
							
							<input type="hidden" name="rfecha" id="rfecha" value="">
							<input type="hidden" name="Frfecha" id="Frfecha" value="<?php echo $_POST['rfecha']; ?>">
						</div>
					</div>
				</div>

				<div class="col-md-4 text-center">
					<h3>Libro Mayor</h3>
					<div class="col-md-6 text-right">
						<div class="input-group">
							<span class="input-group-addon">Mes</span>
							<select class="form-control" id="messelect" name="messelect" onchange="Updta()" required>
							<?php 
								$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
								$i=1;
								$dmes=$dmes*1;
								while($i<=12){
									if ($i==$dmes) {
										if ($i<10) {
											echo "<option value ='0".$i."' selected>".$Meses[($i-1)]."</option>";
										}else{
											echo "<option value ='".$i."' selected>".$Meses[($i-1)]."</option>";
										}
									}else{
										if ($i<10) {
											echo "<option value ='0".$i."'>".$Meses[($i-1)]."</option>";
										}else{
											echo "<option value ='".$i."'>".$Meses[($i-1)]."</option>";
										}
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
					<div class="clearfix"></div>
					<br>

					<div class="col-md-12">
						<button type="button" class="btn btn-modificar btn-block" id="BtnVisual" onclick="VisAnual()"><?php if ($_POST['anual']==1){ echo 'Visualizar Mensual'; }else{ echo 'Visualizar Anual';} ?></button>
					</div>
					<div class="clearfix"></div>
					<br>

					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Cuenta</span>
							<select class="form-control" id="seleccue" name="seleccue" onchange="Updta()">
							<option value="0">Todas Cuentas</option>
							<?php
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								if ($_SESSION["PLAN"]=="S"){
									$sqlin = "SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
								}else{
									$sqlin = "SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
								}
								$resultadoin = $mysqli->query($sqlin);

								while ($registro = $resultadoin->fetch_assoc()) {

									if(isset($_POST['CtaMayor']) && $_POST['CtaMayor']!="") {
										if($_POST['CtaMayor']==$registro['numero']){
											echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".strtoupper($registro['detalle'])."</option>";
										}else{
											echo "<option value='".$registro['numero']."'>".$registro['numero']." - ". strtoupper($registro['detalle'])."</option>";
										}
									}else{
										if($_POST['seleccue']==$registro['numero']){
											echo "<option value='".$registro['numero']."' selected>".$registro['numero']." - ".strtoupper($registro['detalle'])."</option>";
										}else{
											echo "<option value='".$registro['numero']."'>".$registro['numero']." - ". strtoupper($registro['detalle'])."</option>";
										}
									}
								}
								$mysqli->close();
							?>
							</select>
						</div>
					</div>

					<div class="clearfix"></div>
					<br>

					<div class="col-md-12">
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
				</div>

				<div class="col-md-4">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Generar Libro</div>
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

				<div class="clearfix"> </div>
				<input type="hidden" name="KeyMod" id="KeyMod">	
				<input type="hidden" name="anual" id="anual" value="<?php echo $_POST['anual']; ?>">
			</form>
		</div>

		<br>
		<br>
		<div class="col-sm-12" id="grilla">


		</div>
	</div>
	</div>
    <script type="text/javascript">
      $( "#fdesde" ).datepicker({
        // Formato de la fecha
        dateFormat: "dd-mm-yy",
        // Primer dia de la semana El lunes
        firstDay: 1,
        // Dias Largo en castellano
        dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
        // Dias cortos en castellano
        dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
        // Nombres largos de los meses en castellano
        monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
        // Nombres de los meses en formato corto 
        monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
        // Cuando seleccionamos la fecha esta se pone en el campo Input 
        onSelect: function(dateText) { 
          // $('#d1').val(dateText);
          // $('#d2').focus();
          // $('#d2').select();
        }
      });

      $( "#fhasta" ).datepicker({
        // Formato de la fecha
        dateFormat: "dd-mm-yy",
        // Primer dia de la semana El lunes
        firstDay: 1,
        // Dias Largo en castellano
        dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
        // Dias cortos en castellano
        dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
        // Nombres largos de los meses en castellano
        monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
        // Nombres de los meses en formato corto 
        monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
        // Cuando seleccionamos la fecha esta se pone en el campo Input 
        onSelect: function(dateText) { 
          // $('#d1').val(dateText);
          // $('#d2').focus();
          // $('#d2').select();
        }
      });       
    </script>
	<?php include '../footer.php'; ?>
	</body>
</html>
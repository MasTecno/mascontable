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

	if (isset($_POST['IdMovDoc'])) {
		if ($_POST['IdMovDoc']!="") {
			$messelect=$_POST['messelect'];
			$anoselect=$_POST['anoselect'];

			if ($messelect<=9) {
				$messelect="0".$messelect;
			}
			$PerMov=$messelect."-".$anoselect;

			$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        	$mysqli->query("UPDATE CTRegDocumentos SET periodo='$PerMov' WHERE id='". $_POST['IdMovDoc']."'");
        	$mysqli->close();
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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


        <script>
			function Volver(){
				form1.action = "index.php<?php echo "?".$_POST['frm']."&Pe=".$_POST['anoselect']."&Me=".$_POST['messelect'].""; ?>;";
				form1.submit();
			}
			function CargaGrilla(){

				var url= "HistorialDet.php";
				$.ajax({
					type: "POST",
					url: url,

					data: $('#form1').serialize(),
					success:function(resp){
						$('#Grilla').html(resp);
					}
				});
			}

			function Liberar() {
				si=0;
				for (i=0;i<document.form1.elements.length;i++){
					if(document.form1.elements[i].type == "checkbox"){
						if (document.form1.elements[i].checked==1) {
							si++;
						}
					}
				}
				
				if (si>0) {
					form1.PorcEli.value="S";
					CargaGrilla();
				}else{
					alert("Debe selecionar al menos 1 documentos para continuar.");
				}
			}

			function LiberaDoc(R){
				var checkbox = document.getElementById(R);
				// Cambiar el estado del checkbox
				checkbox.checked = !checkbox.checked;
				Liberar();
			}
			
        </script>

	</head>
	<body onload="CargaGrilla()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
		<form action="#" name="form1" id="form1" method="POST">
			<br>
			<div class="col-sm-12">
				<div class="col-md-1"></div>
            	<div class="col-md-10">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading text-center">Condiciones</l></div>
						<div class="panel-body">
							1. Este proceso liberara el documento junto con su pago o cobro según corresponda.<br>
							2. Si el documento esta centralizado de forma masiva, también se liberarán los otros documentos que estén en el mismo voucher.<br>
							3. Si el pago fue realizado en otro periodo también será eliminado el voucher.<br>
						</div>
					</div>
				</div>
            	<div class="col-md-1" style="visibility: hidden;">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading text-center">Selecci&oacute;n de Documentos</l></div>
						<div class="panel-body">
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon">Registros</span>
									<select class="form-control" id="frm" name="frm" onchange="CargaGrilla()" required>
										<option value="">Seleccione</option>
										<option value="C" <?php if(isset($_POST['frm']) && $_POST['frm']=='C'){ echo "selected";}?> >Documentos de Compra</option>
										<option value="V" <?php if(isset($_POST['frm']) && $_POST['frm']=='V'){ echo "selected";}?> >Documentos de Venta</option>
										<option value="H" <?php if(isset($_POST['frm']) && $_POST['frm']=='H'){ echo "selected";}?> >Documentos Honorarios</option>
										<!-- <option value="H">* Honorarios</option>
										<option value="R">* Remuneraciones</option> -->
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="input-group">
									<span class="input-group-addon">Mes</span>
									<select class="form-control" id="messelect" name="messelect" onchange="CargaGrilla()" required>
									<?php 
                                        $mes=$_POST['messelect'];

										$Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
										$i=1;
										$dmes=$dmes*1;

										if(isset($_POST['messelect']) && $_POST['messelect']!=""){
											$dmes=$_POST['messelect']*1;
										}


										while($i<=12){

											if ($mes==$i) {
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

										$yoano=$_POST['anoselect'];
										$tano="2010";

										if(isset($_POST['anoselect']) && $_POST['anoselect']!=""){
											$dano=$_POST['anoselect'];
										}

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
						</div>
					</div>
				</div>

				<div class="clearfix"></div>
				<br>

                <div class="col-md-12" id="Grilla">
			    </div>
				
				<!-- -->
			</div>
		</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>


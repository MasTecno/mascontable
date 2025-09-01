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

		<div class="container-fluid">
		<div class="row content">
			<br>
			<form action="#" name="form1" id="form1" method="POST">
				<input type="hidden" name="EliRut" id="EliRut">
			<div class="col-md-2">

				<div class="col-md-12">
					<div class="input-group">
						<span class="input-group-addon">Rut </span>
						<input type="text" class="form-control" id="rut" autocomplete="off" name="rut" onChange="javascript:this.value=this.value.toUpperCase();" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" required>
					</div>
					<br>
					<button type="submit" class="btn btn-success btn-sm btn-block">
						<span class="glyphicon glyphicon-saved"></span>Agregar
					</button>


				</div> 

				<p><?php echo $lMensaje; ?></p>

				<div class="clearfix"></div>
				<br>


				<h4>Mis Rut</h4>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr style="background-color: #e51c20; color: #FFF;">
							<th style="text-align: center;">Fecha</th>
							<th style="text-align: center;">Rut</th>
							<th style="text-align: center;" width="1%"></th>
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
								<tr>
									<td style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
									<td>'.$registro["Rut"].'</td>
									<td style="text-align: center;">
										<button type="button" class="btn btn-danger btn-xs" onclick="FEliRut(\''.$Pref.$registro["Id"].$Suf.'\')">
											<span class="glyphicon glyphicon-trash"></span>
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

			<div class="col-md-8">
				<?php
					$mysqli=ConCobranza();
					$SQL="SELECT max(Fecha) As UFecha FROM Transferencias";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$UFecha=date('d-m-Y',strtotime($registro["UFecha"]));
					}
				?>

				<h3>Transferencias Recibidas (Cartola <?php echo $UFecha; ?>)</h3>
				<a href="../Facturas" class="btn btn-warning">
					<span class="glyphicon glyphicon-new-window"></span> Volver
				</a>
				<br>
				
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr style="background-color: #e51c20; color: #FFF;">
							<th style="text-align: center;" width="10%">Fecha</th>
							<th style="text-align: center;">Banco</th>
							<th style="text-align: center;" width="10%">N. Operaci&oacute;n</th>
							<th style="text-align: center;" width="10%">N. Cuenta</th>
							<th style="text-align: center;" width="10%">Monto</th>
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
								<tr>
									<td style="text-align: center;">'.date('d-m-Y',strtotime($registro["Fecha"])).'</td>
									<td>'.$registro["Banco"].'</td>
									<td style="text-align: center;">'.$registro["NOperacion"].'</td>
									<td style="text-align: center;">'.$registro["Cta"].'</td>
									<td style="text-align: center;">'.number_format($registro["Monto"],0,",",".").'</td>
								</tr>
								';
							}
							$mysqli->close();
						?>
					</tbody>
				</table>
				
			</div>
			</form>
		</div>
		</div>

		<?php include '../footer.php'; ?>

	</body>

</html>
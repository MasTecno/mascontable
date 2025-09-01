<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$sw=0;

	if(isset($_POST['idccosto']) && $_POST['idccosto']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTCCosto WHERE id='".$_POST['idccosto']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$codigo=$registro["codigo"];
			$nombre=$registro["nombre"];
		}  
		$mysqli->close();
	}

	if (isset($_POST['idccostob']) && $_POST['idccostob']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTCCosto SET estado='B' WHERE id='".$_POST['idccostob']."'");
		$mysqli->close();
	}

	if (isset($_POST['idccostoa']) && $_POST['idccostoa']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTCCosto SET estado='A' WHERE id='".$_POST['idccostoa']."'");
		$mysqli->close();
	}

	if (isset($_POST['idccostoe']) && $_POST['idccostoe']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQL="SELECT * FROM CTRegLibroDiario WHERE ccosto='".$_POST['idccostoe']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("DELETE FROM CTCCosto WHERE id='".$_POST['idccostoe']."' AND rutempresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$NoElimina="N";
		}

		$mysqli->close();
	}
?>
<!DOCTYPE html>
<html > 
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
		function Modifi(valor){
			form1.idccosto.value=valor;
			form1.action="#";
			form1.submit();
		}

		function Baja(valor){
			form1.idccostob.value=valor;
			form1.action="#";
			form1.submit();
		}

		function Alta(valor){
			form1.idccostoa.value=valor;
			form1.action="#";
			form1.submit();
		}

		function Elimi(valor){
			form1.idccostoe.value=valor;
			form1.action="#";
			form1.submit();
		}

		function OrdeCli(){
			if(form1.orden.value==1){
				form1.orden.value=0;
			}else{
				form1.orden.value=1;
			}
			form1.action="";
			form1.submit();
		}

		function Volver(){
			form1.action="../frmMain.php";
			form1.submit();
		}
	</script>  

	</head>
<body>

	<?php 
		include '../nav.php';
	?>

	<div class="container-fluid text-left">
	<div class="row content">
		<form action="xfrmCCostos.php" method="POST" name="form1" id="form1">
			<br>
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading text-center">Centros de Costos</div>
					<div class="panel-body">

						<div class="col-md-4">
							<div class="input-group">
								<span class="input-group-addon">Codigo</span>
								<input type="text" class="form-control" id="codigo" name="codigo" autocomplete="off" onChange="javascript:this.value=this.value.toUpperCase();" placeholder="05-ADMIN-MADRID" value="<?php echo $codigo; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>
							</div>

							<input type="hidden" name="idccosto" id="idccosto" value="<?php echo $_POST['idccosto']; ?>">
							<input type="hidden" name="idccostob" id="idccostob">
							<input type="hidden" name="idccostoa" id="idccostoa">
							<input type="hidden" name="idccostoe" id="idccostoe">
						</div> 

						<div class="col-md-8">
						<div class="input-group">
							<span class="input-group-addon">Nombre</span>
							<input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $nombre; ?>"  autocomplete="off" required>
						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"> </div>
			<br>
			<div class="col-sm-2">
			</div>

			<div class="col-md-8 text-right">
				<?php if ($sw==1) {	?>
					<button type="submit" class="btn btn-modificar">
						<span class="glyphicon glyphicon-edit"></span> Modificar
					</button>
				<?php }else{ ?>
					<button type="submit" class="btn btn btn-grabar">
						<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
					</button>
				<?php } ?>
					<button type="button" class="btn btn-cancelar" onclick="Volver()">
						<span class="glyphicon glyphicon-remove"></span> Cancelar
					</button>
			</div>



			<div class="clearfix"> </div>
			<br>
			<div class="col-sm-2">
			</div>

			<div class="col-md-8">
				<input class="form-control" id="myInput" type="text" placeholder="Buscar...">
				<br>
				<table class="table table-condensed table-hover">
					<thead>
						<tr style="background-color: #d9d9d9;">
							<th width="20%">Codigo</th>
							<th>Nombre</th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody id="myTable">
						<?php 
							$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
							$SQL="SELECT * FROM CTCCosto WHERE estado<>'X' AND rutempresa='".$_SESSION['RUTEMPRESA']."' ORDER BY nombre";

							$resultados = $mysqli->query($SQL);
							while ($registro = $resultados->fetch_assoc()) {
								echo '
									<tr>
									<td>'.$registro["codigo"].'</td>
									<td>'.$registro["nombre"].'</td>
								';

									echo '<td><button type="button" class="btn btn-modificar btn-xs" onclick="Modifi('.$registro["id"].')">Modificar</button></td>';

									if($registro["estado"]=="B"){
										echo '<td><button type="button" class="btn btn-warning btn-xs" onclick="Alta('.$registro["id"].')">Alta</button></td>';
									}else{
										echo '<td><button type="button" class="btn btn-cancelar btn-xs" onclick="Baja('.$registro["id"].')">Baja</button></td>';
									}

									echo '<td><button type="button" class="btn btn-cancelar btn-xs" onclick="Elimi('.$registro["id"].')">Eliminar</button></td>';


								echo '
									</tr>
								';
							}       
							$mysqli->close();
						?>
					</tbody>
				</table>
			</div>
		</form>

		<?php
			if(isset($_GET['Err']) && $_GET['Err']==1){
				echo '<script> alert("Este Codigo ya esta ingresado"); </script>';
			}

			if ($NoElimina=="N") {
				echo '<script> alert("Este Centro de Costo tiene movimientos, no se puede eliminar"); </script>';
			}

		?>
	</div>
	</div>

	<script>
		$(document).ready(function(){
		$("#myInput").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$("#myTable tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
		});
		});
	</script>
	<?php include '../footer.php'; ?>

</body>
</html>


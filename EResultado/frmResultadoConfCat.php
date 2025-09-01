<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	if(isset($_POST['idmodcat']) && $_POST['idmodcat']!=""){
		$swcat=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTEstResultadoCab WHERE Id='".$_POST['idmodcat']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xnomcat=$registro["Nombre"];
			$XTipo=$registro["Tipo"];
		}  
		$mysqli->close();
	}

	if (isset($_POST['idestadocat']) && $_POST['idestadocat']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQL="SELECT * FROM CTEstResultadoCab WHERE id='".$_POST['idestadocat']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			if ($registro["Estado"]=="A") {
				$mysqli->query("UPDATE CTEstResultadoCab SET Estado='B' WHERE Id='".$_POST['idestadocat']."'");
			}else{
				$mysqli->query("UPDATE CTEstResultadoCab SET Estado='A' WHERE Id='".$_POST['idestadocat']."'");
			}
		}  
		$mysqli->close();
	}

	if (isset($_POST['sw1']) && $_POST['sw1']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTEstResultadoCab SET Nombre='".$_POST['NombreCat']."', Tipo='".$_POST['Tipo']."' WHERE Id='".$_POST['sw1']."'");
		$mysqli->close();
	}

	if (isset($_POST['idelimcat']) && $_POST['idelimcat']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("DELETE FROM CTEstResultadoCab WHERE Id='".$_POST['idelimcat']."'");
		$mysqli->query("DELETE FROM CTEstResultadoDet WHERE IdCab='".$_POST['idelimcat']."'");
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

			function Volver(){
				form2.action="frmResultadoConf.php";
				form2.submit();
			}

			function ModCat(valor){
				form2.idmodcat.value=valor;
				form2.action="#";
				form2.submit();
			}
			function EstadoCat(valor){
				form2.idestadocat.value=valor;
				form2.action="#";
				form2.submit();			
			}

			function EliCat(valor){
				form2.action="#";
				var r = confirm("Al eliminar la categoria se eliminaran las cuenta que esta  asigandas a ella.\r\nDesea Eliminar la Categoria?");
				if (r == true) {
					form2.idelimcat.value=valor;
					form2.submit();									
				}
			}
			
		</script>  

	</head>
<body>

	<?php 
		include '../nav.php';
	?>

	<div class="container-fluid text-left">
	<div class="row content">
		<br>

	<form action="xfrmResultadoConfCat.php" method="POST" name="form2" id="form2">
		<div class="col-sm-12 text-left">

				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Nueva Categor&iacute;a</div>
					<div class="panel-body">

						<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">Nombre</span>
							<input type="text" class="form-control" id="NombreCat" name="NombreCat" value="<?php echo $xnomcat; ?>"  autocomplete="off" required>
							<input type="hidden" name="idmodcat" id="idmodcat" value="<?php echo $_POST['idmodcat']; ?>">
							<input type="hidden" name="idestadocat" id="idestadocat">
							<input type="hidden" name="idelimcat" id="idelimcat">
							<input type="hidden" name="sw1" id="sw1" value="<?php echo $_POST['idmodcat']; ?>">
						</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-addon">Tipo</span>
							<select class="form-control" id="Tipo" name="Tipo" required>
								<option value="">Seleccionar</option>
								<option value="I" <?php if($XTipo=="I"){ echo "selected"; } ?>>Ingreso</option>
								<option value="E" <?php if($XTipo=="E"){ echo "selected"; } ?>>Egreso</option>
							</select>
						</div>
						</div> 
						<div class="clearfix"></div>
						<br>

						<div class="col-md-12 text-right">

							<?php if ($swcat==1) { ?>
								<button type="submit" class="btn btn-modificar">
									<span class="glyphicon glyphicon-edit"></span> Modificar
								</button>
							<?php }else{ ?>
								<button type="submit" class="btn btn-grabar">
									<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
								</button>

							<?php } ?>
								<button type="button" class="btn btn-cancelar" onclick="Volver()">
									<span class="glyphicon glyphicon-remove"></span> Cancelar
								</button>            
						</div>

					</div>
				</div>

			<div class="clearfix"> </div>
			<hr>

			<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
				<div class="panel-heading">Categor&iacute;as Registradas</div>
				<div class="panel-body">
					<div class="well">         
						<input class="form-control" id="myInput" type="text" placeholder="Buscar...">
					</div>
					<br>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Nombre</th>
								<th width="10%">Tipo</th>
								<th width="1%"></th>
								<th width="1%"></th>
								<th width="1%"></th>
							</tr>
						</thead>

						<tbody id="myTable">
							<?php 
								$BotEstado='';
								$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
								$SQL="SELECT * FROM CTEstResultadoCab ORDER BY Id, Tipo";

								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {

									if ($registro['Tipo']=="I") {
										$Tipo="Ingreso";
									}else{
										$Tipo="Egreso";
									}

									if ($registro['Estado']=="A") {
										$BotEstado='<button type="button" class="btn btn-success btn-xs" onclick="EstadoCat('.$registro['Id'].')"><span style="color: #000;" class="glyphicon glyphicon-eye-open"></span>  </button>';
									}else{
										$BotEstado='<button type="button" class="btn btn-danger btn-xs" onclick="EstadoCat('.$registro['Id'].')"><span class="glyphicon glyphicon-eye-close"></span>  </button>';
									}

									echo '
										<tr>
										<td>'.$registro['Nombre'].'</td>
										<td>'.$Tipo.'</td>
										<td><button type="button" class="btn btn-warning btn-xs" onclick="ModCat('.$registro['Id'].')"><span class="glyphicon glyphicon-edit"></span>  </button></td>
										<td>'.$BotEstado.'</td>
										<td><button type="button" class="btn btn-danger btn-xs" onclick="EliCat('.$registro['Id'].')"><span class="glyphicon glyphicon-trash"></span>  </button></td>
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
	</form>

	<?php
		if(isset($_GET['Err']) && $_GET['Err']==1){
			echo '<script>alert("Este Codigo ya esta ingresado");</script>';
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


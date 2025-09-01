<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';
	
	$sw=0;

	if(isset($_POST['idemp']) && $_POST['idemp']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTContadores WHERE id='".$_POST['idemp']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$nombre=$registro["nombre"];
			$correo=$registro["correo"];
			$rol=$registro["tipo"];
		}  
		$mysqli->close();
	}

	if (isset($_POST['idempb']) && $_POST['idempb']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTContadores SET estado='B' WHERE id='".$_POST['idempb']."'");
		$mysqli->query("UPDATE CTEmpresas SET user='0' WHERE user='".$_POST['idempb']."'");
		// if($registro["correo"]=="admin@mastecno.cl"){
		// 	$mysqli->query("UPDATE CTContadores SET estado='A' WHERE id='".$_POST['idempb']."'");
		// }
		// echo "UPDATE CTContadores SET estado='A' WHERE id='".$_POST['idempb']."'";
		$mysqli->close();
	}

	if (isset($_POST['idempa']) && $_POST['idempa']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTContadores SET estado='A' WHERE id='".$_POST['idempa']."'");
		$mysqli->close();
	}

	if (isset($_POST['idrol']) && $_POST['idrol']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTContadores WHERE id='".$_POST['idrol']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			if($registro["tipo"]=="U"){
				$rol="A";
			}else{
				$rol="U";
			}
		}
		$mysqli->query("UPDATE CTContadores SET tipo='$rol' WHERE id='".$_POST['idrol']."'");
		$mysqli->close();
	}


	if (isset($_POST['ideliusu']) && $_POST['ideliusu']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$mysqli->query("UPDATE CTEmpresas SET user='0' WHERE user='".$_POST['ideliusu']."'");
		$mysqli->query("DELETE FROM CTContadores WHERE id='".$_POST['ideliusu']."'");
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
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">

			function Baja(valor){
				form1.idempb.value=valor;
				form1.action="#";
				form1.submit();
			}

			function Eliminar(valor){
				form1.ideliusu.value=valor;
				form1.action="#";
				form1.submit();
			}

			function Alta(valor){
				form1.idempa.value=valor;
				form1.action="#";
				form1.submit();
			}
			function Reset(valor){
				form1C.idmod.value=valor;
			}
			function GBt(){
				form1C.submit();
			}

			function Rol(r1){
				form1.idrol.value=r1;
				form1.action="#";
				form1.submit();
			}
			function Volver(){
				form1.action="frmMain.php";
				form1.submit();
			}    
		</script>  

	</head>
	<body>
		<?php 
			include 'nav.php';
		?>

		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-sm-12 text-left">
				<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Cambiar Clave</h4>
						</div>
						<div class="modal-body">
							<form action="xfrmUsuarios.php" method="POST" name="form1C" id="form1C">
								<label for="clavX">Nueva Clave</label>
								<input type="text" class="form-control" id="claveX" name="claveX" maxlength="50" value="">
								<input type="hidden" name="idmod" id="idmod">
							</form>
						</div>
						<div class="modal-footer">
							<div class="btn-group btn-group-justified" role="group" aria-label="group button">


								<div class="btn-group" role="group">
									<button type="button" id="saveImage" class="btn btn-grabar" data-action="save" role="button" onclick="GBt()">Grabar</button>
								</div>
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-cancelar" data-dismiss="modal" role="button" id="CMOD">Cancelar</button>
								</div>			
							</div>
						</div>
					</div>
				</div>
				</div>

				<br>
				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading text-center">Registro de Usuarios</div>
					<div class="panel-body">				

						<form action="xfrmUsuarios.php" method="POST" name="form1" id="form1">

							<div class="col-md-10">
								<div class="input-group">
									<span class="input-group-addon">Nombre</span>
									<input type="text" class="form-control" autocomplete="off" id="tnombre" name="tnombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $razonsocial; ?>" required>
									<input type="hidden" name="idempa" id="idempa">
									<input type="hidden" name="idempb" id="idempb">
									<input type="hidden" name="ideliusu" id="ideliusu">
									<input type="hidden" name="idrol" id="idrol">
								</div>
							</div>

							<div class="clearfix"> </div>
							<br>

							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon">Correo</span>
									<input type="text" class="form-control" id="correo" autocomplete="off" name="correo" required>
								</div>
							</div> 

							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon">Clave</span>
									<input type="text" class="form-control" id="clave" autocomplete="off" name="clave" required>
								</div>
							</div> 

							<div class="clearfix"> </div>
							<br>

							<div class="col-md-12">
							

							<p>
							<?php 
								if ($sw==1) {
							?>

							<button type="submit" class="btn btn-modificar">
								<span class="glyphicon glyphicon-edit"></span> Modificar
							</button>

							<?php 
								}else{
							?>

							<button type="submit" class="btn btn-grabar">
								<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
							</button>

							<?php 
								}
							?>

								<button type="button" class="btn btn-cancelar" onclick="Volver()">
									<span class="glyphicon glyphicon-remove"></span> Cancelar
								</button>            
							</p>

							</div>
						</form>
					</div>
				</div>

				<div class="clearfix"> </div>
				<hr>

				<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading text-center">Usuario Registrados</div>
					<div class="panel-body">
						<form name="form2" action="#" method="POST">
							<table class="table table-hover">
								<thead>
								<tr>
									<th>Nombre</th>
									<th>Correo</th>
									<th width="1%"></th>
									<th width="1%"></th>
									<th width="1%"></th>
									<th width="1%"></th>
								</tr>
								</thead>

								<tbody>
									<?php 
									$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
									$SQL="SELECT * FROM CTContadores WHERE estado<>'X'";

									$resultados = $mysqli->query($SQL);
									while ($registro = $resultados->fetch_assoc()) {
										echo '
										<tr>
										<td>'.$registro["nombre"].'</td>
										<td>'.$registro["correo"].'</td>
										';

										if($registro["correo"]=="admin@mastecno.cl"){
											echo '          <td></td>';
										}else{
											echo '          <td><button type="button" class="btn btn-modificar btn-xs" data-toggle="modal" data-target="#myModal" onclick="Reset('.$registro["id"].')">Clave</button></td>';
										}
										if($registro["estado"]=="B"){
											echo '          <td><button type="button" class="btn btn-warning btn-xs" onclick="Alta('.$registro["id"].')">Alta</button></td>';
										}else{								
											echo '          <td><button type="button" class="btn btn-danger btn-xs" onclick="Baja('.$registro["id"].')">Baja</button></td>';
										}
										if($registro["tipo"]=="U"){
											echo '          <td><button type="button" class="btn btn-cancelar btn-xs" onclick="Eliminar('.$registro["id"].')">Eliminar</button></td>';
										}else{
											echo '          <td></td>';
										}
										if($_SESSION["CORREO"]=="admin@mastecno.cl"){
											if($registro["tipo"]=="U"){
												echo '          <td><button type="button" class="btn btn-warning btn-xs" onclick="Rol('.$registro["id"].')">Usuario</button></td>';
											}else{
												echo '          <td><button type="button" class="btn btn-warning btn-xs" onclick="Rol('.$registro["id"].')">Administrador</button></td>';
											}
										}

										echo '
										</tr>
										';
									}       
									$mysqli->close();
								?>
								</tbody>
							</table>      
						</form>
					</div>
				</div>
				<div class="clearfix"> </div>

				<?php
					if(isset($_GET['Err']) && $_GET['Err']==1){
						echo '<script>
							alert("Usuarios ya esta ingresado");
						</script>';
					}
				?>
			</div>
		</div>
		</div>
		<?php include 'footer.php'; ?>
	</body>
</html>
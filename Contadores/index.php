<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';


    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


	$sw=0;
	if(isset($_POST['idmod']) && $_POST['idmod']!=""){
		$sw=1;
		$SQL="SELECT * FROM CTContadoresFirma WHERE Id='".$_POST['idmod']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$rut=$registro["Rut"];
			$xnombre=strtoupper($registro["Nombre"]);
			$xcargo=strtoupper($registro["Cargo"]);
		} 
	}

	if (isset($_POST['idempb']) && $_POST['idempb']!="") {
		$mysqli->query("UPDATE CTContadoresFirma SET Estado='B' WHERE Id='".$_POST['idempb']."'");
	}

	if (isset($_POST['idempa']) && $_POST['idempa']!="") {
			$mysqli->query("UPDATE CTContadoresFirma SET Estado='A' WHERE Id='".$_POST['idempa']."'");
	}

	if (isset($_POST['ideli']) && $_POST['ideli']!="") {
		$mysqli->query("DELETE FROM CTContadoresFirma WHERE Id='".$_POST['ideli']."'");
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

		<script src="../js/jquery.Rut.js" type="text/javascript"></script>
		<script src="../js/jquery.validate.js" type="text/javascript"></script>	

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type="text/javascript">
			function Grilla(){
				var url= "frmGrilla.php";
				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#TableContadores').html(resp);
				}
				});				
			}

			function Baja(valor){
				form1.idempb.value=valor;
				form1.action="#";
				form1.submit();
			}

			function Alta(valor){
				form1.idempa.value=valor;
				form1.action="#";
				form1.submit();
			}
			function Modifi(valor){
				form1.idmod.value=valor;
				form1.action="#";
				form1.submit();
			}
			function Elimina(valor){
				form1.ideli.value=valor;
				form1.action="#";
				form1.submit();
			}
			function Volver(){
				form1.action="frmMain.php";
				form1.submit();
			}
			$(document).ready(function(){
				$('#rut').Rut({ 
					on_error: function(){alert('Rut incorrecto'); $('#rut').val(""); $('#rut').focus();} 
				});
			});

		</script>
	</head>
	<body onload="Grilla()">

	<?php 
		include '../nav.php';
	?>

		<div class="container-fluid">
		<div class="row content">
			
			<form action="xfrmIndex.php" method="POST" name="form1" id="form1">
				<input type="hidden" name="idempb" id="idempb">
				<input type="hidden" name="idempa" id="idempa">
				<input type="hidden" name="ideli" id="ideli">
				<input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">

				<br>
				<div class="col-md-8">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Mantenedor de Contadores</div>
						<div class="panel-body">

                            <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">Rut</span>
                                <input type="text" class="form-control" id="rut" name="rut" autocomplete="off" placeholder="Ej: 13520300-5" value="<?php echo $rut; ?>" required>
                            </div>
                            </div>
							<div class="clearfix"> </div>
							<br>

							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Nombre Completo</span>
								<input type="text" class="form-control" maxlength="100" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xnombre; ?>"  autocomplete="off" required>
							</div>
							</div> 

							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Cargo</span>
								<input type="text" class="form-control" maxlength="100" id="cargo" name="cargo" autocomplete="off" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xcargo; ?>"  autocomplete="off">
							</div>
							</div>                             

							<div class="clearfix"> </div>
							<br>
						</div>
					</div>
				</div>

				<div class="clearfix"></div>

				<div class="col-md-8 text-right">
					<?php 
						if ($sw==1) {
					?>
						<button type="submit" class="btn btn-modificar">
							<span class="glyphicon glyphicon-edit"></span> Modificar
						</button>

					<?php 
						}else{
					?>
						<button type="submit" class="btn btn btn-grabar">
							<span class="glyphicon glyphicon-floppy-saved"></span> Grabar
						</button>

					<?php 
						}
					?>
						<button type="button" class="btn btn-cancelar" onclick="Volver()">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</button>      
				</div>

				<div class="clearfix"></div>
				<br>
				<div class="col-md-12">
					<div cl class="col-sm-10">
						<input class="form-control" id="myInput" type="text" placeholder="Buscar...">
					</div>

					<div class="clearfix"></div>
					<br>
					<div id="TableContadores">
					</div>
				</div>
			</form>
		</div>
		</div>
		<script>
			<?php
				if (isset($_GET['ex']) && $_GET['ex']=="yes") {
					echo 'alert ("Contador ya registrada");';
				}
				if ($NoElimina=="N") {
					echo 'alert ("Esta cuenta tiene movimientos, no se puede eliminar.");';
				}
				if ($NoEliminaCom=="N") {
					echo 'alert ("Esta cuenta tiene movimientos y puede estar utilizada en alguna empresa, ya que es plan de cuenta comun, no se puede eliminar.");';                
				}
			?>
		</script>			

		<?php include 'footer.php'; ?>

	</body>
</html>
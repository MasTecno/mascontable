<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SWCTA=0;
	$SQL="SELECT * FROM CTCategoria WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		if ($registro["N1"]==0) {
			$SWCTA=1;
		}
	}	
	
	if ($SWCTA==1) {
		$SQL="SELECT * FROM CTCategoria WHERE estado='A'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
		
			$SQL1="SELECT * FROM CTCuentas WHERE id_categoria='".$registro["id"]."' AND estado='A' LIMIT 1";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$d1=substr($registro1["numero"],0,1);
				$d2=substr($registro1["numero"],1,1);

				$scr="UPDATE CTCategoria SET N1='".$d1."', N2='".$d2."' WHERE id='".$registro["id"]."';";
				$mysqli->query($scr);

			}
		}
	}

	$mysqli->close();

	$sw=0;
	$xauxiliar="O";
	$sw1=0;
	if(isset($_POST['idmod']) && $_POST['idmod']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S") {
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE id='".$_POST['idmod']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE id='".$_POST['idmod']."'";
		}
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xnumero=$registro["numero"];
			$xdetalle=strtoupper($registro["detalle"]);
			$xidcategoria=$registro["id_categoria"];
			$xauxiliar=$registro["auxiliar"];
			if ($registro["ingreso"]=="S"){
				$sw1=1;
			}
		} 
		$mysqli->close();
	}

	if (isset($_POST['idempb']) && $_POST['idempb']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S") {
			$mysqli->query("UPDATE CTCuentasEmpresa SET estado='B' WHERE id='".$_POST['idempb']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("UPDATE CTCuentas SET estado='B' WHERE id='".$_POST['idempb']."'");
		}
		$mysqli->close();
	}

	if (isset($_POST['idempa']) && $_POST['idempa']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S"){
			$mysqli->query("UPDATE CTCuentasEmpresa SET estado='A' WHERE id='".$_POST['idempa']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("UPDATE CTCuentas SET estado='A' WHERE id='".$_POST['idempa']."'");
		}
		$mysqli->close();
	}

	if (isset($_POST['ideli']) && $_POST['ideli']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		if ($_SESSION["PLAN"]=="S") {
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE id='".$_POST['ideli']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE id='".$_POST['ideli']."'";
		}










		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$Lxnumero=$registro["numero"];
		} 

		if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTRegLibroDiario WHERE cuenta='$Lxnumero' AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$mysqli->query("DELETE FROM CTCuentasEmpresa WHERE id='".$_POST['ideli']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
			}else{
				$NoElimina="N";
			}
		}else{
			$SQL="SELECT * FROM CTRegLibroDiario WHERE cuenta='$Lxnumero'";// AND rutempresa='".$_SESSION['RUTEMPRESA']."'";
			$resultados = $mysqli->query($SQL);
			$row_cnt = $resultados->num_rows;
			if ($row_cnt==0) {
				$mysqli->query("DELETE FROM CTCuentas WHERE id='".$_POST['ideli']."'");
			}else{
				$NoEliminaCom="N";
			}
		}
		$mysqli->close();
	}

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTIngresoEgreso WHERE estado='A'";
	$resultados = $mysqli->query($SQL);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		$SQL="SELECT * FROM CTIngresoEgreso WHERE estado='A'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {		
			$mysqli->query("UPDATE CTCuentasEmpresa SET ingreso='S' WHERE numero='".$registro['cuenta']."' AND estado='A'");
			$mysqli->query("UPDATE CTCuentas SET ingreso='S' WHERE numero='".$registro['cuenta']."' AND estado='A'");
		}
		$mysqli->query("DELETE FROM CTIngresoEgreso");
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
			function CtaCont(){
				var url= "frmCuentasBuscar.php";
				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					form1.numero.value=resp;
				}
				});				
			}

			function Grilla(){
				var url= "frmCuentasGrilla.php";
				$.ajax({
				type: "POST",
				url: url,
				data: $('#form1').serialize(),
				success:function(resp){
					$('#TableCta').html(resp);
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
			function GenLibro(){
				form1.method="POST";
				form1.target="_blank";
				form1.action="frmCuentasXLS.php";
				form1.submit();
				form1.target="";
				form1.action="#";
			}
				

		</script>
	</head>
	<body onload="Grilla()">

	<?php 
		include 'nav.php';
	?>

		<div class="container-fluid">
		<div class="row content">
			
			<form action="xfrmCuentas.php" method="POST" name="form1" id="form1">
				<input type="hidden" name="idempb" id="idempb">
				<input type="hidden" name="idempa" id="idempa">
				<input type="hidden" name="ideli" id="ideli">
				<input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">


				<br>
				<div class="col-md-8">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
						<div class="panel-heading">Mantenedor de Cuentas</div>
						<div class="panel-body">

							<div class="col-md-4">
							<div class="input-group">
								<span class="input-group-addon">Categor&iacute;a</span>
								<select class="form-control" id="SelCat" name="SelCat" onchange="CtaCont()" required>
									<option value="">Selecciones</option>
									<?php 
										$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
										$SQL="SELECT * FROM CTCategoria WHERE estado<>'X'";
										$resultados = $mysqli->query($SQL);
										while ($registro = $resultados->fetch_assoc()) {
											if ($xidcategoria!="") {
												if ($xidcategoria==$registro["id"]) {
													echo "<option value ='".$registro["id"]."' selected>".$registro["nombre"]."</option>";
												}else{
													echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
												}
											}else{
												echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
											}
										}
										$mysqli->close();
									?>
								</select>
							</div>
							</div>

							<div class="clearfix"></div>
							<br>

							<div class="col-md-3">
							<div class="input-group">
								<span class="input-group-addon">N&uacute;mero</span>
								<input type="text" class="form-control" id="numero" name="numero" autocomplete="off" value="<?php echo $xnumero; ?>" <?php if($sw==1){ echo 'readonly="false"';}?> required>
							</div>
							</div> 

							<div class="col-md-9">
							<div class="input-group">
								<span class="input-group-addon">Nombre</span>
								<input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xdetalle; ?>"  autocomplete="off" required>
							</div>
							</div> 

							<div class="clearfix"> </div>
							<br>
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Control de Ingreso o Egreso</div>
						<div class="panel-body">
							<div class="radio">
							<label><input type="radio" name="t1" value="S" <?php if ($sw1==1) { echo "checked"; } ?>>SI</label>
							</div>
							<div class="radio">
							<label><input type="radio" name="t1" value="N" <?php if ($sw1==0) { echo "checked"; } ?>>NO</label>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
					<div class="panel-heading">Control Auxiliar</div>
						<div class="panel-body">
							<div class="radio">
							<label><input type="radio" name="opt1" value="X" <?php if ($xauxiliar=="X") { echo "checked"; } ?>>Auxiliar</label>
							</div>
							<div class="radio">
							<label><input type="radio" name="opt1" value="E" <?php if ($xauxiliar=="E") { echo "checked"; } ?>>Efectivo</label>
							</div>
							<div class="radio">
							<label><input type="radio" name="opt1" value="B" <?php if ($xauxiliar=="B") { echo "checked"; } ?>>Banco</label>
							</div>
							<div class="radio">
							<label><input type="radio" name="opt1" value="N" <?php if ($xauxiliar=="N" || $xauxiliar=="O") { echo "checked"; } ?>>No Aplica</label>
							</div>
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
					<div cl class="col-sm-2">
						<button type="button" class="btn btn-sm btn-exportar btn-block" onclick="GenLibro()">
							<span class="glyphicon glyphicon-file"></span> Exportar Excel
						</button>      
					</div>
					<div class="clearfix"></div>
					<br>
					<div id="TableCta">
					</div>
				</div>
			</form>
		</div>
		</div>
		<script>
			<?php
				if (isset($_GET['ex']) && $_GET['ex']=="yes") {
					echo 'alert ("Numero de cuenta ya registrada");';
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
<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

	if ($_POST['Ecasilla']!="") {
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

		$SQL="SELECT nombre, id FROM CTAsientoPlantilla WHERE id='".$_POST['Ecasilla']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$mysqli->query("DELETE FROM CTAsientoPlantilla WHERE nombre='".$registro['nombre']."'");
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
			function Procesar(valor){
				form1.casilla.value=valor;
				form1.action="frmPlantillas.php";
				form1.submit();
			}
			function Eliminar(valor,nombre){
				if (confirm("Esta seguro de eliminar la plantilla: " + nombre) == true) {
					form1.Ecasilla.value=valor;
					form1.submit();
				} 
			}
		</script>
	</head>

	<body>


	<?php include '../nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="#" method="POST" name="form1" id="form1">
			<input type="hidden" name="casilla" id="casilla">
			<input type="hidden" name="Ecasilla" id="Ecasilla">


			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">            
				<h3>Plantillas de Voucher Grabadas</h3> 

				<table class="table table-hover">
				<thead>
					<tr>
						<th width="5%">#</th>
						<th width="60%">Nombre</th>
						<th width="20%">Tipo</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$Cont=1;
						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
						$SQL="SELECT nombre, id, tipo FROM CTAsientoPlantilla GROUP BY nombre ORDER BY nombre";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							$TipMov="Sin Definir";
							if($registro['tipo']=="I"){
								$TipMov="Ingreso";
							}else{
								if($registro['tipo']=="E"){
									$TipMov="Egreso";
								}else{
									if($registro['tipo']=="T"){
										$TipMov="Traspaso";
									}					
								}
							}

							echo'
								<tr>
									<td>'.$Cont.'</td>
									<td>'.$registro['nombre'].'</td>
									<td>'.$TipMov.'</td>
									<td>
										<button type="button" class="btn btn-sm btn-grabar" onclick="Procesar('.$registro["id"].')">
											<span class="glyphicon glyphicon-pencil"></span> Utilizar
										</button>
									</td>
									<td>
										<button type="button" class="btn btn-sm btn-cancelar" onclick="Eliminar('.$registro["id"].',\''.$registro['nombre'].'\')">
											<span class="glyphicon glyphicon-remove"></span> Eliminar
										</button>
									</td>
								</tr>
							';
							$Cont++;
						}

						$mysqli->close();
					?>
				</tbody>
				</table>

			</div>
			<div class="col-sm-2">
			</div>
			<input type="hidden" name="Clineas" id="Clineas" value="<?php echo ($Nlinea-1); ?>">
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>

<br><br>

	<?php include '../footer.php'; ?>

	</body>
</html>
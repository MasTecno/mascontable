<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	if (isset($_GET['Cont'])) {
		$XidCont=$_GET['Cont'];
	}else{
		if (isset($_POST['ListCont'])) {
			$XidCont=$_POST['ListCont'];
		}
	}


	

?>
<!DOCTYPE html>
<html >
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

		<style>
			/* Remove the navbar's default margin-bottom and rounded borders */
			.navbar {
				margin-bottom: 0;
				border-radius: 0;
			}

			/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
			.row.content {height: 450px}

			/* Set gray background color and 100% height */
			.sidenav {
				padding-top: 20px;
				background-color: #f1f1f1;
				height: 100%;
			}

			/* Set black background color, white text and some padding */
			footer {
				background-color: #555;
				color: white;
				padding: 15px;
			}

			/* On small screens, set height to 'auto' for sidenav and grid */
			@media screen and (max-width: 767px) {
				.sidenav {
					height: auto;
					padding: 15px;
				}
				.row.content {height:auto;}
			}


		</style>
		<script type="text/javascript">
			function Refr(){
				form1.action="frmAsignaEmpresa.php?Cont="+form1.ListCont.value;
				form1.submit();
			}
			function Elim(valor){
				form1.idmov.value=valor;
				form1.action="xfrmAsignaEmpresa.php";
				form1.submit();
			}
			function Asig(valor){
				form1.idmov.value=valor;
				form1.action="xfrmAsignaEmpresa.php";
				form1.submit();
			}


		</script>

	</head>

	<body>


	<?php include 'nav.php'; ?>

	<div class="container-fluid text-left">
	<div class="row content">

		<form action="" method="POST" name="form1" id="form1">
			<br>
			<div class="col-md-2">
			</div>
			<div class="col-md-8">


			<div class="col-md-12">
			<div class="input-group">
				<span class="input-group-addon">Contador</span>
				<select id="ListCont" name="ListCont" class="form-control" onchange="Refr()" required>
					<option value="">Usuarios</option>
					<?php
						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
						$SQL="SELECT * FROM CTContadores WHERE estado='A' AND tipo='U'";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {
							if ($XidCont==$registro["id"]) {
								echo '<option value="'.$registro["id"].'" selected>'.$registro["nombre"].'</option>';
							}else{
								echo '<option value="'.$registro["id"].'">'.$registro["nombre"].'</option>';
							}
						}  
						$mysqli->close();

					?>
				</select>
				<input type="hidden" name="idmov" id="idmov">
			</div>
			<span>* Si el Contador no tiene acceso a una empresa, tiene disponible el acceso a todas las empresas.</span>
			</div> 

			<div class="clearfix"></div>
			<br>
					<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar por Razón Social...">
						
				<table class="table table-hover table-condensed">
				<thead>
					<tr>
						<th width="1%" style="text-align: center;">Permiso</th>
						<th width="10%" style="text-align: right;">Rut</th>
						<th width="">Raz&oacute;n Social</th>
					</tr>
				</thead>
				<tbody id="myTable">
					<?php

					if ($XidCont!="") {

						$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
						$SQL="SELECT * FROM CTEmpresas WHERE estado='A' ";
						$resultados = $mysqli->query($SQL);
						while ($registro = $resultados->fetch_assoc()) {

							$SQL1="SELECT * FROM CTContadoresAsignado WHERE rutempresa='".$registro["rut"]."' AND idcontador='".$XidCont."'";
							$resultados1 = $mysqli->query($SQL1);
							$row_cnt = $resultados1->num_rows;
							if ($row_cnt==0) {
								echo '
									<tr>
										<td style="text-align: right;">
											<button  type="button" class="btn btn-danger btn-xs" onclick="Asig('.$registro["id"].');">
												<span class="glyphicon glyphicon-eye-close"></span> Sin Acceso
											</button>
										</td>
										<td style="text-align: right;">'.$registro["rut"].'</td>
										<td>'.$registro["razonsocial"].'</td>
									</tr>							
								';
							}else{
								echo '
									<tr>
										<td style="text-align: right;">
											<button type="button" class="btn btn-success btn-xs" onclick="Elim('.$registro["id"].');">
												<span class="glyphicon glyphicon-eye-open"></span> Acceso
											</button>
										</td>
										<td style="text-align: right;">'.$registro["rut"].'</td>
										<td>'.$registro["razonsocial"].'</td>
									</tr>							
								';
							}
						}  

						$mysqli->close();
					}

					?>
				</tbody>
				</table>




			</div>
			<div class="col-md-2">
			</div>
		</form>

	</div>
	</div>

	<div class="clearfix"> </div>
	<br>
	<br>
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

	<?php include 'footer.php'; ?>

	</body>
</html>
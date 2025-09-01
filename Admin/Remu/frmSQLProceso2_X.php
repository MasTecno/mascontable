<?php
	include 'conexionserver.php';

	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../../index.php?Msj=95");
		exit;
	}
	
?> 
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript">
	        function ProcesarStr(valor, valor1){

				if (document.getElementById(valor).value==0) {
					alert("No se puede procesar el String del Server: "+valor);
				}else{
					var Ck="Ck"+valor;
					var Bt="Bt"+valor;
					var url= "xsqlProcesoX.php";
					var Ms='#Msj'+valor;
					form1.sel1.value=valor1;
					$.ajax({
						type: "POST",
						url: url,
						data: $('#form1').serialize(),
						success:function(resp){
							$(Ms).html(resp);
							document.getElementById(valor).value=0;
	        				document.getElementById(Bt).disabled = true;
	        				document.getElementById(Ck).checked = false;
						}
					});
				}


	        } 
	        function Chec(valor){
				var Bt="Bt"+valor;

	        	if (document.getElementById(valor).value==0) {
	        		document.getElementById(valor).value=1;
	        		document.getElementById(Bt).disabled = false;
	        	}else{
	        		document.getElementById(valor).value=0;
	        		document.getElementById(Bt).disabled = true;
	        	}
	        }

			function seleccionar_todo(){
			for (i=0;i<document.form1.elements.length;i++)
				if(document.form1.elements[i].type == "checkbox"){
					document.form1.elements[i].checked=1;
				}
			}

			function deseleccionar_todo(){
			for (i=0;i<document.form1.elements.length;i++)
				if(document.form1.elements[i].type == "checkbox"){
					document.form1.elements[i].checked=0
				}
			}


		</script>
		<style type="text/css">
			.table {
				font-size: 10px;			
			}
		</style>
	</head>
	<body>
		<div class="container-fluid text-left">
		<div class="row content">

			<div class="col-sm-12 text-left">
				<br>

				<div class="well well-sm">
					<h1>Sistema de Remuneraciones</h1>
				</div>

				<form action="#" method="POST" name="form1" id="form1">

					<div class="col-md-12">
					<div class="input-group">
						<span class="input-group-addon">String SQL</span>
						<textarea class="form-control" rows="10" id="SqlScript" name="SqlScript" required="" style="font-size: 12px;"></textarea>
						<input type="hidden" name="sel1" id="sel1">
					</div>
					</div> 
					<br>
					<br>

					<div class="col-sm-12">
						
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Tip</th>
									<th>Server</th>
									<th>Mensaje</th>
									<th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
							<?php

								$mysqli=conectarServer();

								$sql = "SELECT * FROM UnionServer WHERE Estado='A' ORDER BY Server";
								$resultado = $mysqli->query($sql);

								while ($registro = $resultado->fetch_assoc()) {
									echo '
										<tr>
											<td>
												<input type="hidden" name="'.$registro["Server"].'" id="'.$registro["Server"].'" value="0">
												<div class="checkbox">
  													<label><input type="checkbox" value="" id="Ck'.$registro["Server"].'" onclick="Chec(\''.$registro["Server"].'\')">'.$registro["Server"].'</label>
												</div>
											</td>
											<td>'.$registro["Server"].'</td>
											<td>
												<dir id="Msj'.$registro["Server"].'">
												</dir>
											</td>
											<td>
												<button type="button" class="btn btn-default btn-xs" id="Bt'.$registro["Server"].'" onclick="ProcesarStr(\''.$registro["Server"].'\','.$registro["id"].')" disabled>Procesar</button>
											</td>
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
		</div>
	</body>
</html>
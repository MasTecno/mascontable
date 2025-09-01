<?php
	// include '../../conexion/conexion.php';
	include '../../js/funciones.php';
	include '../conexionserver.php';

	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../index.php?Msj=95");
		exit;
	}

	$mysqli=conectarServer();

	$SQL="SELECT * FROM FacturasMastecno WHERE Codigo=''";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$CodigoDoc=randomTextSV(35);
		$i=0;

		while ($i==0) {
			// echo "<br>";
			$SQL1="SELECT * FROM FacturasMastecno WHERE Codigo='".$CodigoDoc."'";
			$resultados1 = $mysqli->query($SQL1);
			$row_cnt = $resultados1->num_rows;
			if ($row_cnt==0) {	
				$mysqli->query("UPDATE FacturasMastecno SET Codigo='".$CodigoDoc."' WHERE id='".$registro['id']."'");
				$i=1;
			}else{
				$CodigoDoc=randomTextSV(35);
			}
		}
	}

	$SQL="SELECT * FROM FacturasMastecno WHERE Server=''";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {

		$SQL1="SELECT * FROM UnionServer WHERE id='".$registro['id_Server']."'";
		$resultados1 = $mysqli->query($SQL1);
		while ($registro1 = $resultados1->fetch_assoc()) {

			$mysqli->query("UPDATE FacturasMastecno SET Server='".$registro1['Server']."' WHERE id='".$registro['id']."'");

		}
	}

	$mysqli->close();

?> 
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript">
			function Volver(){
				form1.action="../frmMain.php";
				form1.submit();
			}
			function Datos(d1,d2,d3,d4){
				form1.NServer.value=d1;
				form1.NIdServer.value=d2;
				form1.PServer.value=d3;
				document.getElementById(d4).checked = true;
				// document.getElementById('OpPago').value=d4;
				//document.getElementById(d4).selected;
				DContacto();
			}

			function DContacto(){
				var url= "DatosContacto.php";
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'json',
					data: $('#form1').serialize(),
					success:function(resp){
						$("#NContacto").val(resp.dato1);
						$("#NFPago").val(resp.dato2);
						//document.getElementById("XEstado").selected;
					}
				});	
			}

			function Porcesar(){
				form1.action="ProDocumento.php";
				form1.submit();
			}

			function Refre(){
				form1.action="../Facturacion";
				form1.submit();
			}

		</script>

	</head>
	<body>
		<div class="container-fluid text-left">
		<div class="row content">
			<form name="form1" id="form1" method="POST" action="#">
			<div class="col-md-12 text-left">
				<br>

				<div class="well well-sm">
					<strong>Carga de Facturas </strong>
				</div>
				<div class="col-md-12 text-center">
					<button type="button" class="btn btn-success" onclick="Refre()">Refresca</button>
					<button type="button" class="btn btn-danger" onclick="Volver()">Volver</button>
				</div>
			</div>
				
			<dir class="clearfix"></dir>
			<br>


				<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Control</h4>
						</div>
						<div class="modal-body">

							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Server</span>
								<input id="NServer" name="NServer" type="text" class="form-control" autocomplete="false" required>
								<input type="hidden" name="NIdServer" id="NIdServer">
							</div>
							</div> 

							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Periodo</span>
								<input id="PServer" name="PServer" type="text" class="form-control" autocomplete="false" required>
							</div>
							</div> 
							<div class="clearfix"></div>
							<br>

							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">Contacto</span>
								<input id="NContacto" name="NContacto" type="text" class="form-control" autocomplete="false" required>
							</div>
							</div> 

							<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon">F. Pago</span>
								<input id="NFPago" name="NFPago" type="text" class="form-control" autocomplete="false" required>
							</div>
							</div> 


							<div class="clearfix"></div>
							<br>
							<br>
							<br>

							<!-- checked -->
							<div class="col-md-6">
								<label class="radio-inline"><input type="radio" name="OpPago" id="A" Value="A">Sin Pagar</label>
								<label class="radio-inline"><input type="radio" name="OpPago" id="C" Value="C">Pagado</label>
								<label class="radio-inline"><input type="radio" name="OpPago" id="S" Value="S">Salto</label>

							</div>

							<div class="clearfix"></div>
							<br>


						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal" onclick="Porcesar()">Procesar</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
				</div>


			<div class="col-md-12 text-left">

				<table class="table table-hover table-bordered" style="font-size: 10px;">
					<thead>
						<tr>
							<th>Servidor</th>
							<th>Nombre</th>
							<th>FPago</th>
							<th style="text-align: center;">Ene</th>
							<th style="text-align: center;">Feb</th>
							<th style="text-align: center;">Mar</th>
							<th style="text-align: center;">Abr</th>
							<th style="text-align: center;">May</th>
							<th style="text-align: center;">Jun</th>
							<th style="text-align: center;">Jul</th>
							<th style="text-align: center;">Ago</th>
							<th style="text-align: center;">Sep</th>
							<th style="text-align: center;">Oct</th>
							<th style="text-align: center;">Nov</th>
							<th style="text-align: center;">Dic</th>
							<th style="text-align: center;">Ene</th>
							<th style="text-align: center;">Feb</th>
							<th style="text-align: center;">Mar</th>
							<th style="text-align: center;">Abr</th>
							<th style="text-align: center;">May</th>
							<th style="text-align: center;">Jun</th>
							<th style="text-align: center;">Jul</th>
							<th style="text-align: center;">Ago</th>
							<th style="text-align: center;">Sep</th>
							<th style="text-align: center;">Oct</th>
							<th style="text-align: center;">Nov</th>
							<th style="text-align: center;">Dic</th>
						</tr>
					</thead>
					<tbody id="DetaFact">

<?php
			$mysqli=conectarServer();

			$SQL1="SELECT * FROM UnionServer ORDER BY Numero";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$XContacto="";
				$Cont=1;

				$Ano=2019;
				$Mes=1;

				$SQL2="SELECT * FROM DatosPersonales WHERE idServer='".$registro1['id']."'";
				$resultados2 = $mysqli->query($SQL2);
				while ($registro2 = $resultados2->fetch_assoc()) {
					$XContacto=$registro2['Contacto'];
					$XFPago=$registro2['FPago'];
				}

					echo '<tr id="'.$registro1['Server'].'">
								<td>'.$registro1['Server'].'</td>
								<td>'.strtoupper($XContacto).'</td>
								<td>'.date('d-m-Y',strtotime($XFPago)).'</td>
					';

							while ($Cont<=24) {

								if ($Mes==13) {
									$Mes=1;
									$Ano=$Ano+1;
								}

								if ($Mes<=9) {
									$PerLinea="0".$Mes."-".$Ano;
								}else{
									$PerLinea=$Mes."-".$Ano;
								}
								
								$XNumero="";
								$XMonto="";
								$XEstado="";
								$Color="";

								$SQL="SELECT * FROM FacturasMastecno WHERE Periodo='".$PerLinea."' AND id_Server='".$registro1['id']."'";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {
									$XNumero=$registro['Numero'];
									$XMonto=$registro['Monto'];
									$XEstado=$registro['Estado'];

								}

								if ($XEstado=="C") {
									$Color="#8bc34a;";
								}else{
									if ($XEstado=="A") {
										$Color="#f44336;";
									}else{
										$XEstado="S";
									}
								}

								echo '
									<td style="background-color:'.$Color.'" data-toggle="modal" data-target="#myModal" onclick="Datos(\''.$registro1['Server'].'\',\''.$registro1['id'].'\',\''.$PerLinea.'\',\''.$XEstado.'\')" >'.$PerLinea.'</td>
								';

								$Mes++;

								$Cont++;

							}

								echo '</tr>';
	
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
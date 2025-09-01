<?php
	include 'conexionserver.php';
	include 'conexionserverRemu.php';
	include 'conexion.php';
	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:index.php?Msj=95");
		exit;
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<style>

		.table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
			border: 1px solid #de9226;
		}

		.table-bordered {
			border: 1px solid #de9226;
		}

		</style>

		<script type="text/javascript">
			function Bitacora(valor){
				form1.Bita.value=valor;
				form1.action="frmBitacora.php";
				form1.submit();
			}
			function Personal(valor){
				form1.Pers.value=valor;
				form1.action="frmPersonal.php";
				form1.submit();
			}
			function Pago(valor){
				form1.Bita.value=valor;
				form1.action="frmPagos.php";
				form1.submit();
			}

			function sortTable(n) {
			  // var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  // table = document.getElementById("myTable");
			  // switching = true;
			  // //Set the sorting direction to ascending:
			  // dir = "asc"; 
			  // /*Make a loop that will continue until
			  // no switching has been done:*/
			  // while (switching) {
			  //   //start by saying: no switching is done:
			  //   switching = false;
			  //   rows = table.rows;
			  //   /*Loop through all table rows (except the
			  //   first, which contains table headers):*/
			  //   for (i = 0; i < (rows.length - 1); i++) {
			  //     //start by saying there should be no switching:
			  //     shouldSwitch = false;
			  //     /*Get the two elements you want to compare,
			  //     one from current row and one from the next:*/
			  //     x = rows[i].getElementsByTagName("TD")[n];
			  //     y = rows[i + 1].getElementsByTagName("TD")[n];
			  //     /*check if the two rows should switch place,
			  //     based on the direction, asc or desc:*/
			  //     if (dir == "asc") {
			  //       if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
			  //         //if so, mark as a switch and break the loop:
			  //         shouldSwitch= true;
			  //         break;
			  //       }
			  //     } else if (dir == "desc") {
			  //       if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
			  //         //if so, mark as a switch and break the loop:
			  //         shouldSwitch = true;
			  //         break;
			  //       }
			  //     }
			  //   }
			  //   if (shouldSwitch) {
			  //     /*If a switch has been marked, make the switch
			  //     and mark that a switch has been done:*/
			  //     rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			  //     switching = true;
			  //     //Each time a switch is done, increase this count by 1:
			  //     switchcount ++;      
			  //   } else {
			  //     /*If no switching has been done AND the direction is "asc",
			  //     set the direction to "desc" and run the while loop again.*/
			  //     if (switchcount == 0 && dir == "asc") {
			  //       dir = "desc";
			  //       switching = true;
			  //     }
			  //   }
			  // }
			}

		</script>
	</head>

	<body onload="form1.myInput.focus();">
		<div class="container-fluid text-left">
		<div class="row content">

			<form action="" method="POST" name="form1" id="form1">

				<div class="col-md-12">
					<br>
					<br>
					<input class="form-control" id="myInput" type="text" placeholder="Buscar...">
					<input type="hidden" name="Bita" id="Bita">
					<input type="hidden" name="Pers" id="Pers">
					<input type="hidden" name="Pagox" id="Pagox">
					<br>

					<table class="table table-bordered table-hover" style="font-size: 10px;">
						<thead>
							<tr>
								<th onclick="sortTable(0)">Server</th>
								<th onclick="sortTable(1)">Usuario</th>
								<th onclick="sortTable(2)">Ultimo Conta</th>
								<th onclick="sortTable(3)">Ultimo Remu</th>
								<th onclick="sortTable(4)">Plan</th>
								<th onclick="sortTable(5)">Correo</th>
								<th onclick="sortTable(6)">P. Pago</th>
								<!-- <th onclick="sortTable(5)">Ultimo Contacto</th> -->
								<th>Acciones</th>
							</tr>
						</thead>

						<tbody id="myTable">
							<?php 
								$mysqli=conectarServer();

								$MySqliRemu=conectarServerRemu();



								$SQL="SELECT * FROM UnionServer WHERE Estado='A' ORDER BY Numero ASC";
								$resultados = $mysqli->query($SQL);
								while ($registro = $resultados->fetch_assoc()) {

									$mysqliX=xconectar($registro["Usuario"],$registro["Clave"],$registro["Base"]);

									$NumSer=str_replace("Server","",$registro['Server']);
									$NumSer=($NumSer*1);
									//echo "-";

									//$mysqli->query("UPDATE UnionServer SET Numero='$NumSer' WHERE Server='".$registro['Server']."' AND id='".$registro['id']."'");


									$Fecha="";
									// echo $NumSer."<br>";
									$sqlin = "SELECT nombre, max(Ingreso) as maxi, correo FROM CTContadores where Correo<>'admin@mastecno.cl'";
									$resultadoin = $mysqliX->query($sqlin);
									while ($registroin = $resultadoin->fetch_assoc()) {
										$Contador=$registroin["nombre"];
										$Correo=$registroin["correo"];
										if ($registroin["maxi"]=="0000-00-00 00:00:00") {
											$Fecha="";
										}else{
											if ($registroin["maxi"]=="") {
												$Fecha="";
											}else{
												$Fecha= date('d-m-Y',strtotime($registroin["maxi"]));
											}
										}
									}

									$idPlan="";
									$fPago="";
									$sqlin = "SELECT * FROM DatosPersonales where idServer='".$registro["id"]."' AND Estado='A'";
									$resultadoin = $mysqli->query($sqlin);
									while ($registroin = $resultadoin->fetch_assoc()) {
										$idPlan=$registroin["idPlan"];
										$fPago=$registroin["FPago"];
										$Correo=$registroin["Correo"];
									}

									if ($fPago=="0000-00-00") {
										$fPago="";
									}else{
										if ($fPago=="") {
											$fPago="";
										}else{
											$fPago= date('d-m-Y',strtotime($fPago));
										}
									}

									$NomPlan="";
									$sqlin = "SELECT * FROM Planes where id='$idPlan'";
									$resultadoin = $mysqli->query($sqlin);
									while ($registroin = $resultadoin->fetch_assoc()) {
										$NomPlan=$registroin["Nombre"];
									}

									$FechaRemu="";
									$SQLRemu="SELECT * FROM UnionServer WHERE Server='".$registro['Server']."'";
									$resu = $MySqliRemu->query($SQLRemu);
									while ($regRemu= $resu->fetch_assoc()) {

										$NumSerRem=str_replace("Server","",$registro['Server']);
										$NumSerRem=($NumSerRem*1);
										//echo "-";
										//$MySqliRemu->query("UPDATE UnionServer SET Numero='$NumSerRem' WHERE Server='".$regRemu['Server']."' AND id='".$regRemu['id']."'");


										$MyRemu=xconectar($regRemu["Usuario"],$regRemu["Clave"],$regRemu["Base"]);

										// echo "UPDATE UnionServer SET Numero='$NumSerRem' WHERE Server='".$regRemu['Server']."' AND id='".$regRemu['id']."'<br>";

										$FechaRemu="";
										$sqlin = "SELECT nombre, max(ingreso) as maxi FROM RMAcceso where correo<>'admin@mastecno.cl'";

										$resultadoin = $MyRemu->query($sqlin);

										while ($registroin = $resultadoin->fetch_assoc()) {
											if ($registroin["maxi"]=="0000-00-00 00:00:00") {
												$FechaRemu="";
											}else{
												if ($registroin["maxi"]=="") {
													$FechaRemu="";
												}else{
													$FechaRemu= date('d-m-Y',strtotime($registroin["maxi"]));
												}
											}
										}

										$MyRemu->close();
									}

									echo '
										<tr>
											<td>'.strtoupper($registro["Server"]).'</td>
											<td>'.strtoupper($Contador).'</td>
											<td>'.$Fecha.'</td>
											<td>'.$FechaRemu.'</td>
											<td>'.$NomPlan.'</td>
											<td>'.$Correo.'</td>
											<td>'.$fPago.'</td>
											<td>
												<button type="button" class="btn btn-default btn-xs" onclick="Personal('.$registro["id"].')">Informaci&oacute;n</button>
												<button type="button" class="btn btn-default btn-xs" onclick="Bitacora('.$registro["id"].')">Bitacora</button>
												<button type="button" class="btn btn-default btn-xs" onclick="Pago('.$registro["id"].')">Pago</button>
											</td>
										</tr>
									';
								}
							?>
						</tbody>
					</table>
				</div>
			</form>

		</div>
		</div>

		<div class="clearfix"> </div>

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

	</body>

</html>
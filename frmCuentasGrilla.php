<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

echo '
					<table class="table table-condensed table-hover">
						<thead>
							<tr style="background-color: #d9d9d9;">
								<th width="10%">Codigo</th>
								<th>Cuenta</th>
								<th>T&iacute;po</th>
								<th>Categor&iacute;a</th>
								<th class="text-center">Ingreso/Egreso</th>
								<th class="text-center">Auxiliar</th>
								<th width="1%"></th>
								<th width="1%"></th>
								<th width="1%"></th>
							</tr>
						</thead>
						<tbody id="myTable">

';



		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		if ($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE estado<>'X' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero ASC";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE estado<>'X' ORDER BY numero ASC";
		}
		// echo $SQL;
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$tipcat="";

			$SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
			$resultados1 = $mysqli->query($SQL1);
			while ($registro1 = $resultados1->fetch_assoc()) {
				$tipcat=$registro1["nombre"];
				$tiptip=$registro1["tipo"];
			}
 
			$mens="";
			if($registro["ingreso"]=="S"){
				$mens='SI';
			}
			// $SQL1="SELECT * FROM CTIngresoEgreso WHERE cuenta='".$registro["numero"]."'";
			// $resultados1 = $mysqli->query($SQL1);
			// $row_cnt = $resultados1->num_rows;
			// if ($row_cnt>0) {
			// 	$mens='SI';
			// }
			if ($_SERVER["REQUEST_URI"]=="/frmCuentasXLS.php") {
				echo '
					<tr>
					<td>'.$registro["numero"].'</td>
					<td>'.strtoupper(utf8_decode($registro["detalle"])).'</td>
					<td>'.$tiptip.'</td>
					<td>'.utf8_decode($tipcat).'</td>
					<td class="text-center">'.$mens.'</td>
				';
			}else{
				echo '
					<tr>
					<td>'.$registro["numero"].'</td>
					<td>'.strtoupper($registro["detalle"]).'</td>
					<td>'.$tiptip.'</td>
					<td>'.$tipcat.'</td>
					<td class="text-center">'.$mens.'</td>
				';
			}

				if($registro["auxiliar"]=="E"){
					echo '<td class="text-center">EFECTIVO</td>';
				}else{
					if($registro["auxiliar"]=="B"){
						echo '<td class="text-center">BANCO</td>';
					}else{
						if($registro["auxiliar"]=="X"){
							echo '<td class="text-center">AUXILIAR</td>';
						}else{
								echo '<td></td>';
						}
					}
				}

				echo '<td><button type="button" class="btn btn-modificar btn-xs" onclick="Modifi('.$registro["id"].')">Modificar</button></td>';

			if($registro["estado"]=="B"){
				echo '<td><button type="button" class="btn btn-success btn-xs" onclick="Alta('.$registro["id"].')">Alta</button></td>';
			}else{
				echo '<td><button type="button" class="btn btn-warning btn-xs" onclick="Baja('.$registro["id"].')">Baja</button></td>';
			}
			echo '<td><button type="button" class="btn btn-cancelar btn-xs" onclick="Elimina('.$registro["id"].')">Eliminar</button></td>';


			echo '
			</tr>
			';
		}       
		$mysqli->close();

?>
						</tbody>
					</table>

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
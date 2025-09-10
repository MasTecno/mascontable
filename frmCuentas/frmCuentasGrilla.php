<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

echo '
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="10%">Codigo</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">T&iacute;po</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categor&iacute;a</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingreso/Egreso</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auxiliar</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
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
					<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["numero"].'</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.mb_strtoupper($registro["detalle"], 'UTF-8').'</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$tiptip.'</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.mb_strtoupper($tipcat, 'UTF-8').'</td>
					<td class="text-center">'.$mens.'</td>
				';
			}else{
				echo '
					<tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["numero"].'</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.mb_strtoupper($registro["detalle"], 'UTF-8').'</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$tiptip.'</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.mb_strtoupper($tipcat, 'UTF-8').'</td>
					<td class="text-center">'.$mens.'</td>
				';
			}

				if($registro["auxiliar"]=="E"){
					echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">EFECTIVO</td>';
				}else{
					if($registro["auxiliar"]=="B"){
						echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BANCO</td>';
					}else{
						if($registro["auxiliar"]=="X"){
							echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">AUXILIAR</td>';
						}else{
								echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>';
						}
					}
				}

				echo '<td><button type="button" class="mr-2 bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Modifi('.$registro["id"].')">Modificar</button></td>';

			if($registro["estado"]=="B"){
				echo '<td><button type="button" class="mr-2 bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Alta('.$registro["id"].')">Alta</button></td>';
			}else{
				echo '<td><button type="button" class="mr-2 bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Baja('.$registro["id"].')">Baja</button></td>';
			}
			echo '<td><button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Elimina('.$registro["id"].')">Eliminar</button></td>';


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
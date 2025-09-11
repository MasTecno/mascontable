<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../index.php?Msj=95");
		exit;
	}
	
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

      $d01=0;
      $d02=0;
      $d03=0;
      $d04=0;
      $d05=0;
      $d06=0;
      $d07=0;
      $d08=0;
      $d09=0;
      $d10=0;
      $d11=0;
      $d12=0;

    $SQL="SELECT * FROM CTFactores WHERE periodo='".$_POST['ano']."'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
      $d01=$registro['mes1'];
      $d02=$registro['mes2'];
      $d03=$registro['mes3'];
      $d04=$registro['mes4'];
      $d05=$registro['mes5'];
      $d06=$registro['mes6'];
      $d07=$registro['mes7'];
      $d08=$registro['mes8'];
      $d09=$registro['mes9'];
      $d10=$registro['mes10'];
      $d11=$registro['mes11'];
      $d12=$registro['mes12'];
    }
    $mysqli->close();
?>


	<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
		<table class="min-w-full divide-y divide-gray-300">
			<thead class="bg-gray-50">
				<tr>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mes</th>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factor de Conversi&oacute;n</th>
				</tr>
			</thead>
			<tbody class="bg-white divide-y divide-gray-200">
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Enero</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d01" name="d01" value="<?php echo $d01; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Febrero</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d02" name="d02" value="<?php echo $d02; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Marzo</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d03" name="d03" value="<?php echo $d03; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Abril</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d04" name="d04" value="<?php echo $d04; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Mayo</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d05" name="d05" value="<?php echo $d05; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Junio</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d06" name="d06" value="<?php echo $d06; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Julio</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d07" name="d07" value="<?php echo $d07; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Agosto</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d08" name="d08" value="<?php echo $d08; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Septiembre</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d09" name="d09" value="<?php echo $d09; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Octubre</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d10" name="d10" value="<?php echo $d10; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Noviembre</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d11" name="d11" value="<?php echo $d11; ?>" placeholder="0.00">
					</td>
				</tr>
				<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
					<td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Diciembre</td>
					<td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
						<input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" id="d12" name="d12" value="<?php echo $d12; ?>" placeholder="0.00">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
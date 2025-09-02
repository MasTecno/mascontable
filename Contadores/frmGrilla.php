<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

echo '
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="10%">Rut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="1%"></th>
            </tr>
        </thead>
        <tbody id="myTable">
';
            $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
            $SQL="SELECT * FROM CTContadoresFirma WHERE Estado<>'X' ORDER BY Nombre ASC";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {

                echo '
                    <tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["Rut"].'</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.strtoupper($registro["Nombre"]).'</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.strtoupper($registro["Cargo"]).'</td>
                ';
                echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Modifi('.$registro["Id"].')">
                <i class="fa fa-edit mr-1"></i>Modificar
                </button></td>';

                if($registro["Estado"]=="B"){
                    echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-success-700 bg-success-100 hover:bg-success-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 transition duration-200" onclick="Alta('.$registro["Id"].')">
                    <i class="fa fa-check mr-1"></i>Alta
                    </button></td>';
                }else{
                    echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="Baja('.$registro["Id"].')">
                    <i class="fa fa-ban mr-1"></i>Baja
                    </button></td>';
                }
                echo '<td><button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="Elimina('.$registro["Id"].')">
                    <i class="fa fa-trash mr-1"></i>Eliminar
                    </button></td>';


                echo '
                </tr>
                ';
            }
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
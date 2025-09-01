<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

echo '
    <table class="table table-condensed table-hover">
        <thead>
            <tr style="background-color: #d9d9d9;">
                <th width="10%">Rut</th>
                <th>Nombre</th>
                <th>Cargo</th>
                <th width="1%"></th>
                <th width="1%"></th>
                <th width="1%"></th>
            </tr>
        </thead>
        <tbody id="myTable">
';
            $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
            $SQL="SELECT * FROM CTContadoresFirma WHERE Estado<>'X' ORDER BY Nombre ASC";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {

                echo '
                    <tr>
                    <td>'.$registro["Rut"].'</td>
                    <td>'.strtoupper($registro["Nombre"]).'</td>
                    <td>'.strtoupper($registro["Cargo"]).'</td>
                ';
                echo '<td><button type="button" class="btn btn-modificar btn-xs" onclick="Modifi('.$registro["Id"].')">Modificar</button></td>';

                if($registro["Estado"]=="B"){
                    echo '<td><button type="button" class="btn btn-success btn-xs" onclick="Alta('.$registro["Id"].')">Alta</button></td>';
                }else{
                    echo '<td><button type="button" class="btn btn-warning btn-xs" onclick="Baja('.$registro["Id"].')">Baja</button></td>';
                }
                echo '<td><button type="button" class="btn btn-cancelar btn-xs" onclick="Elimina('.$registro["Id"].')">Eliminar</button></td>';


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
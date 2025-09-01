<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
?>
	<table class="table table-condensed table-hover">	
		<thead>
			<tr>
				<th style="text-align: right;">Rut</th>
				<th>&nbsp;&nbsp;</th>
				<th>Raz&oacute;n Social</th>
			</tr>
		</thead>
		<tbody id="TableRutRaz">
			<?php
				$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

				if ($_POST['SelCliPro']=="") {
					echo "<h3>La cuenta asociada no es de tipo auxiliar</h3>";
					$SQL="SELECT * FROM CTCliPro WHERE tipo='L' AND estado='A'";
				}else{
					$SQL="SELECT * FROM CTCliPro WHERE tipo='".$_POST['SelCliPro']."' AND estado='A'";
				}
  
				$resultados = $mysqli->query($SQL);
				while ($registro = $resultados->fetch_assoc()) {

					echo '
						<tr onclick="Buscar(\''.$registro["rut"].'\')">
							<td style="text-align: right;">'.$registro["rut"].'</td>
							<td>&nbsp;&nbsp;</td>
							<td>'.$registro["razonsocial"].'</td>
						</tr>
					';
				}
				$mysqli->close();
			?>
		</tbody>
	</table>

	<script>
		$(document).ready(function(){
			$("#BRutRaz").on("keyup", function() {
			var value = $(this).val().toLowerCase();
				$("#TableRutRaz tr").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
			});
		});
	</script>



	<input type="hidden" name="frm" id="frm" value="<?php echo $Par; ?>">
	<input type="hidden" name="TipoCta" id="TipoCta" value="<?php echo $tipocta; ?>">
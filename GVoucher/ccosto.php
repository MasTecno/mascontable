<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	$textfecha=date("d-m-Y");

?>
<option value="0">Default</option>
<?php
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if ($_POST['valcc']==1) {


		$sw=0;

		if(is_array($_POST['check_list'])){
			foreach($_POST['check_list'] as $selected) {

				if ($_POST["tdocumentos"]=="H") {
					$SQL="SELECT * FROM CTHonorarios WHERE id='".$selected."' AND rutempresa='$RutEmpresa'";
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$xkeyas=$registro['movimiento'];
					}
				}else{
					$SQL="SELECT * FROM CTRegDocumentos WHERE id='".$selected."' AND rutempresa='$RutEmpresa'";				
					$resultados = $mysqli->query($SQL);
					while ($registro = $resultados->fetch_assoc()) {
						$xkeyas=$registro['keyas'];
					}
				}

			}
		}

		$SQL="SELECT * FROM CTRegLibroDiario WHERE glosa<>'' AND rutempresa='$RutEmpresa' AND keyas='$xkeyas'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$xccosto=$registro['ccosto'];
		}

		$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			if ($xccosto==$registro['id']) {
				echo '<option value="'.$registro['id'].'" selected>'.($registro['nombre']).'</option>';
			}else{
				echo '<option value="'.$registro['id'].'">'.($registro['nombre']).'</option>';
			}
		}


	}else{
		$SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			echo '<option value="'.$registro['id'].'">'.($registro['nombre']).'</option>';
		}
	}

	$mysqli->close();
?>
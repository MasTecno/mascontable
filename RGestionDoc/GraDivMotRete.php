<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	if (isset($_SESSION['CARRITO'])) {
		if ($_POST['SwMov']=="E") {
			$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
			$mysqli->query("DELETE FROM CTRegDocumentosDivRete WHERE Id_Doc='".descriptSV($_POST['KeyMov'])."';");
			$mysqli->close();
		}else{
			$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
			$mysqli->query("DELETE FROM CTRegDocumentosDivRete WHERE Id_Doc='".descriptSV($_POST['KeyMov'])."';");
			$Total=0;
			foreach($_SESSION['CARRITO'] as $indice=>$producto){
				$NCta=$producto['SelCtaDivR'];
				$NCC=$producto['SelCCDivR'];
				$MonLi=$producto['MontDivR'];

				$mysqli->query("INSERT INTO CTRegDocumentosDivRete VALUES('','".descriptSV($_POST['KeyMov'])."','$NCta','$NCC','$MonLi','A');");
			}
			$mysqli->close();			
		}
	}
	unset($_SESSION['CARRITO']);
?>
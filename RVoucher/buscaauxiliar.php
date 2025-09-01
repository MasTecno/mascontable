<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	if ($_SESSION["PLAN"]=="S"){
		$SQL1="SELECT CTCuentasEmpresa.numero, CTCuentasEmpresa.detalle, CTCategoria.nombre, CTCuentasEmpresa.auxiliar, CTCategoria.tipo, CTCuentasEmpresa.estado";
		$SQL1=$SQL1." FROM CTCuentasEmpresa LEFT JOIN CTCategoria ON CTCuentasEmpresa.id_categoria = CTCategoria.id ";
		$SQL1=$SQL1." WHERE CTCuentasEmpresa.auxiliar='X' AND CTCuentasEmpresa.estado='A' AND CTCuentasEmpresa.numero='".$_POST["Codigo"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."';";
	}else{
		$SQL1="SELECT CTCuentas.numero, CTCuentas.detalle, CTCategoria.nombre, CTCuentas.auxiliar, CTCategoria.tipo, CTCuentas.estado";
		$SQL1=$SQL1." FROM CTCuentas LEFT JOIN CTCategoria ON CTCuentas.id_categoria = CTCategoria.id ";
		$SQL1=$SQL1." WHERE CTCuentas.auxiliar='X' AND CTCuentas.estado='A' AND CTCuentas.numero='".$_POST["Codigo"]."';";
	}

	// $Resul = $mysqli->query($SQL1);
	// while ($Reg = $Resul->fetch_assoc()) {
	// 	echo "SI";
	// }

	$resultados = $mysqli->query($SQL1);
	$row_cnt = $resultados->num_rows;
	if ($row_cnt>0) {
		echo "SI";
	}


	$mysqli->close();	
?>
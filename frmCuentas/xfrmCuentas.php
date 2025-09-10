<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if ($_POST['idmod']!="") {
		if ($_SESSION["PLAN"]=="S"){
			echo "bloque if <br>";
			$sql = "UPDATE CTCuentasEmpresa SET detalle='".$_POST['nombre']."',id_categoria='".$_POST['SelCat']."', auxiliar='".$_POST['opt1']."', ingreso='".$_POST['t1']."' WHERE id='".$_POST['idmod']."'";
			echo $sql;
			// exit;
			$mysqli->query($sql);
		}else{
			echo "bloque else <br>";
			$sql = "UPDATE CTCuentas SET detalle='".$_POST['nombre']."',id_categoria='".$_POST['SelCat']."', auxiliar='".$_POST['opt1']."', ingreso='".$_POST['t1']."' WHERE id='".$_POST['idmod']."'";
			echo $sql;
			// exit;
			$mysqli->query("UPDATE CTCuentas SET detalle='".$_POST['nombre']."',id_categoria='".$_POST['SelCat']."', auxiliar='".$_POST['opt1']."', ingreso='".$_POST['t1']."' WHERE id='".$_POST['idmod']."'");
		}
	}else{
		if($_SESSION["PLAN"]=="S"){
			$SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='".$_POST['numero']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		}else{
			$SQL="SELECT * FROM CTCuentas WHERE numero='".$_POST['numero']."'";
		}
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			if ($_SESSION["PLAN"]=="S"){
				$mysqli->query("INSERT INTO CTCuentasEmpresa VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['numero']."','".$_POST['nombre']."','".$_POST['SelCat']."','".$_POST['opt1']."','".$_POST['t1']."','A')");
			}else{
				$mysqli->query("INSERT INTO CTCuentas VALUES('','".$_POST['numero']."','".$_POST['nombre']."','".$_POST['SelCat']."','".$_POST['opt1']."','".$_POST['t1']."','A')");
			}
		}else{
			$mysqli->close();
			header("location:frmCuentas.php?ex=yes");
			exit;
		}
	}

	$mysqli->close();
	header("location:frmCuentas.php");
?>
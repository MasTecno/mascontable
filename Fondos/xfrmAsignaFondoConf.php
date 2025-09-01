<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';
   
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if (isset($_POST['DefeAsie']) && $_POST['DefeAsie']!="") {

		$SQL="SELECT * FROM CTAsientoFondo WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$mysqli->query("UPDATE CTAsientoFondo SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."' WHERE tipo='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("INSERT INTO CTAsientoFondo VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','A');");
		}

		$mysqli->query("UPDATE CTAsientoFondo SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."' WHERE tipo='A' AND rut_empresa=''");

	}else{

		$SQL="SELECT * FROM CTAsientoFondo WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$mysqli->query("UPDATE CTAsientoFondo SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."' WHERE tipo='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("INSERT INTO CTAsientoFondo VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Comp1']."','".$_POST['Comp2']."','A');");
		}
	}

    $mysqli->close();

	header("location:../Fondos/");
 ?>
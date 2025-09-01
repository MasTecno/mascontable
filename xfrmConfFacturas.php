<?php 
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if (isset($_POST['DefeAsie']) && $_POST['DefeAsie']!="") {

		// $SQL="SELECT * FROM CTAsiento WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		// $resultados = $mysqli->query($SQL);
		// $row_cnt = $resultados->num_rows;
		// if ($row_cnt>0) {
		// 	$mysqli->query("UPDATE CTAsiento SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."',L3='".$_POST['Comp3']."',L4='".$_POST['Comp4']."',L5='".$_POST['Comp5']."' WHERE tipo='C' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		// 	$mysqli->query("UPDATE CTAsiento SET L1='".$_POST['Venta1']."',L2='".$_POST['Venta2']."',L3='".$_POST['Venta3']."',L4='".$_POST['Venta4']."',L5='".$_POST['Venta5']."' WHERE tipo='V' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		// }else{

			$mysqli->query("DELETE FROM CTAsiento WHERE TIPO='C' AND rut_empresa='';");
			$mysqli->query("DELETE FROM CTAsiento WHERE TIPO='V' AND rut_empresa='';");

			$mysqli->query("INSERT INTO CTAsiento VALUES('','','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','".$_POST['Comp4']."','".$_POST['Comp5']."','C');");
			$mysqli->query("INSERT INTO CTAsiento VALUES('','','".$_POST['Venta1']."','".$_POST['Venta2']."','".$_POST['Venta3']."','".$_POST['Venta4']."','".$_POST['Venta5']."','V');");
		// }


		// $mysqli->query("UPDATE CTAsiento SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."',L3='".$_POST['Comp3']."',L4='".$_POST['Comp4']."',L5='".$_POST['Comp5']."' WHERE tipo='C' AND rut_empresa=''");
		// $mysqli->query("UPDATE CTAsiento SET L1='".$_POST['Venta1']."',L2='".$_POST['Venta2']."',L3='".$_POST['Venta3']."',L4='".$_POST['Venta4']."',L5='".$_POST['Venta5']."' WHERE tipo='V' AND rut_empresa=''");

	}else{

		$SQL="SELECT * FROM CTAsiento WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$mysqli->query("UPDATE CTAsiento SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."',L3='".$_POST['Comp3']."',L4='".$_POST['Comp4']."',L5='".$_POST['Comp5']."' WHERE tipo='C' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
			$mysqli->query("UPDATE CTAsiento SET L1='".$_POST['Venta1']."',L2='".$_POST['Venta2']."',L3='".$_POST['Venta3']."',L4='".$_POST['Venta4']."',L5='".$_POST['Venta5']."' WHERE tipo='V' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("INSERT INTO CTAsiento VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','".$_POST['Comp4']."','".$_POST['Comp5']."','C');");
			$mysqli->query("INSERT INTO CTAsiento VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Venta1']."','".$_POST['Venta2']."','".$_POST['Venta3']."','".$_POST['Venta4']."','".$_POST['Venta5']."','V');");
		}

	}



	$mysqli->close();

	header("location:frmConfFacturas.php");
?>
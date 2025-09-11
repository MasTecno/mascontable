<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

	if (isset($_POST['DefeAsie']) && $_POST['DefeAsie']!="") {

		$SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt>0) {
			$mysqli->query("UPDATE CTAsientoHono SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."',L3='".$_POST['Comp3']."',L4='".$_POST['Comp4']."' WHERE tipo='R' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
		}else{
			$mysqli->query("INSERT INTO CTAsientoHono VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','".$_POST['Comp4']."','R');");
		}

		$mysqli->query("UPDATE CTAsientoHono SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."',L3='".$_POST['Comp3']."',L4='".$_POST['Comp4']."' WHERE tipo='R' AND rut_empresa=''");

	}else{

        $SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
        $resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt>0) {
            $mysqli->query("UPDATE CTAsientoHono SET L1='".$_POST['Comp1']."',L2='".$_POST['Comp2']."',L3='".$_POST['Comp3']."',L4='".$_POST['Comp4']."' WHERE tipo='R' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");
        }else{
            $mysqli->query("INSERT INTO CTAsientoHono VALUES('','".$_SESSION['RUTEMPRESA']."','".$_POST['Comp1']."','".$_POST['Comp2']."','".$_POST['Comp3']."','".$_POST['Comp4']."','R');");
        }
	}

    $mysqli->close();

	header("location:frmConfHonorario.php?Exito");
 ?>
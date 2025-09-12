<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTParametros WHERE tipo='ANTI_PROV' AND estado='A'";
		$resultados = $mysqli->query($SQL);
		$row_cnt = $resultados->num_rows;
		if ($row_cnt==0) {
			$mysqli->query("INSERT INTO CTParametros VALUES('','ANTI_PROV','','A');");
			$mysqli->query("INSERT INTO CTParametros VALUES('','ANTI_CLIE','','A');");
			$mysqli->query("INSERT INTO CTParametros VALUES('','CUEN_REND','','A');");
		}

		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['DIVA']."' WHERE tipo='IVA'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['DMILE']."' WHERE tipo='SEPA_MILE'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['DDECI']."' WHERE tipo='SEPA_DECI'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['DLIST']."' WHERE tipo='SEPA_LIST'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['DMONE']."' WHERE tipo='TIPO_MONE'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['NDECI']."' WHERE tipo='NUME_DECI'");

		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['DPORC']."' WHERE tipo='RETE_HONO'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['DFACT']."' WHERE tipo='RETE_FACT'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['Comp1']."' WHERE tipo='PPM'");

		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['Comp4']."' WHERE tipo='CUEN_REND'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['Comp2']."' WHERE tipo='ANTI_PROV'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['Comp3']."' WHERE tipo='ANTI_CLIE'");

		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['CFOLIO']."' WHERE tipo='CERO_FOLI'");
		$mysqli->query("UPDATE CTParametros SET valor='".$_POST['TFOLIO']."' WHERE tipo='TEXT_FOLI'");
    $mysqli->close();   	

	header("location:../frmMain.php?msg=ParametrosOK");
<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

    $TXAnticipo=$_POST['TXAnticipo'];
    echo '<option value="0">Seleccione...</option>';

    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTCliPro WHERE estado='A' AND tipo='$TXAnticipo' ORDER BY razonsocial";
    $resultado = $mysqli->query("$SQL");
    while ($registro = $resultado->fetch_assoc()) {
        echo "<option value ='".$registro["id"]."'>".$registro["rut"]." - ".$registro["razonsocial"]."</option>";
    }
    $mysqli->close();


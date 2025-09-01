<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    if ($_POST['tccosto']!="" && $_POST['ttmovimiento']!="") {
        
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

        $mysqli->query("UPDATE CTComprobante SET ccosto='".$_POST['tccosto']."', movimiento='".$_POST['ttmovimiento']."' WHERE keyas='".$_SESSION['KEYCOMPROBANTE']."'");
        
        $_SESSION['KEYCOMPROBANTE']="";

        echo "";
        $mysqli->close();
        exit;

    }

?>
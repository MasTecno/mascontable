<?php 
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    $resultado="";

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    if ($_SESSION["PLAN"]=="S"){
        $SQL="SELECT * FROM CTCuentasEmpresa WHERE numero='".$_POST['dat1']."' AND estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    }else{
        $SQL="SELECT * FROM CTCuentas WHERE numero='".$_POST['dat1']."' AND estado='A'";
    }
    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {
        $resultado=$registro["detalle"];
    }

    $mysqli->close();

    if($resultado==""){
        echo "";
    }else{
        echo utf8_encode($resultado);
    }

?>
<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $IvaCredito=$_POST['SelIvaCredito'];
    $IvaDebito=$_POST['SelIvaDebito'];
    $CtaImpuestoUnico=$_POST['SelCtaImpuestoUnico'];
    $CtaRetencionHonorarios=$_POST['SelCtaRetencionHonorarios'];
    $CtaRetencion3Remuneraciones=$_POST['SelCtaRetencion3Remuneraciones'];
    $CtaRetencion3Honorarios=$_POST['SelCtaRetencion3Honorarios'];
    $CtaPPM=$_POST['SelCtaPPM'];
    $CtaRemanente=$_POST['SelCtaRemanente'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='IVACredito'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$IvaCredito' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='IVACredito'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'IVACredito', '$IvaCredito')";
        $mysqli->query($SQL);
    }

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='IVADebito'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$IvaDebito' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='IVADebito'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'IVADebito', '$IvaDebito')";
        $mysqli->query($SQL);
    }

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='ImpUnico'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$CtaImpuestoUnico' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='ImpUnico'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'ImpUnico', '$CtaImpuestoUnico')";
        $mysqli->query($SQL);
    }

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='RetHonorarios'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$CtaRetencionHonorarios' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='RetHonorarios'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'RetHonorarios', '$CtaRetencionHonorarios')";
        $mysqli->query($SQL);
    }

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='Prestamo3Sueldo'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$CtaRetencion3Remuneraciones' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='Prestamo3Sueldo'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'Prestamo3Sueldo', '$CtaRetencion3Remuneraciones')";
        $mysqli->query($SQL);
    }

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='Prestamo3Honorarios'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$CtaRetencion3Honorarios' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='Prestamo3Honorarios'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'Prestamo3Honorarios', '$CtaRetencion3Honorarios')";
        $mysqli->query($SQL);
    }

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='PPM'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$CtaPPM' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='PPM'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'PPM', '$CtaPPM')";
        $mysqli->query($SQL);
    }

    $SQL=" SELECT * FROM CTParametrosF29 WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='Remanente'";
    $resultados = $mysqli->query($SQL);
    if($resultados->num_rows > 0){
        $SQL="UPDATE CTParametrosF29 SET Valor='$CtaRemanente' WHERE RutEmpresa='$RutEmpresa' AND Periodo='' AND Tipo='Remanente'";
        $mysqli->query($SQL);
    }else{
        $SQL="INSERT INTO CTParametrosF29 (RutEmpresa, Periodo, Tipo, Valor) VALUES ('$RutEmpresa', '', 'Remanente', '$CtaRemanente')";
        $mysqli->query($SQL);
    }

    header("location:configF29.php");
?>
<?php

    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $contador=$NomCont;

    if($Periodo==""){
        header("location:../frmMain.php");
        exit;
    }

    $d1 = $_POST['Mod01'];  ///NOTA DE CREDITO ID
    $d2 = $_POST['Mod02']; ///FACTURA ID

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    ///datos factura
    $SQLx="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND id='$d2'";
    $consultax = $mysqli->query($SQLx);
    while ($registrox = $consultax->fetch_assoc()) {
        $CodSii=$registrox['id_tipodocumento'];
        $RutFac=$registrox['rut'];
        $NumFac=$registrox['numero'];
    }

    $SQLx="SELECT * FROM CTTipoDocumento WHERE id='$CodSii'";
    $consultax = $mysqli->query($SQLx);
    while ($registrox = $consultax->fetch_assoc()) {
        $CodSii=$registrox['tiposii'];
    }

    $SQL="UPDATE CTRegDocumentos SET TipoDocRef='$CodSii', FolioDocRef='$NumFac' WHERE rutempresa='$RutEmpresa' AND rut='$RutFac' AND id='$d1'";
    $mysqli->query($SQL);





    // ///$KeyAs="NC".date("YmdHis");

    // $SQL="UPDATE CTRegDocumentos SET keyas='$KeyAs', lote='$KeyAs' WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id_tipodocumento='5' AND id='$d1' AND FolioDocRef='$d2'";
    // $mysqli->query($SQL);

    // $SQL="UPDATE CTRegDocumentos SET keyas='$KeyAs', lote='$KeyAs' WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$RutNC' AND id_tipodocumento='$CodSii' AND numero='$d2'";
    // $mysqli->query($SQL);
    
    header("location:../NCredito");
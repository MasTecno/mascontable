<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    // $contador=$NomCont;

    if($Periodo==""){
        header("location:../frmMain.php");
        exit;
    }

        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

        $SQL="SELECT * FROM CTParametros WHERE estado='A'";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            if($registro['tipo']=="SEPA_MILE"){
                $DMILE=$registro['valor'];  
            }

            if($registro['tipo']=="SEPA_DECI"){
                $DDECI=$registro['valor'];  
            }

            if($registro['tipo']=="NUME_DECI"){
                $NDECI=$registro['valor'];  
            } 
        }
        // echo $_POST['ListSinDoc'];
        $porciones = explode(",", $_POST['ListSinDoc']);
        $porciones[0]; // porción1
        $porciones[1]; // porción2
        $porciones[2]; // porción2
        $porciones[3];
        $Str='';
        


        $SQLx="SELECT * FROM CTRegDocumentos WHERE rut='".$porciones[1]."' AND fecha<='".$porciones[2]."' AND total>='".$porciones[3]."' AND tipo='".$_POST['frm']."' AND keyas='' AND (id_tipodocumento<>'4' OR id_tipodocumento<>'5' OR id_tipodocumento<>'32' OR id_tipodocumento<>'37') ORDER BY fecha DESC";
        $resultadosx = $mysqli->query($SQLx);
        while ($registrox = $resultadosx->fetch_assoc()) {

            $rrut="";
            $rsoc="";
            $SQL="SELECT * FROM CTCliPro WHERE rut='".$registrox["rut"]."'";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {
                $rrut=$registro["rut"];
                $rsoc=$registro["razonsocial"];
            }

            $Str=$Str.'<option value="'.$registrox["id"].','.$registrox["numero"].','.$registrox["fecha"].'">Factura: '.$registrox["numero"].', Fecha: '.date('d-m-Y',strtotime($registrox["fecha"])).', '.$rrut.' '.$rsoc.', Monto: '.number_format($registrox["total"], $NDECI, $DDECI, $DMILE).'</option>';
        }

        if($Str==''){
            $Str='<option value="">No Existen documentos para Asociar al Rut seleccionado</option>';
        }

		echo $Str;
?>
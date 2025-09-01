<?php 

    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $FECHA=date("Y/m/d");

    $xfecha=$_POST['d1'];
    $xrut=$_POST['d2'];
    $xrs=$_POST['d3'];

    $xiddoc=$_POST['d4'];
    $xnodoc=$_POST['d5'];
    $xndoc=$_POST['d6'];

    $xcuenta=$_POST['d7'];
    $xncuenta=$_POST['d8'];

    $xexento=$_POST['d9'];
    $xneto=$_POST['d10'];
    $xiva=$_POST['d11'];
    $xrete=$_POST['d12'];

    $xfrm=$_POST['frm'];

    if($xexento==""){
        $xexento=0;
    }
    if($xneto==""){
        $xneto=0;
    }
    if($xiva==""){
        $xiva=0;
    }
    if($xrete==""){
        $xrete=0;
    }

    $xtotal=$xexento+$xneto+$xiva+$xrete;

    if ($_POST['ModReg']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

        if ($_POST['swrd7']!="" && $_POST['rd7']!="") {
            if($xfrm=="V"){
                $Mov="C";
            }else{
                $Mov="P";
            }
            $SQL="SELECT * FROM CTCliProCuenta WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'";

            $resultado = $mysqli->query($SQL);

            $row_cnt = $resultado->num_rows;
            if ($row_cnt>0) {
                $mysqli->query("UPDATE CTCliProCuenta SET cuenta='$xcuenta' WHERE estado='A' AND rutempresa='$RutEmpresa' AND rut='$xrut' AND tipo='$Mov'");
            }else{
                $mysqli->query("INSERT INTO CTCliProCuenta VALUE('','$RutEmpresa','$xrut','$xcuenta','','$Mov','A')");
            }

            $mysqli->query("UPDATE CTRegDocumentos SET cuenta='$xcuenta' WHERE rut='$xrut' AND rutempresa='$RutEmpresa' AND lote='' AND tipo='".$xfrm."'");
        }

        $SQL="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND rut='$xrut' AND id_tipodocumento='$xiddoc' AND numero='$xndoc' AND tipo='$xfrm'";
        $resultado = $mysqli->query($SQL);
        $row_cnt = $resultado->num_rows;
        if ($row_cnt>0) {
            echo '<div class="alert alert-danger"><strong>Advertencia!</strong> Ya existe un documento con los datos que esta intentando cambiar.</div>';
            $mysqli->close();
            exit;
        }


        $mysqli->query("UPDATE CTRegDocumentos SET cuenta='$xcuenta', exento='$xexento', neto='$xneto', iva='$xiva', retencion='$xrete', total='$xtotal', numero='$xndoc', rut='$xrut' WHERE id='". $_POST['ModReg']."' AND tipo='".$xfrm."'");

        echo "";
        $mysqli->close();
        exit;
    }

    if($xfecha=="" || $xrut=="" || $xcuenta=="" || $xiddoc=="" || $xndoc=="" ||  $xrs=="" || $xncuenta=="" || $xnodoc==""){
        echo '<div class="alert alert-danger"><strong>Advertencia!</strong> El Formulario debe contener todos los datos.</div>';
        exit;
    }

	$dia = substr($xfecha,0,2);
    $mes = substr($xfecha,3,2);
    $ano = substr($xfecha,6,4);

    $xfecha=$ano."/".$mes."/".$dia;

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTTipoDocumento WHERE tiposii='$xiddoc' AND estado='A'";
    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {
       $xiddoc=$registro["id"];           
    }

    $SQL="SELECT * FROM CTRegDocumentos WHERE rutempresa='$RutEmpresa' AND rut='$xrut' AND id_tipodocumento='$xiddoc' AND numero='$xndoc' AND tipo='$xfrm'";
    $resultado = $mysqli->query($SQL);

    $row_cnt = $resultado->num_rows;
    if ($row_cnt>0) {
        echo '<div class="alert alert-danger"><strong>Advertencia!</strong> Documento ya ingresado.</div>';
        $mysqli->close();
        exit;
    }
// echo "INSERT INTO CTRegDocumentos VALUE('','$Periodo','$RutEmpresa','$xrut','$xcuenta','$xiddoc','$xndoc','$xfecha','$xexento','$xneto','$xiva','$xrete','$xtotal','','','$xfrm','$FECHA','A','','','')";
    $mysqli->query("INSERT INTO CTRegDocumentos VALUE('','$Periodo','$RutEmpresa','$xrut','$xcuenta',0,'$xiddoc','$xndoc','$xfecha','$xexento','$xneto','$xiva','$xrete','$xtotal','','','$xfrm','$FECHA','A','','','')");

    $mysqli->close();

    echo "";
    
?>
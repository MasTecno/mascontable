<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $FECHA=date("Y/m/d");

    $xcue1=$_POST['mcuenta1'];
    $xcue2=$_POST['mcuenta2'];
    $xcue3=$_POST['mcuenta3'];
    $xcue4=$_POST['mcuenta4'];

    $xdeb1=$_POST['mdebe1'];
    $xdeb2=$_POST['mdebe2'];
    $xdeb3=$_POST['mdebe3'];
    $xdeb4=$_POST['mdebe4'];

    $xhab1=$_POST['mhaber1'];
    $xhab2=$_POST['mhaber2'];
    $xhab3=$_POST['mhaber3'];
    $xhab4=$_POST['mhaber4'];

    $xcco1=$_POST['tccosto1'];
    $xcco2=$_POST['tccosto2'];
    $xcco3=$_POST['tccosto3'];
    $xcco4=$_POST['tccosto4'];

    $NIdHono=$_POST['iddoc'];
    $TotaAsic=$_POST['canBruto'];
    $xfecha=$_POST['mfecha'];
    $RutHono=$_POST['RutHono'];
    $NHono="Hono".$_POST['NDoc'];

    $dia = substr($xfecha,0,2);
    $mes = substr($xfecha,3,2);
    $ano = substr($xfecha,6,4);

    $xfecha=$ano."/".$mes."/".$dia;

    $KeyAs=date("YmdHis");

    $SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";

    if ($xcue1!="" && ($xdeb1>0 || $xhab1>0)) {
        $sdeb=$sdeb+$xdeb1;
        $shab=$shab+$xhab1;
        $SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$xcue1','$xdeb1','$xhab1','$FECHA','A','$KeyAs','$NHono','$RutHono','0','',$xcco1)";
    }
       
    if ($xcue2!="" && ($xdeb2>0 || $xhab2>0)) {
        $sdeb=$sdeb+$xdeb2;
        $shab=$shab+$xhab2;
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','','$xcue2','$xdeb2','$xhab2','$FECHA','A','$KeyAs','$NHono','$RutHono','0','',$xcco2)";
    }

    if ($xcue3!="" && ($xdeb3>0 || $xhab3>0)) {
        $sdeb=$sdeb+$xdeb3;
        $shab=$shab+$xhab3;
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','','$xcue3','$xdeb3','$xhab3','$FECHA','A','$KeyAs','$NHono','$RutHono','0','',$xcco3)";
    }
    
    if ($xcue4!="" && ($xdeb4>0 || $xhab4>0)) {
        $sdeb=$sdeb+$xdeb4;
        $shab=$shab+$xhab4;
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','','$xcue4','$xdeb4','$xhab4','$FECHA','A','$KeyAs','$NHono','$RutHono','0','',$xcco4)";
    }


    if (($sdeb!=$TotaAsic ) || ($shab!=$TotaAsic)) {
        echo 'Las sumatorias no corresponde'.$sdeb."-".$shab."-".$TotaAsic;
        exit;
    }

    $xglosa=utf8_decode($_POST['Glosa']);
 
    if ($xglosa==""){
        echo 'Faltan Datos';
        exit;
    }else{

        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

        $TanoD = substr($Periodo,3,4);
        $FolioComp=0;
        $SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
        $resultados = $mysqli->query($SQL1);
        while ($registro = $resultados->fetch_assoc()) {
            $FolioComp=$registro['valor'];
        }

        if ($FolioComp==0) {
            $mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','T','2','A');");
            $FolioComp=1;
        }else{
            $mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
        }

        $mysqli->close();
        

        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','$xglosa','','','','$FECHA','A','$KeyAs','$NHono','$RutHono','$FolioComp','T','0')";
    
    }

    $SQL = $SQL.";";

    // exit;

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $mysqli->query($SQL);
    $mysqli->query("UPDATE CTHonorarios SET movimiento='$KeyAs' WHERE id='$NIdHono'");

    $mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");

    $mysqli->close();

    echo "";

 ?>
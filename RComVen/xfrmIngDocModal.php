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
    $xcue5=$_POST['mcuenta5'];

    $xdeb1=$_POST['mdebe1'];
    $xdeb2=$_POST['mdebe2'];
    $xdeb3=$_POST['mdebe3'];
    $xdeb4=$_POST['mdebe4'];
    $xdeb5=$_POST['mdebe5'];

    $xhab1=$_POST['mhaber1'];
    $xhab2=$_POST['mhaber2'];
    $xhab3=$_POST['mhaber3'];
    $xhab4=$_POST['mhaber4'];
    $xhab5=$_POST['mhaber5'];

    $xtcc1=$_POST['tccosto1'];
    $xtcc2=$_POST['tccosto2'];
    $xtcc3=$_POST['tccosto3'];
    $xtcc4=$_POST['tccosto4'];
    $xtcc5=$_POST['tccosto5'];


    $NFactura=$_POST['NFactura'];
    $TotalFac=$_POST['TotalAsi'];
    $xfecha=$_POST['mfecha'];
    
    $dia = substr($xfecha,0,2);
    $mes = substr($xfecha,3,2);
    $ano = substr($xfecha,6,4);

    $xfecha=$ano."/".$mes."/".$dia;

    // if(isset($_POST['tccosto']) && $_POST['tccosto']!=""){
    //     $xccosto=$_POST['tccosto'];
    // }else{
    //     $xccosto=0;
    // }

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTRegDocumentos WHERE id='$NFactura' AND rutempresa='$RutEmpresa'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $idupdate=$registro['id'];
        $NFactura=$registro['numero'];
        $RutFat=$registro['rut'];
        $TDoc=$registro['tipo'];
    }

    // $SwComp="";
    // $SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEmpresa'";
    // $resultados = $mysqli->query($SQL);
    // while ($registro = $resultados->fetch_assoc()) {
    //     $SwComp=$registro['comprobante'];
    // }


    // $mysqli->close();

    $KeyAs=$_SESSION['KEYASIENTOFAC'];


    // $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
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

    $SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";

    if($xdeb1==""){
        $xdeb1=0;
    }
    if($xhab1==""){
        $xhab1=0;
    }
    if($xdeb2==""){
        $xdeb2=0;
    }
    if($xhab2==""){
        $xhab2=0;
    }
    if($xdeb3==""){
        $xdeb3=0;
    }
    if($xhab3==""){
        $xhab3=0;
    }
    if($xdeb4==""){
        $xdeb4=0;
    }
    if($xhab4==""){
        $xhab4=0;
    }
    if($xdeb5==""){
        $xdeb5=0;
    }
    if($xhab5==""){
        $xhab5=0;
    }

    if ($xcue1!="" && ($xdeb1>0 || $xhab1>0)) {
        if($xdeb1==""){
            $xdeb1=0;
        }
        if($xhab1==""){
            $xhab1=0;
        }
        $sdeb=$sdeb+$xdeb1;
        $shab=$shab+$xhab1;
        $SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$xcue1','$xdeb1','$xhab1','$FECHA','A','$KeyAs','$NFactura','$RutFat',$FolioComp,'T',$xtcc1)";
    }
       
    if ($xcue2!="" && ($xdeb2>0 || $xhab2>0)) {
        $sdeb=$sdeb+$xdeb2;
        $shab=$shab+$xhab2;
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','','$xcue2','$xdeb2','$xhab2','$FECHA','A','$KeyAs','$NFactura','$RutFat',$FolioComp,'T',$xtcc2)";
    }

    if ($xcue3!="" && ($xdeb3>0 || $xhab3>0)) {
        $sdeb=$sdeb+$xdeb3;
        $shab=$shab+$xhab3;
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','','$xcue3','$xdeb3','$xhab3','$FECHA','A','$KeyAs','$NFactura','$RutFat',$FolioComp,'T',$xtcc3)";
    }
    
    if ($xcue4!="" && ($xdeb4>0 || $xhab4>0)) {
        $sdeb=$sdeb+$xdeb4;
        $shab=$shab+$xhab4;
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','','$xcue4','$xdeb4','$xhab4','$FECHA','A','$KeyAs','$NFactura','$RutFat',$FolioComp,'T',$xtcc4)";
    }

    if ($xcue5!="" && ($xdeb5>0 || $xhab5>0)) {
        $sdeb=$sdeb+$xdeb5;
        $shab=$shab+$xhab5;
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','','$xcue5','$xdeb5','$xhab5','$FECHA','A','$KeyAs','$NFactura','$RutFat',$FolioComp,'T',$xtcc5)";
    }
    

    if (($sdeb!=$sdeb)) {
        echo 'Las sumatorias no corresponde';
        exit;
    }

    $xglosa=$_POST['Glosa'];
 
    if ($xglosa==""){
        echo 'Faltan Datos';
        exit;
    }else{
        if ($TDoc=="C") {
            $TDoc="E";
        }else{
            if ($TDoc=="V") {
                $TDoc="I";
            }else{
                $TDoc="E";
            }
        }
        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','$xglosa','','','','$FECHA','A','$KeyAs','$NFactura','$RutFat',$FolioComp,'T','0')";
    }

    $SQL = $SQL.";";

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $mysqli->query($SQL);

    $mysqli->query("UPDATE CTRegDocumentos SET lote='Directo', keyas='$KeyAs' WHERE id='$idupdate'");

    $mysqli->close();
    $_SESSION['KEYASIENTOFAC']=date("YmdHis");
    echo "";

?>
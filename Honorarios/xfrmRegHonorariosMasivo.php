<?php 
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $Periodo=$_SESSION['PERIODO'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $FECHA=date("Y/m/d");

    // echo $xcue1=$_POST['mcuenta1'];
    // $xcue2=$_POST['mcuenta2'];
    // $xcue3=$_POST['mcuenta3'];

    // $xdeb1=$_POST['mdebe1'];
    // $xdeb2=$_POST['mdebe2'];
    // $xdeb3=$_POST['mdebe3'];

    // $xhab1=$_POST['mhaber1'];
    // $xhab2=$_POST['mhaber2'];
    // $xhab3=$_POST['mhaber3'];

    // $NIdHono=$_POST['iddoc'];
    // $TotaAsic=$_POST['canBruto'];
    $xfecha=$_POST['d1'];
    $dia = substr($xfecha,0,2);
    $mes = substr($xfecha,3,2);
    $ano = substr($xfecha,6,4);

    $xfecha=$ano."/".$mes."/".$dia;


    $RutHono=$_POST['RutHono'];

    $KeyAs=date("YmdHis");
    $NHono="Hono".$KeyAs;

    $nlineas=$_POST['nlineas'];

    if ($_POST['nlineas']==0 || $_POST['nlineas']=="") {
        header("location:../Honorarios/");
        exit;
    }

    if(isset($_POST['tccosto']) && $_POST['tccosto']!=""){
        $xccosto=$_POST['tccosto'];
    }else{
        $xccosto=0;
    }

    $SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,ccosto) VALUES ";
    $i=1;
    $TPago=0;

    while ( $i <= $nlineas) {
        $Cue=$_POST['mcuenta'.$i];
        $Deb=$_POST['mdebe'.$i];
        $Hab=$_POST['mhaber'.$i];
        
        if($Deb==""){
            $Deb=0;
        }
        if($Hab==""){
            $Hab=0;
        }

        $Su=$Deb+$Hab;

        if ($Su>0) {

            if ($i>1) {
               $SQL =$SQL.",";
            }

            if ($Deb>0) {
                 $SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$Cue','$Deb','$Hab','$FECHA','A','$KeyAs','$NHono','$RutHono','0','',$xccosto)";
            }else{
                 $SQL =$SQL."('$Periodo','$RutEmpresa','$xfecha','','$Cue','$Deb','$Hab','$FECHA','A','$KeyAs','$NHono','$RutHono','0','',$xccosto)";
            }
        }
        if($i==3){
            $TPago=$Hab;
        }
        $i++;
    }

    $xglosa=$_POST['tglosa'];
 
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

        $SQL = $SQL.",('$Periodo','$RutEmpresa','$xfecha','$xglosa','','','','$FECHA','A','$KeyAs','$NHono','$RutHono','$FolioComp','T',$xccosto)";
    }

    $SQL = $SQL.";";

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $mysqli->query($SQL);
    $mysqli->query("UPDATE CTHonorarios SET movimiento='$KeyAs' WHERE estado='A' and origen<>'Z' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND movimiento=''");

    $mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='T' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");


    if (isset($_POST['PAuto'])) {

        $SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='$RutEmpresa' AND tipo='R'";
        $resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt>0) {
            $resultados1 = $mysqli->query($SQL);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $xL2=$registro1["L2"];
                $xL3=$registro1["L3"]; ///por pagar
            }
        }else{
            $SQL="SELECT * FROM CTAsientoHono WHERE rut_empresa='' AND tipo='R'";
            $resultados1 = $mysqli->query($SQL);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $xL2=$registro1["L2"];
                $xL3=$registro1["L3"]; ///por pagar
            }
        }


        // $TanoD = substr($Periodo,3,4);
        // $CtaHonorario="";

        $SQL="SELECT * FROM CTComprobanteFolio WHERE tipo='E' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            $FolioComp=$registro['valor'];
        }

        if ($FolioComp==0) {
            $mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','E','1','A');");
            $FolioComp=1;
        }else{
            $mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='E' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
        }

        $KeyPag=$KeyAs+1;
        $mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','$xL3','$TPago','0','$FECHA','A','$KeyPag','','','0','')");
        $mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','','".$_POST['Comp4']."','0','$TPago','$FECHA','A','$KeyPag','','','0','')");
        $mysqli->query("INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo) VALUES('$Periodo','$RutEmpresa','$xfecha','".$_POST['tglosap']."','','0','0','$FECHA','A','$KeyPag','','','$FolioComp','E')");

        $SQL="SELECT * FROM CTHonorarios WHERE estado='A' and origen<>'Z' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND movimiento='$KeyAs'";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            $mysqli->query("INSERT INTO CTControRegDocPago (rutempresa,rut,periodo,id_tipodocumento,ndoc,keyas,monto,fecha,fregistro,tipo,origen,estado) VALUES ('$RutEmpresa','".$registro['rut']."','$Periodo','0','".$registro['numero']."','$KeyPag','".$registro['liquido']."','".$registro['fecha']."','$FECHA','H','M','A')");
        }
    }
 
    $mysqli->close();
    header("location:../Honorarios/");
 ?>
<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $Anticipos_Clientes=$_POST['SelAnticipoCli'];
    $Anticipos_Proveedores=$_POST['SelAnticipoProv'];
    $Anticipos_Contra=$_POST['SelAnticipoContra'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    if($_POST['TXAction']=="Modal"){
        $SQL="SELECT * FROM CTAnticiposConf WHERE RutEmpresa='$RutEmpresa'";   
        $resultado = $mysqli->query($SQL);
        if($resultado->num_rows == 0) {
            $SQL="INSERT INTO CTAnticiposConf VALUES('','$RutEmpresa','$Anticipos_Clientes','$Anticipos_Proveedores','$Anticipos_Contra','A')";
        }else{
            $SQL="UPDATE CTAnticiposConf SET Anticipos_Clientes='$Anticipos_Clientes', Anticipos_Proveedores='$Anticipos_Proveedores', Anticipos_Contra='$Anticipos_Contra', Estado='A' WHERE RutEmpresa='$RutEmpresa'";
        }
        $Action="ConfigOK";
        $mysqli->query($SQL);
    }

    if($_POST['TXAction']=="Anticipo"){
        $FechaAnticipo = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['FechaAnticipo'])));
        $LPeriodo=date('m-Y', strtotime(str_replace('/', '-', $_POST['FechaAnticipo'])));
        $SelRutAnticipo = $_POST['SelRutAnticipo'];
        $GlosaAnticipo = $_POST['GlosaAnticipo'];
        $MontoAnticipo = str_replace('.', '', $_POST['MontoAnticipo']);
        $TipoAnticipo = $_POST['TXAnticipo'];

        if($TipoAnticipo=="C"){
            $tCliPro="I";
        }else{
            $tCliPro="E";
        }

        $Sql="SELECT * FROM CTCliPro WHERE id='$SelRutAnticipo' LIMIT 1";
        $resultado = $mysqli->query($Sql);
        while ($registro = $resultado->fetch_assoc()) {
            $SelRutAnticipo=$registro['rut'];
        }

        $SQL="SELECT * FROM CTAnticiposConf WHERE RutEmpresa='$RutEmpresa'";
        $resultado = $mysqli->query($SQL);
        while ($registro = $resultado->fetch_assoc()) {
            $Anticipos_Clientes=$registro['Anticipos_Clientes'];
            $Anticipos_Proveedores=$registro['Anticipos_Proveedores'];
            $Anticipos_Contra=$registro['Anticipos_Contra'];
        }

        $KeyAs=date("YmdHis");

        $SQL="INSERT INTO CTAnticipos (id,Fecha,RutEmpresa,Rut,Glosa,Monto,Keyas,Estado,FechaReg,Tipo) 
        VALUES('','$FechaAnticipo','$RutEmpresa','$SelRutAnticipo','$GlosaAnticipo','$MontoAnticipo','$KeyAs','A','".date("Y-m-d")."','$TipoAnticipo')";

        $mysqli->query($SQL);

        $TanoD = substr($LPeriodo,3,4);
        $FolioComp=0;
        $SQL1="SELECT * FROM CTComprobanteFolio WHERE tipo='$tCliPro' AND rutempresa='$RutEmpresa' AND ano='$TanoD'";
        $resultados = $mysqli->query($SQL1);
        while ($registro = $resultados->fetch_assoc()) {
            $FolioComp=$registro['valor'];
        }

        if ($FolioComp==0) {
            $mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$TanoD','$tCliPro','2','A');");
            $FolioComp=1;
        }else{
            $mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$tCliPro' AND rutempresa='$RutEmpresa' AND ano='$TanoD'");
        }

        $SQL ="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,rut_auxiliar) VALUES ";
        if($TipoAnticipo=="C"){
            $SQL =$SQL."('$LPeriodo','$RutEmpresa','$FechaAnticipo','','$Anticipos_Contra','$MontoAnticipo','0','".date("Y-m-d")."','A','$KeyAs','$FolioComp','$tCliPro','$SelRutAnticipo'),";
            $SQL =$SQL."('$LPeriodo','$RutEmpresa','$FechaAnticipo','','$Anticipos_Clientes','0','$MontoAnticipo','".date("Y-m-d")."','A','$KeyAs','$FolioComp','$tCliPro','$SelRutAnticipo'),";
        }else{
            $SQL =$SQL."('$LPeriodo','$RutEmpresa','$FechaAnticipo','','$Anticipos_Proveedores','0','$MontoAnticipo','".date("Y-m-d")."','A','$KeyAs','$FolioComp','$tCliPro','$SelRutAnticipo'),";
            $SQL =$SQL."('$LPeriodo','$RutEmpresa','$FechaAnticipo','','$Anticipos_Contra','$MontoAnticipo','0','".date("Y-m-d")."','A','$KeyAs','$FolioComp','$tCliPro','$SelRutAnticipo'),";
        }

        $SQL =$SQL."('$LPeriodo','$RutEmpresa','$FechaAnticipo','$GlosaAnticipo','0','0','0','".date("Y-m-d")."','A','$KeyAs','$FolioComp','$tCliPro','$SelRutAnticipo');";

        $mysqli->query($SQL);
        $Action="AnticipoOK";
    }

    if($_POST['TXAction']=="Eliminar"){
        $Id=$_POST['TXId'];


        $SQL="SELECT * FROM CTAnticipos WHERE id='$Id'";
        $resultado = $mysqli->query($SQL);
        while ($registro = $resultado->fetch_assoc()) {
            $KeyAs=$registro['Keyas'];
        }

        $SQL="DELETE FROM CTAnticipos WHERE id='$Id'";
        $mysqli->query($SQL);


        $SQL="DELETE FROM CTRegLibroDiario WHERE keyas='$KeyAs'";
        $mysqli->query($SQL);

        $Action="EliminarOK";
    }

    $mysqli->close();

    header("location:index.php?msj=$Action");
    exit;
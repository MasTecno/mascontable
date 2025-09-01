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

    $FECHA=date('Y-m-d');

	function LFolio($x1,$x2){
		$mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$RutEmpresa=$_SESSION['RUTEMPRESA'];

		$FolioComp=0;
		$SrtSql="SELECT * FROM CTComprobanteFolio WHERE tipo='$x1' AND rutempresa='$RutEmpresa' AND ano='$x2'";
		$Resul = $mysqli->query($SrtSql);
		while ($Regi = $Resul->fetch_assoc()) {
			$FolioComp=$Regi['valor'];
		}

		if ($FolioComp==0) {
			$mysqli->query("INSERT INTO CTComprobanteFolio VALUES('','$RutEmpresa','$x2','$x1','2','A');");
			$FolioComp=1;
		}else{
			$mysqli->query("UPDATE CTComprobanteFolio SET valor='".($FolioComp+1)."' WHERE tipo='$x1' AND rutempresa='$RutEmpresa' AND ano='$x2'");
		}
		return $FolioComp;
	}

    $d1 = $_POST['Mod01'];
    $d2 = $_POST['Mod02'];
    $d3 = $_POST['Mod03'];
    $GlosaAsiPago = $_POST['GlosaNC'];
    $SelCta=$_POST['SelCta'];

	$dia = substr($_POST['ModFCen'],0,2);
	$mes = substr($_POST['ModFCen'],3,2);
	$ano = substr($_POST['ModFCen'],6,4);

	$FecCen=$ano."-".$mes."-".$dia;

    $PerCen=date('m-Y',strtotime($FecCen));


    // $FecCen=$_POST['ModFCen'];
    
        $d1=explode("-", $d1);
        $KeyAs="NC".date("YmdHis");
        // $KeyAs=date("YmdHis");
        // print_r($d1);

        // echo $KeyAs;
        // exit;

        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

        $SqlAux="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa='$RutEmpresa'";
        $resultados = $mysqli->query($SqlAux);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt==0) {
            $SqlAux="SELECT * FROM CTAsiento WHERE tipo='".$_POST['frm']."' AND rut_empresa=''";
        }

        if ($_POST['frm']=="C") {
            $res1 = $mysqli->query($SqlAux);
            while ($reg1 = $res1->fetch_assoc()) {
                $AUX=$reg1["L4"];// AUXILIAR PROVEEDORES
                $OIM=$reg1["L3"];// Otro impuesto
                $IVA=$reg1["L2"];// IVA
            }

            $TipFol="I";
        }

        if ($_POST['frm']=="V") {
            $res1 = $mysqli->query($SqlAux);
            while ($reg1 = $res1->fetch_assoc()) {
                $AUX=$reg1["L1"];// AUXILIAR CLIENTES
                $IVA=$reg1["L3"];// IVA
                $OIM=$reg1["L2"];// Otro impuesto
            }
            $TipFol="E";
        }

        $NetNC=0;
        $IvaNC=0;
        $RetNC=0;
        $TotNC=0;

        foreach($d1 as $indice=>$IdCN){
            $SQLx="SELECT * FROM CTRegDocumentos WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='$IdCN' AND FolioDocRef='$d2' AND (id_tipodocumento='4' OR id_tipodocumento='5' OR id_tipodocumento='32' OR id_tipodocumento='37')";
            $consultax = $mysqli->query($SQLx);
            while ($registrox = $consultax->fetch_assoc()) {
                $CodSii=$registrox['TipoDocRef'];
                $NumNC=$registrox['numero'];
                $FecNC=$registrox['fecha'];
                $PerNC=date('m-Y',strtotime($registrox['fecha']));
                $RutNC=$registrox['rut'];
                $NetNC=$NetNC+$registrox['exento']+$registrox['neto'];
                $IvaNC=$IvaNC+$registrox['iva'];
                $RetNC=$RetNC+$registrox['retencion'];
                $TotNC=$TotNC+$registrox['total'];
                $CodSii1=$registrox['id_tipodocumento'];
            }

            $SQL="UPDATE CTRegDocumentos SET keyas='$KeyAs', lote='$KeyAs' WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND id='$IdCN' AND FolioDocRef='$d2' AND (id_tipodocumento='4' OR id_tipodocumento='5' OR id_tipodocumento='32' OR id_tipodocumento='37')";
            $mysqli->query($SQL);
        }
        
        $AnoNC = substr($PerNC,3,4);

        $SQLx="SELECT * FROM CTTipoDocumento WHERE tiposii='$CodSii'";
        $consultax = $mysqli->query($SQLx);
        while ($registrox = $consultax->fetch_assoc()) {
            $CodSii=$registrox['id'];
        }

        $SQLx="SELECT * FROM CTTipoDocumento WHERE id='$CodSii1'";
        $consultax = $mysqli->query($SQLx);
        while ($registrox = $consultax->fetch_assoc()) {
            $NomDocNC=strtoupper($registrox["nombre"]);
        }

        $SQLx="SELECT * FROM CTRegDocumentos WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$RutNC' AND id_tipodocumento='$CodSii' AND numero='$d2'";
    
        $consultax = $mysqli->query($SQLx);
        while ($registrox = $consultax->fetch_assoc()) {
            $IdFC=$registrox['id'];
            $FecFC=$registrox['fecha'];
            $NumFC=$registrox['numero'];
            $CodSii=$registrox['id_tipodocumento'];
            $RutFC=$registrox['rut'];
            $PerFC=date('m-Y',strtotime($registrox['fecha']));
            $NetFC=$registrox['exento']+$registrox['neto'];
            $IvaFC=$registrox['iva'];
            $RetFC=$registrox['retencion'];
            $TotFC=$registrox['total'];
            $CtaFC=$registrox['cuenta'];
            $PerFC=$registrox['periodo'];
        }
        
        $SQLx="SELECT * FROM CTTipoDocumento WHERE id='$CodSii'";
        $consultax = $mysqli->query($SQLx);
        while ($registrox = $consultax->fetch_assoc()) {
            $NomDocFC=strtoupper($registrox["nombre"]);
        }

        $SQL="UPDATE CTRegDocumentos SET keyas='$KeyAs', lote='$KeyAs' WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND rut='$RutNC' AND id_tipodocumento='$CodSii' AND numero='$d2'";
        $mysqli->query($SQL);

        // $FecFC = $FecCen;
        // $PerFC=date('m-Y',strtotime($FecCen));
        $AnoFC = substr($PerFC,3,4);

        if ($_POST['frm']=="C") {
            /////FACTURA
            $GlosFac="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDocFC." N:".$NumFC;
            $TipFol="T";
            $Folio=LFolio($TipFol,$AnoFC);

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','$CtaFC','".$NetFC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
            $mysqli->query($SrtSql);
            if($IvaFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','".$IVA."','".$IvaFC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
                $mysqli->query($SrtSql);
            }
            
            if($RetFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','".$OIM."','".$RetFC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
                $mysqli->query($SrtSql);
            }

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','$AUX','0','".$TotFC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
            $mysqli->query($SrtSql);
            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','$GlosFac','0','0','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
            $mysqli->query($SrtSql);

            //////Nota de Credito
            $GlosCre="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDocNC." N:".$NumNC;
            $TipFol="T";
            $Folio=LFolio($TipFol,$AnoNC);

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','$AUX','".$TotNC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
            $mysqli->query($SrtSql);

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','$CtaFC','0','".$NetNC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
            $mysqli->query($SrtSql);
            if($IvaFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','".$IVA."','0','".$IvaNC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
                $mysqli->query($SrtSql);
            }

            if($RetFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','".$OIM."','0','".$RetNC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
                $mysqli->query($SrtSql);
            }

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','$GlosCre','0','0','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
            $mysqli->query($SrtSql);


            if(($TotFC-$TotNC)>0){
                ///EGRESO DEL DIFERCIAL
                $TipFol="E";
                $Folio=LFolio($TipFol,$AnoFC);
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerCen','$RutEmpresa','$FecCen','','$AUX','".($TotFC-$TotNC)."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','','')";
                $mysqli->query($SrtSql);
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerCen','$RutEmpresa','$FecCen','','$SelCta','0','".($TotFC-$TotNC)."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','','')";
                $mysqli->query($SrtSql);
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerCen','$RutEmpresa','$FecCen','$GlosaAsiPago','0','0','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','','')";
                $mysqli->query($SrtSql);

                $SrtSql="INSERT INTO CTControRegDocPago VALUES ('','$RutEmpresa','$RutNC','$PerCen','$d2','$CodSii','$KeyAs','".($TotFC-$TotNC)."','$FecCen','$FECHA','".$_POST['frm']."','C','A')";
                $mysqli->query($SrtSql);
            }
        }


        if ($_POST['frm']=="V") {
            $GlosFac="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDocFC." N:".$NumFC;
            $TipFol="T";
            $Folio=LFolio($TipFol,$AnoFC);

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','$AUX','".$TotFC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
            $mysqli->query($SrtSql);

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','$CtaFC','0','".$NetFC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
            $mysqli->query($SrtSql);
            if($IvaFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','".$IVA."','0','".$IvaFC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
                $mysqli->query($SrtSql);
            }

            if($RetFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','','".$OIM."','0','".$RetFC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
                $mysqli->query($SrtSql);
            }

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerFC','$RutEmpresa','$FecFC','$GlosFac','0','0','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumFC','$RutFC')";
            $mysqli->query($SrtSql);


            //////Nota de Credito
            $GlosCre="CENTRALIZACIÓN DE DOCUMENTO, ".$NomDocNC." N:".$NumNC;
            $TipFol="T";
            $Folio=LFolio($TipFol,$AnoNC);

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','$AUX','0','".$TotNC."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
            $mysqli->query($SrtSql);

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','$CtaFC','".$NetNC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
            $mysqli->query($SrtSql);
            if($IvaFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','".$IVA."','".$IvaNC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
                $mysqli->query($SrtSql);
            }

            if($RetFC>0){
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','','".$OIM."','".$RetNC."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
                $mysqli->query($SrtSql);
            }

            $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerNC','$RutEmpresa','$FecNC','$GlosCre','0','0','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','$NumNC','$RutNC')";
            $mysqli->query($SrtSql);

            if(($TotFC-$TotNC)>0){
                $TipFol="I";
                $Folio=LFolio($TipFol,$AnoFC);
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerCen','$RutEmpresa','$FecCen','','$SelCta','".($TotFC-$TotNC)."','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','','')";
                $mysqli->query($SrtSql);
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerCen','$RutEmpresa','$FecCen','','$AUX','0','".($TotFC-$TotNC)."','$FECHA','A','$KeyAs','$Folio','$TipFol','0','','')";
                $mysqli->query($SrtSql);
                $SrtSql="INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,ncomprobante,tipo,ccosto,nfactura,rut) VALUES ('$PerCen','$RutEmpresa','$FecCen','$GlosaAsiPago','0','0','0','$FECHA','A','$KeyAs','$Folio','$TipFol','0','','')";
                $mysqli->query($SrtSql);

                $SrtSql="INSERT INTO CTControRegDocPago VALUES ('','$RutEmpresa','$RutNC','$PerCen','$d2','$CodSii','$KeyAs','".($TotFC-$TotNC)."','$FecCen','$FECHA','".$_POST['frm']."','C','A')";
                $mysqli->query($SrtSql);
            }
        }

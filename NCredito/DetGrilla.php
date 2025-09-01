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
    $Str='';
    
    $Str=$Str.'
    <br>
<table class="table table-condensed table-hover table-bordered" width="100%" border="0" align="center">
    <thead>
        <tr style="background-color: #d9d9d9;">
            <th style="text-align: right;"  width="5%">N&deg; NC</th>
            <th style="text-align: center;" width="5%">Fecha NC</th>
            <th style="text-align: center;" width="5%">Rut</th>
            <th width="">Raz&oacute;n Social</th>
            <th style="text-align: right;"  width="5%">Monto Total</th>
            <th style="text-align: right;" width="5%">N&deg; Factura</th>
            <th style="text-align: center;" width="5%">Fecha Factura</th>
            <th style="text-align: center;">Cuenta</th>
            <th style="text-align: right;" width="10%">Monto Total</th>
            <th width="10%" style="text-align: center;" width="5%">Procesar</th>
        </tr>
    
    </thead>
';

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

        if ($_SESSION["PLAN"]=="S"){
            $SQLCta="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' ";
        }else{
            $SQLCta="SELECT * FROM CTCuentas WHERE 1=1 ";
        }

        $SQLx="SELECT COUNT(numero)as x,rutempresa, SUM(total) as q, FolioDocRef, TipoDocRef, rut FROM CTRegDocumentos WHERE rutempresa='".$_SESSION['RUTEMPRESA']."' AND keyas='' AND tipo='".$_POST['frm']."' AND (id_tipodocumento='4' OR id_tipodocumento='5' OR id_tipodocumento='32' OR id_tipodocumento='37') GROUP BY FolioDocRef";
        $SQLx=$SQLx." ORDER BY numero";

        $consultax = $mysqli->query($SQLx);
        while ($registrox = $consultax->fetch_assoc()) {
            $Cta="";
            $nNC="";
            $mNC=$registrox["q"];
            $fNC="";
            $nId="";
            $SQL="SELECT * FROM CTRegDocumentos WHERE FolioDocRef='".$registrox["FolioDocRef"]."' AND TipoDocRef='".$registrox["TipoDocRef"]."' AND rut='".$registrox["rut"]."' AND keyas='' AND tipo='".$_POST['frm']."' AND (id_tipodocumento='4' OR id_tipodocumento='5' OR id_tipodocumento='32' OR id_tipodocumento='37')";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {
                if($registrox['x']>1){
                    if($nNC!=""){
                        $nNC=$nNC."-".$registro["numero"];
                        $nId=$nId."-".$registro["id"];
                    }else{
                        $nNC=$registro["numero"];    
                        $nId=$registro["id"];    
                    }
                }else{
                    $nNC=$registro["numero"];
                    $nId=$registro["id"];
                }
                $fNC=date('d-m-Y',strtotime($registro["fecha"]));
            }

            if($registrox["FolioDocRef"]==0){
                $SinDoc="SII no informa Documento";
                $nfac="No hay documento asociado (".$SinDoc.")";
            }else{
                $SinDoc="Nr: ".$registrox["FolioDocRef"];
                $nfac="Documento de Refencia sin Sincronizar (Factura ".$SinDoc.", Tipo Documento ".$registrox["TipoDocRef"].")";
            }

            // $Linea='';
            $Linea='SW';
            // $nfac="No hay documento asociado (".$SinDoc.")";
            $mfac="";
            $ffac="";
            $fkey="";

            $SQL="SELECT * FROM CTTipoDocumento WHERE tiposii='".$registrox["TipoDocRef"]."'";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {
                $IdTipoDoc=$registro['id'];
            }

            $SQL="SELECT * FROM CTRegDocumentos WHERE numero='".$registrox["FolioDocRef"]."' AND id_tipodocumento='$IdTipoDoc' AND rut='".$registrox["rut"]."'";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {
                $Linea='';
                $nfac=$registro["numero"];
                $mfac=$registro["total"];
                $ffac=date('d-m-Y',strtotime($registro["fecha"]));
                $fkey=$registro["keyas"];
                $Cta=$registro["cuenta"];
            }

            $SQL="SELECT * FROM CTCliPro WHERE rut='".$registrox["rut"]."'";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {
                $rrut=$registro["rut"];
                $rsoc=$registro["razonsocial"];
            }
            $SQLCta1=$SQLCta."AND numero='".$Cta."'";

            $resultados = $mysqli->query($SQLCta1);
            while ($registro = $resultados->fetch_assoc()) {
                $Cta=$Cta." - ".$registro["detalle"];
            }

            $Sw="";
            if($registrox["q"]==$mfac){
                $Sw='<button type="button" class="btn btn-grabar btn-block btn-xs" id="BtnVisual" onclick="ProcesarX(\''.$nId.'\','.$registrox["FolioDocRef"].','.$registrox["x"].',\''.$rsoc.'\',\''.$nNC.'\',\''.$fNC.'\',\''.$mNC.'\',\''.$nfac.'\',\''.$ffac.'\',\''.$mfac.'\')" data-toggle="modal" data-target="#myModal">Centralizar</button>';
            }

            if($registrox["q"]<$mfac){
                $Sw='<button type="button" class="btn btn-cancelar btn-block btn-xs" id="BtnVisual" onclick="ProcesarX(\''.$nId.'\','.$registrox["FolioDocRef"].','.$registrox["x"].',\''.$rsoc.'\',\''.$nNC.'\',\''.$fNC.'\',\''.$mNC.'\',\''.$nfac.'\',\''.$ffac.'\',\''.$mfac.'\')" data-toggle="modal" data-target="#myModal">Centralizar</button>';
            }

            if($fkey!=""){
                $mfac="Documento ya centralizado";
                $Sw="";
            }else{
                if($mfac!=""){
                    $mfac=number_format($mfac, $NDECI, $DDECI, $DMILE);
                } 
            }

            if($Linea=='SW'){
                $Str=$Str.
                '
                    <tr>
                        <td align="right">'.$nNC.'</td>
                        <td align="center">'.$fNC.'</td>
                        <td align="center">'.$rrut.'</td>
                        <td>'.$rsoc.'</td>
                        <td align="right">'.number_format($mNC, $NDECI, $DDECI, $DMILE).'</td>
                        <td align="" colspan="5">'.$nfac.'</td>
                    </tr>
                ';
            }else{
                $Str=$Str.
                '
                    <tr>
                        <td align="right">'.$nNC.'</td>
                        <td align="center">'.$fNC.'</td>
                        <td align="center">'.$rrut.'</td>
                        <td>'.$rsoc.'</td>
                        <td align="right">'.number_format($mNC, $NDECI, $DDECI, $DMILE).'</td>
                        <td align="right">'.$nfac.'</td>
                        <td align="center">'.$ffac.'</td>
                        <td align="">'.$Cta.'</td>
                        <td align="right">'.$mfac.'</td>
                        <td align="right">'.$Sw.'</td>
                    </tr>
                ';
            }
        }

        $mysqli->close();

    $Str=$Str.'</table>';
	echo $Str;
?>
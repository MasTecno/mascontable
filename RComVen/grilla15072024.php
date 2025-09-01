<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $frm=$_POST['frm'];

    if($_POST['dat1']!=""){
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("DELETE FROM CTRegDocumentos WHERE id='".$_POST['dat1']."'");
        $mysqli->close();
    }

    if($_POST['EliRefX']!=""){
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTRegDocumentos SET lote='', keyas='' WHERE lote='".$_POST['EliRefX']."' OR keyas='".$_POST['EliRefX']."'");
        $mysqli->close();
    }


    if($_POST['EliRegi']!="" && $_POST['EliRegi']=="S"){
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("DELETE FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND lote='' ");
        $mysqli->close();
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
    $mysqli->close();


        echo '
            <table class="table table-hover table-condensed">

            <thead>
                <tr style="background-color: #d9d9d9;">
                    <th width="7%">Fecha</th>
                    <th width="7%">Rut</th>
                    <th>Razon Social</th>
                    <th>Cuenta</th> 
                    <th>T. Documento</th>
                    <th style="text-align: right;">N&deg; Doc</th>
                    <th style="text-align: right;">Exento</th>
                    <th style="text-align: right;">Neto</th>
                    <th style="text-align: right;">IVA</th>
                    <th style="text-align: right;">Otro Imp</th>
                    <th style="text-align: right;">Total</th>
                    <th width="1%"></th>
                    <th width="1%"></th>
                    <th width="1%"></th>
                </tr>
            </thead>
            <tbody id="myTable"> 
        ';

        if ($frm=="V") {
            $f="C";
        }else{
            $f="P";
        }

        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY fecha";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {

            $rsocial="";
            $SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."' AND tipo='$f'";
            $resultados1 = $mysqli->query($SQL1);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $rsocial=$registro1["razonsocial"];
            }

            $SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
            $resultados1 = $mysqli->query($SQL1);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $nomdoc=$registro1["nombre"];
                $operador=$registro1["operador"];
            }

            $nomcuenta="";
            if ($_SESSION["PLAN"]=="S"){
                $SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
            }else{
                $SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
            }
            $resultados1 = $mysqli->query($SQL1);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $nomcuenta=$registro1["detalle"];
            }

            $SWBOT=0;

            //$SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND lote>'0' AND numero='".$registro["numero"]."'";
            $SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND lote>'0' AND id='".$registro["id"]."'";
            $resultados1 = $mysqli->query($SQL1);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $SWBOT=1;
            }

            if($operador=="R"){
                $operador=-1;
            }else{
                $operador=1;
            }

            $Marca=$registro["exento"]+$registro["neto"]+$registro["iva"]+$registro["retencion"];
            $Rete=$registro["retencion"];
            $CLi='';
            if ($Marca>$registro["total"]) {
                $Rete=$registro["retencion"]*-1;
                $CLi='style="background-color: #ebf1d2;"';
            }else{
                if ($Marca!=$registro["total"]) {
                    $CLi='style="background-color: #ffd2d2;"';
                }
            }

            echo '
                <tr '.$CLi.'>
                    <td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
                    <td>'.$registro["rut"].'</td>
                    <td>'.($rsocial).'</td>
                    <td>'.$registro["cuenta"]." - ".(strtoupper($nomcuenta)).'</td>
                    <td>'.($nomdoc).'</td>
                    <td align="right">'.$registro["numero"].'</td>
                    <td align="right">$'.number_format(($registro["exento"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
                    <td align="right">$'.number_format(($registro["neto"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
                    <td align="right">$'.number_format(($registro["iva"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
                    <td align="right">$'.number_format(($Rete*$operador), $NDECI, $DDECI, $DMILE).'</td>
                    <td align="right">$'.number_format(($registro["total"]*$operador), $NDECI, $DDECI, $DMILE).'</td>
            ';

            if ($frm=="V") {
                $nf="'CENTRALIZA ".$nomdoc." ".$registro["numero"].", DE VENTAS'";
            }else{
                if ($frm=="C") {
                    $nf="'CENTRALIZA ".$nomdoc." ".$registro["numero"].", DE COMPRAS'";
                }
            }

            $calmerc1=$registro["exento"]+$registro["neto"];
            $caliva=$registro["iva"];
            $calmerc2=$registro["retencion"];
            $caltotal=$registro["total"];

            $NC=substr($registro["keyas"],0,2);

            $SwNC=0;
            if ($NC=="NC"){
                $tncredito=0;
                $SQL1="SELECT * FROM CTRegDocumentos WHERE keyas='".$registro["keyas"]."' AND rutempresa='$RutEmpresa'";
                $resultados1 = $mysqli->query($SQL1);
                while ($registro1 = $resultados1->fetch_assoc()) {
                    if($registro1["id_tipodocumento"]=='4' || $registro1["id_tipodocumento"]=='5' || $registro1["id_tipodocumento"]=='32' || $registro1["id_tipodocumento"]=='37'){
                        $tncredito=$tncredito-$registro1["total"];
                    }else{
                        $tncredito=$tncredito+$registro1["total"];
                    }
                }
                if($tncredito<>0){
                    $SwNC=1;
                }
            }

            if ($NC=="NC" &&  $SwNC==0){
                echo '
                    <td align="center" colspan="3">
                        <button type="button" class="btn btn-cancelar btn-sm" title="Este Documento est&aacute; asociado a una Nota de Cr&eacute;dito, y su monto anula documento lo que no genera un Voucher en sistema." onclick="EliRef(\''.$registro["keyas"].'\')">
                            <span class="glyphicon glyphicon-remove-circle"> Liberar Referencia</span>        
                        </button>
                    </td>
                ';
            }

            if ($SWBOT==0) {
                echo '
                    <td align="center" >
                        <button type="button" class="btn btn-cancelar btn-sm" title="Eliminar Documento" onclick="EliReg('.$registro["id"].',\''.$registro["rut"].'\',\''.$registro["numero"].'\')">
                            <span class="glyphicon glyphicon-remove-circle"></span>        
                        </button>
                    </td>
                ';
            }else{
                // echo '
                //     <td align="center" >
                //     </td>
                // ';
            }


            if ($SWBOT==0) {
                echo '
                    <td align="center" >
                        <button type="button" class="btn btn-modificar btn-sm" title="Modificar Cuenta, Montos o Movimiento" onclick="ModReg('.$registro["id"].')">
                            <span class="glyphicon glyphicon-edit"></span>        
                        </button>
                    </td>
                ';
            }else{
                // echo '
                //     <td align="center" >
                //     </td>
                // ';
            }

            if ($SWBOT==0 && $registro["origen"]!="Z") {
                echo '              
                    <td align="center" >
                        <button type="button" class="btn btn-grabar btn-sm" data-toggle="modal" data-target="#squarespaceModal" title="Centralizacion Individual" onclick="Lala('.$registro["id"].','.$registro["cuenta"].',\''.(strtoupper($nomcuenta)).'\','.$calmerc1.','.$caliva.','.$calmerc2.','.$caltotal.','.$nf.',\''.$operador.'\',\''.date('d-m-Y',strtotime($registro["fecha"])).'\')">
                            <span class="glyphicon glyphicon-paste"></span>        
                        </button>
                    </td>
                ';  
            }else{
                // echo '
                //     <td align="center" >
                //     </td>
                // ';
            }

            echo '
            </tr>
            ';            

            $texento=$texento+($registro["exento"]*$operador);
            $tneto=$tneto+($registro["neto"]*$operador);
            $tiva=$tiva+($registro["iva"]*$operador);
            $tretencion=$tretencion+($registro["retencion"]*$operador);
            $ttotal=$ttotal+($registro["total"]*$operador);

        }

        $mysqli->close();

        echo'
                <tr style="background-color: #d9d9d9;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right"><strong>Totales</strong></td>
                    <td align="right"><strong>$'.number_format($texento, $NDECI, $DDECI, $DMILE).'</strong></td>
                    <td align="right"><strong>$'.number_format($tneto, $NDECI, $DDECI, $DMILE).'</strong></td>
                    <td align="right"><strong>$'.number_format($tiva, $NDECI, $DDECI, $DMILE).'</strong></td>
                    <td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
                    <td align="right"><strong>$'.number_format($ttotal, $NDECI, $DDECI, $DMILE).'</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        ';

?>
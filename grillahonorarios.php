<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $frm=$_POST['frm'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


    if($_POST['dat1']!=""){
      $mysqli->query("DELETE FROM CTHonorarios WHERE id='".$_POST['dat1']."'");
    }

    $SQL="SELECT * FROM CTParametros WHERE estado='A'";
    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {

      if($registro['tipo']=="SEPA_MILE"){
        $DMILE=$registro['valor'];  
      }

      if($registro['tipo']=="SEPA_MILE"){
        $DMILE=$registro['valor'];  
      }

      if($registro['tipo']=="SEPA_DECI"){
        $DDECI=$registro['valor'];  
      }

      if($registro['tipo']=="NUME_DECI"){
        $NDECI=$registro['valor'];  
      } 

      if($registro['tipo']=="TIPO_MONE"){
        $DMONE=$registro['valor'];  
      }
    }
    $mysqli->close();

?>

<?php 

  echo '
           <thead>
              <tr>
                <th>Fecha</th>
                <th>Rut</th>
                <th>Razon Social</th>
                <th>Cuenta</th> 
                <th>N&deg; Doc</th>
                <th>Tipo Documento</th>
                <th>Bruto</th>
                <th>Retenci&oacute;n</th>
                <th>Liquido</th>
                <th width="1%">Eliminar</th>
                <th width="1%">Voucher</th>
              </tr>
            </thead>
            <tbody id="ListDoc"> 
            ';
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SWBOT=0;

    $SQL="SELECT * FROM CTHonorarios WHERE estado='A' and origen<>'Z' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY fecha";
    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {


      if ($registro['movimiento']!="") {
        $SWBOT=1;
      }else{
        $SWBOT=0;
      }
           
      $rsocial="";
      $SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
      $resultados1 = $mysqli->query($SQL1);
      while ($registro1 = $resultados1->fetch_assoc()) {
        $rsocial=$registro1["razonsocial"];
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

      if ($registro["tdocumento"]=="R") {
        $tdocum="Recibido";
      }else{
        $tdocum="Emitido";
      }

      $calbruto=$registro["bruto"];
      $calrete=$registro["retencion"];
      $calliqui=$registro["liquido"];

echo '
              <tr>
                <td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
                <td>'.$registro["rut"].'</td>
                <td>'.$rsocial.'</td>
                <td>'.$registro["cuenta"]." - ".strtoupper($nomcuenta).'</td>
                <td align="right">'.$registro["numero"].'</td>
                <td>'.$tdocum.'</td>
                <td align="right">$'.number_format(($registro["bruto"]), $NDECI, $DDECI, $DMILE).'</td>
                <td align="right">$'.number_format(($registro["retencion"]), $NDECI, $DDECI, $DMILE).'</td>
                <td align="right">$'.number_format(($registro["liquido"]), $NDECI, $DDECI, $DMILE).'</td>
';


    if ($SWBOT==0) {


      echo '
                      <td align="center" >
                        <button type="button" class="btn btn-danger btn-sm" onclick="EliReg('.$registro["id"].')">
                          <span class="glyphicon glyphicon-remove-circle"></span>        
                        </button>
                      </td>
      ';

      echo '              
                      <td align="center" >
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#squarespaceModal" onclick="Lala('.$registro["id"].','.$calbruto.','.$calrete.','.$calliqui.','.$registro["numero"].','.$registro["cuenta"].',\''.$registro["tdocumento"].'\',\''.$registro["tdocumento"].'-'.$registro["numero"].'-'.$registro["rut"].'\',\''.$registro["rut"].'\')">
                          <span class="glyphicon glyphicon-paste"></span>        
                        </button>
                      </td>
      ';  
    }else{
      echo '
                      <td align="center" >
                      </td>
      ';
    }






echo '
              </tr>
';

      $tbruto=$tbruto+($registro["bruto"]);
      $tretencion=$tretencion+($registro["retencion"]);
      $tliquido=$tliquido+($registro["liquido"]);

    }

    $mysqli->close();

echo'
              <tr class="success">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><strong>Totales</strong></td>
                <td align="right"><strong>$'.number_format($tbruto, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td align="right"><strong>$'.number_format($tliquido, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td></td>
              </tr>
';





?>


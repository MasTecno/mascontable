<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    // $frm=$_POST['frm'];

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


    if($_POST['dat1']!=""){
      $mysqli->query("DELETE FROM CTHonorarios WHERE id='".$_POST['dat1']."'");
    }

    if($_POST['EliRegi']!="" &&$_POST['EliRegi']=="S"){
      // $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
      $mysqli->query("DELETE FROM CTHonorarios WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' AND movimiento=''");
      // $mysqli->close();
    }

    $dmes = substr($Periodo,0,2);
    $dano = substr($Periodo,3,4);
  
    if(isset($_POST['d1'])){
      if ($_POST['d1']!="") {
        $textfecha=$_POST['d1'];
      }else{
        $textfecha="01-".$dmes."-".$dano;
      }      
    }else{
      $textfecha="01-".$dmes."-".$dano;
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
              <tr style="background-color: #d9d9d9;">
                <th>Fecha</th>
                <th>Rut</th>
                <th>Razon Social</th>
                <th>Cuenta</th> 
                <th style="text-align:center;">N&deg; Doc</th>
                <th style="text-align:center;">Tipo Documento</th>
                <th style="text-align:right;">Bruto</th>
                <th style="text-align:right;">Retenci&oacute;n</th>
                <th style="text-align:right;">3% Prestamo</th>
                <th style="text-align:right;">Liquido</th>
                <th width="1%"></th>
                <th width="1%"></th>
              </tr>
            </thead>
            <tbody id="ListDoc"> 
            ';
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTParametros";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
      if ($registro['tipo']=='RETE_HONO') {
        $Val_Ret=$registro['valor'];
      }
    }
  
    if ($dano=="2020") {
      $Val_Ret=10.75;
    }
  
    if ($dano=="2021") {
      $Val_Ret=11.5;
    }
  
    if ($dano=="2022") {
      $Val_Ret=12.25;
    }
  
    if ($dano=="2023") {
      $Val_Ret=13;
    }
  
    if ($dano=="2024") {
      $Val_Ret=13.75;
    }
  
    if ($dano=="2025") {
      $Val_Ret=14.5;
    }
  
    if ($dano=="2026") {
      $Val_Ret=15.25;
    }
  
    if ($dano=="2027") {
      $Val_Ret=16;
    }
  
    if ($dano=="2028") {
      $Val_Ret=17;
    }

    $SWBOT=0;

    $SQL="SELECT * FROM CTHonorarios WHERE estado='A' and origen<>'Z' AND rutempresa='$RutEmpresa' AND periodo='$Periodo' ORDER BY fecha";
    $resultados = $mysqli->query($SQL);

    while ($registro = $resultados->fetch_assoc()) {
      if ($registro['movimiento']!="" && $registro["origen"]!="M") {
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
        $tdocum="Terceros";
      }

      $calbruto=$registro["bruto"];
      $calrete=$registro["retencion"];
      $calliqui=$registro["liquido"];
      $calreteCal=round(($registro["bruto"]*$Val_Ret)/100);
      $calreteCal3=round(($registro["bruto"]*3)/100);

      $calrete3=$calrete-$calreteCal;
      $calrete=$calreteCal;

      $color='';
      if($calrete3<0){
        $calrete3=0;
      }

      if($registro["retencion"]==0){
        $calrete=0;
      }
      // if($calrete3<0){
      //   $color='style="background-color: #ed9e9e;"';
      // }

      // if($calreteCal3!=$calrete3 && $calrete3!=0){
      //   $color='style="background-color: #ed9e9e;"';
      // }




echo '
              <tr '.$color.'>
                <td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>
                <td>'.$registro["rut"].'</td>
                <td>'.$rsocial.'</td>
                <td>'.$registro["cuenta"]." - ".strtoupper($nomcuenta).'</td>
                <td align="center">'.$registro["numero"].'</td>
                <td align="center">'.$tdocum.'</td>
                <td align="right">$'.number_format(($calbruto), $NDECI, $DDECI, $DMILE).'</td>
                <td align="right">$'.number_format(($calrete), $NDECI, $DDECI, $DMILE).'</td>
                <td align="right">$'.number_format(($calrete3), $NDECI, $DDECI, $DMILE).'</td>
                <td align="right">$'.number_format(($calliqui), $NDECI, $DDECI, $DMILE).'</td>
';


    if ($SWBOT==0) {


      echo '
                      <td align="center" >
                        <button type="button" class="btn btn-cancelar btn-xs" onclick="EliReg('.$registro["id"].')">
                          <span class="glyphicon glyphicon-remove-circle"></span>        
                        </button>
                      </td>
      ';

      echo '              
                      <td align="center" >
                        <button type="button" class="btn btn-grabar btn-xs" data-toggle="modal" data-target="#squarespaceModal" onclick="Lala('.$registro["id"].','.$calbruto.','.$calrete.','.$calrete3.','.$calliqui.','.$registro["numero"].','.$registro["cuenta"].',\''.$registro["tdocumento"].'\',\''.$registro["tdocumento"].'-'.$registro["numero"].'-'.$registro["rut"].'\',\''.$registro["rut"].'\',\''.date('d-m-Y',strtotime($registro["fecha"])).'\')">
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

      $tbruto=$tbruto+($calbruto);
      $tretencion=$tretencion+($calrete);
      $tretencion3=$tretencion3+($calrete3);
      $tliquido=$tliquido+($calliqui);

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
                <td align="right"><strong>$'.number_format($tbruto, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td align="right"><strong>$'.number_format($tretencion, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td align="right"><strong>$'.number_format($tretencion3, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td align="right"><strong>$'.number_format($tliquido, $NDECI, $DDECI, $DMILE).'</strong></td>
                <td></td>
                <td></td>
              </tr>
';





?>


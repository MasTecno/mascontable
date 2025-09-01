<?php
    header('Content-Type: text/html; charset=iso-8859-1');
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODOPC'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $frm=$_POST['frm'];

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

      if($registro['tipo']=="TIPO_MONE"){
        $DMONE=$registro['valor'];  
      }

      if($registro['tipo']=="NUME_DECI"){
        $NDECI=$registro['valor'];  
      } 
    }
    $mysqli->close();

    if($_SESSION['FILTRO']==1){
      $PeriodoX=substr($_SESSION['PERIODOPC'],3,4);
    }else{
      $PeriodoX=$_SESSION['PERIODOPC'];
    }


?> 
  
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

           <thead>
              <tr>
                <th width="10%">Fecha</th>
                <?php if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"): ?>
                  <th width="10%" style="text-align: center;">Comprobante</th>
                  <th width="10%" style="text-align: center;">Tipo</th>
                <?php endif ?>
                <th width="10%">Codigo</th>
                <th>Cuenta</th>
                <th width="10%" style="text-align: right;">Debe</th>
                <th width="10%" style="text-align: right;">Haber</th>
                <th width="1%"> </th>
              </tr>
            </thead>
            <tbody>
            <?php

              $NomCont=$_SESSION['NOMBRE'];
              //$Periodo=$_SESSION['PERIODOPC'];
              $RazonSocial=$_SESSION['RAZONSOCIAL'];
              $RutEmpresa=$_SESSION['RUTEMPRESA'];

              $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
              // $const="";
              // $str="";
              // $sw=0;



              $SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo LIKE '%$PeriodoX' GROUP BY id,keyas ORDER BY fecha, id, debe ASC";

              if(isset($_POST['seleccue'])){
                if ($_POST['seleccue']!="") {
                  //$PeriodoX = substr($Periodo,3,4);
                  $SQL="SELECT * FROM CTRegLibroDiario WHERE estado='A' AND rutempresa='$RutEmpresa' AND periodo LIKE '%$PeriodoX' AND cuenta='".$_POST['seleccue']."' GROUP BY id,keyas ORDER BY id, debe ASC";
                }
              }
              
              $resultados = $mysqli->query($SQL);
              while ($registro = $resultados->fetch_assoc()) {
                     
                if ($_SESSION["PLAN"]=="S"){
                  $SQL1="SELECT * FROM CTCuentasEmpresa WHERE numero='".$registro["cuenta"]."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'";
                }else{
                  $SQL1="SELECT * FROM CTCuentas WHERE numero='".$registro["cuenta"]."'";
                }
                $resultados1 = $mysqli->query($SQL1);
                while ($registro1 = $resultados1->fetch_assoc()) {
                  $ncuenta=strtoupper($registro1["detalle"]);
                }

                $SQL1="SELECT * FROM CTRegLibroDiario WHERE keyas='".$registro["keyas"]."' AND glosa <>''";
                $resultados1 = $mysqli->query($SQL1);
                while ($registro1 = $resultados1->fetch_assoc()) {
                    if ($registro1["tipo"]=="E") {
                      $xMen="Egreso";
                    }
                    if ($registro1["tipo"]=="I") {
                      $xMen="Ingreso";  
                    }
                    if ($registro1["tipo"]=="T") {
                      $xMen="Traspaso";
                    }


                    $ncomprobante=number_format($registro1["ncomprobante"], $NDECI, $DDECI, $DMILE);
                }


                if($registro["glosa"]==""){
                                echo '<tr>
                                      <td>'.date('d-m-Y',strtotime($registro["fecha"])).'</td>';

                                if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"){
                                  echo '
                                    <td>'.$ncomprobante.'</td>
                                    <td>'.$xMen.'</td>';
                                }

                                echo '<td>'.$registro["cuenta"].'</td>
                                      <td>'.$ncuenta.'</td>
                                      <td align="right"> '.number_format($registro["debe"], $NDECI, $DDECI, $DMILE).'</td>
                                      <td align="right"> '.number_format($registro["haber"], $NDECI, $DDECI, $DMILE).'</td>
                                    </tr>';
                          $tgdebe=$tgdebe+$registro["debe"];
                          $tghaber=$tghaber+$registro["haber"];
                }

              if($registro["glosa"]!=""){


                              echo '
                              <tr class="info"> 
                                <td></td>';

                              if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"){
                                echo'
                                <td align="center"></td>
                                <td align="center"></td>';
                              }

                              echo '  
                                <td></td>
                                <td><strong>'.strtoupper($registro["glosa"]).'</strong></td>
                                <td align="right"> '.number_format($tgdebe, $NDECI, $DDECI, $DMILE).'</td>
                                <td align="right"> '.number_format($tghaber, $NDECI, $DDECI, $DMILE).'</td>
                              </tr>';
                              $Totdebe=$Totdebe+$tgdebe;
                              $Tothabe=$Tothabe+$tghaber;
                              $tgdebe=0;
                              $tghaber=0;
                              $BtsEli=0;
                }
              } 
  
  // echo $const;
                              echo '
                              <tr class="success">';

                              if ($_SESSION['COMPROBANTE']=="S" && $_SESSION['CCOSTO']=="S"){
                                echo '<th></th>
                                      <th></th>';
                              }
echo '
                                <td></td>
                                <td></td>
                                <td><strong>Totales</strong></td>
                                <td align="right"> '.number_format($Totdebe, $NDECI, $DDECI, $DMILE).'</td>
                                <td align="right"> '.number_format($Tothabe, $NDECI, $DDECI, $DMILE).'</td>
                              </tr>';

  $mysqli->close();

            ?>
            </tbody>
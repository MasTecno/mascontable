<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    if (isset($_POST['idempb'])) {
      if ($_POST['idempb']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
         if ($_SESSION["PLAN"]=="S"){
          $mysqli->query("UPDATE CTCuentasEmpresa SET estado='B' WHERE id='".$_POST['idempb']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");          
         }else{
          $mysqli->query("UPDATE CTCuentas SET estado='B' WHERE id='".$_POST['idempb']."'");          
         }
        $mysqli->close();
      }
    }

    if (isset($_POST['idempa'])) {
      if ($_POST['idempa']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        if ($_SESSION["PLAN"]=="S"){
            $mysqli->query("UPDATE CTCuentasEmpresa SET estado='A' WHERE id='".$_POST['idempb']."' AND rut_empresa='".$_SESSION['RUTEMPRESA']."'");          
        }else{
            $mysqli->query("UPDATE CTCuentas SET estado='A' WHERE id='".$_POST['idempa']."'");
        }
        $mysqli->close();
      }
    }

?>
<!DOCTYPE html>
<html >
<head>
  <title>MasContable</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/StConta.css">

  <style>
    /* Remove the navbar's default margin-bottom and rounded borders */
    .navbar {
      margin-bottom: 0;
      border-radius: 0;
    }
    
    /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
    .row.content {height: 450px}
    
    /* Set gray background color and 100% height */
    .sidenav {
      padding-top: 20px;
      background-color: #f1f1f1;
      height: 100%;
    }
    
    /* Set black background color, white text and some padding */
    footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height:auto;}
    }


  </style>
<script type="text/javascript">
    
    function Baja(valor){
      form1.idempb.value=valor;
      form1.action="#";
      form1.submit();
    }

    function Alta(valor){
      form1.idempa.value=valor;
      form1.action="#";
      form1.submit();
    }

</script>  

</head>
<body>


<?php 
  include 'nav.php';
?>

<div class="container-fluid text-left">

  <div class="row content">

<div class="col-sm-2"><!--sidenav-->
       <!--<div class="well">
        <p><strong>Contador;</strong> <?php echo $NomCont; ?></p>
        <?php if($Periodo!=""){ ?>
        <p><strong>Empresa;</strong> <?php echo $RazonSocial; ?></p>
        <p><strong>Periodo;</strong> <?php echo $Periodo; ?></p>
        <?php } ?>
        <?php if($Periodo!=""){ ?>
        <button type="button" class="btn btn-warning btn-block" onclick="CamEmpr()">Cambiar Empresa</button>
        <?php } ?>
      </div>-->
</div>

    <div class="col-sm-8 text-left">
      <h3>Regitros de Documentos</h3>
      <form action="xfrmCuentas.php" method="POST" name="form1" id="form1">

          <div class="col-md-2">
            <label for="numero">Numero</label>
            <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $rut; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>

          </div> 

          <div class="col-md-8">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="" required>
            <input type="hidden" name="idempb" id="idempb">
            <input type="hidden" name="idempa" id="idempa">
          </div> 

         

          <div class="col-md-6">
            <label for="direccion">Categoria</label>  
            <select class="form-control" id="categoria" name="categoria" required>
              <option value="">Selecciones</option>
              <?php 
                  $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                  $SQL="SELECT * FROM CTCategoria WHERE estado<>'X'";
                  $resultados = $mysqli->query($SQL);
                  while ($registro = $resultados->fetch_assoc()) {
                      echo "<option value ='".$registro["id"]."'>".$registro["nombre"]."</option>";
                  }
                  $mysqli->close();
               ?>
            </select>
           
          </div>

          <div class="clearfix"> </div>
         <!-- <div class="col-md-4">
            <label for="giro">Giro</label>  
            <input type="text" class="form-control" id="giro" name="giro" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $giro; ?>" required>
          </div>

          <div class="clearfix"> </div>
          <div class="col-md-2">
            <label for="ciudad">Ciudad</label>
            <input type="text" class="form-control" id="ciudad" name="ciudad" maxlength="50" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $ciudad; ?>" required>
          </div>

          <div class="col-md-2">
            <label for="d3">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" maxlength="50" value="<?php echo $correo; ?>">
          </div>

          <div class="clearfix"> </div>-->

          <div class="col-md-12">
            <br>
            <p>
            <?php 
            if ($sw==1) {
            ?>
            <button type="submit" class="btn btn-success" tabindex="15">Modificar</button>
            <?php 
              }else{
             ?>
            <button type="submit" class="btn btn-success" tabindex="15">Grabar</button>
            <?php 
              }
             ?>
             </p>
          </div>

      </form>

      <div class="clearfix"> </div>
      <hr>
      
      <h3>Documentos Disponibles</h3>
      <form name="form2" action="#" method="POST">
      <input type="hidden" name="CTEmpre">
      <p>

          <table class="table table-hover">
            <thead>
              <tr>
                <th width="10%">Cuenta</th>
                <th>Detalle</th>
                <th>Categoria</th>
                <th width="10%"> </th>
              </tr>
            </thead>

            <tbody>
            <?php 
              $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
              if ($_SESSION["PLAN"]=="S"){
                  $SQL="SELECT * FROM CTCuentasEmpresa WHERE estado<>'X' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY numero ASC";
              }else{
                  $SQL="SELECT * FROM CTCuentas WHERE estado<>'X' ORDER BY numero ASC";
              }
              $resultados = $mysqli->query($SQL);
              while ($registro = $resultados->fetch_assoc()) {

                $SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
                $resultados1 = $mysqli->query($SQL1);
                while ($registro1 = $resultados1->fetch_assoc()) {
                  $tipcat=$registro1["nombre"];
                }

echo '
              <tr>
                <td>'.$registro["numero"].'</td>
                <td>'.strtoupper($registro["detalle"]).'</td>
                <td>'.$tipcat.'</td>
';

//echo '          <td><button type="button" class="btn btn-info btn-xs" onclick="Modifi('.$registro["id"].')">Modificar</button></td>';

if($registro["estado"]=="B"){
echo '          <td><button type="button" class="btn btn-warning btn-xs" onclick="Alta('.$registro["id"].')">Alta</button></td>';
}else{
echo '          <td><button type="button" class="btn btn-danger btn-xs" onclick="Baja('.$registro["id"].')">Baja</button></td>';
}


echo '
              </tr>
';
              }       
              $mysqli->close();
            ?>
              
             

            </tbody>

          </table>      


    </div>
    <div class="col-sm-2"><!--sidenav-->
     <!-- <div class="well">
        <p>ADS</p>
      </div>
      <div class="well">
        <p>ADS</p>
      </div>-->
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>


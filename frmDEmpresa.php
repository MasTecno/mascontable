<?php






?>
<!DOCTYPE html>
<html >
<head>
  <title>MasContable</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
    
    function Volver(){
      form1.action="frmMain.php";
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

    <div class="col-sm-12 text-left">
      <h3>Datos de la Empresa</h3>
      <form action="#" method="POST" name="form1" id="form1">

          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon">Rut</span>
              <input type="text" class="form-control" placeholder="Ej. 96900500-1" id="numero" name="numero" value="<?php echo $rut; ?>" readonly="false" required autocomplete="off">
            </div>
          </div> 

          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">Raz&oacute;n Social</span>
              <input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $razonsocial; ?>" readonly="false" required autocomplete="off">
            </div>
          </div> 

          <div class="clearfix"> </div>
          <br>

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon">Direcci&oacute;n</span>
              <input type="text" class="form-control" id="direccion" name="direccion" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $direccion; ?>" readonly="false" required autocomplete="off">
            </div>
          </div>          

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon">Cuidad</span>
              <input type="text" class="form-control" id="ciudad" name="ciudad" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $ciudad; ?>" readonly="false" required autocomplete="off">
            </div>
          </div>

          <div class="clearfix"> </div>
          <br>

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon">Giro</span>
              <input type="text" class="form-control" id="giro" name="giro" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $giro; ?>" readonly="false" required autocomplete="off">
            </div>
          </div>

          <div class="clearfix"> </div>
          <br>

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon">Contacto</span>
              <input type="text" class="form-control" id="pcontacto" name="pcontacto" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $pcontacto; ?>" readonly="false" required autocomplete="off">
            </div>
          </div>          

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon">Correo</span>
              <input type="mail" class="form-control" id="correo" name="correo" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $correo; ?>" required autocomplete="off" readonly="false">
            </div>
          </div>         
          <div class="clearfix"> </div>
          <br>

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon">Telefono</span>
              <input type="mail" class="form-control" id="correo" name="correo" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $telefono; ?>" required autocomplete="off" readonly="false">
            </div>
          </div>         
          <div class="clearfix"> </div>
          <br>

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon">Plan</span>
              <input type="text" class="form-control" id="licencia" name="licencia" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $plan; ?>" readonly="false">
            </div>
          </div>  

          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-addon">Vigencia Licencia</span>
              <input type="text" class="form-control" id="licencia" name="licencia" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $licencia; ?>" readonly="false">
            </div>
          </div>  

          <div class="col-md-12">
            <br>
            <p>

              <button type="button" class="btn btn-default" onclick="Volver()">
                <span class="glyphicon glyphicon-remove"></span> Cancelar
              </button>   
             </p>
          </div>

      </form>

      <div class="clearfix"> </div>
 
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>


<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    if(isset($_POST['tpediodo'])){
      if ($_POST['tpediodo']!="") {      
       $_SESSION['PERIODOPC']=$_POST['tpediodo'];
      }
    }
      
    //if (isset($_SESSION['KEYASIENTO'])){
      if($_SESSION['KEYASIENTO']==""){
          $_SESSION['KEYASIENTO']=date("YmdHis");
      }
    //}
    
    
    $NomCont=$_SESSION['NOMBRE'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    //$Periodo=$_SESSION['PERIODO'];
    $Periodo=$_SESSION['PERIODOPC'];

    $RazonSocial=$_SESSION['RAZONSOCIAL'];

    $dmes = substr($Periodo,0,2);
    $dano = substr($Periodo,3,4);

    if($Periodo==""){
      header("location:frmMain.php");
      exit;
    }
    
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTParametros WHERE estado='A'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) { 

      if($registro['tipo']=="IVA"){
        $DIVA=$registro['valor']; 
      }

      if($registro['tipo']=="SEPA_MILE"){
        $DMILE=$registro['valor'];  
      }

      if($registro['tipo']=="SEPA_DECI"){
        $DDECI=$registro['valor'];  
      }

      if($registro['tipo']=="SEPA_LIST"){
        $DLIST=$registro['valor'];  
      }

      if($registro['tipo']=="TIPO_MONE"){
        $DMONE=$registro['valor'];  
      }

      if($registro['tipo']=="NUME_DECI"){
        $NDECI=$registro['valor'];  
      } 
    }
    $mysqli->close();
    
    if(isset($_POST['d1'])){
      if ($_POST['d1']!="") {
        $textfecha=$_POST['d1'];
      }else{
        $textfecha="01-".$dmes."-".$dano;
      }      
    }else{
      $textfecha="01-".$dmes."-".$dano;
    }

    if(isset($_POST['ModCue'])){
      if ($_POST['ModCue']!="") {
        $NModCue=$_POST['ModCue'];

        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $SQL="SELECT * FROM CTRegLibroDiario WHERE id='$NModCue'";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) { 
            $textfecha=date('d-m-Y',strtotime($registro["fecha"]));
            $LCuenta=$registro["cuenta"];
            $LDebe=$registro["debe"];
            $LHaber=$registro["haber"];
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
<!--   <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" src="js/jquery.maskedinput-1.2.2-co.min.js"></script>

  <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
  <script src="js/jquery.dataTables.min.js"></script> -->

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
  <script src="js/jquery.dataTables.min.js"></script>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

  <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
/*    #btinsert{
      display: none
    }*/
    .modal-footer {
        border-top: 0px solid #e5e5e5;
    }
  </style>
  
<script>

  $(document).ready(function() {
      $('#example').DataTable();
  } );

function BuscaCuenta(){
      var url= "buscacuenta.php";
      var x1=$('#d2').val();
      $.ajax({
        type: "POST",
        url: url,
        data: ('dat1='+x1),
        success:function(resp)
        {
          if(resp==""){
            alert("No se encontro cuenta");
            $('#d2').focus(); 
            $('#d2').select();
            form1.d3.value="";
          }else{
            form1.d3.value=resp;
          }
        }
      });
}

function EliReg(valor){

      var url= "grillaldiario.php";
      $.ajax({
        type: "POST",
        url: url,
        data: ('dat1='+valor),
        success:function(resp)
        {
          CargaGrilla();
        }

      });

}


function EliRegA(valor,Tstring){

      var r = confirm("Esta Seguro de eliminar el Voucher\r\n"+Tstring+"\r\nTambien Eliminara los comprobantes asociados");
      if (r == true) {
          var url= "grillaldiario.php";
          $.ajax({
            type: "POST",
            url: url,
            data: ('dat2='+valor),
            success:function(resp)
            {
              CargaGrilla();
            }

          });
      }
      // } else {
      //   alert("Operaciones Cancelada");
        
      // }
}
function EliRegALinea(valor){

      var url= "grillaasiento.php";
      $.ajax({
        type: "POST",
        url: url,
        data: ('dat3='+valor),
        success:function(resp)
        {
          CargaGrillaAsiento();
        }

      });

}

function GBLibroD(){

    var url= "gblibrodiario.php";
    $.ajax({
      type: "POST",
      url: url,
      data: $('#form1').serialize(),
      success:function(resp)
      {
        //form1.d3.value=resp;
        if (resp=="1"){
          $('#msj').html('<div class="alert alert-danger"><strong>Advertencia! </strong>El asiento no esta cuadrado</div>');
          form1.d6.value="";
        }else{
          if (resp=="2"){
            $('#msj').html('<div class="alert alert-danger"><strong>Advertencia! </strong>Los montos no pueden ser 0</div>');
            form1.d6.value="";
          }else{

            if(resp!=""){
              $('#msj').html(resp);
            }else{
              form1.d2.value="";
              form1.d3.value="";
              form1.d4.value="";
              form1.d5.value="";
              form1.d6.value="";
              form1.ModCue.value="";
              $('#msj').html(resp);
              CargaGrilla();
              form1.d2.focus();
              form1.d2.select();
            }

          }
        }

      }

    });
}

  function CargaGrilla(){

      var url= "grillaldiario.php";
      
      $.ajax({
        type: "POST",
        url: url,
        data: $('#form1').serialize(),
        success:function(resp)
        {
            $('#grilla').html(resp);
            CargaGrillaAsiento();
        }
        
      });
  }

  function CargaGrillaAsiento(){

      var url= "grillaasiento.php";
      
      $.ajax({
        type: "POST",
        url: url,
        data: $('#form1').serialize(),
        success:function(resp)
        {
            $('#grilla1').html(resp);
        }
        
      });
  }

  function data(valor){
    form1.d2.value=valor;
    BuscaCuenta();
    document.getElementById("cmodel").click();
  }


 $(document).ready(function (eOuter) {

    $('input').bind('keypress', function (eInner) {
        //alert(eInner.keyCode);
        if (eInner.keyCode == 13) //if its a enter key
        {
          
          var idinput = $(this).attr('id');

          if(idinput=="d1"){
            $('#d2').focus();
            $('#d2').select();
          }

          if(idinput=="d2"){
            BuscaCuenta();
            $('#d4').focus();
            $('#d4').select();
          }

          if(idinput=="d4"){
            $('#d5').focus();
            $('#d5').select();
          }

          if(idinput=="d5"){
            if (form1.d4.value>0 && form1.d5.value>0) {
              alert("Solo monto en Debe o Haber");
            }else{
              GBLibroD();
              $('#d2').focus();              
            }

          }

          if(idinput=="d6"){
            if($(this).val()!=""){
              GBLibroD();
              $('#d2').focus();
              $('#d2').select();
            }else{
              alert("Ingrese Glosa para cerrar asiento");
            }
          }

          return false;
        }
    });
}
);

 function ofcuenta(){
  if (form1.d2.value!="") {
    BuscaCuenta();
  }
 }

 function insertlin(){
    if (form1.d4.value>0 && form1.d5.value>0) {
      alert("Solo monto en Debe o Haber");
    }else{
      GBLibroD();
      form1.d2.focus();              
    }  
 }

</script>

<script>

  function CamEmpr(){
    form1.CTEmpre.value="";
    form1.action="frmMain.php";
    form1.submit();
  }

  function ModCue(valor){
    form1.ModCue.value=valor;
    form1.action="#";
    form1.submit();  
  }

    $( function() {
      $( "#d1" ).datepicker();
    } );

</script>


</head>
<body onload="CargaGrilla()">
  

<?php 
  include 'nav.php';
?>

<div class="container-fluid text-left">

  <div class="row content">

<?php
 include 'frmIzquierdo.php';
?>

  <!-- Modal  buscar codigo-->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
          <h4 class="modal-title">Listado de Cuentas</h4>
        </div>

        <div class="modal-body">
          <div class="col-md-12">

          <table id="example" class="display" cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th>Codigo</th>
                          <th>Detalle</th>
                          <th>Tipo de Cuenta</th>
                      </tr>
                  </thead>
                  <tfoot>
                      <tr>
                          <th>Codigo</th>
                          <th>Detalle</th>
                          <th>Tipo de Cuenta</th>
                      </tr>
                  </tfoot>
                  <tbody>
                  <?php 
                    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                    if ($_SESSION["PLAN"]=="S"){
                      $SQL="SELECT * FROM CTCuentasEmpresa WHERE estado='A' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
                    }else{
                      $SQL="SELECT * FROM CTCuentas WHERE estado='A' ORDER BY detalle";
                    }
                    $resultados = $mysqli->query($SQL);

                    while ($registro = $resultados->fetch_assoc()) {
                        
                        $SQL1="SELECT * FROM CTCategoria WHERE id='".$registro["id_categoria"]."'";
                        $resultados1 = $mysqli->query($SQL1);
                        while ($registro1 = $resultados1->fetch_assoc()) {
                          $tcuenta=$registro1["nombre"];
                        }

                        echo '<tr onclick="data(\''.$registro["numero"].'\')">
                                <td>'.$registro["numero"].'</td>
                                <td>'.$registro["detalle"].'</td>
                                <td>'.$tcuenta.'</td>
                            </tr>
                        ';
                    }
                    $mysqli->close();
                  ?>

                  </tbody>
          </table>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" id="cmodel">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- fin buscar codigo -->

    <div class="col-sm-10 text-left">
      <br>
      <div class="well well-sm">
        <strong>Registro de Asiento, Periodo <?php echo $Periodo; ?></strong>
      </div>

      <form action="#" method="POST" name="form1" id="form1">

          <?php if ($_SESSION['COMPROBANTE']=="S"): ?>
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon">Tipo</span>
                  <select class="form-control" id="ttmovimiento" name="ttmovimiento" required>
                    <option value="I" >Ingreso</option>
                    <option value="E" selected>Egreso</option>
                    <option value="T">Traspaso</option>
                  </select>
                </div>
              </div>
          <?php endif ?>

          <?php if ($_SESSION['CCOSTO']=="S"): ?>
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon">Centro de Costo</span>
                  <select class="form-control" id="tccosto" name="tccosto">
                    <option value="0">Default</option>
                    <?php //utf8_encode()
                      $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

                      $SQL="SELECT * FROM CTCCosto WHERE estado='A' AND rutempresa='$RutEmpresa' ORDER BY nombre";
                      $resultados = $mysqli->query($SQL);
                      while ($registro = $resultados->fetch_assoc()) {
                        echo '<option value="'.$registro['id'].'">'.$registro['nombre'].'</option>';
                      }
                      $mysqli->close();
                    ?>
                  </select>
                </div>
              </div>
          <?php endif ?>

          <div class="clearfix"></div>
          <br>


          <div class="col-md-2">
            <label for="d11">Fecha</label>
            <input id="d1" name="d1" type="text" class="form-control" size="10" maxlength="10" value="<?php echo $textfecha; ?>">
            <input type="hidden" name="ModCue" id="ModCue" value="<?php echo $NModCue;?>">

            <input type="hidden" name="CTEmpre">
          </div> 

          <div class="col-md-2">
            <label for="d11">Cuenta</label>
            <div class="input-group"> 
              <input type="text" class="form-control" id="d2" name="d2" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $LCuenta;?>">
                <div class="input-group-btn"> 
                  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" onfocus="javascritp:document.getElementById('d4').focus();">
                    <span class="glyphicon glyphicon-search"></span> 
                  </button>
                </div> 
            </div> 
          </div>

          <div class="col-md-4">
            <label for="d2">Detalle</label>  
            <input type="text" class="form-control" id="d3" name="d3" onChange="javascript:this.value=this.value.toUpperCase();" value=""  readonly="false" >
          </div>
          <div class="col-md-2">
            <label for="d5">Debe</label>
            <input type="number" class="form-control" id="d4" name="d4" maxlength="50" onfocus="ofcuenta()" value="<?php echo $LDebe;?>">
          </div>
          <div class="col-md-2">
            <label for="d5">Haber</label>
            <input type="number" class="form-control" id="d5" name="d5" maxlength="50" value="<?php echo $LHaber;?>">
            <button type="button" class="btn btn-default btn-block" id="btinsert" name="btinsert" onclick="insertlin()">Insertar Linea</button>
          </div>

          <div class="clearfix"> </div>

          <div class="col-md-12">
                <div class="input-group">
                  <span class="input-group-addon">Glosa</span>
                  <input type="text" class="form-control" id="d6" name="d6" maxlength="50" value="" onChange="javascript:this.value=this.value.toUpperCase();">
                </div>

          </div>

          <div class="clearfix"> </div>
          <div class="col-md-12">
            <div id="msj"></div>         
          </div>

      </form>

      <div class="clearfix"> </div>

      <hr>
      
      <div class="well well-sm">
        <strong>Asiento Actual</strong>
      </div>      
      <p>
        <table class="table table-hover" id="grilla1">

        </table>
      </p>      
      <hr>

      <div class="well well-sm">
        <strong>Transacciones del Periodo</strong>
      </div>

      <p>
        <table class="table table-hover" id="grilla">

        </table>
      </p>
    </div>
    <!--<div class="col-sm-2">sidenav-->
     <!-- <div class="well">
        <p>ADS</p>
      </div>
      <div class="well">
        <p>ADS</p>
      </div>
    </div>-->
  </div>
</div>

<?php include 'footer.php'; ?>

<script type="text/javascript">
  document.getElementById("d2").focus();
  document.getElementById("d2").select();
</script>

<script type="text/javascript">
// jQuery(function($){
//   $("#date0").mask("99/99/9999");
//   // $("#date1").mask("99/99/9999", {placeholder: 'dd/mm/yyyy'});
// });
$( "#d1" ).datepicker({
    // Formato de la fecha
    dateFormat: "dd-mm-yy",
    // Primer dia de la semana El lunes
    firstDay: 1,
    // Dias Largo en castellano
    dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
    // Dias cortos en castellano
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    // Nombres largos de los meses en castellano
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
    // Nombres de los meses en formato corto 
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
    // Cuando seleccionamos la fecha esta se pone en el campo Input 
    onSelect: function(dateText) { 
        $('#d1').val(dateText);
        $('#d2').focus();
        $('#d2').select();
    }
});

 <?php
  if ($LCuenta!="") {
    echo "BuscaCuenta();";
  }
 ?>

function getPlatform(){
 var platform=["Win32","Android","iOS"];
 for(var i=0;i<platform.length;i++){
   if(navigator.platform.indexOf(platform[i])>-1){
      return platform[i];
   }
 }
}
function mens(){
  alert("ok");
}
if(getPlatform()=="Win32"){
  //document.getElementById('btinsert').style.display= 'block' ;
  document.getElementById("btinsert").style.visibility = "hidden";
  // document.getElementById("d2").setAttribute = ("onClick", "mens()");
  // document.getElementById("btinsert").style.visibility = "visible";
// 
}else{
  // alert(getPlatform());
  document.getElementById("btinsert").style.visibility = "visible";
  // document.getElementById("d2").setAttribute = ("onClick", "mens()");
  // document.getElementById('btinsert').style.visibility = 'hidden';
  // document.getElementById("btinsert").style.visibility = "hidden";
}

</script>

</body>
</html>


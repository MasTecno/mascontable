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
    
    if (isset($_POST['anoselect'])) {
        if ($_POST['anoselect']!=""){
            // $dmes = substr($_POST['anoselect'],0,2);
            // $danol = substr($_POST['anoselect'],3,4);
            $danol=$_POST['anoselect'];
            $Xfdesde="01-01-".$danol;
            $Xfhasta="31-12-".$danol;
        }else{
            $dmes = substr($Periodo,0,2);
            $danol = substr($Periodo,3,4);
            $Xfdesde="01-01-".$danol;
            $Xfhasta="31-12-".$danol;
        } 
    }else{
        $dmes = substr($Periodo,0,2);
        $danol = substr($Periodo,3,4);
        $Xfdesde="01-01-".$danol;
        $Xfhasta="31-12-".$danol;
    } 

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTEmpresas WHERE rut='".$RutEmpresa."'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $representante=$registro['representante'];
        $xRrep=$registro['rut_representante'];    
    }
    // $mysqli->close();
    if (strlen($xRrep)==9) {
        $RutPunto1=substr($xRrep,-10,1);
    }else{
        $RutPunto1=substr($xRrep,-10,2);
    }
    
    $RutPunto2=substr($xRrep,-5);
    $RutPunto3=substr($xRrep,-8,3);
    $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

    if($dmes=="01"){
        $dmes="Enero";
    }
    if($dmes=="02"){
        $dmes="Febrero";
    }
    if($dmes=="03"){
        $dmes="Marzo";
    }
    if($dmes=="04"){
        $dmes="Abril";
    }
    if($dmes=="05"){
        $dmes="Mayo";
    }
    if($dmes=="06"){
        $dmes="Junio";
    }
    if($dmes=="07"){
        $dmes="Julio";
    }
    if($dmes=="08"){
        $dmes="Agosto";
    }
    if($dmes=="09"){
        $dmes="Septiembre";
    }
    if($dmes=="10"){
        $dmes="Octubre";
    }
    if($dmes=="11"){
        $dmes="Noviembre";
    }
    if($dmes=="12"){
        $dmes="Diciembre";
    }

    if ($_POST['rfecha']!="" && isset($_POST['rfecha'])) {
      $Xfdesde=$_POST['fdesde'];
      $Xfhasta=$_POST['fhasta'];
    }
// echo $danol;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MasContable</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <script type="text/javascript">

            function Updta(){
                // form1.submit();  
                CargaGrilla();
            }
            function GenLibro(){
                form1.method="POST";
                form1.target="_blank";
                form1.action="frmBalanceXLS.php";
                form1.submit();
                form1.target="";
                form1.action="#";        
            }

            function GenLibroPDF(){
                form1.method="POST";
                form1.target="_blank";
                form1.action="frmBalancePDF.php";
                form1.submit();
                form1.target="";
                form1.action="#";        
            }

            $( function() {
                $("#fdesde").datepicker();
                $("#fhasta").datepicker();
            });

            function MayorCta(valor){
                form1.CtaMayor.value=valor;
                form1.anual.value=1;
                // form1.action="../Mayor/";
                // form1.submit();

                form1.method="POST";
                form1.target="_blank";
                form1.action="../Mayor/";
                form1.submit();
                form1.target="";
                form1.action="#";


            }

            function CargaGrilla(){
                var url= "DetGrilla.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $('#form1').serialize(),
                    success:function(resp){
                        $('#Grilla').html(resp);
                    }
                });
            }

        </script>
    </head>
    <body onload="CargaGrilla()">
        <?php 
            include '../nav.php';
        ?>

        <div class="container-fluid text-left">
        <div class="row content">
            <div class="col-sm-12">
                <br>    
                <form action="#" method="POST" name="form1" id="form1">

                    <div class="col-md-4">
                        <div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
                            <div class="panel-heading">
                                <h3 class="panel-title">Balance General A&ntilde;o</h3>
                            </div>
                            <div class="panel-body">
                                <input type="hidden" name="anual" id="anual">
                                <input type="hidden" name="CtaMayor" id="CtaMayor">
                                
                                <div class="col-md-12">
                                    <div class="input-group">
                                    <span class="input-group-addon">A&ntilde;o</span>
                                        <select class="form-control" id="anoselect" name="anoselect" required onchange="Updta()">
                                        <?php 
                                            $yoano=date('Y');
                                            $tano="2010";
                                            while($tano<=$yoano){
                                                if ($danol==$tano) {
                                                    echo "<option value ='".$tano."' selected>".$tano."</option>";
                                                }else{
                                                    echo "<option value ='".$tano."'>".$tano."</option>";
                                                }
                                                $tano=$tano+1;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <hr>

                                <h4>Generar por rango de Fecha</h4>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Desde</span>
                                        <input id="fdesde" name="fdesde" type="text" class="form-control text-right" value="<?php echo $Xfdesde; ?>" size="10" maxlength="10">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br>

                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Hasta</span>
                                        <input id="fhasta" name="fhasta" type="text" class="form-control text-right" value="<?php echo $Xfhasta; ?>" size="10" maxlength="10">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-modificar" onclick="Updta(form.rfecha.value='1')">Generar</button>
                                </div>
                                
                                <input type="hidden" name="rfecha" id="rfecha" value="">
                                <input type="hidden" name="Frfecha" id="Frfecha" value="<?php echo $_POST['rfecha']; ?>">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
                            <div class="panel-heading">
                                <h3 class="panel-title">Opciones Balance</h3>
                                <input type="hidden" name="aproceso" id="aproceso" value="<?php echo $danol; ?>">
                            </div>
                            <div class="panel-body">
                                            
                                <div class="col-md-12">
                                    <input type="checkbox" id="check2" name="check2"> Insertar Art. 100 del C&oacute;digo Tributario
                                    <br>
                                    <input type="checkbox" id="check3" name="check3"> Insertar Representante
                                    <br>

                                    <input type="checkbox" id="check4" name="check4"> Insertar Periodo Impresi&oacute;n
                                    <br>
                                    Tama&ntilde;o <input type="text" name="tTexto" id="tTexto" value="8" maxlength="2" size="3"> (PDF)
                                    <br>
                                    <br>
                                    <l4 style="font-size: 12px; font-weight: 600;">Contador(es):</l4>

                                        <?php
                                            $Conta=0;
                                            $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

                                            $SQL="SELECT * FROM CTContadoresFirma WHERE Estado='A'";
                                            $resultados = $mysqli->query($SQL);
                                            while ($registro = $resultados->fetch_assoc()) {
                                                echo '
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="check_list[]" value="'.$registro["Id"].'">'.$registro['Rut'].', '.$registro['Nombre'].'
                                                        </label>
                                                    </div>
                                                    ';    
                                                $Conta=1;
                                            }
                                            if($Conta==0){
                                                echo '<p>Debe registrarse en menú mantenedor - contadores para aparecer en las opciones de firma.</p>';
                                            }
                                        ?>
                                </div>
                                <!-- <div class="clearfix"></div>
                                <br> -->
                                <!-- <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Rut Representante</span>
                                        <input type="text" class="form-control" autocomplete="off" id="rrepresentante" name="rrepresentante" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($srtRut); ?>">
                                    </div>
                                </div>                                
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Representante Legal</span>
                                        <input type="text" class="form-control" autocomplete="off" id="representante" name="representante" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($representante); ?>">
                                    </div>
                                </div> -->

                                <!-- <div class="clearfix"> </div>
                                <br> -->

                                <!-- <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Contador(a)</span>
                                        <input type="text" class="form-control" autocomplete="off" id="contador" name="contador" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo strtoupper($contador); ?>">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Contador(a)</span>
                                        <input type="text" class="form-control" autocomplete="off" id="rcontador" name="rcontador" onChange="javascript:this.value=this.value.toUpperCase();" value="">
                                    </div>
                                </div> -->

                                <div class="clearfix"> </div>
                                <br>

                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Comentario</span>
                                        <textarea class="form-control" rows="3" id="comment" name="comment"></textarea>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                    </div>

        
                    <div class="col-md-4">
                        <div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
                        <div class="panel-heading">
                            <h3 class="panel-title">Generar Libro</h3>
                            <input type="hidden" name="aproceso" id="aproceso" value="<?php echo $danol; ?>">
                        </div>
                        <div class="panel-body">

                        

                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-addon">Hasta</span>
                                    <select class="form-control" id="PageOri" name="PageOri">
                                        <option value="L">Horizontal</option>
                                        <option value="P">Vertical</option>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br>

                            <div class="col-md-12">                
                                    <input type="checkbox" name="ConMem" id="ConMem" value="">Membrete Empresa
                                <br>						

                                    <input type="checkbox" name="ConRep" id="ConRep" value="">Membrete Representante
                                <br>						

                                    <input type="checkbox" name="MarSup" id="MarSup" value="">Margen Superior
                                    <input class="text-right" type="text" name="nlines" id="nlines" value="4" maxlength="2" size="3">
                                <br>
                                
                                    <input type="checkbox" id="check5" name="check5"> Insertar Titulo Pre-Balance (PDF)
                                <br>

                                    <input type="checkbox" name="MarFol" id="MarFol" value="" checked>Folio Inicial PDF
                                    <input class="text-right" type="text" name="folio" id="folio" value="1" maxlength="20" size="3">
                                <br>

                                <input type="checkbox" id="check2" name="check2"> Excel - Solo Ctas con Movimiento
                                <br>

                            </div>
                            <div class="clearfix"></div>
                            <br>

                            <button type="button" class="btn btn-success btn-block" onclick="GenLibro()">Generar Excel</button>
                            <button type="button" class="btn btn-success btn-block" onclick="GenLibroPDF()">Generar PDF</button>
                        
                        </div>
                    </div>
                    </div>


                <div class="clearfix"> </div>
                </form>
            </div>
            
            <br>
            <br>



            <div class="col-sm-12 text-left" id="Grilla">

            </div>
            <br>
            <br>


        </div>
        </div>


        <?php include '../footer.php'; ?>

    </body>
    <script type="text/javascript">
    $( "#fdesde" ).datepicker({
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
          // $('#d1').val(dateText);
          // $('#d2').focus();
          // $('#d2').select();
        }
      });

      $( "#fhasta" ).datepicker({
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
          // $('#d1').val(dateText);
          // $('#d2').focus();
          // $('#d2').select();
        }
      });       
    </script>
</html>
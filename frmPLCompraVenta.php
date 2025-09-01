<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    $sw=0;

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTPlantillas WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."'";
    $resultados = $mysqli->query($SQL);
    $row_cnt = $resultados->num_rows;
    if ($row_cnt==0 && $_SESSION["PLAN"]=="S") {
        $SQL="SELECT * FROM CTPlantillas WHERE rut_empresa=''";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            $xnombre=$registro["nombre"];
            $xtdocumento=$registro["tipodocumento"];
            $xrut=$registro["rut"];
            $xrsocial=$registro["rsocial"];
            $xnumero=$registro["numero"];
            $xfecha=$registro["fecha"];
            $xexento=$registro["exento"];
            $xneto=$registro["neto"];
            $xiva=$registro["iva"];
            $xretencion=$registro["retencion"];
            $xtotal=$registro["total"];
            $xtipo=$registro["tipo"];
            $xcuenta=$registro["cuenta"];
            $mysqli->query("INSERT INTO CTPlantillas VALUE('','".$_SESSION['RUTEMPRESA']."','$xnombre','$xrut','$xrsocial','$xcuenta','$xtdocumento','$xnumero','$xfecha','$xexento','$xneto','$xiva','$xretencion','$xtotal','$xtipo','A')");
        }
    }
    $mysqli->close();

    if(isset($_POST['idmod']) && $_POST['idmod']!=""){
        $sw=1;
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $SQL="SELECT * FROM CTPlantillas WHERE id='".$_POST['idmod']."'";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            $xnombre=$registro["nombre"];
            $xtdocumento=$registro["tipodocumento"];
            $xrut=$registro["rut"];
            $xrsocial=$registro["rsocial"];
            $xnumero=$registro["numero"];
            $xfecha=$registro["fecha"];
            $xexento=$registro["exento"];
            $xneto=$registro["neto"];
            $xiva=$registro["iva"];
            $xretencion=$registro["retencion"];
            $xtotal=$registro["total"];
            $xtipo=$registro["tipo"];
            $xcuenta=$registro["cuenta"];
        }
        $mysqli->close();
    }

    if (isset($_POST['idempb']) && $_POST['idempb']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTPlantillas SET estado='B' WHERE id='".$_POST['idempb']."'");
        $mysqli->close();
    }

    if (isset($_POST['idempa']) && $_POST['idempa']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTPlantillas SET estado='A' WHERE id='".$_POST['idempa']."'");
        $mysqli->close();
    }

?> 
<!DOCTYPE html>
<html >
<head>
    <title>MasContable</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="css/StConta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <script type="text/javascript">

    function Baja(valor){
    form1.idempb.value=valor;
    form1.idmod.value="";
    form1.action="#";
    form1.submit();
    }

    function Alta(valor){
    form1.idempa.value=valor;
    form1.idmod.value="";
    form1.action="#";
    form1.submit();
    }
    function Modifi(valor){
    form1.idmod.value=valor;
    form1.action="#";
    form1.submit();
    }

    function data(valor){
        form1.cuenta.value=valor;
        document.getElementById("cmodel").click();
    }

    
    function Volver(){
        form1.action="frmMain.php";
        form1.submit();
    }

    jQuery(document).ready(function(e) {
        $('#myModal').on('shown.bs.modal', function() {
            $('input[name="BCodigo"]').focus();
        });
    });


    </script>  

    </head>
    <body>

    <?php 
    include 'nav.php';
    ?>

    <div class="container-fluid text-left">

    <div class="row content">

        <div class="col-sm-12 text-left">
            <br>
            <div class="well well-sm"><strong>Registro de Importaci&oacute;n de Libro</strong></div>

            <form action="xfrmPLCompraVenta.php" method="POST" name="form1" id="form1">

                <div class="col-md-8">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xnombre; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>
                    <input type="hidden" name="idempb" id="idempb">
                    <input type="hidden" name="idempa" id="idempa">
                    <input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">
                </div> 

                <div class="col-md-4">
                    <label for="stipo">Tipo de Libro</label>  
                    <select class="form-control" id="stipo" name="stipo" required>
                        <option value="">Selecciones</option>
                        <?php 
                            if ($xtipo!="") {
                                if ($xtipo=="C") {
                                    echo "<option value ='C' selected>Compras</option>";
                                    echo "<option value ='V'>Ventas</option>";
                                }else{
                                    echo "<option value ='C'>Compras</option>";
                                    echo "<option value ='V' selected>Ventas</option>";
                                }
                            }else{
                                echo "<option value ='C'>Compras</option>";
                                echo "<option value ='V'>Ventas</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="clearfix"></div>
                <br>

                <div class="well well-sm"><strong>Posiciones de Campos</strong></div>
                    <div class="col-md-1">
                        <label for="tdoc">Tipo Documento</label>
                        <input type="text" class="form-control" id="tdoc" name="tdoc" value="<?php echo $xtdocumento; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="trut">Rut</label>
                        <input type="text" class="form-control" id="trut" name="trut" value="<?php echo $xrut; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="trut">RSocial</label>
                        <input type="text" class="form-control" id="trsocial" name="trsocial" value="<?php echo $xrsocial; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="tnum">Numero</label>
                        <input type="text" class="form-control" id="tnum" name="tnum" value="<?php echo $xnumero; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="tfec">Fecha</label>
                        <input type="text" class="form-control" id="tfec" name="tfec" value="<?php echo $xfecha; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div>

                    <div class="col-md-1">
                        <label for="texe">Exento</label>
                        <input type="text" class="form-control" id="texe" name="texe" value="<?php echo $xexento; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="tnet">Neto</label>
                        <input type="text" class="form-control" id="tnet" name="tnet" value="<?php echo $xneto; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="tiva">IVA</label>
                        <input type="text" class="form-control" id="tiva" name="tiva" value="<?php echo $xiva; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="tret">Otros Impuestos</label>
                        <input type="text" class="form-control" id="tret" name="tret" value="<?php echo $xretencion; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 
                    <div class="col-md-1">
                        <label for="ttot">Total</label>
                        <input type="text" class="form-control" id="ttot" name="ttot" value="<?php echo $xtotal; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                    </div> 

                <div class="clearfix"> </div>
                <br>

                <div class="col-md-2">
                    <label for="d7">Cuenta</label>
                    <div class="input-group"> 
                        <input type="text" class="form-control text-right" id="cuenta" name="cuenta" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xcuenta;?>"> 
                        <div class="input-group-btn"> 
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
                                <span class="glyphicon glyphicon-search"></span> 
                            </button>
                        </div> 
                    </div> 
                </div>

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
                                <input class="form-control" id="BCodigo" name="BCodigo" type="text" placeholder="Buscar...">
                            </div>
							<div class="col-md-12">
                                <table class="table table-condensed table-hover">							
                                <thead>
                                <tr>
                                    <th>Codigo</th>
                                    <th>Detalle</th>
                                    <th>Tipo de Cuenta</th>
                                </tr>
                                </thead>
                                <tbody id="TableCod">
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
                                            <td>'.strtoupper($registro["detalle"]).'</td>
                                            <td>'.$tcuenta.'</td>
                                            </tr>
                                        ';
                                        }
                                        $mysqli->close();
                                    ?>

                                </tbody>
                                </table>

                                <script>
                                    $(document).ready(function(){
                                        $("#BCodigo").on("keyup", function() {
                                        var value = $(this).val().toLowerCase();
                                            $("#TableCod tr").filter(function() {
                                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                                        });
                                        });
                                    });
                                </script>


                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" id="cmodel">Cerrar</button>
                        </div>
                    </div>
                </div>
                </div>
                <!-- fin buscar codigo -->   


                <div class="col-md-12 text-right">
                    <?php 
                        if ($sw==1) {
                            echo '<button type="submit" class="btn btn-modificar" tabindex="15"><span class="glyphicon glyphicon-repeat"></span> Modificar</button>';
                        }else{
                            echo '<button type="submit" class="btn btn-grabar" tabindex="15" '.$BloqueBtn.'><span class="glyphicon glyphicon-ok"></span> Grabar</button>';
                        }
                    ?>
                    <button type="button" class="btn btn-cancelar" onclick="Volver()"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
				</div>

            </form>

            <div class="clearfix"> </div>
            <hr>

            <div class="well well-sm"><strong>Plantillas Disponibles</strong></div>
            <input type="hidden" name="CTEmpre">
            <p>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th width="10%"> </th>
                        <th width="10%"> </th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                    if($_SESSION["PLAN"]=="S"){
                        $SQL="SELECT * FROM CTPlantillas WHERE estado<>'X' AND rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY id ASC";
                    }else{
                        $SQL="SELECT * FROM CTPlantillas WHERE estado<>'X' ORDER BY id ASC";
                    }
                    
                    $resultados = $mysqli->query($SQL);
                    while ($registro = $resultados->fetch_assoc()) {
                        echo '
                            <tr>
                                <td>'.$registro["nombre"].'</td>
                            ';
                        echo '          <td><button type="button" class="btn btn-info btn-xs" onclick="Modifi('.$registro["id"].')">Modificar</button></td>';

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


    </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
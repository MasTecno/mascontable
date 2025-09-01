<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../conexion/secciones.php';
	
	$NomCont=$_SESSION['NOMBRE'];
	$Periodo=$_SESSION['PERIODO'];
	$RazonSocial=$_SESSION['RAZONSOCIAL'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];

	if($Periodo==""){
		header("location:../frmMain.php");
		exit;
	}
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

    $SQL="SELECT * FROM CTAnticiposConf WHERE RutEmpresa='$RutEmpresa'";
    $resultado = $mysqli->query($SQL);
    while ($registro = $resultado->fetch_assoc()) {
        $Anticipos_Clientes=$registro['Anticipos_Clientes'];
        $Anticipos_Proveedores=$registro['Anticipos_Proveedores'];
        $Anticipos_Contra=$registro['Anticipos_Contra'];
    }

    $mysqli->close();
?>
<!DOCTYPE html>
<html>
	<head>
        <title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<script type='text/javascript' src="../js/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/select2.css">

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            .ui-widget.ui-widget-content {
				z-index: 9999 !important;
			}		
        </style>
        <script type='text/javascript'>
			$(window).load(function(){
				$('#SelAnticipoCli').select2();
				$('#SelAnticipoProv').select2();
				$('#SelAnticipoContra').select2();
                $('#SelRutAnticipo').select2();
			});
            $( function() {
                $("#FechaAnticipo").datepicker();
            });

            function Procesar(r1){
                form1.TXAction.value=r1;
                form1.action="xAnticipo.php";
                form1.submit();
            }

            function CerrarModal(){
                $('#ModAnticipo').modal('hide');
            }
            <?php
                if(isset($_GET['msj']) && $_GET['msj']=="ConfigOK"){
                    echo "alert('Configuración de Anticipos actualizada correctamente');";
                }
            ?>
            function AsiAnticipo(r1){
                if(r1=="C"){
                    $('#TituloAnticipo').text('Anticipo Clientes');
                }else{
                    $('#TituloAnticipo').text('Anticipo Proveedores');
                }
                ListaAnticiposClientes(r1)
                form1.TXAnticipo.value=r1;
                // form1.action="xAnticipo.php";
                // form1.submit();
            }
            function ListaAnticipos(){
                $.ajax({
                    url: 'grillaAnticipos.php',
                    type: 'POST',
                    data: {TXAction: 'ListaAnticipos'},
                    success: function(response){
                        $('#TblAnticipos').html(response);  
                    }
                });
            }
            function EliminarAnticipo(r1){
                if(confirm("¿Está seguro de querer eliminar este anticipo?")){
                    form1.TXAction.value="Eliminar";
                    form1.TXId.value=r1;
                    form1.action="xAnticipo.php";
                    form1.submit();
                }
            }
            function ListaAnticiposClientes(r1){
                $.ajax({
                    url: 'rutanticipos.php',
                    type: 'POST',
                    data: {TXAnticipo: r1},
                    success: function(response){
                        $('#SelRutAnticipo').html(response);
                    }
                });
            }

        </script>
	</head>
	<body onload="ListaAnticipos()">
		<?php 
			include '../nav.php';
		?>

		<div class="container-fluid">
		<div class="row content">
            <form action="#" method="POST" name="form1" id="form1"> 
                <input type="hidden" name="TXAction" id="TXAction" value="">
                <input type="hidden" name="TXAnticipo" id="TXAnticipo" value="">
                <input type="hidden" name="TXId" id="TXId" value="">
                <br>
                <div class="col-sm-12 text-left">
                    <div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
                    <div class="panel-heading">Procesar Documentos</div>
                    <div class="panel-body">
                        <div class="col-md-12 text-right">

                            <button type="button" class="btn btn-success btn-sm" onclick="AsiAnticipo('C')" data-toggle="modal" data-target="#AsientoAnticipo">
                                <i class="fa fa-cog"></i> Anticipo Clientes +
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="AsiAnticipo('P')" data-toggle="modal" data-target="#AsientoAnticipo">
                                <i class="fa fa-cog"></i> Anticipo Proveedores -
                            </button>

                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ModAnticipo">
                                <i class="fa fa-cog"></i> Configuración
                            </button>
                        </div>

                        <!-- Modal Configuración -->
                        <div class="modal fade" id="ModAnticipo" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Configuración de Anticipos</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon">Anticipos Clientes</span>
                                                <select id="SelAnticipoCli" name="SelAnticipoCli" class="form-control"> 
                                                <option value="0">Seleccione...</option>
                                                <?php
                                                    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

                                                    if ($_SESSION["PLAN"]=="S"){
                                                        $SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
                                                    }else{
                                                        $SQL="SELECT * FROM CTCuentas WHERE 1=1 ORDER BY detalle";
                                                    }
                                                    $resultado = $mysqli->query("$SQL");
                                                    while ($registro = $resultado->fetch_assoc()) {
                                                        if ($Anticipos_Clientes==$registro["numero"]) { 
                                                            echo "<option value ='".$registro["numero"]."' selected>".$registro["numero"]." ".$registro["detalle"]."</option>";
                                                        }else{
                                                            echo "<option value ='".$registro["numero"]."'>".$registro["numero"]." ".$registro["detalle"]."</option>";
                                                        }
                                                    }
                                                    $mysqli->close();
                                                ?>
                                                </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon">Anticipos Proveedores</span>
                                            <select id="SelAnticipoProv" name="SelAnticipoProv" class="form-control"> 
                                                <option value="0">Seleccione...</option>
                                                <?php
                                                    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

                                                    if ($_SESSION["PLAN"]=="S"){
                                                        $SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
                                                    }else{
                                                        $SQL="SELECT * FROM CTCuentas WHERE 1=1 ORDER BY detalle";
                                                    }
                                                    $resultado = $mysqli->query("$SQL");
                                                    while ($registro = $resultado->fetch_assoc()) {
                                                        if ($Anticipos_Proveedores==$registro["numero"]) {
                                                            echo "<option value ='".$registro["numero"]."' selected>".$registro["numero"]." ".$registro["detalle"]."</option>";
                                                        }else{
                                                            echo "<option value ='".$registro["numero"]."'>".$registro["numero"]." ".$registro["detalle"]."</option>";
                                                        }
                                                    }
                                                    $mysqli->close();
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon">Contra Cuenta Anticipos</span>
                                            <select id="SelAnticipoContra" name="SelAnticipoContra" class="form-control"> 
                                                <option value="0">Seleccione...</option>
                                                <?php
                                                    $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);

                                                    if ($_SESSION["PLAN"]=="S"){
                                                        $SQL="SELECT * FROM CTCuentasEmpresa WHERE rut_empresa='".$_SESSION['RUTEMPRESA']."' ORDER BY detalle";
                                                    }else{
                                                        $SQL="SELECT * FROM CTCuentas WHERE 1=1 ORDER BY detalle";
                                                    }
                                                    $resultado = $mysqli->query("$SQL");
                                                    while ($registro = $resultado->fetch_assoc()) {
                                                        if ($Anticipos_Contra==$registro["numero"]) {
                                                            echo "<option value ='".$registro["numero"]."' selected>".$registro["numero"]." ".$registro["detalle"]."</option>";
                                                        }else{
                                                            echo "<option value ='".$registro["numero"]."'>".$registro["numero"]." ".$registro["detalle"]."</option>";
                                                        }
                                                    }
                                                    $mysqli->close();
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-grabar" data-dismiss="modal" onclick="Procesar('Modal')">Grabar</button>
                                    <button type="button" class="btn btn-cancelar" data-dismiss="modal" onclick="CerrarModal()">Cerrar</button>
                                </div>
                            </div>
                        </div>
                        </div>

                        <!-- Modal Anticipo Clientes -->
                        <div class="modal fade" id="AsientoAnticipo" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title" id="TituloAnticipo"></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Fecha</span>
                                            <input type="text" name="FechaAnticipo" id="FechaAnticipo" maxlength="10" class="form-control text-right" placeholder="Fecha de Anticipo" value="<?php echo date('d-m-Y'); ?>">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>

                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rut Anticipo</span>
                                            <select id="SelRutAnticipo" name="SelRutAnticipo" class="form-control"> 
                                                <option value="0">Seleccione...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>

                                    <div class="col-md-6">
                                        <div class="input-group">   
                                            <span class="input-group-addon">Glosa</span>
                                            <input type="text" name="GlosaAnticipo" id="GlosaAnticipo" class="form-control"  placeholder="Glosa de Anticipo" onblur="this.value=this.value.toUpperCase()">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Monto</span>
                                            <input type="text" name="MontoAnticipo" id="MontoAnticipo" class="form-control text-right" placeholder="Monto de Anticipo">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-grabar" data-dismiss="modal" onclick="Procesar('Anticipo')">Grabar</button>
                                    <button type="button" class="btn btn-cancelar" data-dismiss="modal" onclick="CerrarModal()">Cerrar</button>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="clearfix"></div>
                        <br>

                        <div class="col-md-12">
                            <table class="table table-bordered table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">N°</th>
                                        <th style="text-align: center;">Fecha</th>
                                        <th style="text-align: right;">Rut</th>
                                        <th>Razón Social</th>
                                        <th>Glosa</th>
                                        <th style="text-align: right;">Monto</th>
                                        <th style="text-align: center;">Tipo</th>
                                        <th style="text-align: right;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="TblAnticipos">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-sm-12 text-left">

            </form>
		</div>
        <div class="clearfix"></div>
        <br>
		<?php include '../footer.php'; ?>
		</div>

	</body>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#MontoAnticipo").on('input', function() {
                // Remove any non-digit characters
                let value = $(this).val().replace(/[^\d]/g, '');
                
                // Add thousand separators
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                
                $(this).val(value);
            });
        });

        $( "#FechaAnticipo" ).datepicker({
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
            // Mostrar el mes y año en el panel de navegación
            changeMonth: true,
            changeYear: true,
            // Mostrar botón para borrar fecha
            showButtonPanel: true,
            // Texto del botón borrar
            closeText: "Borrar",
            // Permitir borrar la fecha haciendo click en el botón
            onClose: function(dateText, inst) {
                if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                    document.getElementById(this.id).value = '';
                }
            },
            // Año mínimo y máximo permitido
            yearRange: "-100:+0",
            // Validar fecha mínima permitida
            minDate: new Date(1900, 0, 1),
            // Validar fecha máxima permitida (hoy)
            maxDate: 0

        });
    </script>

</html>
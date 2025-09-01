<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    // Verificamos que el usuario haya iniciado sesión
    if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE'] == ""){
        header("location:../index.php?Msj=95");
        exit;
    }

    // Control de mensajes
    $SwMes = "";
    if(isset($_GET['OK'])){
        $SwMes = "S";
    }

    // Variable hipotética para mostrar mensajes
    // Si $Msj no está definida en tu lógica, puedes quitar esta línea
    $Msj = isset($_GET['Msj']) ? $_GET['Msj'] : "";  

    if(isset($_GET['NExite']) && $_GET['NExite']!=""){
        $Msj ="La Cuenta ".$_GET['NExite'].", no existe";
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>MasContable - Importador de Voucher Masivos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- jQuery y Bootstrap JS -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" type="text/css" href="../css/StConta.css">

    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Archivo JavaScript propio -->
    <script src="../js/propio.js"></script>

    <style>
        body {
            font-family: 'Saira', sans-serif;
            background-color: #697a8d21;
        }
        .panel-heading {
            font-weight: bold;
        }
        .file-input-label {
            margin-top: 10px;
            font-weight: 600;
        }
        /* Ajusta según tu preferencia */
        .btn-block {
            margin-top: 15px;
        }
        .modal-body strong {
            text-decoration: underline;
        }
    </style>

    <script type="text/javascript">
        function ActivaBtn(){
            var btn = document.getElementById("BtnVisual");
            btn.style.visibility = (btn.style.visibility === "hidden") ? "visible" : "hidden";
        }

        function ClonaPlan(){
            if (confirm("Está a punto de realizar una carga masiva de vouchers. Por favor, asegúrese de haber seguido las instrucciones.\n\n¿Desea continuar?")) {
                document.form1.swImport.value = "S";
                document.form1.submit();
            }
        }
    </script>
</head>
<body>
    <!-- Barra de navegación -->
    <?php include '../nav.php'; ?>

    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <div class="row">
            <div class="col-xs-12">
                <!-- Título principal de la página -->
                <h2 class="text-center" style="font-weight: 600; margin-bottom: 30px;">
                    <i class="fa fa-upload"></i> Importador de Voucher Masivos
                </h2>
            </div>
        </div>

        <form name="form1" method="post" action="procesar.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-2"></div> <!-- Espacio a la izquierda -->

                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-file-excel-o"></i> Carga de Archivo
                        </div>
                        <div class="panel-body">

                            <!-- Descarga de plantilla -->
                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <a href="PlantillaVoucher.xlsx" class="btn btn-info btn-block" role="button">
                                        <i class="fa fa-download"></i> Descargar Plantilla
                                    </a>
                                </div>
                            </div>

                            <hr>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12 text-center">
                                    <!-- Campo para subir el archivo -->
                                    <div class="form-group">
                                        <label class="file-input-label" for="excel">Seleccione el archivo Excel:</label>
                                        <input 
                                            type="file" 
                                            id="excel" 
                                            name="excel" 
                                            class="form-control" 
                                            style="display:inline-block; margin: 0 auto;" 
                                            required
                                        >
                                    </div>

                                    <!-- Campos ocultos -->
                                    <input type="hidden" name="action" value="upload">
                                    <input type="hidden" name="swImport" id="swImport">

                                    <!-- Mensaje de error o éxito (opcional) -->
                                    <?php if(!empty($Msj)): ?>
                                        <div class="alert alert-info" role="alert" style="margin-top: 20px;">
                                            <?php echo $Msj; ?>
                                        </div>
                                    <?php endif; ?>

                                    <p style="margin-top: 20px;">
                                        <strong>¿Está seguro de querer importar el archivo?</strong>
                                    </p>

                                    <!-- Checkbox para habilitar el botón Importar -->
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="SwPago" name="SwPago" onclick="ActivaBtn()"> Aceptar
                                        </label>
                                    </div>

                                    <!-- Botón "Importar" que aparece/oculta con el checkbox -->
                                    <input 
                                        class="btn btn-success btn-block" 
                                        type="button" 
                                        name="BtnVisual" 
                                        id="BtnVisual" 
                                        style="visibility:hidden; margin-top: 20px;" 
                                        onclick="ClonaPlan()" 
                                        value="Importar"
                                    />
                                </div>
                            </div>

                            <hr>

                            <!-- Botón para ver instrucciones (Modal) -->
                            <div class="row">
                                <div class="col-md-12">
                                    <button 
                                        type="button" 
                                        class="btn btn-warning btn-block" 
                                        data-toggle="modal" 
                                        data-target="#myModal"
                                    >
                                        <i class="fa fa-info-circle"></i> Ver Instrucciones
                                    </button>
                                </div>
                            </div>

                            <!-- Modal con instrucciones -->
                            <div class="modal fade" id="myModal" role="dialog" style="text-align: left;">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #f5f5f5;">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Instrucciones para la Importación de Vouchers</h4>
                                        </div>
                                        <div class="modal-body" style="font-size: 14px;">
                                            <strong>1.</strong> El archivo Excel debe tener las fechas en orden cronológico ascendente.<br><br>
                                            <strong>2.</strong> Todas las líneas de un mismo voucher deben compartir la misma glosa, para que el sistema identifique el cierre del voucher.<br><br>
                                            <strong>3.</strong> Para indicar un nuevo voucher, utiliza una glosa diferente a la anterior.<br><br>
                                            <strong>Nota Importante:</strong> Asegúrese de revisar cada paso para evitar errores en la configuración y procesamiento.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin del modal -->

                        </div><!-- panel-body -->
                    </div><!-- panel-default -->
                </div><!-- col-sm-8 -->

                <div class="col-sm-2"></div> <!-- Espacio a la derecha -->
            </div>
        </form>
    </div>

    <!-- Alertas automáticas según estado (opcional) -->
    <script type="text/javascript">
        <?php
            if ($SwMes == "N") {
                echo 'alert("Ha ocurrido un error, favor contactar con soporte.");';
            }
            if ($SwMes == "S") {
                echo 'alert("Se ha completado la operación con éxito.");';
            }
        ?>
    </script>

    <?php include '../footer.php'; ?>
</body>
</html>

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

    $ColorAlerta="alert-danger";

    $Msj = isset($_GET['Msj']) ? $_GET['Msj'] : "";  

    if(isset($_GET['NExite']) && $_GET['NExite']!=""){
        $Msj ="La Cuenta ".$_GET['NExite'].", no existe.";
    }

    if(isset($_GET['NExiteCC']) && $_GET['NExiteCC']!=""){
        $Msj ="El Centro de Costo ".$_GET['NExiteCC'].", no existe.";
    }

    if(isset($_GET['ErrorSum']) && $_GET['ErrorSum']!=""){
        $Msj ="Descuadre en el Asiento,".$_GET['ErrorSum'].".";
    }
    if(isset($_GET['FileError']) && $_GET['FileError']!=""){
        $Msj ="".$_GET['FileError'].".";
    }

    if(isset($_GET['OK']) && $_GET['OK']!=""){
        $Msj ="Cantidad de Asientos Pre-Cargados: ".$_GET['OK'].".";
        $ColorAlerta="alert-success";
    }

    
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>MasContable - Importador de Voucher Masivos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/tailwind.js"></script>
    
    <!-- jQuery -->
    <script src="../js/jquery.min.js"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">
    
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" type="text/css" href="../css/StConta.css">
    
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>
    
    <!-- Archivo JavaScript propio -->
    <script src="../js/propio.js"></script>

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
<body class="min-h-screen bg-gray-50">
    <!-- Barra de navegación -->
    <?php include '../nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-8">
            <!-- Título principal de la página -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    <i class="fa-solid fa-upload mr-3 text-blue-600"></i> Importador de Voucher Masivos
                </h1>
                <p class="text-gray-600">Carga masiva de vouchers desde archivos Excel</p>
            </div>

            <form name="form1" method="post" action="procesar.php" enctype="multipart/form-data">
                <!-- Card principal -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex justify-start items-center px-6 pt-4 pb-4 bg-gray-100 w-full shadow">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
                            <i class="fa-solid fa-file-excel text-lg text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Cargar Archivo</h3>
                            <p class="text-sm text-gray-600">Seleccione y procese su archivo Excel</p>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Descarga de plantilla -->
                        <div class="flex justify-start">
                            <a href="PlantillaVoucherV2.xlsx" class="inline-flex items-center px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fa-solid fa-download mr-2"></i> Descargar Plantilla
                            </a>
                        </div>

                        <hr class="border-gray-200">

                        <!-- Campo para subir el archivo -->
                        <div class="space-y-4">
                            <div>
                                <label for="excel" class="block text-sm font-medium text-gray-700 mb-2">
                                    Seleccione el archivo Excel:
                                </label>
                                <input type="file" id="excel" name="excel" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-l-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200" required>
                            </div>

                            <!-- Campos ocultos -->
                            <input type="hidden" name="action" value="upload">
                            <input type="hidden" name="swImport" id="swImport">

                            <!-- Mensaje de error o éxito -->
                            <?php if(!empty($Msj)): ?>
                                <div class="p-4 rounded-lg <?php echo $ColorAlerta === 'alert-success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 text-red-800'; ?>">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid <?php echo $ColorAlerta === 'alert-success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm"><?php echo $Msj; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Confirmación -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <!-- <div class="flex-shrink-0">
                                        <i class="fa-solid fa-exclamation-triangle text-yellow-400"></i>
                                    </div> -->
                                    <div class="ml-3">
                                        <!-- <h3 class="text-sm font-medium text-yellow-800">Confirmación requerida</h3> -->
                                        <div class="text-sm text-yellow-700">
                                            <p>¿Está seguro de querer importar el archivo?</p>
                                        </div>
                                        <div class="mt-3">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="SwPago" name="SwPago" onclick="ActivaBtn()" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-1 border-gray-400 rounded">
                                                <label for="SwPago" class="ml-2 block text-sm text-gray-900">Acepto proceder con la importación</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón Importar -->
                            <div class="flex justify-center">
                                <button type="button" name="BtnVisual" id="BtnVisual" style="visibility:hidden;" onclick="ClonaPlan()" class="w-full inline-flex justify-center items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <i class="fa-solid fa-upload mr-2"></i> Importar Archivo
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <!-- Botón para ver instrucciones -->
                        <div class="flex justify-center">
                            <button type="button" data-modal-target="instructions-modal" data-modal-toggle="instructions-modal" class="w-full inline-flex justify-center items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                <i class="fa-solid fa-info-circle mr-2"></i> Ver Instrucciones
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de instrucciones con Flowbite -->
    <div id="instructions-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fa-solid fa-info-circle mr-2 text-blue-600"></i>
                        Instrucciones para la Importación de Vouchers
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="instructions-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <div class="space-y-4 text-sm text-gray-700">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">1</span>
                            <p>El archivo Excel debe tener las fechas en orden cronológico ascendente.</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">2</span>
                            <p>Seguir el ejemplo de la Plantilla.</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">3</span>
                            <p>Si el asiento es de tipo apertura solo marcar la línea de la glosa, en la columna apertura con S.</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">4</span>
                            <p>El nombre de la cuenta no es relevante, solo que el número de cta. exista. El sistema valida que exista.</p>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">5</span>
                            <p>El Centro de Costo el valor a indicar en el código con el que fue creado en el mantenedor.</p>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-exclamation-triangle text-sm text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-yellow-800">Nota Importante</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Asegúrese de revisar cada paso para evitar errores en la configuración y procesamiento.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button data-modal-hide="instructions-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
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
    
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>

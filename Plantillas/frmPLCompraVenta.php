<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

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
    <script src="../js/jquery.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="../css/StConta.css">
    <script src="../js/propio.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>


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
        const closeButton = document.querySelector('[data-modal-hide="default-modal"]');
        if (closeButton) {
            closeButton.click();
        }
    }

    
    function Volver(){
        form1.action="../frmMain.php";
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
    include '../nav.php';
    ?>

    <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="space-y-8">
        <form action="xfrmPLCompraVenta.php" method="POST" name="form1" id="form1">
            <input type="hidden" name="idempb" id="idempb">
            <input type="hidden" name="idempa" id="idempa">
            <input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">

            <div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2 mb-5">
                <?php 
                    if ($sw==1) {
                        echo '<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"><i class="fa fa-save mr-2"></i> Modificar</button>';
                    }else{
                        echo '<button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"><i class="fa fa-save mr-2"></i> Grabar</button>';
                    }
                ?>
                <button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
                    <i class="fa fa-times mr-2"></i> Cancelar
                </button> 
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200">            
                <div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
                        <i class="fa-solid fa-file-import text-lg text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Registro de Importaci&oacute;n de Libro
                        </h3>
                    </div>
                </div> 
                    
                <div class="p-6 pt-1 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xnombre; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>
                        </div> 

                        <div>
                            <label for="stipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Libro</label>  
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="stipo" name="stipo" required>
                                <option value="">Selecciona</option>
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
                    </div>

                    <div class="mt-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-4">Posiciones de Campos</h4>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div>
                                <label for="tdoc" class="block text-sm font-medium text-gray-700 mb-1">Tipo Documento</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tdoc" name="tdoc" value="<?php echo $xtdocumento; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="trut" class="block text-sm font-medium text-gray-700 mb-1">Rut</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="trut" name="trut" value="<?php echo $xrut; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="trsocial" class="block text-sm font-medium text-gray-700 mb-1">RSocial</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="trsocial" name="trsocial" value="<?php echo $xrsocial; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="tnum" class="block text-sm font-medium text-gray-700 mb-1">Numero</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tnum" name="tnum" value="<?php echo $xnumero; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="tfec" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tfec" name="tfec" value="<?php echo $xfecha; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4">
                            <div>
                                <label for="texe" class="block text-sm font-medium text-gray-700 mb-1">Exento</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="texe" name="texe" value="<?php echo $xexento; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="tnet" class="block text-sm font-medium text-gray-700 mb-1">Neto</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tnet" name="tnet" value="<?php echo $xneto; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="tiva" class="block text-sm font-medium text-gray-700 mb-1">IVA</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tiva" name="tiva" value="<?php echo $xiva; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="tret" class="block text-sm font-medium text-gray-700 mb-1">Otros Impuestos</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tret" name="tret" value="<?php echo $xretencion; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                            <div>
                                <label for="ttot" class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="ttot" name="ttot" value="<?php echo $xtotal; ?>" onChange="javascript:this.value=this.value.toUpperCase();" required>
                            </div> 
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="cuenta" class="block text-sm font-medium text-gray-700 mb-2">Cuenta</label>
                        <div class="flex items-center gap-2"> 
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="cuenta" name="cuenta" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xcuenta;?>"> 
                            <button type="button" class="bg-slate-100 text-sm hover:bg-gray-300 text-gray-600 font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" data-modal-target="default-modal" data-modal-toggle="default-modal">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div> 
                    </div>

                </div>
            </div>

            <!-- Modal  buscar codigo-->
            <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-7xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow-sm">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900">
                                Listado de Cuentas
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5 space-y-4">
                            
                            <div class="block">
                                <input class="w-full mb-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="BCodigo" name="BCodigo" type="text" placeholder="Buscar...">
                            </div>
                            <div class="col-md-12">

                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Codigo</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Detalle</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Tipo de Cuenta</th>
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

                                                echo '
                                                    <tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out" onclick="data(\''.$registro["numero"].'\')">
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["numero"].'</td>
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.strtoupper($registro["detalle"]).'</td>
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">'.$tcuenta.'</td>
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
                        <!-- Modal footer -->
                        <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100" id="cmodel">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin buscar codigo -->   


        </form>

            <div class="clearfix"> </div>
            <hr>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex justify-center items-center mr-4">
                    <i class="fa-solid fa-list text-lg text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Plantillas Disponibles
                    </h3>
                </div>
            </div>

            <div class="p-6">

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
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
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["nombre"].'</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex space-x-2">
                            ';
                            
                            echo '<button type="button" class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium py-1 px-2 rounded transition duration-200" onclick="Modifi('.$registro["id"].')">
                                <i class="fa fa-edit mr-1"></i> Modificar
                            </button>';

                            if($registro["estado"]=="B"){
                                echo '<button type="button" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs font-medium py-1 px-2 rounded transition duration-200" onclick="Alta('.$registro["id"].')">
                                    <i class="fa fa-arrow-up mr-1"></i> Alta
                                </button>';
                            }else{
                                echo '<button type="button" class="bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium py-1 px-2 rounded transition duration-200" onclick="Baja('.$registro["id"].')">
                                    <i class="fa fa-arrow-down mr-1"></i> Baja
                                </button>';
                            }

                            echo '
                                        </div>
                                    </td>
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
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>
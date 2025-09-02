<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';
    include 'clases/clasesCss.php';

    if (isset($_POST['idempb']) && $_POST['idempb']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTTipoDocumento SET estado='B' WHERE id='".$_POST['idempb']."'");
        $mysqli->close();
    }

    if (isset($_POST['idempa']) && $_POST['idempa']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTTipoDocumento SET estado='A' WHERE id='".$_POST['idempa']."'");
        $mysqli->close();
    }

	if(isset($_POST['idmod']) && $_POST['idmod']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTTipoDocumento WHERE id='".$_POST['idmod']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$tiposii=$registro["tiposii"];
			$xdetalle=strtoupper($registro["nombre"]);
			$operador=$registro["operador"];
		} 
		$mysqli->close();
	}


?>
<!DOCTYPE html>
<html>
    <head>
    <title>MasContable</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
    <script src="js/jquery.min.js"></script>

    <!-- tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="css/StConta.css">
    <script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

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
        function Volver(){
            form1.action="frmMain.php";
            form1.submit();
        }

        function Modifi(valor){
            form1.idmod.value=valor;
            form1.action="#";
            form1.submit();
        }

        function limpiarFormulario() {
            document.getElementById("nombre").value = "";
            document.getElementById("csii").value = "";
            document.getElementById("operador").value = "";
            document.getElementById("idmod").value = "";
            window.location.href = "frmTipoDocumento.php";
        }

    </script>  
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            color: #fff;
            z-index: 9999;
        }
        .overlay-content {
            text-align: center;
        }
        .overlay img {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }
    </style>

    </head>
    <body onload="<?php if(isset($_GET['Exito']) && $_POST['idmod']=="") { echo "showMessage()";}?>">

        <?php 
            include 'nav.php';
        ?>

        <div class="min-h-screen bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="space-y-8">

            <div id="overlay" class="overlay">
                <div class="overlay-content">
                    <img src="https://img.icons8.com/ios-filled/100/ffffff/checkmark.png" alt="Check">
                    <h1>Enviado Satisfactoriamente</h1>
                </div>
            </div>
                <form action="xfrmTipoDocumento.php" method="POST" name="form1" id="form1" class="space-y-8">

                    <div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2">
                        <button type="button" 
                                class="bg-slate-100 text-sm hover:bg-gray-300 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
                                onclick="limpiarFormulario()">
                            <i class="fa fa-plus mr-2"></i>Nuevo
                        </button>
                        <?php 
                            if ($sw==1) {
                        ?>
                            <button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                <i class="fa fa-edit mr-2"></i>Modificar
                            </button>
                        <?php 
                        }else{
                        ?>
                            <button type="submit" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                <i class="fa fa-save mr-2"></i>Grabar
                            </button>
                        <?php 
                        }
                        ?>

                        <button data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" type="button">
                            <i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
                        </button>

                        <button type="button" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" onclick="Volver()">
                            <i class="fa fa-times mr-2"></i>Cancelar
                        </button>
                    </div>

                    <!-- Document Type Information Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
                                <i class="fas fa-file-alt text-lg text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Registros de Documentos
                                </h3>
                                <p class="text-sm text-gray-600">Datos para ingresar un tipo de documento</p>     
                            </div>
                        </div>
                        <div class="p-6 pt-1 space-y-6">

                            <!-- Hidden inputs -->
                            <input type="hidden" name="idempb" id="idempb">
                            <input type="hidden" name="idempa" id="idempa">
                            <input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">

                            <!-- First Row: Name -->
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
                                        <i class="fa fa-file-text mr-1"></i>Nombre del Documento
                                    </label>
                                    <input type="text" 
                                           class="<?php input_css(); ?>" 
                                           id="nombre" 
                                           name="nombre" 
                                           onChange="javascript:this.value=this.value.toUpperCase();" 
                                           value="<?php echo $xdetalle; ?>" 
                                           required>
                                </div>
                            </div>

                            <!-- Second Row: SII Code and Operator -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="csii" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
                                        <i class="fa fa-key mr-1"></i>Código SII
                                    </label>
                                    <input type="text" 
                                           class="<?php input_css(); ?>" 
                                           id="csii" 
                                           name="csii" 
                                           value="<?php echo $tiposii; ?>" 
                                           required>
                                </div>

                                <div>
                                    <label for="operador" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
                                        <i class="fa fa-calculator mr-1"></i>Operador
                                    </label>
                                    <select class="<?php input_css(); ?>" 
                                            id="operador" 
                                            name="operador" 
                                            required>
                                        <option value="">Seleccione</option>
                                        <option value="S" <?php if ($operador=="S") { echo "selected"; } ?>>Suma</option>
                                        <option value="R" <?php if ($operador=="R") { echo "selected"; } ?>>Resta</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Available Documents Card -->
                    <!-- <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
                                <i class="fas fa-list text-lg text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Documentos Disponibles
                                </h3>
                                <p class="text-sm text-gray-600">Lista de tipos de documentos registrados</p>     
                            </div>
                        </div>
                        <div class="p-6">
                            <form name="form2" action="#" method="POST">
                                <input type="hidden" name="CTEmpre">

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código SII</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sigla</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $SQL="SELECT * FROM CTTipoDocumento WHERE estado<>'X'";
                                $resultados = $mysqli->query($SQL);
                                while ($registro = $resultados->fetch_assoc()) {

                                echo '
                                <tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["tiposii"].'</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">'.$registro["nombre"].'</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">'.$registro["sigla"].'</td>
                                ';
                                if ($registro["operador"]=="S") {
                                    echo '
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Suma</td>
                                    ';
                                }else{
                                    echo '
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Resta</td>
                                    ';
                                }

                                echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Modifi('.$registro["id"].')">
                                                <i class="fa fa-edit mr-1"></i>Modificar
                                            </button>';

                                if($registro["estado"]=="B"){
                                    echo '<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-success-700 bg-success-100 hover:bg-success-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 transition duration-200" onclick="Alta('.$registro["id"].')">
                                                <i class="fa fa-check mr-1"></i>Alta
                                            </button>';
                                }else{
                                    echo '<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="Baja('.$registro["id"].')">
                                                <i class="fa fa-ban mr-1"></i>Baja
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
                            </form>
                        </div>
                    </div> -->

                </div>
            </div>
        </div>

        <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-7xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
                            <i class="fas fa-list text-lg text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Documentos Disponibles
                            </h3>
                            <p class="text-sm text-gray-600">Lista de tipos de documentos registrados</p>     
                        </div>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        
                        <div>
                            <form name="form2" action="#" method="POST">
                                <input type="hidden" name="CTEmpre">

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código SII</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sigla</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $SQL="SELECT * FROM CTTipoDocumento WHERE estado<>'X'";
                                $resultados = $mysqli->query($SQL);
                                while ($registro = $resultados->fetch_assoc()) {

                                echo '
                                <tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.$registro["tiposii"].'</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">'.$registro["nombre"].'</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">'.$registro["sigla"].'</td>
                                ';
                                if ($registro["operador"]=="S") {
                                    echo '
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Suma</td>
                                    ';
                                }else{
                                    echo '
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Resta</td>
                                    ';
                                }

                                echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-warning-700 bg-warning-100 hover:bg-warning-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 transition duration-200" onclick="Modifi('.$registro["id"].')">
                                                <i class="fa fa-edit mr-1"></i>Modificar
                                            </button>';

                                if($registro["estado"]=="B"){
                                    echo '<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-success-700 bg-success-100 hover:bg-success-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 transition duration-200" onclick="Alta('.$registro["id"].')">
                                                <i class="fa fa-check mr-1"></i>Alta
                                            </button>';
                                }else{
                                    echo '<button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="Baja('.$registro["id"].')">
                                                <i class="fa fa-ban mr-1"></i>Baja
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
                            </form>
                        </div>
                    </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <script>
            function showMessage() {
                $("#overlay").fadeIn();
                setTimeout(function(){
                    $("#overlay").fadeOut();
                }, 3000); // Ocultar después de 3 segundos
            }
        </script>

        <?php include 'footer.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    </body>
</html>
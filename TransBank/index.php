<?php
// Configuración de la conexión a la base de datos
// $servername = "localhost";
// $username = "usuario";
// $password = "contraseña";
// $dbname = "nombre_base_datos";

// // Crear conexión
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Verificar conexión
// if ($conn->connect_error) {
//     die("Error de conexión: " . $conn->connect_error);
// }

// // Consulta SQL
// $sql = "SELECT Documento, Folio, Fecha, Vencimiento, Total FROM Facturas";
// $result = $conn->query($sql);



// include '../conexion/conexionmysqli.php';
// include '../js/funciones.php';
// include '../conexion/secciones.php';
// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

// echo $sql = "SELECT IdDocumento, Folio, Fecha, Fecha, Total FROM Facturas";
// $result = $mysqli->query($sql);

// print_r($result);

// include '../js/funciones.php';
// include '../conexion/conexionmysqli.php';

// $mysqli = ConCobranza();

// $SQL="SELECT IdDocumento, Folio, Fecha, Fecha, Total FROM Facturas";
// $result = $mysqli->query($SQL);
// // while ($registro = $result->fetch_assoc()) {
// //     echo $registro['Folio'];
// // }


    // if($KeyTransbank!=""){
        // echo $KeyTransbank;
        // exit;
        // echo $_GET['action'];

        ini_set("display_errors", 1);
        ini_set("display_startup_errors", 1);

        // error_reporting(E_ALL); //* Informe de todo tipo de errores

        // $formatoAbono = str_replace(".", "", $montoAbono);
        // var_dump($formatoAbono);
    // }
    session_start();

    // print_r($_SESSION['DOCUMENTOS']);

    // echo $_SESSION['NOMBRE']."qqqqqqq";

    // exit;

    if($_SESSION['NOMBRE']=="" || !isset($_SESSION['NOMBRE'])){
        header('Location: ../frmMain.php');
    }
    // $RutEmpresa=$_SESSION['RUTEMPRESA'];
	
	// if($RutEmpresa=="" ){
	// 	header('Location: frmMain.php');
	// }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturas - MasTecno</title>

    <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="../css/StConta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        :root {
            --primary-red: #E31937;
            --primary-gray: #58595B;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .logo {
            max-width: 200px;
            margin-right: 20px;
        }
        
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: var(--primary-gray);
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .checkbox-cell {
            width: 40px;
            text-align: center;
        }
        
        .total-section {
            background-color: #f8f8f8;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .total-label {
            color: var(--primary-gray);
            font-weight: bold;
        }
        
        .total-amount {
            color: var(--primary-red);
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
    <script>
        function calcularTotal() {
            let total = 0;
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const totalStr = row.querySelector('.monto').textContent;
                const monto = parseFloat(totalStr.replace(/[$.]/g, '').replace(',', '.'));
                total += monto;
            });
            
            document.getElementById('MProcesa').value=total;

            document.getElementById('total-seleccionado').textContent = 
                new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP'
                }).format(total);
        }

        function Proce(){
            si=0;
            for (i=0;i<document.form1.elements.length;i++){
                if(document.form1.elements[i].type == "checkbox"){
                    if (document.form1.elements[i].checked==1) {
                        si++;
                    }
                }
            }

            if (si>0) {
                form1.action="tramitar.php";
                form1.submit();
            }else{
                alert("Debe selecionar al menos 1 documentos para continuar.");
            }
        }

    </script>
</head>
<body>
    <div class="container">
        <form action="#" name="form1" id="form1" method="POST">
        <!-- <form name="brouterForm" id="brouterForm"  method="POST" action="<?=$url_tbk?>" style="display:block;"> -->
            <div class="header">
                <img src="../images/MasTecno.png" alt="MasTecno Logo" class="logo">
                <h1>Sistema de Facturas</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <!-- <input type="checkbox" onclick="document.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = this.checked); calcularTotal();"> -->
                        </th>
                        <th style="text-align: center;">Documento</th>
                        <th style="text-align: right;">Folio</th>
                        <th style="text-align: center;">Fecha</th>
                        <th style="text-align: center;">Vencimiento</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($_SESSION['DocInpagos']>0) {
                            foreach($_SESSION['DOCUMENTOS'] as $indice=>$LAsiento){
                                if ($LAsiento['Docu']!="xxxx"){
                                    echo
                                    '
                                        <tr>
                                            <td class="checkbox-cell"><input type="checkbox" name="check_list[]" value="'.$LAsiento["Docu"].','.$LAsiento['TDoc'].','.$LAsiento['Fecha'].','.$LAsiento['Monto'].'" onchange="calcularTotal()"></td>
                                            <td align="center">'.$LAsiento['TDoc'].'</td>
                                            <td align="right">'.$LAsiento['Docu'].'</td>
                                            <td align="center">'.date('d-m-Y',strtotime($LAsiento['Fecha'])).'</td>
                                            <td align="center">'.date('d-m-Y',strtotime($LAsiento['Fecha']."+ 10 days")).'</td>
                                            <td align="right" class="monto">'.number_format($LAsiento['Monto'], 0, ',', '.').'</td>
                                        </tr>
                                    ';
                                }
                            }
                        }else{
                            echo "<tr><td colspan='6'>No se encontraron facturas</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
            
            <div class="total-section">
                <span class="total-label">Total Seleccionado:</span>
                <span id="total-seleccionado" class="total-amount">$0</span>

                <input type="hidden" name="MProcesa" id="MProcesa">
                <input type="hidden" name="NServer" id="NServer" value="<?php echo $_SESSION['NomServer']?>">
                <button type="button" onclick="Proce()" class="btn btn-warning">Procesar</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// $conn->close();
?>
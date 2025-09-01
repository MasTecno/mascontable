<?php
    if($_POST['name']!=""){
        session_start();
        include '../js/funciones.php';
        include '../conexion/conexionmysqli.php';

        $mysqli = ConCobranza();

        $server = $_POST['name'];
        $correo = $_POST['email'];

        // exit;
        function redirectToIndexWithMessage($messageCode) {
            header('Location: portalPago.php?Msj='.$messageCode);
            die();
        }    

        $IdServer = "";
        $sql = "SELECT Id FROM Servidores WHERE Nombre = ? AND Estado = 'A' LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $server);
        $stmt->execute();
        $resultados = $stmt->get_result();
        if ($registro = $resultados->fetch_assoc()) {
            $IdServer = $registro["Id"];
        }   

        // Limpiar documentos en sesión
        $_SESSION['DOCUMENTOS'] = array();

        // Obtener RutFactura
        $RutFactura = "";
        $sql = "SELECT RutFactura FROM Maestra WHERE IdServer = ? LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $IdServer);
        $stmt->execute();
        $resultados = $stmt->get_result();
        if ($registro = $resultados->fetch_assoc()) {
            $RutFactura = $registro['RutFactura'];
        }
        $stmt->close();

        $Cadera = "";
        $sql = "SELECT RutFactura FROM FacturasRut WHERE IdServer = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $IdServer);
        $stmt->execute();
        $resultados = $stmt->get_result();
        while ($registro = $resultados->fetch_assoc()) {
            $Cadera .= " OR Rut = '".$registro['RutFactura']."'";
        }
        $stmt->close();

        $ToPe = date('Y-m-d');

        $sql = "SELECT * FROM Facturas WHERE Fecha BETWEEN '2023-01-01' AND ? AND (Rut = ? ".$Cadera.") ORDER BY Fecha, Folio ASC";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $ToPe, $RutFactura);
        $stmt->execute();
        $resultados = $stmt->get_result();

        while ($registro = $resultados->fetch_assoc()) {
            $SumTrans = 0;
            $SQL1 = "SELECT SUM(MontoTrans) AS SumTrans FROM FactTrans WHERE IdFactura = ?";
            $stmt1 = $mysqli->prepare($SQL1);
            $stmt1->bind_param("s", $registro["Id"]);
            $stmt1->execute();
            $resultados1 = $stmt1->get_result();

            if ($registro1 = $resultados1->fetch_assoc()) {
                $SumTrans = $registro1["SumTrans"];
            }
            $stmt1->close();

            if($SumTrans=="" || $SumTrans == NULL){
                $SumTrans=0;
            }

            $XTipo = "";
            if ($registro["IdDocumento"] == "34") {
                $XTipo = "FacExe";
                $CnRefe=$registro["IdDocumento"];
            } 
            if($registro["IdDocumento"] == "33") {
                $XTipo = "FacAfe";
                $CnRefe=$registro["IdDocumento"];
            } 
            if($registro["IdDocumento"] == "39") {
                $CnRefe=$registro["IdDocumento"];
                $XTipo = "BolEle";
            } 
            if ($registro["IdDocumento"] == "61") {
                $XTipo = "NotCre";
                $CnRefe=9999;
            }

            $NC = 0;

            if ($registro["CnNuRefe"] > 0 && ($registro["CnRefe"] == "34" || $registro["CnRefe"] == "33" || $registro["CnRefe"] == "39")) {
                $NC = 1;
            } else {
                // $SQL1 = "SELECT * FROM Facturas WHERE CnNuRefe = ? AND (CnRefe = '34' OR CnRefe = '33' OR CnRefe = '39')";
                $SQL1 = "SELECT * FROM Facturas WHERE CnNuRefe = ? AND CnRefe = ?";
                $stmt1 = $mysqli->prepare($SQL1);
                $stmt1->bind_param("ss", $registro["Folio"], $CnRefe);
                $stmt1->execute();
                $Res = $stmt1->get_result();
                $NC = $Res->num_rows;
                $stmt1->close();
            }

            // if($server=="server795" ){
            //     echo $NC;
            //     echo "<br>";
            //     echo "**".$SumTrans."**";
            //     echo "<br>";
            //     // echo $registro["Total"];
            //     // echo "<br>";
            //     echo $registro["CnRefe"];
            //     echo "<br>";
                
            //     echo $registro["CnNuRefe"];
            //     echo "<br>";
            //     echo $registro['Folio'];
            //     echo "<br>";
            //     echo "<br>";
            //     echo "<br>";

            // }

            // if($server=="server795"){
            //     echo "**".$SumTrans."**";
            //     echo "<br>";
            //     echo $registro["Total"];
            //     echo "<br>";
            //     echo $NC."+++++";
            //     echo "<br>";
            //     echo $registro["CnNuRefe"];
            //     echo "<br>-----------";
            // }



            if ($SumTrans < $registro["Total"] && $NC == 0) {
                $ConFac++;
                $ConLin = count($_SESSION['DOCUMENTOS']);
                $LCta = array(
                    'Docu' => $registro["Folio"],
                    'TDoc' => $XTipo,
                    'Rut' => $registro["Rut"],
                    'RSocial' => $registro["RSocial"],
                    'Fecha' => $registro["Fecha"],
                    'Monto' => $registro["Total"]
                );
                $_SESSION['DOCUMENTOS'][$ConLin] = $LCta;
            }
        }
        $stmt->close();

        // fuentesandpartners@gmail.com
        // if($server==""){

        // //     // echo $ToPe;
        // //     // echo "<br>";
        // //     // echo $RutFactura;
        // //     // echo "<br>";
        // //     // echo $sql;
        // //     // echo "<br>";
        // //     // echo $Cadera;
        // //     // echo "<br>";
        // //     // // echo $registro["Total"];

        // //     // print_r($_SESSION['DOCUMENTOS']);

        //     exit;
        // }


        $_SESSION['DocInpagos'] = $ConFac;

        if($correo=="admin@mastecno.cl"){
            redirectToIndexWithMessage(55);
        }

        $mysqli = conectarUnion();
        $mysqli->set_charset('utf8mb4');

        $stmt = $mysqli->prepare('SELECT * FROM UnionServer WHERE (Server=?) AND Estado="A"');
        $stmt->bind_param('s', $server);
        $stmt->execute();
        $resultados = $stmt->get_result();
        $NumServer = '';

        if ($resultados->num_rows == 0) {
            $mysqli->close();
            redirectToIndexWithMessage(95);
        }

        while ($registro = $resultados->fetch_assoc()) {
            $NumServer = $registro["Numero"];
            $_SESSION['NomServer'] = $registro["Server"];
            $_SESSION['BaseSV'] = $registro["Base"];
            $_SESSION['UsuariaSV'] = $registro["Usuario"];
            $_SESSION['PassSV'] = randomTextSV(35) . $registro["Clave"] . randomTextSV(8);
        }

        $mysqli = xconectar($_SESSION['UsuariaSV'], descriptSV($_SESSION['PassSV']), $_SESSION['BaseSV']);

        $stmt = $mysqli->prepare('SELECT * FROM CTContadores WHERE Correo=? AND Estado="A"');
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $resultados = $stmt->get_result();

        if ($resultados->num_rows == 0) {

            
            // exit;

            $mysqli = conectarRemu();
            $mysqli->set_charset('utf8mb4');
    
            $stmt = $mysqli->prepare('SELECT * FROM UnionServer WHERE (Server=?) AND Estado="A"');
            $stmt->bind_param('s', $server);
            $stmt->execute();
            $resultados = $stmt->get_result();
            $NumServer = '';
    
            if ($resultados->num_rows == 0) {
                $mysqli->close();
                redirectToIndexWithMessage(95);
            }
    
            while ($registro = $resultados->fetch_assoc()) {
                $NumServer = $registro["Numero"];
                $_SESSION['NomServer'] = $registro["Server"];
                $_SESSION['BaseSV'] = $registro["Base"];
                $_SESSION['UsuariaSV'] = $registro["Usuario"];
                $_SESSION['PassSV'] = randomTextSV(35) . $registro["Clave"] . randomTextSV(8);
            }
    
            $mysqli = xconectar($_SESSION['UsuariaSV'], descriptSV($_SESSION['PassSV']), $_SESSION['BaseSV']);
    
            $stmt = $mysqli->prepare('SELECT * FROM RMAcceso WHERE Correo=? AND Estado="A"');
            $stmt->bind_param('s', $correo);
            $stmt->execute();
            $resultados = $stmt->get_result();

            // echo $correo;
            // echo "<br>";
            // echo $_SESSION['NomServer'];
            // echo "<br>";
            // echo $_SESSION['BaseSV'];
            // echo "<br>";
            // echo $_SESSION['UsuariaSV'];
            // echo "<br>";
            // echo $_SESSION['PassSV'];
            // echo "<br>";

        }

        if ($resultados->num_rows == 0) {
            $mysqli->close();
            redirectToIndexWithMessage(55);
        }
        
        $_SESSION['NOMBRE']=$server;

        // print_r( $_SESSION['DOCUMENTOS']);

        // exit;
        header("location: index.php");
        exit();
    }
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
    <title>Formulario de Facturación</title>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center">
                            <br>
                            <img src="../images/Mastecno450x100.png" alt="Logo" class="img-fluid mb-3" style="max-width: 200px;">
                        </div>
                        <h4 class="text-center mb-4">Recuperar Facturas Pendientes</h4>
                        <form id="recoveryForm" method="POST">

                            <div class="mb-3">
                                <label for="name" class="form-label">Server</label>
                                <input type="text" class="form-control" id="name" name="name" value="server99" required placeholder="Ingrese el servidor">
                                <div class="invalid-feedback">
                                    Por favor, ingresa tu nombre.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="correo@correo.cl" required placeholder="nombre@ejemplo.com">
                                <div class="invalid-feedback">
                                    Por favor, ingresa un correo electrónico válido.
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" onclick="Ok()">
                                Revisar Facturación
                            </button>

                            <br>
                            <strong>Instrucciones: "TransBank"</strong>
                            <ul>
                                <li>Solo presiona revisar facturación. </li>
                                <li>En la siguiente pantalla selecionar uno o varios documentos para pagar.</li>
                                <li>Y presionar procesar pago.</li>
                                
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // document.getElementById('recoveryForm').addEventListener('submit', function(e) {

        //     recoveryForm.action="#";
        //     recoveryForm.submit();
        //     // e.preventDefault();
        //     // const name = document.getElementById('name');
        //     // const email = document.getElementById('email');
            
        //     // if (name.validity.valid && email.validity.valid) {
        //     //     alert(`Se ha enviado un enlace de recuperación a ${name.value} (${email.value})`);
        //     // } else {
        //     //     if (!name.validity.valid) name.classList.add('is-invalid');
        //     //     if (!email.validity.valid) email.classList.add('is-invalid');
        //     // }
        // });
        function Ok(){
            recoveryForm.action="#";
            recoveryForm.submit();
        }
        <?php 

            if($_GET['Msj']==95){
                echo 'alert("Servidore no existe");';
            }
            if($_GET['Msj']==55){
                echo 'alert("Debe ser un correo valido dentro del server");';
            }
        ?>
    </script>
</body>
</html>
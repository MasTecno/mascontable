<?php
    session_start();

    // print_r($_SESSION['Respuesta']);

    // echo $_SESSION['KeyTransbank'];


    // echo $_SESSION['SumaMonto'];


    $response=$_SESSION['Respuesta'];

    $vci = $response->vci;                     // "TSY"
    $amount = $response->amount;               // 178500
    $status = $response->status;               // "AUTHORIZED"
    $buyOrder = $response->buy_order;          // "60890101829379177465"
    $sessionId = $response->session_id;        // "1119595809"
    $cardNumber = $response->card_detail->card_number;  // "7763"
    $accountingDate = $response->accounting_date;       // "1227"
    $transactionDate = $response->transaction_date;     // "2024-12-27T20:36:11.613Z"
    $authorizationCode = $response->authorization_code; // "1415"
    $paymentTypeCode = $response->payment_type_code;    // "VD"
    $responseCode = $response->response_code;           // 0
    $installmentsNumber = $response->installments_number; // 0

    include '../js/funciones.php';
    include '../conexion/conexionmysqli.php';
    $mysqli = ConCobranza();

    $SumaMonto=0;
    $SQL="SELECT sum(MontoDoc) as xMontoDoc FROM TransBankMovi WHERE KeyTransbank='$buyOrder'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $SumaMonto=$registro['xMontoDoc'];
    }
    // echo $buyOrder;
    // echo "<br>";
    // echo $SumaMonto;
    // echo "<br>";
    // echo $amount;
    // exit;
    if($SumaMonto==$amount){

        $nOpera="TBK_".$buyOrder;
        $NomServer="";
        $SQL="SELECT * FROM TransBankMovi WHERE KeyTransbank='$buyOrder' AND Estado='P'";
        $resultados = $mysqli->query($SQL);
        while ($registro = $resultados->fetch_assoc()) {
            $NomServer=$registro["NombreServer"];

            $XTipo="";
            if ($registro["TipoDoc"]=="FacExe") {
                $XTipo="34";
            }
            if ($registro["TipoDoc"]=="FacAfe") {
                $XTipo="33";
            }
            if ($registro["TipoDoc"]=="NotCre") {
                $XTipo="61";
            }
            if ($registro["TipoDoc"]=="BolEle") {
                $XTipo="39";
            }

            $SQL1="SELECT * FROM Facturas WHERE Folio='".$registro["NumeroDoc"]."' AND IdDocumento='$XTipo' AND Total='".$registro["MontoDoc"]."'";
            $resultados1 = $mysqli->query($SQL1);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $IdFact=$registro1["Id"];
                $MonFac=$registro1["Total"];
                $NFactura=$registro1["Folio"];
                $RFactura=$registro1["Rut"];
            }

            $mysqli->query("INSERT INTO Transferencias VALUES('','".date('Y-m-d')."','$nOpera','$MonFac','TRANSBANK','$sessionId','$RFactura','A','".date('Y-m-d')."','".date("H:i:s")."');");

            $SQL1="SELECT max(Id) as FId FROM Transferencias WHERE Id>0";
            $resultados1 = $mysqli->query($SQL1);
            while ($registro1 = $resultados1->fetch_assoc()) {
                $IdTrans=$registro1["FId"];
            }

            $mysqli->query("INSERT INTO FactTrans VALUES('','$IdFact','$NFactura','$MonFac','$IdTrans','$nOpera','$MonFac','".date('Y-m-d')."');");
        }

        if($NomServer!=""){
            $SQL="SELECT * FROM Bloqueos WHERE Nombre='$NomServer' AND Estado='A'";
            $resultados = $mysqli->query($SQL);
            $SwBloqueo = $resultados->num_rows;
            if ($SwBloqueo>0) {
                $mysqli->query("UPDATE Bloqueos SET Estado='X' WHERE Nombre='$NomServer' AND Estado='A'");
            }
        }

        $mysqli->query("UPDATE TransBankMovi SET Estado='A' WHERE KeyTransbank='$buyOrder' AND Estado='P'");

        $SQL="SELECT * FROM TransBankRespuesta WHERE sessionId='$sessionId' AND vci='$vci' AND buyOrder='$buyOrder'";
        $resultados = $mysqli->query($SQL);
        $row_cnt = $resultados->num_rows;
        if ($row_cnt==0) {
            $mysqli->query("INSERT INTO TransBankRespuesta VALUES('','$NomServer','".date('Y-m-d')."','".date("H:i:s")."','$vci','$amount','$status','$buyOrder','$sessionId','$cardNumber','$accountingDate','$transactionDate','$authorizationCode','$paymentTypeCode','$responseCode','$installmentsNumber')");
        }

        $SQL1="SELECT * FROM Servidores WHERE Nombre='$NomServer'";
        $resultados1 = $mysqli->query($SQL1);
        while ($registro1 = $resultados1->fetch_assoc()) {
            $IdServer=$registro1["Id"];
        }

        $SQL1="SELECT * FROM Contacto WHERE IdServer='$IdServer'";
        $resultados1 = $mysqli->query($SQL1);
        while ($registro1 = $resultados1->fetch_assoc()) {
            $Correo=$registro1["Correo"];
            $Nombre=$registro1["Nombre"];
        }


        $mysqli->close();
        $Sw=1;



        // if($_POST['email']!=""){

            // $CorreoTec=$Correo;
            // $XMensaje='';
            $destino=$Correo;

            $TextCorreo='
                <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <title>Notificaci&oacute;n</title>
                    <link rel="important stylesheet" href="chrome://messagebody/skin/messageBody.css">
    
                    <style type="text/css">
                        body {
                            background-color: #F6F6F6;
                        }
                    </style>
                    <style type="text/css">
                        body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }
                    </style>
                </head>
    
                <body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
                    <div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
                        <table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
                            <tr>
                                <td align="center" valign="top" style="padding:20px 0 20px 0">
                                    <!-- [ header starts here] -->
                                    <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                                        <tr>
                                            <td valign="top">
                                                <a href="https://www.mastecno.cl/"><img src="https://mascontable.maserp.cl/images/MasTecnoCorreo.png" alt="Logo MasTecno" style="margin-bottom:10px;" border="0"/></a>
                                            </td>
                                        </tr>
                                        <!-- [ middle starts here] -->
                                        <tr style="text-align: justify;">
                                            <td valign="top">
                                                Estimado(a) '.$Nombre.', '.$NomServer.'<br><br>

                                                Esperamos revisar la siguiente información de su pago:
                                                <p>
                                                    <strong><i class="fas fa-hashtag"></i> ID de Transacción:</strong> 
                                                    '.$buyOrder.'
                                                </p>
                                                <p>
                                                    <strong><i class="fas fa-calendar-alt"></i> Fecha:</strong> 
                                                    '.$date = new DateTime($transactionDate); echo $date->format('d-m-Y').'
                                                </p>
                                                <p>
                                                    <strong><i class="fas fa-money-bill-wave"></i>Monto:</strong> $
                                                    '.$amount.'
                                                </p>
                                                <p>
                                                    <strong><i class="fas fa-check-circle"></i>Estado:</strong> Completado
                                                </p>
                                                <p style="font-size: 10px;">
                                                    <strong><i class="fas fa-check-circle"></i>Copia del Comprobante a:</strong> 
                                                    '.$Correo.'
                                                </p>

                                                ¡Nuevamente muchas gracias por su preferencia!<br>
                                                Estamos a su disposici&oacute;n para ayudarle en lo que necesite.<br><br>

                                                Saludos cordiales,<br>
                                                <strong>MasTecno Spa</strong><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center>
                                                <p style="font-size:12px; margin:0;">Sistema desarrollados por <strong> MasTecno</strong></p></center>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </body>
                </html>
            ';
    
            // $destino = "samuel@mastecno.cl"; // correo del destino 
            // $destino = $_POST['email'];
    
            require_once('../PHPMailer_5.2.1/class.phpmailer.php'); // Especificamos la utilización de la librería PHPMailer 5.2.1 contenida en el directorio actual
            $mail = new PHPMailer(true); //el parámetro 'true' significa que lanzará excepciones en los errores que se produzcan, las cuales deben ser capturadas
            
            $mail->IsSMTP(); //le decimos a la clase que utilice SMTP
    
            try {
                $mail->SMTPDebug  = 0; // activa la información SMTP de depuración (para pruebas)
                $mail->SMTPAuth   = true; //activa autenficicación SMTP
                $mail->SMTPSecure = "ssl";                 // especifica la seguridad SMTP
                $mail->Host       = "servidor.mastecno.cl";      // especificamos la dirección del servidor de correo 
                $mail->Port       = 465;                   // puerto del servidor de correo
                $mail->Username   = "notificaciones@mastecno.cl";  // usuario del correo origen
                $mail->Password   = "@Samuel2024";         //contraseña del correo origen
                $mail->AddAddress($destino, ''); // dirección de correo destino
                // $mail->addCC("samuel@mastecno.cl");
                // $mail->addCC($CorreoTec);
                // $mail->AddAttachment("Termino_y_Condiciones.pdf"); 
                // $mail->addBCC("5294235@bcc.hubspot.com");
                $mail->Subject = "Confirmación de Pago. (".$_POST['Server'].")"; // titulo del email
                $mail->MsgHTML($TextCorreo); // cuerpo del email

                $mail->IsHTML(true);

                $mail->Send();

            } catch (phpmailerException $e) {
                echo $e->errorMessage(); //Excepción de PHPMailer
                exit;
            } catch (Exception $e) {
                echo $e->getMessage(); //Cualquier otra excepción
                exit;
            } finally {
                session_destroy();
                header('Location: ../');
                die();
            }
    // }else{
    //     session_destroy();
    //     header('Location: ../');
    //     die();
    // }






    }else{
        $mysqli->query("UPDATE TransBankMovi SET Estado='R' WHERE KeyTransbank='$buyOrder' AND Estado='P'");
        echo "Error en monto";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Pago</title>
    <!-- Fuente de Google (Opcional) -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />

    <style>
        /* Reinicio de márgenes y espaciados */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Configuración base del body */
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to right, #eef2f3, #8e9eab); /* Fondo degradado */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Encabezado con logo */
        header {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
            padding-top: 20px;
        }

        .logo {
            max-width: 250px;
            height: auto;
        }

        /* Contenedor principal */
        .container {
            background-color: #fff;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        /* Título */
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }

        /* Sección de información de pago */
        .payment-info {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
        }

        .payment-info p {
            margin-bottom: 10px;
        }

        .payment-info strong {
            color: #333;
        }

        /* Sección de redes sociales */
        .social-section {
            margin-top: 20px;
            text-align: center;
        }

        .social-section h2 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #333;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
        }

        .social-icons a {
            text-decoration: none;
            color: #555;
            font-size: 1.4rem;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: #007bff;
        }

        /* Pie de página */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #555;
        }

        /* Responsividad básica */
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
            h1 {
                font-size: 1.3rem;
            }
            .logo {
                max-width: 180px;
            }
            .social-icons {
                gap: 10px;
            }
            .social-section h2 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Encabezado con Logo -->
    <header>
        <img src="https://maserp.cl/mascontable/images/MasTecnoCorreo.png" alt="Logo de MasTecno" class="logo">
    </header>

    <!-- Contenedor de la información de pago -->
    <div class="container">
        <h1>Confirmación de Pago</h1>

        <div class="payment-info">
            <p>
                <strong><i class="fas fa-hashtag"></i> ID de Transacción:</strong> 
                <?php echo $buyOrder; ?>
            </p>
            <p>
                <strong><i class="fas fa-calendar-alt"></i> Fecha:</strong> 
                <?php $date = new DateTime($transactionDate); echo $date->format('d-m-Y');?>
            </p>
            <p>
                <strong><i class="fas fa-money-bill-wave"></i>Monto:</strong> $
                <?php echo $amount; ?>
            </p>
            <p>
                <strong><i class="fas fa-check-circle"></i>Estado:</strong> Completado
            </p>

            <p>
                <strong><i class="fas fa-check-circle"></i>Copia del Comprobante a:</strong> 
                <?php echo $Nombre; ?>
            </p>
        </div>

        <!-- Sección de redes sociales (versión con <div>) -->
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
        <!-- Facebook -->
        <a href="https://www.facebook.com/groups/2079974722329358" target="_blank">
        <img 
        src="https://mascontable.maserp.cl/images/facebook.png" 
        width="32" 
        height="32" 
        alt="Facebook" 
        style="border:none;"
        />
        </a>

        <!-- Instagram -->
        <a href="https://www.instagram.com/mastecnospa/" target="_blank">
        <img 
        src="https://mascontable.maserp.cl/images/instagram.png" 
        width="32" 
        height="32" 
        alt="Instagram" 
        style="border:none;"
        />
        </a>

        <!-- YouTube -->
        <a href="https://www.youtube.com/@mastecno4017" target="_blank">
        <img 
        src="https://mascontable.maserp.cl/images/youtube.png" 
        height="32" 
        alt="YouTube" 
        style="border:none;"
        />
        </a>
        </div>


        <!-- Pie de página -->
        <div class="footer">
        <p>Muchas gracias por su atención.</p>
        <p>© 2025 MasTecno Spa - Todos los derechos reservados</p>
        </div>
    </div>

</body>
</html>
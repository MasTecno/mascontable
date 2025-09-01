<?php

include '../js/funciones.php';
include '../conexion/conexionmysqli.php';

session_start();

if($_SESSION['NomServer']=="" || $_SESSION['CONTRATO']!="N"){
    header("location:../");
    exit;
}

    $mysqli = ConCobranza();

    $Sql="UPDATE Maestra SET TCRut='".str_replace(".","",$_POST['rut'])."', 
    TCNombre='".$_POST['name']."', TCCorreo='".$_POST['email']."', 
    TCFecha='".date('Y-m-d')."', TCHora='".date("H:i:s")."', TCIdUsuario='".$_POST['XId']."', TCUsuario='".$_POST['Usuario']."', TCServer='".$_POST['Server']."', TCAcepta='S', TCWeb='".$_POST['Site']."'
    WHERE IdServer='".$_SESSION['IDSERVER']."' AND TCAcepta='N'";
    // exit;

    $mysqli->query($Sql);

    if($_POST['email']!=""){

            $CorreoTec=$_SESSION['ServCorreo'];
            $XMensaje='';
            $TContacto=$_POST['name'];

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
                                                Estimado(a) '.$_POST['name'].',<br><br>

                                                Esperamos que se encuentre muy bien. A trav&eacute;s de este correo queremos agradecerle sinceramente por completar la firma de nuestros T&eacute;rminos y Condiciones. Adjuntamos una copia del documento para su revisi&oacute;n y archivo personal.<br>
                                                En MASTECNO valoramos su confianza y estamos comprometidos a brindar un servicio de calidad, asegurando siempre la transparencia y el respaldo legal necesario para respaldar nuestras soluciones. Recuerde que ante cualquier duda o consulta, puede contactarnos mediante los canales de atenci&oacute;n que encontrar&aacute; en el documento adjunto o en nuestra p&aacute;gina web.<br><br>
                                                ¡Nuevamente muchas gracias por su preferencia!<br>
                                                Estamos a su disposici&oacute;n para ayudarle en lo que necesite.<br><br>

                                                Saludos cordiales,<br>
                                                '.$_SESSION['ServTecnico'].'<br>
                                                <strong>MasTecno Spa</strong><br>
                                                '.$_SESSION['ServTelefono'].'<br>
                                                '.$_SESSION['ServCorreo'].'<br>
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
            $destino = $_POST['email'];
    
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
                $mail->SetFrom($CorreoTec, $_SESSION['ServTecnico']." - MasTecno"); // especificamos el origen del correo
                // $mail->addCC("samuel@mastecno.cl");
                $mail->addCC($CorreoTec);
                $mail->AddAttachment("Termino_y_Condiciones.pdf"); 
                // $mail->addBCC("5294235@bcc.hubspot.com");
                $mail->Subject = "Gracias por firmar nuestros Terminos y Condiciones! (".$_POST['Server'].")"; // titulo del email
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
    }else{
        session_destroy();
        header('Location: ../');
        die();
    }




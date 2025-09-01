<?php
    // @session_start();
    // print_r($_SESSION['Respuesta']);
    // print_r($_SESSION['token']);
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
        .btn-warning {
            background-color:rgb(255, 66, 66);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
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
        <h1 style="color:rgb(255, 66, 66); border-bottom: 2px solid rgb(255, 66, 66);">Pago Rechazado</h1>

        <div class="payment-info text-center">
            <p>
                <table style="width: 90%;">
                    <tr>
                        <td>
                            <strong><i class="fas fa-calendar-alt"></i> Fecha:</strong> 
                        </td>
                        <td style="text-align: right;">
                            <?php echo date('d-m-Y');?>
                        </td>
                    </tr>
                </table>
                <table style="width: 90%;">    
                    <tr>
                        <td>
                            <strong><i class="fas fa-check-circle"></i>Estado:</strong> 
                        </td>
                        <td style="text-align: right;">
                            Rechazado o Cancelado
                        </td>
                    </tr>
                </table>
            </p>
        </div>

        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            <button type="button" onclick="window.location.href='https://mascontable.maserp.cl'" class="btn btn-warning">
                <i class="fas fa-arrow-circle-left me-2"></i>
                Volver MasContable
            </button>
            <button type="button" onclick="window.location.href='https://masremu.maserp.cl'" class="btn btn-warning">
                <i class="fas fa-arrow-circle-left me-2"></i>
                Volver MasRemuneraciones
            </button>
        </div>

        <!-- Sección de redes sociales (versión con <div>) -->
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            <!-- Facebook -->
            <a href="https://www.facebook.com/groups/2079974722329358" target="_blank">
                <img src="https://mascontable.maserp.cl/images/facebook.png" width="32" height="32" alt="Facebook" style="border:none;"/>
            </a>

            <!-- Instagram -->
            <a href="https://www.instagram.com/mastecnospa/" target="_blank">
                <img src="https://mascontable.maserp.cl/images/instagram.png" width="32" height="32" alt="Instagram" style="border:none;"/>
            </a>

            <!-- YouTube -->
            <a href="https://www.youtube.com/@mastecno4017" target="_blank">
                <img src="https://mascontable.maserp.cl/images/youtube.png" height="32" alt="YouTube" style="border:none;"/>
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
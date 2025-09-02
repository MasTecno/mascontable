<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasTecno - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Mochiy+Pop+One&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Spicy+Rice&display=swap" rel="stylesheet">
    <style>
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body {
            background: linear-gradient(135deg, #ff0000 0%, #333333 50%, #ff0000 100%);
            background-size: 200% 200%;
            animation: gradientAnimation 15s ease infinite;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 60px;
            position: relative;
            font-family: 'Kanit', sans-serif;
        }

        .btn-mastecno {
            background-color: #ff0000;
            border-color: #ff0000;
            color: white;
            font-family: 'Kanit', sans-serif;
            letter-spacing: 0.5px;
        }

        .btn-mastecno:hover {
            background-color: #cc0000;
            border-color: #cc0000;
            color: white;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
        }

        .form-control:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 0.25rem rgba(255, 0, 0, 0.25);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            max-width: 200px;
            height: auto;
        }

        .main-content {
            flex: 1 0 auto;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
            padding: 1rem 0;
            z-index: 1000;
            font-family: 'Kanit', sans-serif;
        }

        .footer a {
            color: #ff0000;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            color: #cc0000;
        }

        .form-label {
            font-family: 'Kanit', sans-serif;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            font-family: 'Kanit', sans-serif;
            font-weight: 400;
        }

        .floating-button {
            position: fixed;
            right: 20px;
            bottom: 80px;
            z-index: 1000;
        }
    </style>
</head>
<body onload="javascript:fLogin.server.focus();">
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center min-vh-100 align-items-center">
                <div class="col-12 col-md-6 col-lg-6">

                <!-- <div class="alert alert-warning" style="font-size: 14px; text-align: justify;">
                    <strong>Estimado</strong> Colega, Emprendedor, Amigo, Colaborador, Cliente. <strong>El día Jueves 27 desde las 7:00 horas hasta las 15:00 del mismo día</strong>, se realizará una mantención Programada de nuestros Sistemas, por lo que durante dicho periodo no estara disponible, (El horario de vueltas de los sistemas puede ser menor). Esto con el fin mejorar la performance de nuestros sistemas como para la implementación de nuevos Sistemas de MasTecno (DJ versión1v). Agradecemos tomar las previsiones del caso.
                </div> -->


                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <div class="logo-container">
                                <img src="images/Mastecno450x100.png" alt="MasTecno Logo">
                            </div>
                            <form method="POST" name="fLogin" action="xvalidar.php">
                                <?php 
                                    $TitSis="";
                                    if($_SERVER['HTTP_HOST']=="server99.maserp.cl"){
                                        $TitSis="MasContable - Desarrollo";
                                    }
                                    if($_SERVER['HTTP_HOST']=="mascontable.maserp.cl"){
                                        $TitSis="MasContable";
                                    }
                                    if($_SERVER['HTTP_HOST']=="server98.maserp.cl"){
                                        $TitSis="MasRemuneraciones - Desarrollo";
                                    }
                                    if($_SERVER['HTTP_HOST']=="masremu.maserp.cl"){
                                        $TitSis="MasRemuneraciones";

                                    }
                                ?>
                                <div class="mb-3 text-center">
                                    <h3><?php echo $TitSis; ?></h3>
                                </div>
                                <div class="mb-3">
                                    <label for="server" class="form-label">Servidor</label>
                                    <input type="text" class="form-control" id="server" name="server" placeholder="Ingrese el servidor">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com">
                                </div>
                                <div class="mb-4">
                                    <label for="pwd" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="pwd" name="pwd" placeholder="Ingrese su contraseña">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-mastecno btn-lg">Iniciar sesión</button>
                                </div>
                                <div class="d-grid text-center" style="font-size: 11px;">
                                    <?php 
                                        if(!isset($_GET['Msj'])) {
                                            $_GET['Msj'] = '';
                                        }
                                        if($_GET['Msj']==95){
                                            echo'<p>*Alguno de los datos ingresado es son válidos.</p>';
                                        }
                                        if($_GET['Msj']==1){
                                            echo'<p>*Sección finalidad por tiempo de no uso, ingrese nuevamente.</p>';
                                        }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="https://mascontable.maserp.cl/TransBank/portalPago.php" class="btn btn-mastecno floating-button">Acceso Portal de Pago</a>
    <footer class="footer">
        <div class="container">
            <p class="mb-0">© 2016 MasContable. Todos los derechos reservados | Diseñado por <a href="#">MasTecno</a></p>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php
            if($_GET['Msj']=="53"){
                echo '
                
                if (confirm("Cuenta con documentos pendientes de pago. \n\n¿Desea ingresar al portal de pago?") == true) {
                    fLogin.action="transbank/portalPago.php";
                    fLogin.submit();
                }
                
                ';
            }
        ?>        
    </script>
</body>
</html>
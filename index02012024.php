<?php
	if ($_SERVER['HTTP_HOST']=="mascontable.maserp.cl") {
		$Sistema="MasContable";
	}elseif ($_SERVER['HTTP_HOST']=="masremu.maserp.cl") {
		$Sistema="MasRemuneraciones";
	}else{
		$Sistema="Desarrollo";
	}
    $Text="";

    if(isset($_GET['Msj'])){
        if($_GET['Msj']=="53"){
            $Text="Servicio suspendido temporalmente por factura pendiente de pago. Para mayor información contactar a su Soporte.";

        }else{
            if($_GET['Msj']=="75"){
                $Text="Servidor indicado no disponible. Para mayor información contactar a su Soporte.";
            }else{
                $Text="Por seguridad, su sesión en el sistema ha expirado debido a inactividad prolongada.";
            }
        }
    }
    
    ?>
<!doctype html>
<html lang="es">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Acceso Sistemas <?php echo $Sistema; ?> :: MasTecno</title>
    <link href="css5/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        html,
            body {
                height: 100%;
            }

            body {
                display: flex;
                align-items: center;
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }

            .form-signin {
                max-width: 330px;
                padding: 15px;
            }

            .form-signin .form-floating:focus-within {
                z-index: 2;
            }

            .form-signin input[type="email"] {
                margin-bottom: -1px;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0;
            }

            .form-signin input[type="password"] {
                margin-bottom: 10px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }

    </style>


    </head>
        <body onload="javascript:fLogin.server.focus();" class="text-center">

        <main class="form-signin w-100 m-auto">
            <form method="POST" name="fLogin" name="fLogin" action="xvalidar.php">
                <img class="mb-4" src="images/MasTecno.png" alt="">
                <h1 class="h3 mb-3 fw-normal"><?php echo $Sistema; ?></h1>

                <div class="form-floating">
                    <input type="text" class="form-control" id="server" name="server" value="" placeholder="Server" required>
                    <label>Server</label>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" id="email" name="email" placeholder="Usuario" required>
                    <label>Usuario</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="pwd" name="pwd" placeholder="Contrase&ntilde;a" required>
                    <label>Contrase&ntilde;a</label>
                </div>

                <div class="checkbox mb-3">
                    <?php echo $Text; ?>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit">Ingresar</button>
                    <p class="mt-5 mb-3 text-muted"> &copy; 2016 <?php echo $Sistema; ?>. Todos los derechos reservados | Dise&ntilde;ado por <a href="https://www.mastecno.cl" target="_blank">MasTecno</a></p>
            </form>
        </main>

        <script>
            <?php
                // if($Text!=""){
                //     echo "MsjError();";
                // }
            ?>            

            <?php 
                if(isset($_GET['Msj'])){
                    if($_GET['Msj']=="53"){
                        echo '
                        
                        if (confirm("Cuenta con documentos pendientes de pago. \n\n¿Desea ingresar al portal de pago?") == true) {
                            fLogin.action="TransBank/portalPago.php";
                            fLogin.submit();
                        }
                        
                        ';
                    }
                }
            ?>
        </script>
        
    </body>
</html>
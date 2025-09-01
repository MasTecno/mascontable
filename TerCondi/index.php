<?php
session_start();

if($_SESSION['NomServer']=="" || $_SESSION['CONTRATO']!="N"){
    header("location:../");
    exit;
}


// echo ;
// echo "<br>";
// echo $_SESSION['XId'];
// echo "<br>";
// echo $_SESSION['CONTRATO'];
// echo "<br>";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Términos y Condiciones - MASTECNO</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- CSS de Bootstrap -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
        <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico">
        <style>
            /* Estilos personalizados */
            body {
                background: #f9f9f9;
                font-family: Arial, sans-serif;
            }
            .container {
                margin-top: 2rem;
                margin-bottom: 2rem;
            }
            .card {
                border: none;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0,0,0,.1);
                margin-bottom: 2rem;
            }
            .card-body {
                padding: 2rem;
            }
            .logo {
                max-width: 100%;
                height: auto;
            }
            .summary-list {
                list-style-type: decimal;
                padding-left: 1.5rem;
            }
            .summary-list li {
                margin-bottom: 0.5rem;
            }
            .btn-download {
                margin: 1rem 0;
                display: inline-block;
            }
            .alert-rut {
                display: none;
            }
        </style>
    </head>
    <body>

        <div class="container">
            <!-- Encabezado con Logo -->
            <div class="text-center mb-4">
                <img src="https://mascontable.maserp.cl/images/Mastecno450x100.png" alt="Logo MASTECNO" class="logo">
            </div>

            <div class="row">
                <!-- Columna 1: Resumen de Términos y Condiciones -->
                <div class="col-md-6">
                    <div class="card">
                    <div class="card-body" style="text-align: justify;">
                        <h2 class="mb-3 text-center">Resumen de Términos y Condiciones</h2>
                        <p class="lead">
                            A continuación, se presenta un breve resumen de los 12 puntos más relevantes de nuestros
                            Términos y Condiciones:
                        </p>
                        <ul class="summary-list">
                            <li><strong>Objeto y Alcance del Servicio:</strong> Soluciones tecnológicas para contabilidad y remuneraciones.</li>
                            <li><strong>Proceso de Contratación y Activación:</strong> Realizado vía ejecutivo asignado, con activación en 1-3 días hábiles.</li>
                            <li><strong>Responsabilidades del Cliente:</strong> Veracidad de datos, uso adecuado de la plataforma y protección de la información.</li>
                            <li><strong>Disponibilidad del Servicio (SLA Básico):</strong> Continuidad operativa informando caídas o mantenciones; restauración en máximo 72 horas.</li>
                            <li><strong>Duración Mínima del Servicio:</strong> Se requiere una permanencia mínima de <strong>6 meses</strong>.</li>
                            <li><strong>Pagos y Facturación:</strong> Cobro mensual anticipado; retraso de pago puede generar bloqueo del servicio.</li>
                            <li><strong>Suspensión y Terminación del Contrato:</strong> Por incumplimiento de pago o mal uso; el Cliente puede solicitar baja con 30 días de antelación (respetando los 6 meses mínimos).</li>
                            <li><strong>Confidencialidad, Protección de Datos y Backups:</strong> Tratamiento de datos según normativa, recomendación de respaldos propios.</li>
                            <li><strong>Propiedad Intelectual:</strong> El software y marcas pertenecen a MASTECNO; prohibida reventa o subarriendo no autorizado.</li>
                            <li><strong>Limitación de Responsabilidad:</strong> MASTECNO ofrece soporte técnico; la veracidad y exactitud de los datos ingresados es responsabilidad del Cliente.</li>
                            <li><strong>Fuerza Mayor:</strong> Eventos fuera de control (desastres, ciberataques) pueden eximir temporalmente de responsabilidad.</li>
                            <li><strong>Modificaciones y Legislación Aplicable:</strong> MASTECNO puede actualizar los Términos y Condiciones; la relación contractual se rige por las leyes chilenas.</li>
                        </ul>
                        <p class="mb-0">
                            Para más detalles, descarga el documento completo (PDF):
                        </p>
                        <a href="https://mascontable.maserp.cl/TerCondi/Termino_y_Condiciones.pdf" target="_blank" class="btn btn-primary btn-download">
                            Descargar Términos y Condiciones
                        </a>
                    </div>
                    </div>
                </div>

                <!-- Columna 2: Formulario de Aceptación -->
                <div class="col-md-6">
                    <div class="card">
                    <div class="card-body">
                        <h3 class="mb-3 text-center">Aceptación de Términos</h3>
                        <form action="procesar.php" method="POST" id="termsForm">
                            <!-- RUT -->
                            <div class="mb-3">
                                <label for="rut" class="form-label">RUT</label>
                                <input type="text" class="form-control" id="rut" name="rut" placeholder="12.345.678-9" required>
                                <div class="alert alert-danger mt-2 alert-rut" id="rutError">
                                    El RUT ingresado no es válido. Por favor verifícalo.
                                </div>
                                <input type="hidden" id="Server" name="Server" value="<?php echo $_SESSION['NomServer'];?>">
                                <input type="hidden" id="Firma" name="Firma" value="<?php echo $_SESSION['CONTRATO'];?>">
                                <input type="hidden" id="XId" name="XId" value="<?php echo $_SESSION['XId'];?>">
                                <input type="hidden" id="Usuario" name="Usuario" value="<?php echo $_SESSION['NOMBRE'];?>">
                                <input type="hidden" id="Site" name="Site" value="<?php echo $_SERVER['HTTP_REFERER'];?>">
                            </div>
                            <!-- Nombre y Apellido -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre y Apellido</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nombre completo" maxlength="100" required>
                            </div>
                            <!-- Correo -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Correo</label>
                                <input type="mail" class="form-control" id="email" name="email" placeholder="Correo" maxlength="50">
                                <span style="font-size: 10px;">*Si necesita una copia en su correo</span>
                            </div>
                            <!-- Check de Aceptación -->
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                                <label class="form-check-label" for="acceptTerms">
                                    Acepto los Términos y Condiciones
                                </label>
                            </div>
                            <!-- Botón Firmar -->
                            <button type="submit" class="btn btn-success w-100 mt-4">
                                Firmar Documento
                            </button>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JS de Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Función para validar RUT y formatearlo
            function validarRutChileno(rut) {
                // Eliminar puntos, guiones y convertir a mayúsculas
                let valor = rut.replace(/\./g, '').replace(/-/g, '').toUpperCase();

                // Asegurarse de que el largo mínimo sea 2 (1 dígito + DV)
                if (valor.length < 2) return false;

                // Separar cuerpo y Dígito Verificador
                let cuerpo = valor.slice(0, -1);
                let dv = valor.slice(-1).toUpperCase();

                // Validar que el cuerpo solo tenga números
                if (!/^[0-9]+$/.test(cuerpo)) return false;

                // Calcular Dígito Verificador
                let suma = 0;
                let multiplo = 2;

                // Para cada dígito del cuerpo (de derecha a izquierda)
                for (let i = cuerpo.length - 1; i >= 0; i--) {
                suma += parseInt(cuerpo[i]) * multiplo;
                multiplo = multiplo < 7 ? multiplo + 1 : 2;
                }

                // Calcular DV esperado
                let resto = suma % 11;
                let dvEsperado = 11 - resto;
                dvEsperado = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'K' : dvEsperado.toString();

                return dv === dvEsperado;
            }

            // Función para formatear el RUT con puntos y guión
            function formatearRut(rut) {
                // Remover todo excepto dígitos y K/k
                let valor = rut.replace(/[^\dkK]/g, '').toUpperCase();

                let cuerpo = valor.slice(0, -1);
                let dv = valor.slice(-1);

                // Asegurar que dv sea válido (número o K)
                if(!/^[0-9K]$/.test(dv)){
                dv = '';
                }

                // Agregar puntos
                let resultado = '';
                while (cuerpo.length > 3) {
                resultado = '.' + cuerpo.slice(-3) + resultado;
                cuerpo = cuerpo.slice(0, -3);
                }
                resultado = cuerpo + resultado;

                return dv ? resultado + '-' + dv : resultado;
            }   

            // Referencias al DOM
            const rutInput = document.getElementById('rut');
            const rutError = document.getElementById('rutError');
            const termsForm = document.getElementById('termsForm');

            // Evento para formatear y validar en blur
            rutInput.addEventListener('blur', () => {
                let rutVal = rutInput.value.trim();
                if (rutVal !== '') {
                    // Formateamos
                    let rutFormateado = formatearRut(rutVal);
                    rutInput.value = rutFormateado;

                    // Validamos
                    if (!validarRutChileno(rutFormateado)) {
                        rutError.style.display = 'block';
                    } else {
                        rutError.style.display = 'none';
                    }
                } else {
                    rutError.style.display = 'none';
                }
            });

            // Evento submit del formulario
            termsForm.addEventListener('submit', (e) => {
                let rutVal = rutInput.value.trim();
                if (!validarRutChileno(rutVal)) {
                e.preventDefault();
                rutError.style.display = 'block';
                rutInput.focus();
                return;
                }
                // Si pasa la validación, se envía el formulario
            });
        </script>
    </body>
</html>
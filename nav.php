<?php
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	// $mysqli = xconectar("root", "", "mastecno_server08");

	$sqlin = "SELECT * FROM CTPeriodoEmpresa WHERE Periodo='".$_SESSION['PERIODO']."' AND RutEmpresa='".$_SESSION['RUTEMPRESA']."'";
	$resultadoin = $mysqli->query($sqlin);
	$SwPeriodo = $resultadoin->num_rows;

	$_SESSION['ESTADOPERIODO']=$SwPeriodo;

	$RutOrg=$_SERVER['PHP_SELF'];

	$ExtArc=explode('/', $_SERVER['PHP_SELF']);

	$ExtArc=array_pop($ExtArc);

	$strfinal=str_replace($ExtArc, '', $RutOrg);

	$nivel="";
	if ($strfinal!="/") {
		$nivel="../";
	}

    $dirname = str_replace('/', '', dirname($RutOrg));
	
?>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>


<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }
    
    .dropdown-menu {
        display: none;
    }
    
    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>

	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script type="text/javascript">
		$('body').on("keydown", function(e) { 
			// alert(e.which);
			//if (e.ctrlKey && e.shiftKey && e.which === 83) {
				// alert("You pressed Ctrl + Shift + s");
			if (e.ctrlKey && e.shiftKey && e.which === 76) {
				$('#MiniSaldo').modal('show');
				e.preventDefault();

			}
		});

		function ConsulMini(){
			var url= "<?php echo $nivel; ?>ConSaldoMini.php";
			
			$.ajax({
			type: "POST",
			url: url,
			data: $('#formMini').serialize(),
			success:function(resp){
				$('#MiniSal').html(resp);
			}

			});
		}
		ConsulMini();
	</script> -->

		<!-- <div class="modal fade" id="MiniSaldo" role="dialog">
		<div class="modal-dialog">
			<form action="#" name="formMini" id="formMini" method="POST">
			<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Consulta de Saldo</h4>
			</div>
			<div class="modal-body">

					<div class="panel panel-default">
					<div class="panel-heading text-center">Consulta de Saldos</div>
					<div class="panel-body" id="MiniSal">

					</div>
					</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
			</div>
			</form>
		</div>
		</div> -->

<!-- Modern Navigation with Tailwind CSS -->
<nav class="bg-gray-500 shadow-lg">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-start items-center h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <a href="<?php echo $nivel . $dirname; ?>/frmMain.php" class="flex-shrink-0 flex items-center">
                    <span class="text-white text-xl font-bold tracking-wide">MasContable</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:block flex items-center">
                <div class="ml-10 flex items-center space-x-4">
                    <a href="<?php echo $nivel . $dirname; ?>/frmMain.php" class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Inicio
                    </a>

                    <!-- Mantenedores Dropdown -->
                    <div class="relative dropdown group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            Mantenedores
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-64 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <?php
                                    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                    $SQL1="SELECT * FROM CTContadoresAsignado WHERE idcontador='".$_SESSION['XId']."'";
                                    $resultados1 = $mysqli->query($SQL1);
                                    $row_cnt = $resultados1->num_rows;
                                        if ($row_cnt==0) {
                                    ?>
                                        <a href="<?php echo $nivel . $dirname ?>/Empresas/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Empresas</a>
                                    <?php
                                        }
                                        $mysqli->close();
                                    ?>
                                <a href="<?php echo $nivel . $dirname ?>/frmCliPro.php?nomfrm=C" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Clientes</a>
                                <a href="<?php echo $nivel . $dirname ?>/frmCliPro.php?nomfrm=P" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Proveedores</a>
                                <a href="<?php echo $nivel . $dirname ?>/frmCuentas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cuentas</a>
                                <a href="<?php echo $nivel . $dirname ?>/frmTipoDocumento.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tipo de Documentos</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel . $dirname ?>/CCostos/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Centros de Costos</a>
                                <a href="<?php echo $nivel . $dirname ?>/Contadores/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Contadores</a>
                                <?php if ($_SESSION['ROL']=="A"): ?>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="<?php echo $nivel . $dirname ?>/frmUsuarios.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Usuarios</a>
                                    <a href="<?php echo $nivel . $dirname ?>/frmAsignaEmpresa.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Asignador de Empresas</a>
                                    <a href="<?php echo $nivel . $dirname ?>/frmPeriodos.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Administrar Periodo</a>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <!-- Registros Dropdown -->
                    <?php if ($SwPeriodo==0): ?>
                    <div class="relative dropdown group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            Registros
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-72 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="<?php echo $nivel; ?>RVoucher/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Registro de Voucher</a>
                                <a href="<?php echo $nivel; ?>RVoucherPlantilla/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Plantillas de Voucher</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>RGestionDoc/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Gestión Documentos Electronicos</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>RComVen/index.php?Doc=1" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Registro de Compras</a>
                                <a href="<?php echo $nivel; ?>RComVen/index.php?Doc=2" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Registro de Ventas</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>NCredito/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Gestión Nota de Crédito</a>
                                <a href="<?php echo $nivel; ?>GVoucher/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Generador de Pagos</a>
                                <?php if($_SESSION['NomServer']=="Server99" || $_SESSION['NomServer']=="Server48"): ?>
                                    <a href="<?php echo $nivel; ?>Anticipos/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Generador de Anticipos</a>
                                <?php endif ?>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>Conciliacion/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Conciliación Bancaria</a>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>

                    <!-- Honorarios Dropdown -->
                    <?php if ($SwPeriodo==0): ?>
                    <div class="relative dropdown group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            Honorarios
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-64 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="<?php echo $nivel; ?>Honorarios/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Registro de Honorarios</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>DJ/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Procesar Honorarios DJ</a>
                                <a href="<?php echo $nivel; ?>DJ/frmInfHonorarios.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Certificados de Honorarios DJ</a>
                                <a href="<?php echo $nivel; ?>DJ/frmInfHonorariosRes.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Resumen de Honorarios DJ</a>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>

                    <!-- 14(D3/D8) Dropdown -->
                    <div class="relative dropdown group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            14(D3/D8)
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-64 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="<?php echo $nivel; ?>14D/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">14(D3/D8)</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>14Ter/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Libro Caja 14Ter</a>
                                <a href="<?php echo $nivel; ?>14Ter/LIngresoEgreso.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Libro Ingreso/Egreso 14Ter</a>
                            </div>
                        </div>
                    </div>

                    <!-- Libros y Reportes Dropdown -->
                    <div class="relative dropdown group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            Libros y Reportes
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-72 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="<?php echo $nivel; ?>RLDiario/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Libro Diario / Voucher</a>
                                <a href="<?php echo $nivel; ?>RLComprasVentas/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Libro Compras y Ventas</a>
                                <a href="<?php echo $nivel; ?>Mayor/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Libro Mayor</a>
                                <a href="<?php echo $nivel; ?>HonorariosReport/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Libro de Honorarios</a>          
                                <a href="<?php echo $nivel; ?>Boletas/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Libro de Boletas Electronicas</a>          
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>Report/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Informes Analíticos</a>
                                <a href="<?php echo $nivel; ?>CCostoReport/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Centro de Costo</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>EResultado/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Estado de Resultado</a>
                                <a href="<?php echo $nivel; ?>BalanceClasi/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Balance Clasificado</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>Balance/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Balance General</a>
                            </div>
                        </div>
                    </div>

                    <!-- Utilidades Dropdown -->
                    <?php if ($SwPeriodo==0): ?>
                    <div class="relative dropdown group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            Utilidades
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-80 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="<?php echo $nivel; ?>SincSII" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sincronizador SII</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>frmImportLibro.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importar Libro Compras/Ventas</a>
                                <a href="<?php echo $nivel; ?>Honorarios/frmImportLibroHono.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importar Libro Honorarios</a>
                                <a href="<?php echo $nivel; ?>Boletas/Procesar.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importar Libro Boletas Electronicas</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>ImportaVoucher/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importar Voucher Masivo</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>Apertura/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Utilitario de Apertura</a>			
                                <a href="<?php echo $nivel; ?>Utilitario/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reordenamiento de Comprobantes</a>
                                <a href="<?php echo $nivel; ?>frmFolioSII.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Foliador de Hojas</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>PlanCtaClonar/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Clonar Plan de Cuenta</a>
                                <a href="<?php echo $nivel; ?>PlanCtaImporta/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importar Plan de Cuenta</a>
                                <a href="<?php echo $nivel; ?>PlanCliProImporta/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importar Cuentas Clientes/Proveedores</a>
                                <?php
                                    if($_SESSION['NomServer']=="Server154"){
                                        echo '<a href="'.$nivel.'Report/UltiServer154.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Utilitario Server154</a>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>

                    <!-- Configuración Dropdown -->
                    <?php if ($SwPeriodo==0): ?>
                    <div class="relative dropdown group">
                        <button class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            Configuración
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-80 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="<?php echo $nivel; ?>frmParGlobales.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Parametros Globales</a>
                                <a href="<?php echo $nivel; ?>frmFactores.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Registro de Factores</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>frmPlantillas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Plantillas de Importación</a>
                                <a href="<?php echo $nivel; ?>EResultado/frmResultadoConf.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Estado de Resultado</a>
                                <a href="<?php echo $nivel; ?>frmConfFacturas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Centralización de Facturas</a>
                                <a href="<?php echo $nivel; ?>frmConfHonorario.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Centralización de Honorarios</a>
                                <a href="<?php echo $nivel; ?>Boletas/frmAsientoBolEle.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Centralización Boletas Electronicas</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?php echo $nivel; ?>Facturas/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Datos Empresa</a>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Right side - Logout -->
            <div class="hidden lg:block ml-auto">
                <div class="ml-4 flex items-center md:ml-6">
                    <a href="<?php echo $nivel; ?>./xvalidar.php?destroy=S" class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Salir
                    </a>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="ml-auto lg:hidden">
                <button type="button" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-md text-sm font-medium transition duration-150 ease-in-out" id="mobile-menu-button">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="lg:hidden hidden" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white bg-opacity-10">
            <a href="<?php echo $nivel; ?>frmMain.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-base font-medium">Inicio</a>
            
            <!-- Mantenedores Mobile -->
            <div class="mobile-dropdown">
                <button class="mobile-dropdown-btn text-white hover:bg-white hover:bg-opacity-20 w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                    Mantenedores
                    <svg class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="mobile-dropdown-content hidden pl-4 space-y-1">
                    <?php
                        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                        $SQL1="SELECT * FROM CTContadoresAsignado WHERE idcontador='".$_SESSION['XId']."'";
                        $resultados1 = $mysqli->query($SQL1);
                        $row_cnt = $resultados1->num_rows;
                            if ($row_cnt==0) {
                        ?>
                            <a href="<?php echo $nivel; ?>Empresas/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Empresas</a>
                        <?php
                            }
                            $mysqli->close();
                        ?>
                    <a href="<?php echo $nivel; ?>frmCliPro.php?nomfrm=C" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Clientes</a>
                    <a href="<?php echo $nivel; ?>frmCliPro.php?nomfrm=P" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Proveedores</a>
                    <a href="<?php echo $nivel; ?>frmCuentas.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Cuentas</a>
                    <a href="<?php echo $nivel; ?>frmTipoDocumento.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Tipo de Documentos</a>
                    <a href="<?php echo $nivel; ?>CCostos/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Centros de Costos</a>
                    <a href="<?php echo $nivel; ?>Contadores/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Contadores</a>
                    <?php if ($_SESSION['ROL']=="A"): ?>
                        <a href="<?php echo $nivel; ?>frmUsuarios.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Usuarios</a>
                        <a href="<?php echo $nivel; ?>frmAsignaEmpresa.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Asignador de Empresas</a>
                        <a href="<?php echo $nivel; ?>frmPeriodos.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Administrar Periodo</a>
                    <?php endif ?>
                </div>
            </div>

            <!-- Registros Mobile -->
            <?php if ($SwPeriodo==0): ?>
            <div class="mobile-dropdown">
                <button class="mobile-dropdown-btn text-white hover:bg-white hover:bg-opacity-20 w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                    Registros
                    <svg class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="mobile-dropdown-content hidden pl-4 space-y-1">
                    <a href="<?php echo $nivel; ?>RVoucher/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Registro de Voucher</a>
                    <a href="<?php echo $nivel; ?>RVoucherPlantilla/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Plantillas de Voucher</a>
                    <a href="<?php echo $nivel; ?>RGestionDoc/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Gestión Documentos</a>
                    <a href="<?php echo $nivel; ?>RComVen/index.php?Doc=1" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Registro de Compras</a>
                    <a href="<?php echo $nivel; ?>RComVen/index.php?Doc=2" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Registro de Ventas</a>
                    <a href="<?php echo $nivel; ?>NCredito/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Gestión Nota de Crédito</a>
                    <a href="<?php echo $nivel; ?>GVoucher/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Generador de Pagos</a>
                    <?php if($_SESSION['NomServer']=="Server99" || $_SESSION['NomServer']=="Server48"): ?>
                        <a href="<?php echo $nivel; ?>Anticipos/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Generador de Anticipos</a>
                    <?php endif ?>
                    <a href="<?php echo $nivel; ?>Conciliacion/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Conciliación Bancaria</a>
                </div>
            </div>
            <?php endif ?>

            <!-- Honorarios Mobile -->
            <?php if ($SwPeriodo==0): ?>
            <div class="mobile-dropdown">
                <button class="mobile-dropdown-btn text-white hover:bg-white hover:bg-opacity-20 w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                    Honorarios
                    <svg class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="mobile-dropdown-content hidden pl-4 space-y-1">
                    <a href="<?php echo $nivel; ?>Honorarios/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Registro de Honorarios</a>
                    <a href="<?php echo $nivel; ?>DJ/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Procesar Honorarios DJ</a>
                    <a href="<?php echo $nivel; ?>DJ/frmInfHonorarios.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Certificados de Honorarios DJ</a>
                    <a href="<?php echo $nivel; ?>DJ/frmInfHonorariosRes.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Resumen de Honorarios DJ</a>
                </div>
            </div>
            <?php endif ?>

            <!-- 14(D3/D8) Mobile -->
            <div class="mobile-dropdown">
                <button class="mobile-dropdown-btn text-white hover:bg-white hover:bg-opacity-20 w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                    14(D3/D8)
                    <svg class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="mobile-dropdown-content hidden pl-4 space-y-1">
                    <a href="<?php echo $nivel; ?>14D/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">14(D3/D8)</a>
                    <a href="<?php echo $nivel; ?>14Ter/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Libro Caja 14Ter</a>
                    <a href="<?php echo $nivel; ?>14Ter/LIngresoEgreso.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Libro Ingreso/Egreso 14Ter</a>
                </div>
            </div>

            <!-- Libros y Reportes Mobile -->
            <div class="mobile-dropdown">
                <button class="mobile-dropdown-btn text-white hover:bg-white hover:bg-opacity-20 w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                    Libros y Reportes
                    <svg class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="mobile-dropdown-content hidden pl-4 space-y-1">
                    <a href="<?php echo $nivel; ?>RLDiario/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Libro Diario / Voucher</a>
                    <a href="<?php echo $nivel; ?>RLComprasVentas/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Libro Compras y Ventas</a>
                    <a href="<?php echo $nivel; ?>Mayor/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Libro Mayor</a>
                    <a href="<?php echo $nivel; ?>HonorariosReport/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Libro de Honorarios</a>
                    <a href="<?php echo $nivel; ?>Boletas/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Libro de Boletas</a>
                    <a href="<?php echo $nivel; ?>Report/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Informes Analíticos</a>
                    <a href="<?php echo $nivel; ?>CCostoReport/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Centro de Costo</a>
                    <a href="<?php echo $nivel; ?>EResultado/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Estado de Resultado</a>
                    <a href="<?php echo $nivel; ?>BalanceClasi/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Balance Clasificado</a>
                    <a href="<?php echo $nivel; ?>Balance/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Balance General</a>
                </div>
            </div>

            <!-- Utilidades Mobile -->
            <?php if ($SwPeriodo==0): ?>
            <div class="mobile-dropdown">
                <button class="mobile-dropdown-btn text-white hover:bg-white hover:bg-opacity-20 w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                    Utilidades
                    <svg class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="mobile-dropdown-content hidden pl-4 space-y-1">
                    <a href="<?php echo $nivel; ?>SincSII" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Sincronizador SII</a>
                    <a href="<?php echo $nivel; ?>frmImportLibro.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Importar Libro Compras/Ventas</a>
                    <a href="<?php echo $nivel; ?>Honorarios/frmImportLibroHono.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Importar Libro Honorarios</a>
                    <a href="<?php echo $nivel; ?>Boletas/Procesar.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Importar Libro Boletas</a>
                    <a href="<?php echo $nivel; ?>ImportaVoucher/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Importar Voucher Masivo</a>
                    <a href="<?php echo $nivel; ?>Apertura/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Utilitario de Apertura</a>
                    <a href="<?php echo $nivel; ?>Utilitario/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Reordenamiento de Comprobantes</a>
                    <a href="<?php echo $nivel; ?>frmFolioSII.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Foliador de Hojas</a>
                    <a href="<?php echo $nivel; ?>PlanCtaClonar/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Clonar Plan de Cuenta</a>
                    <a href="<?php echo $nivel; ?>PlanCtaImporta/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Importar Plan de Cuenta</a>
                    <a href="<?php echo $nivel; ?>PlanCliProImporta/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Importar Cuentas Clientes/Proveedores</a>
                    <?php
                        if($_SESSION['NomServer']=="Server154"){
                            echo '<a href="'.$nivel.'Report/UltiServer154.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Utilitario Server154</a>';
                        }
                    ?>
                </div>
            </div>
            <?php endif ?>

            <!-- Configuración Mobile -->
            <?php if ($SwPeriodo==0): ?>
            <div class="mobile-dropdown">
                <button class="mobile-dropdown-btn text-white hover:bg-white hover:bg-opacity-20 w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                    Configuración
                    <svg class="h-4 w-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="mobile-dropdown-content hidden pl-4 space-y-1">
                    <a href="<?php echo $nivel; ?>frmParGlobales.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Parametros Globales</a>
                    <a href="<?php echo $nivel; ?>frmFactores.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Registro de Factores</a>
                    <a href="<?php echo $nivel; ?>frmPlantillas.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Plantillas de Importación</a>
                    <a href="<?php echo $nivel; ?>EResultado/frmResultadoConf.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Estado de Resultado</a>
                    <a href="<?php echo $nivel; ?>frmConfFacturas.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Centralización de Facturas</a>
                    <a href="<?php echo $nivel; ?>frmConfHonorario.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Centralización de Honorarios</a>
                    <a href="<?php echo $nivel; ?>Boletas/frmAsientoBolEle.php" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Centralización Boletas</a>
                    <a href="<?php echo $nivel; ?>Facturas/" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-sm">Datos Empresa</a>
                </div>
            </div>
            <?php endif ?>

            <!-- Logout Mobile -->
            <a href="<?php echo $nivel; ?>./xvalidar.php?destroy=S" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Salir
            </a>
        </div>
    </div>
</nav>

<!-- Status Bar with Tailwind CSS -->
<div class="bg-red-500 text-white py-2">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="text-xs mb-2 md:mb-0">
                <?php
                    $mysqli=ConCobranza();
                    $SQL="SELECT * FROM CTTablaIndicadores WHERE Dolar>0 ORDER BY id DESC LIMIT 1";
                    $resultados = $mysqli->query($SQL);
                    while ($registro = $resultados->fetch_assoc()) {
                        $XDolar=$registro["Dolar"];
                        $XEuro=$registro["Euro"];
                        $XUF=$registro["UF"];
                        $XUTM=$registro["UTM"];
                        $XIPC=$registro["IPC"];
                        $XIVP=$registro["IVP"];
                        $XIMACEC=$registro["Imacec"];
                    }
                    $mysqli->close();
                    echo $Srttext='Dólar: '.number_format($XDolar, 2, "," , ".").' - Euro: '.number_format($XEuro, 2, "," , ".").' - UF: '.number_format($XUF, 2, "," , ".").' - UTM: '.number_format($XUTM, 2, "," , ".").' - IPC: '.number_format($XIPC, 2, "," , ".").' - IVP: '.number_format($XIVP, 2, "," , ".").' - Imacec: '.number_format($XIMACEC, 2, "," , ".");
                ?>
            </div> 
            
            <form action="#" method="POST" name="formperiodo" id="formperiodo" class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <script type="text/javascript">
                    function UpPeriodo(){
                        var url= "<?php echo $nivel; ?>MoviPeriodo.php";
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: $('#formperiodo').serialize(),
                            success:function(resp){
                                form1.action="<?php echo $nivel; ?>frmMain.php";
                                form1.submit();
                            }
                        });
                    }
                </script>

                <?php 
                    if ($_SESSION['RAZONSOCIAL']!=""){ 
                        echo "<span class='text-sm font-medium'>".strtoupper($_SESSION['NomServer'])." - Empresa: ".$_SESSION['RUTEMPRESA'].", ".$_SESSION['RAZONSOCIAL']." - Periodo: </span>"; 
                ?>

                <select id="messelect" name="messelect" onchange="UpPeriodo()" class="bg-white text-gray-900 px-3 py-1 rounded border border-gray-300 text-sm">
                <?php 
                    $TPerio="01-".$_SESSION['PERIODO'];
                    $dia = substr($TPerio,0,2);
                    $dmes = substr($TPerio,3,2);
                    $dano = substr($TPerio,6,4);

                    $Meses=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
                    $i=1;
                    while($i<=12){

                        if ($i==$dmes) {
                            echo "<option value ='".$i."' selected>".$Meses[($i-1)]."</option>";
                        }else{
                            echo "<option value ='".$i."'>".$Meses[($i-1)]."</option>";
                        }
                        $i++;
                    }
                ?>
                </select>

                <select id="anoselect" name="anoselect" onchange="UpPeriodo()" class="bg-white text-gray-900 px-3 py-1 rounded border border-gray-300 text-sm">
                    <?php 
                        $yoano=date('Y')+4;
                        $tano="2010";

                        while($tano<=($yoano+1)){
                            if ($dano==$tano) {
                                echo "<option value ='".$tano."' selected>".$tano."</option>";
                            }else{
                                echo "<option value ='".$tano."'>".$tano."</option>";
                            }
                            $tano=$tano+1;
                        }
                    ?>
                </select>

                <?php
                    }else{
                        echo "&nbsp;";
                    }
                ?>
            </form>
        </div>
    </div>
</div>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    
    if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
        mobileMenu.classList.add('hidden');
    }
});

// Mobile dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileDropdowns = document.querySelectorAll('.mobile-dropdown-btn');
    
    mobileDropdowns.forEach(button => {
        button.addEventListener('click', function() {
            const dropdownContent = this.nextElementSibling;
            const icon = this.querySelector('svg');
            
            // Toggle dropdown content
            dropdownContent.classList.toggle('hidden');
            
            // Rotate icon
            if (dropdownContent.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });
});
</script>

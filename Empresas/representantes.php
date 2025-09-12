<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	include '../clases/clasesCss.php';
	// include '../conexion/secciones.php';
	session_start();

	if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
		header("location:../?Msj=95");
		exit;
	}

    $Periodo=$_SESSION['PERIODO'];

	if($Periodo!=""){
		echo "
		<script>
			alert('Para crear un representante, debe salir de la empresa actual.');
			location.href ='../frmMain.php';
		</script>
		";
		exit;
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasContable</title>
    <link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
    <script src="../js/jquery.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/tailwind.js"></script>
    <script src="../js/jquery.Rut.js" type="text/javascript"></script>	
    <script src="../js/jquery.validate.js" type="text/javascript"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="../css/StConta.css">
    <script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function(){
			$('#rut').Rut({ 
				on_error: function(){
                    mostrarMensaje("El Rut ingresado es incorrecto, favor validar e intentar nuevamente.", "error"); 
                    $('#rut').val(""); 
                    $('#rut').focus();
                }
			});
		});

        function RutMal(){
            alert('El Rut ingresado es incorrecto, favor validar e intentar nuevamente.');
        }

        function NumYGuion(e){
			var key = window.Event ? e.which : e.keyCode
			return (key >= 48 && key <= 57 || key == 45 || key==75 || key==107)
		}
    </script>
</head>
<body>
    <?php include '../nav.php'; ?>

    <div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
			<div class="space-y-8">

                <form method="POST" name="form1" id="form1" class="space-y-8">

                    <div class="flex flex-wrap justify-start items-center gap-2 border-2 border-gray-300 rounded-md p-2">
                        <button type="button" 
                                class="bg-slate-100 text-sm hover:bg-blue-200 text-blue-600 font-medium py-1 px-2 border-2 border-blue-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" 
                                onclick="limpiarFormulario()">
                            <i class="fa fa-plus mr-2"></i>Nueva
                        </button>
                        
                        <button type="submit" id="btnGrabar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" tabindex="15">
                            <i class="fa fa-save mr-2"></i>Grabar
                        </button>
                            

                        <button type="button" hidden id="btnEliminar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" tabindex="16">
                            <i class="fa fa-trash mr-2"></i>Eliminar
                        </button>

                        <button type="button" id="btnBuscar" class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                data-modal-target="default-modal" 
                                data-modal-toggle="default-modal">
                            <i class="fa-solid fa-magnifying-glass text-gray-600 mr-2"></i>Buscar
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div id="dropdown" class="z-10 hidden bg-gray-100 divide-y divide-gray-100 rounded-lg shadow-sm w-44">
                            <ul class="py-2 text-sm text-gray-200" aria-labelledby="dropdownDefaultButton">
                                <li>
                                    <button type="button" onclick="ExportCSV()" class="w-full block px-4 py-2 hover:bg-gray-300 text-black text-left font-medium">
                                        <i class="fa-solid fa-file-csv mr-2"></i>CSV
                                    </button>
                                </li>
                                <li>
                                    <a href="#" class="w-full block px-4 py-2 hover:bg-gray-300 text-black text-left font-medium">
                                        <i class="fa-solid fa-file-pdf mr-2"></i>PDF
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <a href="../frmMain.php" 
                                class="bg-gray-100 hover:bg-gray-300 text-sm text-black font-medium py-1 px-2 border-2 border-gray-600 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
                                >
                            <i class="fa fa-times mr-2"></i>Cancelar
                        </a>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
						<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
								<i class="fas fa-building text-lg text-blue-600"></i>
							</div>
							<div>
								<h3 class="text-lg font-semibold text-gray-800">
									Información del representante
								</h3>
								<p class="text-sm text-gray-600">Ingresa los parametros</p>     
							</div>
							
                           
                    	</div>
						<div class="p-6 pt-1 space-y-6">

							<div id="divAlertas" class="mt-5"></div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2">
                        
                                <input type="hidden" name="idrep" id="idrep">

                                <div>
                                    <label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-building mr-1"></i>Empresa
									</label>
                                    <select class="<?php input_css(); ?>" id="selectEmpresa" name="selectEmpresa">
                                        <option value="">Selecciona una empresa</option>
                                    </select>
								</div>
                            
								<div>
									<label for="rut" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-id-card mr-1"></i>RUT Representante
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   id="rut" 
										   autocomplete="off" 
										   name="rut" 
										   onChange="javascript:this.value=this.value.toUpperCase();" 
										   onKeyPress="return NumYGuion(event)" 
										   maxlength="10" 
										   placeholder="Ej. 96900500-1"
										   >
								</div>
								
							</div>

							<!-- Second Row: Company Name and Constitution Date -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
									<label for="nombre" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-id-card mr-1"></i>Nombre
									</label>
									<input type="text" 
										   class="<?php input_css(); ?>" 
										   id="nombre" 
										   autocomplete="off" 
										   name="nombre" 
										   onChange="javascript:this.value=this.value.toUpperCase();"
										   >
								</div>

                                <div>
									<label for="correo" class="block text-sm font-medium text-gray-700 mb-1 pl-1">
										<i class="fa fa-envelope mr-1"></i>Correo
									</label>
									<input type="email" 
										   class="<?php input_css(); ?>" 
										   id="correo" 
										   autocomplete="off" 
										   name="correo" 
										   onChange="javascript:this.value=this.value.toUpperCase();"
										   >
								</div>

								
							</div>

						</div>
					</div>

                </form>

                <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-7xl max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow-sm">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
                                        <i class="fa fa-list text-lg text-primary-500"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                            Representantes Creados
                                        </h3>	
                                        <p class="text-sm text-gray-600" id="msgEmpresa">Lista de representantes</p>
                                    </div>	
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
                                <div class="mb-4">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa fa-search text-gray-400"></i>
                                        </div>
                                        <input class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" 
                                            id="myInput" 
                                            type="text" 
                                            placeholder="Buscar representantes...">
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RUT</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTable">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                                <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="../js/funciones.js"></script>
    <script>

        function handleFetchErrors(response) {
			if (!response.ok) {
				throw Error(response.statusText);
			}
			return response.json();
		}

        function cargarEmpresas() {
            fetch("router/router.php?action=cargarEmpresas", {
                method: "GET",
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(handleFetchErrors)
            .then(data => {
                const selectEmpresa = document.getElementById("selectEmpresa");
                if(data.success) {
                    if(data.empresas.length === 0) {
                        selectEmpresa.innerHTML = "<option value=''>No se encontraron empresas</option>";
                        deshabilitarCampo("selectEmpresa");
                    } else {
                        habilitarCampo("selectEmpresa");
                        selectEmpresa.innerHTML = "<option value=''>Selecciona una empresa</option>";
                        data.empresas.forEach(empresa => {
                            const option = document.createElement("option");
                            option.value = empresa.id;
                            option.textContent = empresa.razonsocial;
                            selectEmpresa.appendChild(option);
                        });
                        
                    }
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }

        function cargarRepresentantes(buscar = "") {
            const myTable = document.getElementById("myTable");


            let url = "router/router.php?action=cargarRepresentantes";
            if(buscar !== "") {
                url += `&buscar=${buscar}`;
            }
            
            fetch(url, {
                method: "GET",
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(handleFetchErrors)
            .then(data => {
                console.log(data);
                if(data.success) {
                    if(data.representantes.length === 0) {
                        myTable.innerHTML = "";
                        const tr = document.createElement("tr");
                        tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";
                        tr.innerHTML = `
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">No se encontraron representantes</td>
                        `;
                        myTable.appendChild(tr);
                    }else{
                        myTable.innerHTML = "";
                        const clase = "px-6 py-2 whitespace-nowrap text-sm text-gray-900";
                        let contador = 1;
                        data.representantes.forEach(rep => {
                            const tr = document.createElement("tr");
                            tr.className = "bg-white hover:bg-gray-50 transition duration-150 ease-in-out";
                            myTable.appendChild(tr);

                            const tdNumero = document.createElement("td");
                            tdNumero.className = clase;
                            tdNumero.textContent = contador++;
                            tr.appendChild(tdNumero);

                            const tdRut = document.createElement("td");
                            tdRut.className = clase;
                            tdRut.textContent = rep.rut_repre;
                            tr.appendChild(tdRut);

                            const tdNombre = document.createElement("td");
                            tdNombre.className = clase;
                            tdNombre.textContent = rep.nombre_repre;
                            tr.appendChild(tdNombre);

                            const tdEmpresa = document.createElement("td");
                            tdEmpresa.className = clase;
                            tdEmpresa.textContent = rep.razonsocial;
                            tr.appendChild(tdEmpresa);

                            const tdAcciones = document.createElement("td");
                            tdAcciones.className = clase;
                            tr.appendChild(tdAcciones);

                            const divAcciones = document.createElement("div");
                            divAcciones.className = "flex space-x-2";
                            tdAcciones.appendChild(divAcciones);

                            const btnModificar = document.createElement("button");
                            btnModificar.className = "inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200";
                            btnModificar.innerHTML = "<i class='fa fa-edit mr-1'></i>Modificar";
                            btnModificar.addEventListener("click", function() {
                                document.getElementById("idrep").value = rep.id;
                                document.getElementById("rut").value = rep.rut_repre;
                                document.getElementById("nombre").value = rep.nombre_repre;
                                document.getElementById("correo").value = rep.correo;
                                document.getElementById("selectEmpresa").value = rep.id_empresa;
                                document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Modificar";
                                document.getElementById("btnEliminar").hidden = false;

                                const closeButton = document.querySelector("[data-modal-hide='default-modal']");
                                if (closeButton) closeButton.click();

                                const btnEliminar = document.getElementById("btnEliminar");
                                btnEliminar.hidden = false;

                                const permiso = verificarPermisos();
                                
                                if(permiso) {
                                    btnEliminar.addEventListener("click", function() {
                                        eliminarRepresentante(rep.id);
                                    });
                                } else {
                                    deshabilitarBoton("btnEliminar");
                                    btnEliminar.addEventListener("click", function() {
                                        mostrarMensaje("No tienes permisos para eliminar este representante", "error");
                                    });
                                }
                            });

                            divAcciones.appendChild(btnModificar);

                            

                        });
                    }
                }
            });
        }

        function ingresarRepresentante(e) {
            e.preventDefault();

            const rut = document.getElementById("rut").value;
            const nombre = document.getElementById("nombre").value;
            const correo = document.getElementById("correo").value;
            const selectEmpresa = document.getElementById("selectEmpresa").value;

            const campos = ["rut", "nombre", "selectEmpresa"];
            const camposVacios = campos.some(campo => !document.getElementById(campo).value);
            
            if(camposVacios) {
                mostrarMensaje("Faltan datos", "info");
                return;
            }

            const representanteData = {
                rut: rut,
                nombre: nombre,
                correo: correo,
                selectEmpresa: selectEmpresa
            };

            const idrep = document.getElementById("idrep").value;
            if(idrep !== "") representanteData.idrep = idrep;

            const action = idrep === "" ? "ingresarRepresentante" : "modificarRepresentante";

            fetch(`router/router.php?action=${action}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(representanteData)
            })
            
            .then(handleFetchErrors)
            .then(data => {
                // console.log(data);
                if(data.success) {
                    mostrarMensaje(data.mensaje, "success");
                    limpiarFormulario();
                } else if(data.error) {
                    mostrarMensaje(data.mensaje, "error");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                console.log("Error al procesar los datos: " + error.message);
            });
        }

        function eliminarRepresentante(id) {
            fetch(`router/router.php?action=eliminarRepresentante`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id: id })
            })
            .then(handleFetchErrors)
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error("Error:", error);
                console.error("Error al eliminar el representante: " + error.message);
            });
        }

        function verificarPermisos() {
			fetch(`router/router.php?action=verificarPermisos`, {
				method: "GET",
				headers: {
					'Content-Type': 'application/json',
				}
			})
			.then(handleFetchErrors)
			.then(data => {
				return data.permiso;
			});
		}

        function limpiarFormulario() {
            document.getElementById("idrep").value = "";
            document.getElementById("rut").value = "";
            document.getElementById("nombre").value = "";
            document.getElementById("correo").value = "";
            document.getElementById("selectEmpresa").value = "";
            document.getElementById("idrep").value = "";
            document.getElementById("btnGrabar").innerHTML = "<i class='fa fa-save mr-2'></i>Grabar";
            document.getElementById("btnEliminar").hidden = true;
            document.getElementById("btnEliminar").onclick = null;
        }

        document.addEventListener("DOMContentLoaded", function() {
            cargarEmpresas();

            const btnBuscar = document.getElementById("btnBuscar");
            btnBuscar.addEventListener("click", function() {
                cargarRepresentantes();

                const myInput = document.getElementById("myInput");
                myInput.addEventListener("input", function() {
                    cargarRepresentantes(myInput.value);
                });
            });

            document.getElementById("form1").addEventListener("submit", ingresarRepresentante);
        });

    </script>
</body>
</html>
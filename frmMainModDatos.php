<?php
	$sw=1;
	$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
	$SQL="SELECT * FROM CTEmpresas WHERE rut='".$_SESSION['RUTEMPRESA']."'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$rut=$registro["rut"];
		$razonsocial=$registro["razonsocial"];
		$rutrep=$registro["rut_representante"];
		$representante=$registro["representante"];
		$direccion=$registro["direccion"];
		$giro=$registro["giro"];
		$ciudad=$registro["ciudad"];
		$correo=$registro["correo"];
		$pinicio=$registro["periodo"];
		$pcomprobante=$registro["comprobante"];
		$pplancta=$registro["plan"];
	}
	$mysqli->close();
?>

<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">
                    Actualizar Datos de la Empresa
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
            
			<div class="p-2.5 bg-red-600 text-white border rounded-t-md">Informacion de la Empresa</div>
			<div class="p-3 border-1 border-gray-600 bg-white/30">

				<div class="grid grid-cols-1">
					<div class="input-group">
						<span class="input-group-addon">Rut</span>
						<input type="text" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" id="rut" autocomplete="off" name="rut" onChange="javascript:this.value=this.value.toUpperCase();" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" value="<?php echo $rut; ?>" <?php if($sw==1){ echo 'readonly="false"';} ?> required>
					</div>
				</div>

				<div class="grid grid-cols-1 gap-3 mt-3">
					<div>
						<span class="input-group-addon">Raz&oacute;n Social</span>
						<input type="text" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" autocomplete="off" id="rsocial" name="rsocial" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $razonsocial; ?>" required>
					</div>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3"> 
					<div>
						<span class="input-group-addon">Rut Repre.</span>
						<input type="text" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" id="rutrep" autocomplete="off" name="rutrep" onKeyPress="return NumYGuion(event)" maxlength="10" placeholder="Ej. 96900500-1" value="<?php echo $rutrep; ?>" required>
					</div>

					<div>
						<span class="input-group-addon">Representante Legal</span>
						<input type="text" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" autocomplete="off" id="representante" name="representante" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $representante; ?>" required>
					</div>
				</div>

				<div class="grid grid-cols-1 mt-3">
					<span class="input-group-addon">Direcci&oacute;n</span>
					<input type="text" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" autocomplete="off" id="direccion" name="direccion" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $direccion; ?>" required>
				</div>

				<div class="grid grid-cols-1 mt-3">
					<span class="input-group-addon">Giro</span>
					<input type="text" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" autocomplete="off" id="giro" name="giro" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $giro; ?>" required>        
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
					<div>
						<span class="input-group-addon">Ciudad</span>
						<input type="text" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" id="ciudad" autocomplete="off" name="ciudad" maxlength="50" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $ciudad; ?>" required>
					</div>

					<div>
						<span class="input-group-addon">Correo</span>
						<input type="email" class="block w-full pl-3 pr-3 py-1 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500" id="correo" autocomplete="off" name="correo" maxlength="50" value="<?php echo $correo; ?>">
					</div>
				</div>


				
				</div>

            </div>
            <!-- Modal footer -->
            <div class="flex justify-start gap-3 items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
				<script type="text/javascript">
					function DatEmp(){
						form1.ModUpdEmp.value="Empr";
						form1.action="xfrmMainModDatos.php";
						form1.submit();
					}
				</script>

				<button type="button" class="inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" onclick="DatEmp()">Actualizar</button>
				<button type="button" class="inline-flex border border-gray-300 justify-center items-center px-2.5 py-1.5 text-xs font-medium shadow rounded text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200" data-modal-hide="default-modal">
                    <i class="fa fa-xmark mr-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
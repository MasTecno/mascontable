<?php
	include '../conexion/conexionmysqli.php';
	include '../js/funciones.php';
	session_start();
	//include 'conexion/secciones.php';
	
    $Periodo=$_SESSION['PERIODO'];

	$mysqliX=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTParametros WHERE estado='A'";
    $resultados = $mysqliX->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
    	if($registro['tipo']=="SEPA_LIST"){
    		$DLIST=$registro['valor'];	
    	}
    }

	function valida_rut($rut){
		$rut = preg_replace('/[^k0-9]/i', '', $rut);
		$dv  = substr($rut, -1);
		$numero = substr($rut, 0, strlen($rut)-1);
		$i = 2;
		$suma = 0;
		foreach(array_reverse(str_split($numero)) as $v){
			if($i==8)
				$i = 2;
				$suma += $v * $i;
				++$i;
			}

		$dvr = 11 - ($suma % 11);

		if($dvr == 11){
			$dvr = 0;
		}
		if($dvr == 10){
			$dvr = 'K';
		}

		if($dvr == strtoupper($dv)){
			return true;
		}else{
			return false;
		}
	}

    extract($_POST);

    if ($action == "upload") {
 
    	$archivo = $_FILES['file']['tmp_name'];

      	////Cuento Linea del Archivo
      	$LArchivo=0;
      	$fp = fopen ($archivo,"r"); 
       	while ($data = fgetcsv ($fp, 0, $_POST['separador'])){
       		$LArchivo=$LArchivo+1;
       	}

		$ListError="";
		$STRSQL = "INSERT INTO CTEmpresas VALUES ";
		$row = 1; 
      	
		$NewRSocial=0;
		$fp = fopen ($archivo,"r");

		while ($data = fgetcsv ($fp, 0, $_POST['separador'])){
	        if($row>=2){

	        	$LRut=$data[1];
	        	$LRut=str_replace(".","",$LRut);
	        	$LRut=str_replace(",","",$LRut);
                $a=explode("-", $LRut);

				if (count($a)<=1) {
					$ListError='<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4"><div class="flex"><div class="flex-shrink-0"><i class="fa fa-exclamation-triangle text-red-400"></i></div><div class="ml-3"><h3 class="text-sm font-medium text-red-800">Advertencia!</h3><div class="mt-2 text-sm text-red-700"><p>El rut '.$LRut.', No es valido.<br>Operaci&oacute;n Cancelada</p></div></div></div></div>';
                    break;
				}

                $LRut=($a[0]*1)."-".$a[1];

	        	$RSocial=strtoupper($data[2]);
	        	$Rrepre=strtoupper($data[3]);
	        	$Nrepre=strtoupper($data[4]);
	        	$Direccion=strtoupper($data[5]);
	        	$Giro=strtoupper($data[6]);
	        	$Cuidad=strtoupper($data[7]);
	        	$Correo=$data[8];

				$sqlin = "SELECT * FROM CTEmpresas WHERE rut='".$LRut."'";
				$resultadoin = $mysqliX->query($sqlin);
				$row_cnt = $resultadoin->num_rows;
				if ($row_cnt>0) {
					$ListError='<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4"><div class="flex"><div class="flex-shrink-0"><i class="fa fa-exclamation-triangle text-red-400"></i></div><div class="ml-3"><h3 class="text-sm font-medium text-red-800">Advertencia!</h3><div class="mt-2 text-sm text-red-700"><p>El rut '.$LRut.', Ya esta registrado.<br>Operaci&oacute;n Cancelada</p></div></div></div></div>';
                    break;
				}

				if(valida_rut($LRut)==false){
					$ListError='<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4"><div class="flex"><div class="flex-shrink-0"><i class="fa fa-exclamation-triangle text-red-400"></i></div><div class="ml-3"><h3 class="text-sm font-medium text-red-800">Advertencia!</h3><div class="mt-2 text-sm text-red-700"><p>El rut '.$LRut.', No son validos.<br>Operaci&oacute;n Cancelada</p></div></div></div></div>';
                    break;
				}

		        $STRSQL = $STRSQL." ('','$RSocial','$Rrepre','$Nrepre','$LRut','$Direccion','$Cuidad','$Correo','','$Giro','".date("m-Y")."','S','S','N','A','0')";

		        if ($LArchivo==$row){
			    	$STRSQL = $STRSQL.";";
		        }else{
		        	$STRSQL = $STRSQL.",";
		        }
	        }
	        $row=$row+1; 
	    }

		if ($ListError=="") {
			if (!$resultado = $mysqliX->query($STRSQL)) {
				$ListError='<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4"><div class="flex"><div class="flex-shrink-0"><i class="fa fa-exclamation-triangle text-red-400"></i></div><div class="ml-3"><h3 class="text-sm font-medium text-red-800">Error</h3><div class="mt-2 text-sm text-red-700"><p>Error al intentar procesar el archivo, puede ser que no contenga datos, verifique la estructura del mismo y vuelva a procesar.<br> Si el error persiste Contacte al administrador del Sistema...</p></div></div></div></div>';
			}

			if ($ListError=="") {
          		$ListError='<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4"><div class="flex"><div class="flex-shrink-0"><i class="fa fa-check-circle text-green-400"></i></div><div class="ml-3"><h3 class="text-sm font-medium text-green-800">Éxito</h3><div class="mt-2 text-sm text-green-700"><p>El archivo fue procesado con Exito.</p></div></div></div></div>';
          	}
	   	}

		$mysqliX->close();
		fclose ($fp); 
    }

?>
<!DOCTYPE html>
<html >
	<head>
		<title>MasContable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="../images/MC.ico" type="favicon/ico" />
		<!-- <link rel="stylesheet" href="../css/bootstrap.min.css"> -->
		<script src="../js/jquery.min.js"></script>
		<!-- <script src="../js/bootstrap.min.js"></script> -->

		<script src="https://cdn.tailwindcss.com"></script>
		<script src="../js/tailwind.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<script src="../js/propio.js"></script>

		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<script src="https://kit.fontawesome.com/b8e5063394.js" crossorigin="anonymous"></script>
		<script type="text/javascript">
			function CargaArc(){
				var r = confirm("El proceso puede tomar tiempo");
				if (r == true) {
					importar.action="";
					//importar.submit();
				}else{
					alert("Operacion Cancelada");
					importar.action.value="";
				}
			}
		</script>
	</head>

	<body>

		<?php include '../nav.php'; ?>

		<div class="min-h-screen bg-gray-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

			<div class="space-y-8">
			<form name="importar" method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" value="upload" name="action" />

				<div class="bg-white rounded-lg shadow-sm border border-gray-200">            
					<div class="flex justify-start items-center px-6 pt-3 pb-3 bg-gray-100 w-full shadow">
						<div class="w-10 h-10 bg-blue-100 rounded-lg flex justify-center items-center mr-4">
							<i class="fa-solid fa-building text-lg text-blue-600"></i>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-800">
								Carga Masiva de Empresas
							</h3>
							<p class="text-sm text-gray-600">Importe empresas desde un archivo CSV</p>
						</div>
					</div> 
					
					<div class="p-6 pt-1 space-y-6">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">

							<div>
								<label for="file" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Archivo</label>
								<input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-7 file:rounded-l-md file:border-5 file:text-sm file:font-medium border border-gray-300 rounded-md file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-200" id="file" name="file" aria-describedby="fileHelp" accept=".csv">
								<span id="fileHelp" class="mt-2 p-1 text-sm text-gray-500">* Solo archivos CSV.</span>
							</div>

							<div>
								<label for="separador" class="block text-sm font-medium text-gray-700 mb-2">Separador</label>
								<input type="text" name="separador" value="<?php echo $DLIST; ?>" id="separador" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Separador de columnas">
							</div>

						</div>

						<div class="flex flex-wrap justify-start items-center gap-2 mt-6">
							<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" onclick="CargaArc()">
								<i class="fa fa-upload mr-2"></i> Procesar Archivo
							</button>

							<a href="PlantillaImportarEmpresas.csv" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" role="button">
								<i class="fa fa-download mr-2"></i> Descargar Plantilla
							</a>
						</div>

						<?PHP
							if ($ListError!="") {
								echo $ListError;
							}
						?>

					</div>
				</div>

			</form>
			</div>
		</div>
		</div>

		<div class="clearfix"> </div>


		<?php include '../footer.php'; ?>
		<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	</body>
</html>
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
					$ListError='<div class="alert alert-danger"><strong>Advertencia!</strong> El rut '.$LRut.', No es valido.<br>Operaci&oacute;n Cancelada</div><br>';
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
					$ListError='<div class="alert alert-danger"><strong>Advertencia!</strong> El rut '.$LRut.', Ya esta registrado.<br>Operaci&oacute;n Cancelada</div><br>';
                    break;
				}

				if(valida_rut($LRut)==false){
					$ListError='<div class="alert alert-danger"><strong>Advertencia!</strong> El rut '.$LRut.', No son validos.<br>Operaci&oacute;n Cancelada</div><br>';
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
				$ListError='<div class="alert alert-danger"><strong>Informativo</strong> Error al intentar procesar el archivo, puede ser que no contenga datos, verifique la estructura del mismo y vuelva a procesar.<br> Si el error persiste Contacte al administrador del Sistema...</div><br>';
			}

			if ($ListError=="") {
          		$ListError='<div class="alert alert-success"><strong>Informativo</strong> El archivo fue procesado con Exito.</div><br>';
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
		<link rel="stylesheet" href="../css/bootstrap.min.css">
			<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="../css/StConta.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

		<div class="container-fluid text-left">
		<div class="row content">
			<h3 class="text-center">Carga Masiva Empresas</h3>
			<form name="importar" method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" class="form-horizontal">
				<!-- <div class="col-sm-2">
				</div> -->
				<div class="col-sm-12">


					<div class="form-group">
						<label class="control-label col-sm-2" for="file">Seleccionar Archivo</label>
						<div class="col-sm-10">
							<input type="file" class="form-control-file" id="file" name="file" aria-describedby="fileHelp">
							<small id="fileHelp" class="form-text text-muted">* Solo archivo CSV.</small><br>
							<!-- <small id="fileHelp" class="form-text text-muted">** Solo se procesar&aacute;án documentos que no est&eacute;n cargados previamente.</small> -->
							<input type="hidden" value="upload" name="action" />
						</div>
					</div> 


					<div class="form-group">
						<label class="control-label col-sm-2" for="file">Separador</label>
						<div class="col-sm-10">
							<input type="text" name="separador" value="<?php echo $DLIST; ?>" id="separador">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="file">Seleccionar Archivo</label>
						<div class="col-sm-10">
							<button type="submit" class="btn btn-grabar btn-block">Procesar</button>
						<!-- <span>* Este proceso eliminara todos los Haberes y Descuento Previamente Cargado, para el periodo <?php echo $Periodo; ?></span> -->
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="file">Plantilla</label>
						<div class="col-sm-10">
							<a href="PlantillaImportarEmpresas.csv" class="btn btn-exportar" role="button">
								<span class="glyphicon glyphicon-download-alt"></span> Descargar
							</a>
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-sm-2" for="file"></label>
						<div class="col-sm-12">
						<?PHP
							if ($ListError!="") {
								echo $ListError;
							}
						?>
						</div>
					</div> 

					<div class="clearfix"></div>


				</div>

			</form>

		</div>
		</div>

		<div class="clearfix"> </div>


		<?php include '../footer.php'; ?>

	</body>
</html>
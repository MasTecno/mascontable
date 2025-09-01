<?php
	include 'conexionserver.php';
	include '../js/funciones.php';
	session_start();

	$XMensaje=nl2br($_POST['comment']);

	$archivos = $_FILES["archivos"]["tmp_name"];
	$nombre_archivos = $_FILES["archivos"]["name"];
	$ruta_archivos =  $_FILES["archivos"]["tmp_name"];
	$tamanio = $_FILES["archivos"]["size"];
	$XNCosrto=$_POST['TCorto'];
	$XTCorreo=$_POST['TCorreo'];

	$mysqli=conectarServer();

	$SQL="SELECT * FROM UnionServer WHERE id='".$_POST['ListServ']."' AND estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$xServer=$registro['Server'];	
	}

	$mysqli->close();

	$i=0;
	$SumaPeso=0;
	foreach ($ruta_archivos as $rutas_archivos) {
		$SumaPeso=$SumaPeso+$tamanio[$i];
		$i++;
	}

	if ($SumaPeso>1200000) {
		session_destroy();
		header("location:frmEnviar.php?Msj=95");
		exit;
	}


$CodSeg='		<!-- Set up the path for the initial page view -->
		<script>
		var _hsq = window._hsq = window._hsq || [];
		_hsq.push([\'setPath\', \'/home\']);
		</script>

		<!-- Load the HubSpot tracking code -->
		<!-- Start of HubSpot Embed Code -->
		<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/5294235.js"></script>
		<!-- End of HubSpot Embed Code -->

		<!-- Tracking subsequent page views -->
		<script>
		var _hsq = window._hsq = window._hsq || [];
		_hsq.push([\'setPath\', \'/about-us\']);
		_hsq.push([\'trackPageView\']);
		</script>';


	$TextCorreo='
<html>
	<head>
		<title>Notificaci&oacute;n</title>
		<link rel="important stylesheet" href="chrome://messagebody/skin/messageBody.css">

		<style type="text/css">
			body {
				background-color: #F6F6F6;
			}
		</style>

	</head>

<body>

	<br>
	<style type="text/css">
		body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }
	</style>

<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
	<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
	<tr>
		<td align="center" valign="top" style="padding:20px 0 20px 0">
			<!-- [ header starts here] -->
			<table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
				<tr>
					<td valign="top">
						<a href="https://www.mastecno.cl/"><img src="https://mascontable.mastecno.cl/Nexus/images/MasTecnoCorreo.png" alt="Logo MasTecno" style="margin-bottom:10px;" border="0"/></a>
					</td>
				</tr>
			<!-- [ middle starts here] -->
			<tr>
				<td valign="top">
					
				<p style="border:1px solid #E0E0E0; font-size:12px; line-height:16px; margin:0; padding:13px 18px; background:#f9f9f9;">
				<br/>
				
				'.$XMensaje.'

				<br>
				
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

<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/5294235.js"></script>
<!-- End of HubSpot Embed Code -->
'.$CodSeg.'
</body>
</body>
</html>
	';

	$destino = $XTCorreo; //"samuel@mastecno.cl"; // correo del destino 
	

	require_once('PHPMailer_5.2.1/class.phpmailer.php'); // Especificamos la utilización de la librería PHPMailer 5.2.1 contenida en el directorio actual
	$mail = new PHPMailer(true); //el parámetro 'true' significa que lanzará excepciones en los errores que se produzcan, las cuales deben ser capturadas
	
	$mail->IsSMTP(); //le decimos a la clase que utilice SMTP

	try {
		$mail->SMTPDebug  = 0; // activa la información SMTP de depuración (para pruebas)
		$mail->SMTPAuth   = true; //activa autenficicación SMTP
		$mail->SMTPSecure = "ssl";                 // especifica la seguridad SMTP
		$mail->Host       = "mail.mastecno.cl";      // especificamos la dirección del servidor de correo 
		$mail->Port       = 465;                   // puerto del servidor de correo
		$mail->Username   = "notificaciones@mastecno.cl";  // usuario del correo origen
		$mail->Password   = "Samuel2019";         //contraseña del correo origen
		$mail->AddAddress($destino, ''); // dirección de correo destino
		$mail->SetFrom("samuel@mastecno.cl", "Samuel Santander - MasTecno"); // especificamos el origen del correo
		$mail->addCC("samuel@mastecno.cl");
		$mail->addBCC("5294235@bcc.hubspot.com");
		$mail->Subject = "Factura MasTecno"; // titulo del email
		$mail->MsgHTML($TextCorreo); // cuerpo del email

		if ($SumaPeso>0) {
			$i=0;
			foreach ($ruta_archivos as $rutas_archivos) {
				$mail->AddAttachment($rutas_archivos,$nombre_archivos[$i]);
				$i++;
			}
		}

		$mail->Send();

	} catch (phpmailerException $e) {
		echo $e->errorMessage(); //Excepción de PHPMailer
		exit;
	} catch (Exception $e) {
		echo $e->getMessage(); //Cualquier otra excepción
		exit;
	}


	foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name){

		//Validamos que el archivo exista
		if($_FILES["archivos"]["name"][$key]) {
			$filename = $_FILES["archivos"]["name"][$key]; //Obtenemos el nombre original del archivo
			$source = $_FILES["archivos"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivo
			

			$NomDoc= $_FILES["archivos"]["name"][$key];
			$NumDoc=str_replace ("FE-","",$NomDoc);
			$NumDoc=str_replace ("FI-","",$NumDoc);
			$NumDoc=str_replace (".pdf","",$NumDoc);


			$directorio = '../Facturas/Archivos/'; //Declaramos un  variable con la ruta donde guardaremos los archivos
			
			//Validamos si la ruta de destino existe, en caso de no existir la creamos
			if(!file_exists($directorio)){
				mkdir($directorio, 0777) or die("No se puede crear el directorio de extracci&oacute;n");	
			}
			
			$dir=opendir($directorio); //Abrimos el directorio de destino
			$target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivo
			
			//Movemos y validamos que el archivo se haya cargado correctamente
			//El primer campo es el origen y el segundo el destino
			if(move_uploaded_file($source, $target_path)) {	
				// echo "El archivo $filename se ha almacenado en forma exitosa.<br>";
				} else {	
				echo "Ha ocurrido un error, por favor inténtelo de nuevo.<br>";
				exit;
			}
			closedir($dir); //Cerramos el directorio de destino
		}
	}

	$mysqli=conectarServer();

	$CodigoDoc=randomTextSV(35);
	$i=0;

	while ($i==0) {
		// echo "<br>";
		$SQL1="SELECT * FROM FacturasMastecno WHERE Codigo='".$CodigoDoc."'";
		$resultados1 = $mysqli->query($SQL1);
		$row_cnt = $resultados1->num_rows;
		if ($row_cnt==0) {	
			$mysqli->query("UPDATE FacturasMastecno SET Codigo='".$CodigoDoc."' WHERE id='".$registro['id']."'");
			$i=1;
		}else{
			$CodigoDoc=randomTextSV(35);
		}
	}

	$mysqli->query("UPDATE DatosPersonales SET Monto='".$_POST['TMonto']."' WHERE idServer='".$_POST['ListServ']."'");

	$mysqli->query("INSERT INTO FacturasMastecno VALUES('','".$_POST['ListServ']."','$xServer','".$_POST['TxtPeriodo']."','".date("Y-m-d")."','$CodigoDoc','$NumDoc','$NomDoc','0000-00-00','0000-00-00','".$_POST['TMonto']."','A')");

	$mysqli->query("INSERT INTO LogEnvio VALUES('','$xServer','".$_POST['TxtPeriodo']."','".date("Y-m-d")."','A')");

	$mysqli->close();

	header("location:frmEnviar.php?Msj=2");
?>
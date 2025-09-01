<?php
	include 'conexion/conexionmysqli.php';
	include 'js/funciones.php';
	include 'conexion/secciones.php';

	$mysqli=ConCobranza();
	
	if(isset($_POST['swDoc']) && $_POST['swDoc']=="P"){
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "maspagos@mastecno.cl";
		$to = $_SESSION['ServCorreo'];//"samuel@mastecno.cl";
		$subject = "Pago Realizado ".$_SESSION['NomServer'];
		$message = "Estimada(o): ".utf8_decode($_SESSION['ServTecnico']).", El ".$_SESSION['NomServer']." comenta que el pago ya esta relizado, favor revisar a la brevedad. El jefecito";
		
		// Set proper mail headers
		$headers = array(
			'From: ' . $from,
			'Reply-To: ' . $from,
			'X-Mailer: PHP/' . phpversion(),
			'Content-Type: text/plain; charset=UTF-8'
		);
		
		// Send mail without logging
		mail($to, $subject, $message, implode("\r\n", $headers));
	}

    $IdServer="";
	$SQL="SELECT * FROM Servidores WHERE Nombre='".$_SESSION['NomServer']."' AND Estado='A'";
	$resultados = $mysqli->query($SQL);
	while ($registro = $resultados->fetch_assoc()) {
		$IdServer=$registro["Id"];
	}

	if(isset($_POST['swDoc']) && $_POST['swDoc']=="P"){
		$fecha_actual = date("d-m-Y");
		$fecha_actual=date("Y-m-d",strtotime($fecha_actual."+ 1 days")); 

		$SQL="INSERT INTO Avisos VALUES('','".$IdServer."','".date("Y-m-d")."','A');";
		$mysqli->query($SQL);
		$SQL="INSERT INTO Avisos VALUES('','$IdServer','$fecha_actual','A');";
		$mysqli->query($SQL);

	}else{
		$SQL="INSERT INTO Avisos VALUES('','".$IdServer."','".date("Y-m-d")."','A');";
		$mysqli->query($SQL);
	}
    
    $mysqli->close();
    
	$_SESSION['DocInpagos']=0;
?>
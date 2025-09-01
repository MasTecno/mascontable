<?php

	session_start();

	if(!isset($_SESSION['ROL']) || $_SESSION['ROL']!="Samito"){
		header("location:../index.php?Msj=95");
		exit;
	}

	include 'conexionserver.php';
	include 'conexion.php';
	$mysqli=conectarServer();

	$sql = "SELECT * FROM UnionServer WHERE id='".$_POST['sel1']."'";
	$resultado = $mysqli->query($sql);

	while ($registro = $resultado->fetch_assoc()) {
		echo $xusu=$registro["Usuario"];
		echo "<br>";
		echo $xcla=$registro["Clave"];
		echo "<br>";
		echo $xbas=$registro["Base"];
		echo "<br>";
		echo "<br>";
	}
	$mysqli->close();



// echo $_POST['SqlScript'];


// exit;






	$mysqliX=xconectar($xusu,$xcla,$xbas);


	$StrSql=$_POST['SqlScript'];

	if (mysqli_multi_query($mysqliX, $StrSql)) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $_POST['SqlScript'] . "<br>" . mysqli_error($mysqliX);
	}




	// echo $_POST['SqlScript'];

	// $mysqliX->query($_POST['SqlScript']);




	$mysqliX->close();

?>
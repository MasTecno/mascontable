<?php 

    include 'conexion/conexion.php';
    echo $_POST['d1'];

	$resultado="";
	
	conectar();

	//mysql_query("UPDATE CTRegDocumentos SET Xkey='$Xkey' WHERE Correo='$TxtCorreo'");

    desconectar();

 ?>
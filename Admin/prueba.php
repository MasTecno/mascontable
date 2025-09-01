<?php

session_cache_limiter('nocache,private');

date_default_timezone_set('America/Santiago');

// function conectarServer(){
// 	mysql_connect('localhost', 'mastecno_UMC', 'fBvJq3ynvfBE');
// 	mysql_select_db('mastecno_unionMasCon');
// }

// function desconectarServer(){
// 	mysql_close();
// }

?>

<?php


// Conectarse a y seleccionar una base de datos de MySQL llamada sakila
// Nombre de host: 127.0.0.1, nombre de usuario: tu_usuario, contraseña: tu_contraseña, bd: sakila
$mysqli = new mysqli('localhost', 'mastecno_UMC', 'fBvJq3ynvfBE', 'mastecno_unionMasCon');

// ¡Oh, no! Existe un error 'connect_errno', fallando así el intento de conexión
if ($mysqli->connect_errno) {
    // La conexión falló. ¿Que vamos a hacer? 
    // Se podría contactar con uno mismo (¿email?), registrar el error, mostrar una bonita página, etc.
    // No se debe revelar información delicada

    // Probemos esto:
    echo "Lo sentimos, este sitio web está experimentando problemas.";

    // Algo que no se debería de hacer en un sitio público, aunque este ejemplo lo mostrará
    // de todas formas, es imprimir información relacionada con errores de MySQL -- se podría registrar
    echo "Error: Fallo al conectarse a MySQL debido a: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";
    
    // Podría ser conveniente mostrar algo interesante, aunque nosotros simplemente saldremos
    exit;
}

// Realizar una consulta SQL
$sql = "SELECT * FROM UnionServer WHERE estado = 'A'";
if (!$resultado = $mysqli->query($sql)) {
    // ¡Oh, no! La consulta falló. 
    echo "Lo sentimos, este sitio web está experimentando problemas.";

    // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
    // cómo obtener información del error
    echo "Error: La ejecución de la consulta falló debido a: \n";
    echo "Query: " . $sql . "\n";
    echo "Errno: " . $mysqli->errno . "\n";
    echo "Error: " . $mysqli->error . "\n";
    exit;
}

// ¡Uf, lo conseguimos!. Sabemos que nuestra conexión a MySQL y nuestra consulta
// tuvieron éxito, pero ¿tenemos un resultado?
if ($resultado->num_rows === 0) {
    // ¡Oh, no ha filas! Unas veces es lo previsto, pero otras
    // no. Nosotros decidimos. En este caso, ¿podría haber sido
    // actor_id demasiado grande? 
    echo "Lo sentimos. No se pudo encontrar una coincidencia para el ID $aid. Inténtelo de nuevo.";
    exit;
}

// Ahora, sabemos que existe solamente un único resultado en este ejemplo, por lo
// que vamos a colocarlo en un array asociativo donde las claves del mismo son los
// nombres de las columnas de la tabla
$registro = $resultado->fetch_assoc();
echo "Server; " . $registro['server'] . ", Base; " . $registro['base'] . " ";

// Ahora, vamor a obtener cinco actores aleatorios y a imprimir sus nombres en una lista.
// El manejo de errores va a ser menor aquí, aunque ya sabemos como hacerlo
	$sql = "SELECT * FROM UnionServer ORDER BY rand() LIMIT 5";
	if (!$resultado = $mysqli->query($sql)) {
	    echo "Lo sentimos, este sitio web está experimentando problemas.";
	    exit;
	}

// Imprimir nuestros cinco actores aleatorios en una lista, y enlazar cada uno
echo "<ul>\n";
while ($registro = $resultado->fetch_assoc()) {
    echo "<li><a href='" . $_SERVER['SCRIPT_FILENAME'] . "?aid=" . $registro['id'] . "'>\n";
    echo $registro['server'] . ' ' . $registro['usuario'];
    echo "</a></li>\n";
}
echo "</ul>\n";

// El script automáticamente liberará el resultado y cerrará la conexión
// a MySQL cuando finalice, aunque aquí lo vamos a hacer nostros mismos
$resultado->free();
$mysqli->close();
?>
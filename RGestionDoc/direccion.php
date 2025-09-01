<?php

// $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";

// // Obtener el host
// $host = $_SERVER['HTTP_HOST'];

// // Obtener la URI solicitada
// $uri = $_SERVER['REQUEST_URI'];

// // Construir la URL completa
// echo $url1 = $https.'://'.$host.$uri;
//echo "<br>";
// echo $url2 = $https.'://'.$host;



$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
// echo "<br>";
// Obtener el host
$host = $_SERVER['HTTP_HOST'];
// echo "<br>";
// Obtener la URI solicitada
$uri = $_SERVER['REQUEST_URI'];
// echo "<br>";
// Construir la URL completa
$url1 = $https.'://'.$host.$uri;
// exit;

// echo "<br>";
$url2 = $https.'://'.$host;
// exit;
// echo "<br>";
// echo "<br>";
// echo "<br>";

if($url1<>$url2){
    header("Location: $url2/index.php?1");
    // echo "Location: $https://$host/index.php?1";
}else{
    // header("Location: ../index.php?1");
    echo "Location: ../index.php?1";
}
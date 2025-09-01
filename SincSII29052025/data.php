<?php
// Supongamos que ya tienes el contenido JSON en la variable $jsonString
// o que lo obtienes de la respuesta que mostraste.

// header('Content-Type: text/csv; charset=UTF-8');
// header('Content-Disposition: attachment; filename="resultado.csv"');

// Decodificar el JSON

if($_POST['swData']=="C"){
    $jsonString=$_POST['logCompra'];
    $File = "logCompra";
}else{
    $jsonString=$_POST['logVenta'];
    $File = "logVenta";
}

// echo $_POST['swData'];

// echo $jsonString;

// exit;
// 1) Encabezados para forzar la descarga como CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$File.'.csv"');

// 2) Decodificar el JSON a un array asociativo
$data = json_decode($jsonString, true);

// // Opcionalmente, si quieres combinar los arreglos “33” y “34” en un solo CSV
// $combinedData = array_merge($data["33"], $data["34"]);

// $combinedData = array_merge($data["33"], $data["39"], $data["61"]);


$combinedData = [];

foreach ($data as $key => $val) {
    // Verifica si la propiedad actual ($val) es un array
    if (is_array($val)) {
        // Combina el contenido con $combinedData
        $combinedData = array_merge($combinedData, $val);
    }
}


// 3) Generar el CSV solo si hay registros
if (!empty($combinedData)) {
    // Obtener las columnas (keys) del primer objeto
    $columns = array_keys($combinedData[0]);

    // Abrir la salida estándar en modo escritura
    $output = fopen('php://output', 'w');

    // --- Aquí agregamos el BOM ---
    // El BOM (Byte Order Mark) indica a Excel que el archivo está en UTF-8
    // Sin él, a veces Excel no reconoce los acentos.
    fprintf($output, "\xEF\xBB\xBF");

    // Escribir fila de cabecera con ‘;’ como separador
    fputcsv($output, $columns, ';');

    // Recorrer cada fila (objeto) y escribir sus datos
    foreach ($combinedData as $row) {
        // Tomar valores en el orden de las columnas
        $rowData = [];
        foreach ($columns as $col) {
            // Si la key no existe, poner una cadena vacía
            $rowData[] = $row[$col] ?? '';
        }
        // Escribir la fila con ‘;’ como separador
        fputcsv($output, $rowData, ';');
    }

    // Cerrar el puntero
    fclose($output);
}
exit; // Finalizar el script

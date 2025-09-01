<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$parser = new Parser();
$pdf = $parser->parseFile('05-2025.pdf');
$text = $pdf->getText();
$text = preg_replace('/\s+/', ' ', $text);

// Extraer entradas tipo Código Glosa Valor
preg_match_all('/(\d{3})\s+([A-ZÑÁÉÍÓÚ0-9\.\,\-\(\)\/\s]+?)\s+([\d\.]+)/u', $text, $matches, PREG_SET_ORDER);

// Dividir en dos tablas (cuando aparece código 502)
$tabla1 = [];
$tabla2 = [];
$foundSeparator = false;

foreach ($matches as $entry) {
    $codigo = $entry[1];
    $glosa = trim($entry[2]);
    $valor = $entry[3];

    $row = [$codigo, $glosa, $valor];

    if ($codigo === '502' && !$foundSeparator) {
        $foundSeparator = true;
    }

    if (!$foundSeparator) {
        $tabla1[] = $row;
    } else {
        $tabla2[] = $row;
    }
}

// Crear archivo CSV
$csvFile = fopen('05-2025.csv', 'w');

// Cabecera
fputcsv($csvFile, ['Codigo', 'Glosa', 'Valor']);

// Escribir tabla 1
fputcsv($csvFile, ['=== TABLA 1 ===']);
foreach ($tabla1 as $row) {
    fputcsv($csvFile, $row);
}

// Escribir tabla 2
fputcsv($csvFile, ['=== TABLA 2 ===']);
foreach ($tabla2 as $row) {
    fputcsv($csvFile, $row);
}

fclose($csvFile);

echo "CSV generado correctamente como 05-2025.csv";
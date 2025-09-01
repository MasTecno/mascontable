<?php
// Incluir la librería TCPDF
require_once('../TCPDF/tcpdf.php');

// Clase extendida para personalizar el encabezado y pie de página
class MYPDF extends TCPDF {
    // Encabezado de página
    public function Header() {
        // No mostrar encabezado
    }

    // Pie de página
    public function Footer() {
        // No mostrar pie de página
    }
}

// Crear nueva instancia de PDF
$pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

// Establecer información del documento
$pdf->SetCreator('Sistema Financiero');
$pdf->SetAuthor('SERVICIOS INFORMATICOS VRWEB LTDA.');
$pdf->SetTitle('Estado de Resultado');
$pdf->SetSubject('Estado de Resultado 2021');
$pdf->SetKeywords('TCPDF, PDF, estado, resultado, financiero');

// Establecer información de margen
$pdf->SetMargins(15, 15, 15);

// Eliminar encabezado y pie de página predeterminados
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Establecer fuente predeterminada monoespaciada
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Establecer auto salto de página
$pdf->SetAutoPageBreak(TRUE, 15);

// Establecer factor de escala de imagen
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer algunas cadenas dependientes del idioma
$pdf->setLanguageArray([
    'a_meta_charset' => 'UTF-8',
    'a_meta_dir' => 'ltr',
    'a_meta_language' => 'es',
]);

// Agregar una página
$pdf->AddPage();

// Establecer fuente
$pdf->SetFont('helvetica', '', 10);

// Encabezado
$pdf->Cell(0, 5, 'SERVICIOS INFORMATICOS VRWEB LTDA.', 0, 1, 'L');
$pdf->Cell(0, 5, '', 0, 1, 'L');
$pdf->Cell(100, 5, 'AV.PROVIDENCIA 329 OF 3A, SANTIAGO', 0, 0, 'L');
$pdf->Cell(80, 5, 'Pág.   : 1', 0, 1, 'R');
$pdf->Cell(100, 5, 'R.U.T. 77.392.800-2', 0, 0, 'L');
$pdf->Cell(80, 5, 'Fecha : ' . date('d/m/Y'), 0, 1, 'R');

// Título
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 8, 'ESTADO DE RESULTADO', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Periodo comprendido entre Enero a Diciembre del 2021.', 0, 1, 'L');
$pdf->Ln(2);

// Línea horizontal
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Cell(0, 5, 'RESULTADO OPERACIONAL', 0, 1, 'L');
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(2);

// Función para imprimir líneas de datos
function printLine($pdf, $text, $amount = '', $total = '', $indent = 0, $bold = false) {
    $fontStyle = $bold ? 'B' : '';
    $pdf->SetFont('helvetica', $fontStyle, 10);
    
    $indentSpace = str_repeat(' ', $indent * 4); // 4 espacios por nivel de indentación
    
    if($amount != '' && $total == '') {
        // Línea con texto y monto
        $pdf->Cell(120 - $indent, 5, $indentSpace . $text, 0, 0, 'L');
        $pdf->Cell(60, 5, number_format($amount, 3, '.', '.'), 0, 1, 'R');
    } 
    elseif($total != '') {
        // Línea con texto, monto y total
        $pdf->Cell(110 - $indent, 5, $indentSpace . $text, 0, 0, 'L');
        
        if($amount != '') {
            $pdf->Cell(40, 5, number_format($amount, 3, '.', '.'), 0, 0, 'R');
        } else {
            $pdf->Cell(40, 5, '', 0, 0, 'R');
        }
        
        $pdf->Cell(30, 5, number_format($total, 3, '.', '.'), 0, 1, 'R');
    }
    else {
        // Solo texto
        $pdf->Cell(180, 5, $indentSpace . $text, 0, 1, 'L');
    }
}

// Datos del Estado de Resultado
// Ingresos de Explotación
printLine($pdf, '(+) Ingresos de Explotacion', '', '', 0, true);
printLine($pdf, 'VENTAS NETAS AFECTAS', 808515.381, '', 1);
printLine($pdf, 'VENTAS EXENTAS', 90022.108, '', 1);
printLine($pdf, 'Total  (+) Ingresos de Explotacion', '', 898537.489, 1, true);

// Costos de Explotación
printLine($pdf, '(-) Costos de Explotacion', '', '', 0, true);
printLine($pdf, 'COSTO DE VENTA', -86919.982, '', 1);
printLine($pdf, 'Total  (-) Costos de Explotacion', '', -86919.982, 1, true);

// Margen de Explotación
printLine($pdf, '(=) MARGEN DE EXPLOTACION', '', 811617.507, 0, true);

// Gastos de Administración y Ventas
printLine($pdf, '(-) Gastos de Administracion y Ventas', '', '', 0, true);
printLine($pdf, 'SERVICIOS BASICOS CHILECTRA, AGUA, GAS', -2706.356, '', 1);
printLine($pdf, 'GASTO TELEFONO Y COMUNICACIONES', -13167.618, '', 1);
printLine($pdf, 'GASTOS DE COMPUTACION', -3015.415, '', 1);
printLine($pdf, 'LICENCIAS COMPUTACION Y SOFTWARE', -8023.198, '', 1);
printLine($pdf, 'SEGUROS', -1362.657, '', 1);
printLine($pdf, 'HONORARIOS', -2237.297, '', 1);
printLine($pdf, 'GASTOS DE NOTARIA', -76.100, '', 1);
printLine($pdf, 'REMUNERACIONES', -458502.295, '', 1);
printLine($pdf, 'SEGURO COMPL. SALUD', -10511.364, '', 1);
printLine($pdf, 'CONSUMO CHEQUE RESTAURANT', -20839.430, '', 1);
printLine($pdf, 'ASESORIAS CONTABLES Y TRIBUTARIAS', -9302.630, '', 1);
printLine($pdf, 'CLIENTES INCOBRABLES', -11692.865, '', 1);
printLine($pdf, 'GASTOS GENERALES', -4096.187, '', 1);
printLine($pdf, 'GASTOS BANCARIOS', -3.748, '', 1);
printLine($pdf, 'GASTOS MOVILIZACION Y TRASLADOS', -199.969, '', 1);
printLine($pdf, 'ARRIENDO', -14084.643, '', 1);
printLine($pdf, 'PATENTES', -2884.529, '', 1);
printLine($pdf, 'DEPRECIACION ACTIVO FIJO', -3901.220, '', 1);
printLine($pdf, 'GASTOS NO ACEPTADOS', -14773.874, '', 1);
printLine($pdf, 'GASTOS CORREO', -7.272, '', 1);
printLine($pdf, 'IVA CF NO RECUPERABLE', -3516.114, '', 1);
printLine($pdf, 'MANTENCION Y REPARACION', -342.462, '', 1);
printLine($pdf, 'GASTOS DE PUBLICIDAD', -4222.000, '', 1);
printLine($pdf, 'MULTAS E INTERESES', -63.063, '', 1);
printLine($pdf, 'FINIQUITOS', -13330.651, '', 1);
printLine($pdf, 'LEYES SOCIALES', -20107.599, '', 1);
printLine($pdf, 'GRATIFICACIONES', -19208.657, '', 1);
printLine($pdf, 'COMISIONES', -50584.403, '', 1);
printLine($pdf, 'SOFTWARE EMMA-LISA-INES', -25148.633, '', 1);
printLine($pdf, 'GASTOS COMUNES OFICINA Y OTROS', -6619.196, '', 1);
printLine($pdf, 'COLACION Y LOCOMOCION', -10313.600, '', 1);
printLine($pdf, 'OTROS GASTOS GENERALES', -1039.976, '', 1);
printLine($pdf, 'GASTOS SISTEMAS COMP.', -2660.774, '', 1);
printLine($pdf, 'Total  (-) Gastos de Administracion y Ventas', '', -758625.803, 1, true);

// Resultado Operacional
printLine($pdf, '(=) RESULTADO OPERACIONAL', '', 52991.704, 0, true);

// Ingresos Financieros
printLine($pdf, '(+) Ingresos Financieros', '', '', 0, true);
printLine($pdf, 'Total  (+) Ingresos Financieros', '', 0, 1, true);

// Utilidad Inversión Empresas Relacionadas
printLine($pdf, '(+) Utilidad Inversion Empresas Relacionadas', '', '', 0, true);
printLine($pdf, 'Total  (+) Utilidad Inversion Empresas Relacionada', '', 0, 1, true);

// Otros Ingresos Fuera de Explotación
printLine($pdf, '(+) Otros Ingresos Fuera de Explotacion', '', '', 0, true);
printLine($pdf, 'OTROS INGRESOS', 6145.816, '', 1);
printLine($pdf, 'Total  (+) Otros Ingresos Fuera de Explotacion', '', 6145.816, 1, true);

// Añadir página 2
$pdf->AddPage();

// Encabezado página 2
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'SERVICIOS INFORMATICOS VRWEB LTDA.', 0, 1, 'L');
$pdf->Cell(0, 5, '', 0, 1, 'L');
$pdf->Cell(100, 5, 'AV.PROVIDENCIA 329 OF 3A, SANTIAGO', 0, 0, 'L');
$pdf->Cell(80, 5, 'Pág.   : 2', 0, 1, 'R');
$pdf->Cell(100, 5, 'R.U.T. 77.392.800-2', 0, 0, 'L');
$pdf->Cell(80, 5, 'Fecha : ' . date('d/m/Y'), 0, 1, 'R');

// Título página 2
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 8, 'ESTADO DE RESULTADO', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Periodo comprendido entre Enero a Diciembre del 2021.', 0, 1, 'L');
$pdf->Ln(2);

// Línea horizontal
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Cell(0, 5, 'RESULTADO OPERACIONAL', 0, 1, 'L');
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(2);

// Pérdida Inversión Empresas Relacionadas
printLine($pdf, '(-) Perdida Inversion Empresas Relacionadas', '', '', 0, true);
printLine($pdf, 'Total  (-) Perdida Inversion Empresas Relacionadas', '', 0, 1, true);

// Amortización Menor Valor de Inversiones
printLine($pdf, '(-) Amortizacion Menor Valor de inversiones', '', '', 0, true);
printLine($pdf, 'Total  (-) Amortizacion Menor Valor de Inversiones', '', 0, 1, true);

// Gastos Financieros
printLine($pdf, '(-) Gastos Financieros', '', '', 0, true);
printLine($pdf, 'INTERESES', -123.205, '', 1);
printLine($pdf, 'COMISIONES Y CARGOS BANCARIOS', -1316.309, '', 1);
printLine($pdf, 'Total  (-) Gastos Financieros', '', -1439.514, 1, true);

// Otros Egresos Fuera de Explotación
printLine($pdf, '(-) Otros Egresos Fuera de Explotacion', '', '', 0, true);
printLine($pdf, 'Total  (-) Otros Egresos Fuera de Explotacion', '', 0, 1, true);

// Corrección Monetaria
printLine($pdf, '(+) Correccion Monetaria', '', '', 0, true);
printLine($pdf, 'CM DE ACCIONES', 3.169, '', 1);
printLine($pdf, 'DIF. DE CAMBIO', -215.689, '', 1);
printLine($pdf, 'CM PAGOS PROVISIONALES', 151.663, '', 1);
printLine($pdf, 'REAJUSTE DE FONDOS MUTUOS', 8792.054, '', 1);
printLine($pdf, 'REAJUSTES VARIOS', 2684.562, '', 1);
printLine($pdf, 'Total  (+) Correccion Monetaria', '', 11415.759, 1, true);

// Resultados finales
printLine($pdf, '(=) RESULTADO NO OPERACIONAL', '', 16122.061, 0, true);
printLine($pdf, '(=) RESULTADO ANTES IMPUESTO A LA RENTA', '', 69113.765, 0, true);

// Impuesto a la Renta
printLine($pdf, '(-) Impuesto a la Renta', '', '', 0, true);
printLine($pdf, 'IMPUESTO A LA RENTA', -6204.192, '', 1);
printLine($pdf, 'Total  (-) Impuesto a la Renta', '', -6204.192, 1, true);

// Resultados finales
printLine($pdf, '(+) Utilidad (Perdida) Consolidada', '', 0, 0, true);
printLine($pdf, '(±) Interes Minoritario', '', 0, 0, true);
printLine($pdf, '(=) UTILIDAD (PERDIDA) LIQUIDA', '', 62909.573, 0, true);
printLine($pdf, '(+) Amortizacion Mayor Valor de Inversiones', '', 0, 0, true);
printLine($pdf, '(=) UTILIDAD (PERDIDA) DEL EJERCICIO', '', 62909.573, 0, true);

// Espacio para firma
$pdf->Ln(20);
$pdf->Cell(60, 0, '', 'T', 1, 'C');
$pdf->Cell(60, 5, 'R.U.T. 77.392.800-2', 0, 1, 'C');

// Salida del PDF
$pdf->Output('estado_resultado.pdf', 'I');
?>
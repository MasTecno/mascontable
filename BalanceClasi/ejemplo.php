<?php
require_once('../TCPDF/tcpdf.php');

// Extend TCPDF with custom functions
class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 15, 'Servicios de Asesorías Informáticas', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Boletas y Facturas con LibreDTE www.libredte.cl', 0, false, 'C');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Invoice');
$pdf->SetSubject('Invoice');
$pdf->SetKeywords('TCPDF, PDF, invoice, LibreDTE');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Company details
$pdf->MultiCell(0, 5, 'Psj Patricio Lynch 810, Rengo
+56947338027 / www.mastecno.cl / masinfo@mastecno.cl
R.U.T.: 76.917.161-4', 0, 'L');

// Invoice title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'FACTURA ELECTRÓNICA', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 5, 'N° 11966', 0, 1, 'C');
$pdf->Cell(0, 5, 'S.I.I. - RANCAGUA', 0, 1, 'C');
$pdf->Cell(0, 5, 'Lunes 5 de agosto del 2024', 0, 1, 'C');

// Client details
$pdf->SetFont('helvetica', '', 10);
$pdf->Ln(5);
$pdf->Cell(30, 5, 'R.U.T.:', 0);
$pdf->Cell(0, 5, '77.748.269-6', 0, 1);
$pdf->Cell(30, 5, 'Razón social:', 0);
$pdf->Cell(0, 5, 'EUROPLASTICOS SPA', 0, 1);
$pdf->Cell(30, 5, 'Giro:', 0);
$pdf->Cell(0, 5, 'RECUPERACION Y RECICLAMIENTO DE OTROS DE', 0, 1);
$pdf->Cell(30, 5, 'Dirección:', 0);
$pdf->Cell(0, 5, 'LOS MILITARES #5650 OFICINA 905, LAS CONDES, Santiago', 0, 1);
$pdf->Cell(30, 5, 'Contacto:', 0);
$pdf->Cell(0, 5, 'adm.control13@gmail.com', 0, 1);

// Invoice items
$pdf->Ln(10);
$pdf->SetFillColor(200, 220, 255);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(90, 6, 'Item', 1, 0, 'C', 1);
$pdf->Cell(20, 6, 'Cant.', 1, 0, 'C', 1);
$pdf->Cell(30, 6, 'Unidad', 1, 0, 'C', 1);
$pdf->Cell(25, 6, 'P. unitario', 1, 0, 'C', 1);
$pdf->Cell(25, 6, 'Total item', 1, 1, 'C', 1);

// $pdf->SetFont('helvetica', '', 10);
// $pdf->Cell(90, 6, 'Arriendo de Sistema Informático', 1);
// $pdf->Cell(20, 6, '1', 1, 0, 'C');
// $pdf->Cell(30, 6, 'UN', 1, 0, 'C');
// $pdf->Cell(25, 6, '50.000', 1, 0, 'R');
// $pdf->Cell(25, 6, '50.000', 1, 1, 'R');
// $pdf->Cell(90, 6, 'Periodo Agosto 2024', 1);
// $pdf->Cell(20, 6, '', 1);
// $pdf->Cell(30, 6, '', 1);
// $pdf->Cell(25, 6, '', 1);
// $pdf->Cell(25, 6, '', 1, 1);

$invoice_items = [
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    [
        'description' => 'Arriendo de Sistema Informático',
        'quantity' => 1,
        'unit' => 'UN',
        'unit_price' => 50000,
        'total' => 50000
    ],
    // Puedes agregar más elementos aquí
];

$total = 0;
foreach ($invoice_items as $item) {
    $pdf->Cell(90, 6, $item['description'], 1);
    $pdf->Cell(20, 6, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 6, $item['unit'], 1, 0, 'C');
    $pdf->Cell(25, 6, number_format($item['unit_price'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(25, 6, number_format($item['total'], 0, ',', '.'), 1, 1, 'R');
    $total += $item['total'];
}


// Totals
$pdf->Ln(10);
$pdf->Cell(140, 6, 'En total son: cincuenta y nueve mil quinientos.', 0);
$pdf->Cell(25, 6, 'Neto $:', 0);
$pdf->Cell(25, 6, '50.000', 0, 1, 'R');
$pdf->Cell(165, 6, 'IVA (19%) $:', 0);
$pdf->Cell(25, 6, '9.500', 0, 1, 'R');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(165, 6, 'Total $:', 0);
$pdf->Cell(25, 6, '59.500', 0, 1, 'R');

// SII information
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(0, 4, 'Timbre Electrónico SII
Resolución 80 de 2014
Verifique documento: www.sii.cl', 0, 'C');

$pdf->Output('invoice.pdf', 'I');
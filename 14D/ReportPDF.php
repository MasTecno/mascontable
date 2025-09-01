<?php

    $Report=$_POST['Report'];
    require_once($Report);

    if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
        header("location:../index.php?Msj=95");
        exit;
    }

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    if($Periodo==""){
        header("location:../frmMain.php");
        exit;
    }

    /////Datos adicionales

    $libro="LibroCaja_".$RutEmpresa;

    if ($_POST['Movi']=="Ing") {
        $libro="LibroIngresos_".$RutEmpresa;
    }
    if ($_POST['Movi']=="Egr") {
        $libro="LibroEgresos_".$RutEmpresa;
    }

    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
    $SQL="SELECT * FROM CTEmpresas WHERE rut='$RutEmpresa'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        $xNOM=$registro['razonsocial']; 
        $xRUT=$registro['rut'];
        $xDIR=$registro['direccion']; 
        $xCUI=$registro['ciudad']; 
        $xGIR=$registro['giro'];  
        $xRrep=$registro['rut_representante'];     
        $xRep=$registro['representante'];    
    }
    $mysqli->close();	

    $MarTop=20;
    if (isset($_POST['ConMem'])) {
        if (strlen($xRUT)==9) {
            $RutPunto1=substr($xRUT,-10,1);
        }else{
            $RutPunto1=substr($xRUT,-10,2);
        }
        
        $RutPunto2=substr($xRUT,-5);
        $RutPunto3=substr($xRUT,-8,3);
        $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

        $TMembrete = "Contribuyente: ".$xNOM."<br>";
        $TMembrete = $TMembrete."Rut: ".$srtRut."<br>";
        $TMembrete = $TMembrete."Domicilio: ".$xDIR."<br>";
        $TMembrete = $TMembrete."Cuidad: ".$xCUI."<br>";
        $TMembrete = $TMembrete."Giro: ".$xGIR."<br>";
        $MarTop=$MarTop+10;
    }

    if (isset($_POST['ConRep'])) {
        if (strlen($xRrep)==9) {
            $RutPunto1=substr($xRrep,-10,1);
        }else{
            $RutPunto1=substr($xRrep,-10,2);
        }
        
        $RutPunto2=substr($xRrep,-5);
        $RutPunto3=substr($xRrep,-8,3);
        $srtRut=$RutPunto1.".".$RutPunto3.".".$RutPunto2;

        $TMembrete =  $TMembrete."Rep. Legal: ".$xRep."<br>";
        $TMembrete = $TMembrete."Rep. Rut: ".$srtRut."<br>";
        $MarTop=$MarTop+5;
    }

    if (isset($_POST['MarSup'])) {
        $nlines=$_POST['nlines'];
        $i=1;
        while ($i <= $nlines) {
            $MarTop=$MarTop+2;
            $i++;
        }
    }

    $Folio=0;
    if ($_POST['folio']>0 && isset($_POST['MarFol'])){
        $Folio=$_POST['folio'];
    }

    $HTML1='
    <style>
        body {
            font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
            font-size: 6px;
        }

        td {
            font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
            font-size: 6px;
        }

        th {
            font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
            font-size: 6px;
        }
    </style>
    ';

    $HTML1=$HTML1.$HTML;
// exit;
    // Incluye la biblioteca TCPDF
    ini_set('memory_limit', '-1');
    require_once('../TCPDF/tcpdf.php');

    class MYPDF extends TCPDF {
        // Contador inicializado
        public $contador;

        // Datos del encabezado
        public $membrete;

        // Constructor
        public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false, $inicio, $headerText) {
            parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
            $this->contador = $inicio;
            $this->membrete = $headerText;
        }

        // Función de encabezado sobrescrita
        public function Header() {
            // Set font
            $this->SetFont('helvetica', '', 7);

            if($this->contador>0){
                // Título
                $this->Cell(0, 15, '' . $this->contador, 0, false, 'R', 0, '', 0, false);
                // Incrementa el contador
                $this->contador++;
            }
            // Inserta el texto del encabezado
            $this->writeHTMLCell($w=0, $h=0, $x='20', $y='10', $this->membrete, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);
        }

        // Sobrescribe el método Footer() para eliminar el número de página en el pie de página
        public function Footer() {
            // Deja el método Footer vacío
        }
    }

    // Crea el objeto PDF
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, false, $Folio, $TMembrete);

    // Configura el documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MasTecno Spa - MasContable');
    $pdf->SetTitle($libro);
    $pdf->SetSubject('');
    $pdf->SetKeywords('PDF');

    // Configura la fuente del encabezado y del pie de página
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Establece el margen predeterminado del encabezado y el pie de página
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetMargins(PDF_MARGIN_LEFT, $MarTop, PDF_MARGIN_RIGHT);
    // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Establece el factor de escala de la imagen
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


    // Añade algunas páginas
    $pdf->AddPage();
    $pdf->writeHTML($HTML1, true, false, true, false, '');

    // Genera el PDF
    $pdf->Output($libro.'.pdf', 'I');
?>
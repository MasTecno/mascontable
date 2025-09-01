<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
        header("location:../?Msj=95");
        exit;
    }

    if(!isset($_SESSION['NOMBRE']) || $_SESSION['NOMBRE']==""){
        header("location:../?Msj=95");
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

    $SQL="SELECT * FROM CTParametros WHERE estado='A'";
    $resultados = $mysqli->query($SQL);
    while ($registro = $resultados->fetch_assoc()) {
        if($registro['tipo']=="SEPA_MILE"){
            $DMILE=$registro['valor'];  
        }

        if($registro['tipo']=="SEPA_DECI"){
            $DDECI=$registro['valor'];  
        }

        if($registro['tipo']=="NUME_DECI"){
            $NDECI=$registro['valor'];  
        } 
    }



    $HTML='
        <table width="100%" border="1">

                <tr style="background-color: #ccc;">
                    <td colspan="7" style="text-align: center;"><strong>Cartola</strong></td>
                    <td colspan="3" style="text-align: center;"><strong>Contabilidad</strong></td>
                </tr>
                <tr style="background-color: #ccc;">
                    <td widht="1%"><strong>N</strong></td>
                    <td width="9%" style="text-align: center;"><strong>Fecha</strong></td>
                    <td width="27%"><strong>Glosa</strong></td>
                    <td width="8%" style="text-align: right;"><strong>Abono</strong></td>
                    <td width="7%" style="text-align: right;"><strong>Cargo</strong></td>
                    <td width="7%" style="text-align: right;"><strong>Rut</strong></td>
                    <td width="4%" style="text-align: right;"><strong>N&uacute;mero</strong></td>
                    <td width="5%" style="text-align: center;"><strong>Comprobante</strong></td>
                    <td width="4%" style="text-align: center;"><strong>Tipo</strong></td>
                    <td width="28%"><strong>Glosa</strong></td>
                </tr>

    ';

            $Cont=1;
            $IdCab=$_POST['EdiCon'];
            // $mysqli=xconectar($_SESSION['UsuariaSV'],descript($_SESSION['PassSV']),$_SESSION['BaseSV']);
            $SqlStr="SELECT * FROM CTConciliacionDet WHERE IdCab='$IdCab' ORDER BY Fecha ASC, Id ASC";
            $Resultado = $mysqli->query($SqlStr);
            while ($Registro = $Resultado->fetch_assoc()) {

                $IdDiario="";
                $SStr="SELECT * FROM CTConciliacionLog WHERE IdCab='$IdCab' AND IdDet='".$Registro['Id']."'";
                $Res = $mysqli->query($SStr);
                while ($Reg = $Res->fetch_assoc()) {
                    $IdDiario=$Reg['IdDiario'];											
                }										

                $DComp="";
                $DGlosa="";
                $DTMovio="";
                $SStr="SELECT * FROM CTRegLibroDiario WHERE id='".$IdDiario."' AND rutempresa='$RutEmpresa' AND glosa<>''";
                $Res = $mysqli->query($SStr);
                while ($Reg = $Res->fetch_assoc()) {
                    $DComp=$Reg['ncomprobante'];
                    $DGlosa=$Reg['glosa'];		

                    if ($Reg["tipo"]=="E") {
                        $DTMovio="Egreso";
                    }
                    if ($Reg["tipo"]=="I") {
                        $DTMovio="Ingreso";	
                    }
                    if ($Reg["tipo"]=="T") {
                        $DTMovio="Traspaso";
                    }
                }
                if($Registro['Numero']>0){
                    $DNumDoc=$Registro['Numero'];
                }else{
                    $DNumDoc="";
                }
                if($DComp!=""){
                    $HTML=$HTML.'
                        <tr>
                            <td>'.$Cont.'</td>
                            <td style="text-align: center;">'.date('d-m-Y',strtotime($Registro['Fecha'])).'</td>
                            <td>'.$Registro['Glosa'].'</td>
                            <td style="text-align: right;">'.number_format($Registro['Abonos'], $NDECI, $DDECI, $DMILE).'</td>
                            <td style="text-align: right;">'.number_format($Registro['Cargos'], $NDECI, $DDECI, $DMILE).'</td>
                            <td style="text-align: right;">'.$Registro['Rut'].'</td>
                            <td style="text-align: right;">'.$DNumDoc.'</td>
                            <td style="text-align: center;">'.$DComp.'</td>
                            <td style="text-align: center;">'.$DTMovio.'</td>
                            <td>'.$DGlosa.'</td>
                        </tr>
                    ';
                    $Cont++;
                }
            }

    $HTML=$HTML.'

        </table>
        <br><br>
    ';


$LinCargo='
        <table width="100%" border="1">
        <thead>
            <tr>
                <th widht="1%">N</th>
                <th style="text-align: center;">Fecha</th>
                <th>Glosa</th>
                <th style="text-align: right;">Abono</th>
                <th style="text-align: right;">Rut</th>
                <th style="text-align: right;" widht="1%">N&uacute;mero</th>
            </tr>
        </thead>
        <tbody>
';
$LinAbono='
    <table width="100%" border="1">
    <thead>
        <tr>
            <th widht="1%">N</th>
            <th style="text-align: center;">Fecha</th>
            <th>Glosa</th>
            <th style="text-align: right;">Cargo</th>
            <th style="text-align: right;">Rut</th>
            <th style="text-align: right;" widht="1%">N&uacute;mero</th>
        </tr>
    </thead>
    <tbody>
';

    $SqlStr="SELECT * FROM CTConciliacionDet WHERE IdCab='$IdCab' ORDER BY Fecha ASC, Id ASC";
    $Resultado = $mysqli->query($SqlStr);
    while ($Registro = $Resultado->fetch_assoc()) {

        $IdDiario="";
        $SStr="SELECT * FROM CTConciliacionLog WHERE IdCab='$IdCab' AND IdDet='".$Registro['Id']."'";
        $Res = $mysqli->query($SStr);
        while ($Reg = $Res->fetch_assoc()) {
            $IdDiario=$Reg['IdDiario'];											
        }										

        if($Registro['Numero']>0){
            $DNumDoc=$Registro['Numero'];
        }else{
            $DNumDoc="";
        }

        if($IdDiario=="" && $Registro['Abonos']>0){
            $LinAbono=$LinAbono.'
                <tr>
                    <td>'.$Cont.'</td>
                    <td style="text-align: center;">'.date('d-m-Y',strtotime($Registro['Fecha'])).'</td>
                    <td>'.$Registro['Glosa'].'</td>
                    <td style="text-align: right;">'.number_format($Registro['Abonos'], $NDECI, $DDECI, $DMILE).'</td>
                    <td style="text-align: right;">'.$Registro['Rut'].'</td>
                    <td style="text-align: right;">'.$DNumDoc.'</td>
                </tr>
            ';
            $Cont++;
        }

        if($IdDiario=="" && $Registro['Cargos']>0){
            $LinCargo=$LinCargo.'
                <tr>
                    <td>'.$Cont.'</td>
                    <td style="text-align: center;">'.date('d-m-Y',strtotime($Registro['Fecha'])).'</td>
                    <td>'.$Registro['Glosa'].'</td>
                    <td style="text-align: right;">'.number_format($Registro['Cargos'], $NDECI, $DDECI, $DMILE).'</td>
                    <td style="text-align: right;">'.$Registro['Rut'].'</td>
                    <td style="text-align: right;">'.$DNumDoc.'</td>
                </tr>
            ';
            $Cont++;
        }
    }

$LinAbono=$LinAbono.'
        </tbody>
    </table>
    ';
$LinCargo=$LinCargo.'
    </tbody>
</table>
';




    $HTML=$HTML.'
        <table width="100%" border="1">
            <tr>
                <th width="100%" align="center" style="background-color: #ccc;">Abonos Bancarios No Contabilizados</th>

            </tr>
            <tr>
                <td valign="top">'.$LinAbono.'</td>
            </tr>
        </table>
    ';

    $HTML=$HTML.'
        <br>
        <br>
        <table width="100%" border="1">
            <tr>
                <th width="100%" align="center" style="background-color: #ccc;">Cargos Bancarios No Contabilizados</th>
            </tr>
            <tr>
                <td valign="top">'.$LinCargo.'</td>
            </tr>
        </table>
    ';

// 
   
// exit;


    $mysqli->close();	


    $MarTop=20;
    // if (isset($_POST['ConMem'])) {
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
   
    $Folio=0;
   
    $HTML1='
    <style>
        body {
            font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
            font-size: 7px;
        }

        td {
            font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
            font-size: 7px;
        }

        th {
            font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
            font-size: 7px;
        }
    </style>
    ';

    $HTML1=$HTML1.$HTML;


    $NomArch="ReporteBancario.xls";
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$NomArch.""); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

    echo $HTML;
exit;
    // Incluye la biblioteca TCPDF
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

        // Sobrescribe el método Footer() para eliminar el n&uacute;mero de página en el pie de página
        public function Footer() {
            // Deja el método Footer vacío
        }
    }
    // if($_POST['PageOri']==""){

    // }
    // PageOri

    // Crea el objeto PDF
    // 'L'=horizontal
    // 'P'=vertical
    $pdf = new MYPDF($_POST['PageOri'], PDF_UNIT, 'LETTER', true, 'UTF-8', false, false, $Folio, $TMembrete);

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
    $pdf->Output('Balance8Columnas_'.$_SESSION['RUTEMPRESA'].'.pdf', 'I');
?>
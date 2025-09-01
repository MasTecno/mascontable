<?php
    require('fpdf181/fpdf.php');
    include 'conexion/conexionmysqli.php';
    include 'conexion/secciones.php';

    $frm=$_POST['frm'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];
    $PeriodoX=$_POST['tpediodo'];
    $Pool=0;

    class PDF extends FPDF{

        // Pie de página
        function Footer(){
            // Posición: a 1,5 cm del final
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Número de página
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }

        // Una tabla más completa
        function    TablasDetalle($headerDet,$frm,$RutEmpresa,$PeriodoX,$Pool){

            $ContFila=$Pool;
            $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
            $SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' ORDER BY fecha LIMIT $Pool,22";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {

                $SQL1="SELECT * FROM CTCliPro WHERE rut='".$registro["rut"]."'";
                $resultados1 = $mysqli->query($SQL1);
                while ($registro1 = $resultados1->fetch_assoc()) {
                    $rsocial=$registro1["razonsocial"];
                }

                $SQL1="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
                $resultados1 = $mysqli->query($SQL1);
                while ($registro1 = $resultados1->fetch_assoc()) {
                    $nomdoc=$registro1["nombre"];
                    $operador=$registro1["operador"];
                }

                if($operador=="R"){
                    $operador=-1;
                }else{
                    $operador=1;
                }

                $line=($ContFila=$ContFila+1).";".$PeriodoX.";".$registro["rut"].";".substr($rsocial,0,25).";".$nomdoc.";".$registro["numero"].
                ";".date('d-m-Y',strtotime($registro["fecha"])).
                ";$".number_format(($registro["exento"]*$operador), 0, '', ',').
                ";$".number_format(($registro["neto"]*$operador), 0, '', ',').
                ";$".number_format(($registro["iva"]*$operador), 0, '', ',').
                ";$".number_format(($registro["total"]*$operador), 0, '', ',');

                $data[] = explode(';',$line);
            }

            $SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' ORDER BY fecha";
            $resultados = $mysqli->query($SQL);
            $numero = $resultados->num_rows;
            if (($Pool+22)>=$numero){

                $SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' ORDER BY fecha";
                $resultados = $mysqli->query($SQL);
                while ($registro = $resultados->fetch_assoc()) {

                    $SQL2="SELECT * FROM CTTipoDocumento WHERE id='".$registro["id_tipodocumento"]."'";
                    $resultados2 = $mysqli->query($SQL2);
                    while ($registro2 = $resultados2->fetch_assoc()) {
                      $operador=$registro2["operador"];
                    }

                    if($operador=="R"){
                      $operador=-1;
                    }else{
                      $operador=1;
                    }

                    $texento=$texento+($registro["exento"]*$operador);
                    $tneto=$tneto+($registro["neto"]*$operador);
                    $tiva=$tiva+($registro["iva"]*$operador);
                    $ttotal=$ttotal+($registro["total"]*$operador);              
                }
                $line=";;;;;;Totales;$".number_format($texento, 0, '', ',').";$".number_format($tneto, 0, '', ',').";$".number_format($tiva, 0, '', ',').";$".number_format($ttotal, 0, '', ',');
                $data[] = explode(';',$line);
            }

            $mysqli->close();

            $this->SetFont('Arial','B',15);
            // Movernos a la derecha
            $this->Cell(80);
            // Título
            if ($frm=="V") {
                $this->Cell(30,10,'Libro de Ventas',0,0,'C');
            }else{
                $this->Cell(30,10,'Libro de Compras',0,0,'C');
            }
            $this->Ln();
            //Move to 8 cm to the right
            $this->Cell(80);
            //Texto centrado en una celda con cuadro 20*10 mm y salto de línea
            $this->Cell(30,10,'Periodo: '.$PeriodoX,0,0,'C'); 
            // Salto de línea
            $this->Ln(20);       
            $this->SetFont('Arial','',8);
            $CONT=1;
            // Anchuras de las columnas
            $w = array(10,20, 20, 50, 40, 20, 20, 20, 20, 20, 20);
            // Cabeceras
            for($i=0;$i<count($headerDet);$i++){
                $this->Cell($w[$i],7,$headerDet[$i],1,0,'C');
            }
            $this->Ln();
            // Datos
            foreach($data as $row){
                $this->Cell($w[0],6,$row[0],'LR',0,'R');
                $this->Cell($w[1],6,$row[1],'LR');
                $this->Cell($w[2],6,$row[2],'LR');
                $this->Cell($w[3],6,$row[3],'LR');
                $this->Cell($w[4],6,$row[4],'LR');
                $this->Cell($w[5],6,$row[5],'LR',0,'R');
                $this->Cell($w[6],6,$row[6],'LR',0,'R');
                $this->Cell($w[7],6,$row[7],'LR',0,'R');
                $this->Cell($w[8],6,$row[8],'LR',0,'R');
                $this->Cell($w[9],6,$row[9],'LR',0,'R');
                $this->Cell($w[10],6,$row[10],'LR',0,'R');
                $this->Ln();
            }
            // Línea de cierre
            $this->Cell(array_sum($w),0,'','T');
        }

        function    TablaResumen($headerRes,$frm,$RutEmpresa,$PeriodoX){

            $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
            $SQL="SELECT * FROM CTTipoDocumento WHERE estado='A' ORDER BY id";
            $resultados = $mysqli->query($SQL);
            while ($registro = $resultados->fetch_assoc()) {
                $IDDOC=$registro["id"];
                $Cont=0;
                $Sexento=0;
                $Sneto=0;
                $Siva=0;
                $Stotal=0;

                $SQL1="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' and id_tipodocumento='$IDDOC' ORDER BY fecha";
                $resultados1 = $mysqli->query($SQL1);
                while ($registro1 = $resultados1->fetch_assoc()) {
                        $SQL2="SELECT * FROM CTTipoDocumento WHERE id='".$registro1["id_tipodocumento"]."'";
                        $resultados2 = $mysqli->query($SQL2);
                        while ($registro2 = $resultados2->fetch_assoc()) {
                          $operador=$registro2["operador"];
                        }

                        if($operador=="R"){
                          $operador=-1;
                        }else{
                          $operador=1;
                        }

                        $Cont=$Cont+1;
                        $Sexento=$Sexento+($registro1["exento"]*$operador);
                        $Sneto=$Sneto+($registro1["neto"]*$operador);
                        $Siva=$Siva+($registro1["iva"]*$operador);
                        $Stotal=$Stotal+($registro1["total"]*$operador);
                }

                if ($Cont>0) {

                  $line=($ContFila=$ContFila+1).";".$registro["nombre"].';'.$Cont.';$'.number_format(($Sexento), 0, '', ',').';$'.number_format(($Sneto), 0, '', ',').';$'.number_format(($Siva), 0, '', ',').';$'.number_format(($Stotal), 0, '', ',');    
                  $data[] = explode(';',$line);        
                }
            }
            $mysqli->close();

            $this->SetFont('Arial','B',15);
            // Movernos a la derecha
            $this->Cell(80);
            // Título
            if ($frm=="V") {
                $this->Cell(30,10,'Libro de Ventas',0,0,'C');
            }else{
                $this->Cell(30,10,'Libro de Compras',0,0,'C');
            }
            $this->Ln();
            //Move to 8 cm to the right
            $this->Cell(80);
            //Texto centrado en una celda con cuadro 20*10 mm y salto de línea
            $this->Cell(30,10,'Periodo: '.$PeriodoX,0,0,'C'); 
            // Salto de línea
            $this->Ln(20);       
            $this->SetFont('Arial','',8);
            $CONT=1;
            // Anchuras de las columnas
            $w = array(10, 50, 30, 20, 20, 20, 20);
            // Cabeceras
            for($i=0;$i<count($headerRes);$i++){
                $this->Cell($w[$i],7,$headerRes[$i],1,0,'C');
            }
            $this->Ln();
            // Datos
            foreach($data as $row){
                $this->Cell($w[0],6,$row[0],'LR',0,'R');
                $this->Cell($w[1],6,$row[1],'LR');
                $this->Cell($w[2],6,$row[2],'LR');
                $this->Cell($w[3],6,$row[3],'LR',0,'R');
                $this->Cell($w[4],6,$row[4],'LR',0,'R');
                $this->Cell($w[5],6,$row[5],'LR',0,'R');
                $this->Cell($w[6],6,$row[6],'LR',0,'R');
                $this->Ln();
            }
            // Línea de cierre
            $this->Cell(array_sum($w),0,'','T');
        }


    }


 



    $pdf = new PDF();
    // Títulos de las columnas
    $headerDet = array('ID','PERIODO', 'RUT', 'RAZON SOCIAL', 'DOCUMENTO', 'N. DOC', 'FECHA', 'EXENTO', 'NETO', 'IVA', 'TOTAL');
    $headerRes = array('ID','DOCUMENTO', 'CANT. DOC.', 'EXENTO', 'NETO', 'IVA', 'TOTAL');
    // Carga de datos
    //$data = $pdf->LoadData();
    // $pdf->SetFont('Arial','',8);
    
    //$pdf->AddPage();
    // $pdf->BasicTable($header,$data);
    $pdf->AliasNbPages(); 
    $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $SQL="SELECT * FROM CTRegDocumentos WHERE estado='A' AND tipo='$frm' AND rutempresa='$RutEmpresa' AND periodo='$PeriodoX' ORDER BY fecha";
        $resultados = $mysqli->query($SQL);
        $numero = $resultados->num_rows;

        while ($Pool< $numero) {
            $pdf->AddPage('L','Letter');
            $pdf->TablasDetalle($headerDet,$frm,$RutEmpresa,$PeriodoX,$Pool);
            $Pool=$Pool+22;
            //$pdf->AddPage();
        }
    $mysqli->close();

    $pdf->AddPage('L','Letter');
    $pdf->TablaResumen($headerRes,$frm,$RutEmpresa,$PeriodoX);


    //$pdf->ImprovedTable($header,$data);

    // $pdf->AliasNbPages();    
    // $pdf->AddPage();
    // $pdf->FancyTable($header,$data);
    $pdf->Output();
?>
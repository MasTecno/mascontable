<?php
	session_start();
	//$Periodo=$_SESSION['PERIODOPC'];
	$RutEmpresa=$_SESSION['RUTEMPRESA'];


	if (isset($_POST['messelect']) || isset($_POST['anoselect'])) {
		if ($_POST['messelect']<=9) {
			$_SESSION['PERIODOPC']="0".$_POST['messelect']."-".$_POST['anoselect'];
		}else{
			$_SESSION['PERIODOPC']=$_POST['messelect']."-".$_POST['anoselect'];     
		}
	}else{
		$_SESSION['PERIODOPC']=$_SESSION['PERIODO'];
	}

	$PeriodoX=$_SESSION['PERIODOPC'];

	if (isset($_POST['anual']) && $_POST['anual']==1) {
		$PeriodoX=substr($_SESSION['PERIODOPC'],3,4);
	}

	$NomArch="PlanCta-Emp".$RutEmpresa.".xls";

	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$NomArch.""); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	include 'frmCuentasGrilla.php';

// 	// session_start();
// 	include 'conexion/conexionmysqli.php';
// 	include 'js/funciones.php';
// 	// include 'conexion/secciones.php';


// 	echo $usuario = $_SESSION['UsuariaSV']."ssssss";
//     echo $password = descriptSV($_SESSION['PassSV']);
//     echo $base = $_SESSION['BaseSV'];
// 	exit;

// 	require 'vendor/autoload.php';

// 	use PhpOffice\PhpSpreadsheet\Spreadsheet;
// 	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// 	use PhpOffice\PhpSpreadsheet\Style\Fill;
// 	use PhpOffice\PhpSpreadsheet\Style\Border;

// 	class ExcelExporter {
// 		private $mysqli;
// 		private $spreadsheet;

// 		// $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);


// 		// echo $base;
// 		// , $, $
// 		public function __construct($usuario, $password, $base) {
// 			$this->mysqli = xconectar($usuario, descript($password), $base);
// 			$this->spreadsheet = new Spreadsheet();
// 		}

// 		private function getCategoria($id_categoria) {
// 			$data = ['nombre' => '', 'tipo' => ''];
			
// 			$SQL = "SELECT nombre, tipo FROM CTCategoria WHERE id = ?";
// 			$stmt = $this->mysqli->prepare($SQL);
// 			$stmt->bind_param("s", $id_categoria);
// 			$stmt->execute();
// 			$result = $stmt->get_result();
			
// 			if ($row = $result->fetch_assoc()) {
// 				$data = $row;
// 			}
// 			$stmt->close();
			
// 			return $data;
// 		}

// 		private function getAuxiliarText($auxiliar) {
// 			$auxiliarMap = [
// 				'E' => 'EFECTIVO',
// 				'B' => 'BANCO',
// 				'X' => 'AUXILIAR'
// 			];
// 			return $auxiliarMap[$auxiliar] ?? '';
// 		}

// 		public function exportarCuentas() {
// 			$sheet = $this->spreadsheet->getActiveSheet();
// 			$sheet->setTitle('Cuentas');

// 			// Establecer y formatear cabecera
// 			$headers = ['Código', 'Cuenta', 'Tipo', 'Categoría', 'Ingreso', 'Auxiliar'];
// 			foreach ($headers as $key => $header) {
// 				$col = chr(65 + $key);
// 				$sheet->setCellValue($col . '1', $header);
				
// 				// Formato de cabecera
// 				$sheet->getStyle($col . '1')->applyFromArray([
// 					'font' => ['bold' => true],
// 					'fill' => [
// 						'fillType' => Fill::FILL_SOLID,
// 						'startColor' => ['rgb' => 'E1E1E1']
// 					],
// 					'borders' => [
// 						'allBorders' => [
// 							'borderStyle' => Border::BORDER_THIN
// 						]
// 					]
// 				]);
// 			}

// 			// Consultar datos
// 			$SQL = "SELECT * FROM CTCuentas WHERE estado <> 'X' ORDER BY numero ASC";
// 			$resultados = $this->mysqli->query($SQL);
			
// 			$row = 2;
// 			while ($registro = $resultados->fetch_assoc()) {
// 				// Obtener categoría
// 				$categoria = $this->getCategoria($registro['id_categoria']);
				
// 				// Preparar datos
// 				$data = [
// 					$registro['numero'],
// 					strtoupper($registro['detalle']),
// 					$categoria['tipo'],
// 					$categoria['nombre'],
// 					$registro['ingreso'] == 'S' ? 'SI' : '',
// 					$this->getAuxiliarText($registro['auxiliar'])
// 				];
				
// 				// Llenar fila
// 				foreach ($data as $key => $value) {
// 					$col = chr(65 + $key);
// 					$sheet->setCellValue($col . $row, $value);
					
// 					// Formato de celdas
// 					$sheet->getStyle($col . $row)->applyFromArray([
// 						'borders' => [
// 							'allBorders' => [
// 								'borderStyle' => Border::BORDER_THIN
// 							]
// 						]
// 					]);
// 				}
// 				$row++;
// 			}

// 			// Auto-ajustar columnas
// 			foreach (range('A', 'F') as $col) {
// 				$sheet->getColumnDimension($col)->setAutoSize(true);
// 			}

// 			// Guardar archivo
// 			$writer = new Xlsx($this->spreadsheet);
// 			$filename = 'cuentas_' . date('Y-m-d') . '.xlsx';
			
// 			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// 			header('Content-Disposition: attachment;filename="' . $filename . '"');
// 			header('Cache-Control: max-age=0');
			
// 			$writer->save('php://output');
// 			$this->mysqli->close();
// 		}
// 	}

// // Uso
// $exporter = new ExcelExporter($_SESSION['UsuariaSV'], $_SESSION['PassSV'], $_SESSION['BaseSV']);
// $exporter->exportarCuentas();
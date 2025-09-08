<?php
     
    include '../../conexion/conexionmysqli.php';
    include '../../js/funciones.php';
    include '../../conexion/secciones.php';

class EmpresaController
{
    private $mysqli;
    
    public function __construct() {
        $this->conectar();
    }
    
    private function conectar() {
        try {
            if (!isset($_SESSION['UsuariaSV']) || !isset($_SESSION['PassSV']) || !isset($_SESSION['BaseSV'])) {
                throw new Exception("Sesión no válida o expirada");
            }
            
            $this->mysqli = xconectar($_SESSION['UsuariaSV'], descriptSV($_SESSION['PassSV']), $_SESSION['BaseSV']);
            
            if (!$this->mysqli) {
                throw new Exception("Error al conectar con la base de datos");
            }
        } catch (Exception $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
    
    public static function sanitizar($data) {
        return trim(htmlspecialchars($data));
    }  

    public function cargarEmpresas() {

        try {
            $sql = "SELECT * FROM CTEmpresas WHERE estado <> 'X' ORDER BY razonsocial";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->get_result();
            $empresas = $resultados->fetch_all(MYSQLI_ASSOC);

            $sqlCount = "SELECT count(razonsocial) AS CantEmp FROM CTEmpresas WHERE estado<>'X' ORDER BY razonsocial";
            $stmtCount = $this->mysqli->prepare($sqlCount);
            $stmtCount->execute();

            $resultadosCount = $stmtCount->get_result();
            while ($registro = $resultadosCount->fetch_assoc()) {
                $TotalEmpresa = $registro['CantEmp'];
            }

            $msgEmpresa = $TotalEmpresa." de ".$_SESSION["PlanConta"];
            $fechaActual = new DateTime();
            $fechaComparacion = new DateTime("2024-01-01");
            $msgBloqueo = "";
            if ($TotalEmpresa > $_SESSION['PlanConta'] && $fechaActual > $fechaComparacion) {
                $msgBloqueo = "Alcanz&oacute; el l&iacute;mite de empresas en su plan, puede eliminar empresas para ganar cupos, de lo contrario contactar a su soporte para el aumento de plan.";
                $response = [
                    "warning" => true,
                    "empresas" => $empresas,
                    "msgEmpresa" => $msgEmpresa,
                    "msgBloqueo" => $msgBloqueo
                ];
            }else{
                $response = [
                    "success" => true,
                    "empresas" => $empresas,
                    "msgEmpresa" => $msgEmpresa,
                ];
            }
            
            echo json_encode($response);
        }catch (Exception $e) {
            $response = [
                "error" => true,
                "mensaje" => "Error: " . $e->getMessage()
            ];
            
            echo json_encode($response);
        }

    }

    public function ingresarEmpresa() {
        try {
            
            $input = file_get_contents("php://input");
            $data = json_decode($input, true);
            
            $rut = self::sanitizar($data["rut"]);
            $clasii = self::sanitizar($data["clasii"]);
            $razonsocial = self::sanitizar($data["rsocial"]);
            $finicio = self::sanitizar($data["finicio"] ?? "0000-00-00");
            $representante = self::sanitizar($data["representante"] ?? "");
            $correo = $this->sanitizar($data["correo"]);
            $ciudad = self::sanitizar($data["ciudad"]);
            $direccion = self::sanitizar($data["direccion"]);
            $rutrep = self::sanitizar($data["rutrep"]);
            $giro = self::sanitizar($data["giro"]);
            $seleMes = self::sanitizar($data["seleMes"]);
            $seleYear = self::sanitizar($data["seleAno"]);
            $planCta = self::sanitizar($data["plancta"]);

            if ($seleMes <= 9) {
                $periodo = "0".$seleMes."-".$seleYear;
            }else{
                $periodo = $seleMes."-".$seleYear;
            }

            if ($planCta === ""){
                $tPlanCta = "S";
            }else{
                $tPlanCta = $planCta;
            }

            $sqlValidar = "SELECT * FROM CTEmpresas WHERE rut = ?";
            $stmtValidar = $this->mysqli->prepare($sqlValidar);
            $stmtValidar->bind_param("s", $rut);
            $stmtValidar->execute();
            $resultados = $stmtValidar->get_result();
            
            $row_cnt = $resultados->num_rows;
            
            if ($row_cnt > 0) {
                throw new Exception("La empresa ya existe");
            }


            $SQL = "INSERT INTO CTEmpresas (razonsocial, rut_representante, representante, rut, direccion, ciudad, 
            correo, clave, giro, fechainicio, periodo, comprobante, ccosto, plan, estado, user) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $clave = "";
            $comprobante = "S";
            $ccosto = "S";
            $estado = "A";
            $user = "0";

            $stmt = $this->mysqli->prepare($SQL);
            $stmt->bind_param("ssssssssssssssss", $razonsocial, $rutrep, $representante, 
            $rut, $direccion, $ciudad, $correo, $clave, $giro, $finicio, $periodo, $comprobante, $ccosto, $tPlanCta, $estado, $user);
            
            if(!$stmt->execute()) {
                throw new Exception("Error al ingresar la empresa");
            }

            if($clasii !== ""){
				$pref = randomTextSV(35);
				$suf = randomTextSV(8);

				$sqlDTE = "SELECT * FROM DTEParametros WHERE RutEmpresa = ? AND Estado='A'";
				$stmtDTE = $this->mysqli->prepare($sqlDTE);
				$stmtDTE->bind_param("s", $rut);
				$stmtDTE->execute();
				$resultados = $stmtDTE->get_result();
				$row_cnt = $resultados->num_rows;

				if ($row_cnt == 0) {
                    $stmtDTE = $this->mysqli->prepare("INSERT INTO DTEParametros (RutEmpresa, RutSii, PasSii, Estado) VALUES(?, ?, ?, ?)");
                    $clave = $pref.$clasii.$suf;
                    $estado = "A";
                    $stmtDTE->bind_param("ssss", $rut, $rut, $clave, $estado);
                    $stmtDTE->execute();
                    $stmtDTE->close();

				}else{
					$stmtDTE = $this->mysqli->prepare("SELECT * FROM DTEParametros WHERE RutEmpresa = ? AND Estado = ? AND RutSii = ? AND PasSii = ?");
					$estado = "A";
                    $stmtDTE->bind_param("ssss", $rut, $estado, $rut, $clasii);
					$stmtDTE->execute();
					$resultados = $stmtDTE->get_result();
					$row_cnt = $resultados->num_rows;

					if ($row_cnt == 0) {
						$stmtDTE = $this->mysqli->prepare("UPDATE DTEParametros SET RutSii = ?, PasSii = ? WHERE RutEmpresa = ? AND Estado = ?");
						$clave = $pref.$clasii.$suf;
						$stmtDTE->bind_param("ssss", $rut, $clave, $rut, $estado);
						$stmtDTE->execute();
						$stmtDTE->close();
					}
				}
			}

            if ($planCta == "S") {

                $sql = "SELECT * FROM CTCuentasEmpresa WHERE rut_empresa = ?";
                $stmtCTE = $this->mysqli->prepare($sql);
                $stmtCTE->bind_param("s", $rut);
                $stmtCTE->execute();
                $resultados = $stmtCTE->get_result();
                $row_cnt = $resultados->num_rows;

                if ($row_cnt==0) {
                    $sql = "SELECT * FROM CTCuentas WHERE estado = ?";
                    $stmtCT = $this->mysqli->prepare($sql);
                    $stmtCT->bind_param("s", $estado);
                    $stmtCT->execute();
                    $resultados = $stmtCT->get_result();

                    while ($registro = $resultados->fetch_assoc()) {
        
                        // $this->mysqli->query("INSERT INTO CTCuentasEmpresa VALUES('','".$_POST['rut']."','".$registro['numero']."','".$registro['detalle']."','".$registro['id_categoria']."','".$registro['auxiliar']."','".$registro['ingreso']."','A')");

                        $sql = "INSERT INTO CTCuentasEmpresa (rut_empresa, numero, detalle, id_categoria, auxiliar, ingreso, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmtCTE = $this->mysqli->prepare($sql);
                        $stmtCTE->bind_param("sssssss", $rut, $registro['numero'], $registro['detalle'], $registro['id_categoria'], $registro['auxiliar'], $registro['ingreso'], $estado);
                        $stmtCTE->execute();
                        $stmtCTE->close();
                    }
                }
            }
            
            $response = [
                "success" => true,
                "mensaje" => "Se ingreso una empresa"
            ];
            
            $stmt->close();
            echo json_encode($response);
            
        } catch (Exception $e) {
            $response = [
                "error" => true,
                "mensaje" => "Error: " . $e->getMessage()
            ];
            
            echo json_encode($response);
        }
    }

    public function modificarEmpresa() {
        try {
            if (!isset($_SESSION['UsuariaSV']) || !isset($_SESSION['PassSV']) || !isset($_SESSION['BaseSV'])) {
                throw new Exception("Sesión no válida o expirada");
            }

            $this->mysqli = xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

            $input = file_get_contents("php://input");
            $data = json_decode($input, true);
            
            $id = self::sanitizar($data["idemp"]);
            $rut = self::sanitizar($data["rut"]);
            $clasii = self::sanitizar($data["clasii"]);
            $razonsocial = self::sanitizar($data["rsocial"]);
            $representante = self::sanitizar($data["representante"]);
            $correo = self::sanitizar($data["correo"]);
            $ciudad = self::sanitizar($data["ciudad"]);
            $direccion = self::sanitizar($data["direccion"]);
            $rutrep = self::sanitizar($data["rutrep"]);
            $giro = self::sanitizar($data["giro"]);
            $finicio = self::sanitizar($data["finicio"]);
            $planCta = self::sanitizar($data["plancta"]);
            $seleMes = self::sanitizar($data["seleMes"]);
            $seleYear = self::sanitizar($data["seleAno"]);

            if ($seleMes <= 9) {
                $periodo = "0".$seleMes."-".$seleYear;
            }else{
                $periodo = $seleMes."-".$seleYear;
            }

            if ($planCta === ""){
                $tPlanCta = "S";
            }else{
                $tPlanCta = $planCta;
            }

            $sql = "UPDATE CTEmpresas SET razonsocial = ?, rut_representante = ?, representante = ?, rut = ?, direccion = ?, ciudad = ?,
            correo = ?, giro = ?, fechainicio = ?, periodo = ?, comprobante = ?, ccosto = ?, plan = ?, estado = ?, user = ? WHERE id = ?";

            $comprobante = "S";
            $ccosto = "S";
            $estado = "A";
            $user = "0";

            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ssssssssssssssss", $razonsocial, $rutrep, $representante, $rut, $direccion, $ciudad, $correo , $giro, $finicio, $periodo, $comprobante, $ccosto, $tPlanCta, $estado, $user, $id);
            $stmt->execute();   
            $stmt->close();

            $response = [
                "success" => true,
                "mensaje" => "Se actualizó la empresa"
            ];
            
            echo json_encode($response);
            

        } catch (Exception $e) {
            $response = [
                "error" => true,
                "mensaje" => "Error: " . $e->getMessage()
            ];
            
            echo json_encode($response);
        }
    }

    public function eliminarEmpresa() {
        try {
            if (!isset($_SESSION['UsuariaSV']) || !isset($_SESSION['PassSV']) || !isset($_SESSION['BaseSV'])) {
                throw new Exception("Sesión no válida o expirada");
            }

            $SoloAdmin = 0;
            if($_SESSION["ROL"] == "A") {
                $this->mysqli = xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $id = self::sanitizar($data["id"]);
                $rut = self::sanitizar($data["rut"]);
                $razonsocial = self::sanitizar($data["razonsocial"]);
                
                $stmt = $this->mysqli->prepare("DELETE FROM CT14D WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $SQL="SELECT * FROM CT14TerCab WHERE rutempresa= ?";
                $stmt = $this->mysqli->prepare($SQL);
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $resultados = $stmt->get_result();

                while ($registro = $resultados->fetch_assoc()) {
                    $stmt = $this->mysqli->prepare("DELETE FROM CT14TerDet WHERE idcab = ?");
                    $stmt->bind_param("i", $registro['id']);
                    $stmt->execute();
                    $stmt->close();
                }

                $stmt = $this->mysqli->prepare("DELETE FROM CT14TerCab WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTAnticipos WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTAsiento WHERE rut_empresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();


                $stmt = $this->mysqli->prepare("DELETE FROM CTAsientoApertura WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTAsientoBolEle WHERE rut_empresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTAsientoFondo WHERE rut_empresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTAsientoHono WHERE rut_empresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTAsientoNoBase WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTBoletasDTE WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTCCosto WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTCliProCuenta WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();



                $stmt = $this->mysqli->prepare("DELETE FROM CTComprobanteFolio WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTConciliacionCab WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTConciliacionDet WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTConciliacionLog WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTContadoresAsignado WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTControRegDocPago WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTCuentas14Ter WHERE rut_empresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTCuentasEmpresa WHERE rut_empresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTEmpresas WHERE rut = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTEstResultadoDet WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();


                $stmt = $this->mysqli->prepare("DELETE FROM CTFondo WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();


                $stmt = $this->mysqli->prepare("DELETE FROM CTFondoPersonal WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTHonoGene WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTHonoGeneDeta WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTHonorarios WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTPeriodoEmpresa WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $SQL="SELECT * FROM CTRegDocumentos WHERE rutempresa = ?";
                $stmt = $this->mysqli->prepare($SQL);
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $resultados = $stmt->get_result();

                while ($registro = $resultados->fetch_assoc()) {
                    $stmt = $this->mysqli->prepare("DELETE FROM CTRegDocumentosDiv WHERE Id_Doc = ?");
                    $stmt->bind_param("i", $registro['id']);
                    $stmt->execute();
                    $stmt->close();
                }
                $stmt = $this->mysqli->prepare("DELETE FROM CTRegDocumentos WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTRegLibroDiario WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTRegLibroDiarioCome WHERE rutempresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM CTVoucherT WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM DTEParametros WHERE RutEmpresa = ?");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $stmt->close();


                $fecha = date("Y-m-d");
                $hora = date("H:i:s");

                $stmt = $this->mysqli->prepare("INSERT INTO CTEmpresasLog (rurempresa, razonsocial, fecha, hora, usuario) VALUES(?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $rut, $razonsocial, $fecha, $hora, $_SESSION['NOMBRE']);
                $stmt->execute();
                $stmt->close();

                $this->mysqli->close();

                $response = [
                    "success" => true,
                    "mensaje" => "Se eliminó la empresa"
                ];
                
                echo json_encode($response);
            }else{
                $SoloAdmin = 5;
                $response = [
                    "warning" => true,
                    "mensaje" => "Solo la cuenta administrador puede realizar el proceso de eliminar empresas."
                ];
                
                echo json_encode($response);
            }


            

        } catch (Exception $e) {
            $response = [
                "error" => true,
                "mensaje" => "Error: " . $e->getMessage()
            ];
            
            echo json_encode($response);
        }
    }

    public function estadoEmpresa() {
        try {
            if (!isset($_SESSION['UsuariaSV']) || !isset($_SESSION['PassSV']) || !isset($_SESSION['BaseSV'])) {
                throw new Exception("Sesión no válida o expirada");
            }

            $this->mysqli = xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);

            $input = file_get_contents("php://input");
            $data = json_decode($input, true);
            
            $idEmp = self::sanitizar($data["idEmp"]);

            $sql = "SELECT estado From CTEmpresas WHERE id = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("i", $idEmp);
            $stmt->execute();
            $stmt->bind_result($estado); //* Vincular variable con el resultado
            $stmt->fetch(); //* Asignar el resultado
            $stmt->close();

            $nuevoEstado = ($estado == "A") ? "I" : "A";

            $sqlUpdate = "UPDATE CTEmpresas SET estado = ? WHERE id = ?";
            $stmtUpdate = $this->mysqli->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $nuevoEstado, $idEmp);
        
            if ($stmtUpdate->execute()) {
                $response = [
                    "success" => true,
                    "mensaje" => "Se actualizo el estado",
                ];
            } else {
                throw new Exception($stmtUpdate->error);
            }
            $stmtUpdate->close();

            
            echo json_encode($response);
            
        }catch (Exception $e) {
            $response = [
                "error" => true,
                "mensaje" => "Error: " . $e->getMessage()
            ];
            
            echo json_encode($response);
        }

    }

    public function verificarPermisos() {
        
        $rol = $_SESSION["ROL"];

        if($rol === "A") {
            $permiso = true;
        } else {
            $permiso = false;
        }

        $response = [
            "success" => true,
            "permiso" => $permiso
        ];

        echo json_encode($response);

    }
}

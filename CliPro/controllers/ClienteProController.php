<?php

    include '../../conexion/conexionmysqli.php';
    include '../../js/funciones.php';
    include '../../conexion/secciones.php';
    include '../utils/ErrorLogger.php';

    class ClienteProController {

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

        public function cargarClientePro() {

            try {

                $nomfrm = self::sanitizar($_GET["nomfrm"]);
                $rutEmpresa = self::sanitizar($_SESSION["RUTEMPRESA"]);
                $buscar = self::sanitizar($_GET["buscar"]);

                if($nomfrm === "P") {
                    $sql = "SELECT rut FROM CTRegDocumentos WHERE rutempresa = ? AND (tipo = 'C' OR tipo = 'H') GROUP BY rut";
                }else{
                    $sql = "SELECT rut FROM CTRegDocumentos WHERE rutempresa = ? AND tipo = 'V' GROUP BY rut";
                }

                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("s", $rutEmpresa);
                if (!$stmt->execute()) {
                    throw new Exception("Error ejecutando consulta: " . $stmt->error);
                }
                $resultados = $stmt->get_result();

                $array_ruts = [];

                while($registro = $resultados->fetch_assoc()) {
                    $array_ruts[] = $registro["rut"];
                }

                if($nomfrm === "P"){
                    $sql = "SELECT rut FROM CTHonorarios WHERE rutempresa = ? GROUP BY rut";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("s", $rutEmpresa);
                    if (!$stmt->execute()) {
                        throw new Exception("Error ejecutando consulta honorarios: " . $stmt->error);
                    }
                    $resultados = $stmt->get_result();

                    while ($registro = $resultados->fetch_assoc()) {
                        if (!in_array($registro["rut"], $array_ruts)) {
                            $array_ruts[] = $registro["rut"];
                        }
                    }		
                }

                $clientesProveedores = [];

                foreach($array_ruts as $rut) {
                    //* Buscar
                    if($buscar) {
                        $buscar = "%".$buscar."%";
                        $SQL = "SELECT * FROM CTCliPro WHERE rut = ? AND tipo = ? AND (rut LIKE ? OR razonsocial LIKE ?) ORDER BY razonsocial";
                        $stmt = $this->mysqli->prepare($SQL);
                        $stmt->bind_param("ssss", $rut, $nomfrm, $buscar, $buscar);
                    } else {
                        $SQL = "SELECT * FROM CTCliPro WHERE rut = ? AND tipo = ? ORDER BY razonsocial";
                        $stmt = $this->mysqli->prepare($SQL);
                        $stmt->bind_param("ss", $rut, $nomfrm);
                    }
                    
                    $stmt->execute();
                    $resultados = $stmt->get_result();

                    while ($registro = $resultados->fetch_assoc()) {
                        $cuenta = $registro["cuenta"];
                        $ciudad = $registro["ciudad"];
                        $nuCuenta = $cuenta;
                        $nCuenta = "Sin Cuenta Asignada";

                        
                        if ($rutEmpresa !== "") {
                            $sql1 = "SELECT * FROM CTCliProCuenta WHERE rut = ? AND rutempresa = ?";
                            $stmt1 = $this->mysqli->prepare($sql1);
                            $stmt1->bind_param("ss", $registro["rut"], $rutEmpresa);
                            $stmt1->execute();
                            $resultados1 = $stmt1->get_result();
                            
                            while ($registro1 = $resultados1->fetch_assoc()) {
                                $cuenta = $registro1["cuenta"];
                                $nuCuenta = $registro1["cuenta"];
                            }

                            if ($cuenta != 0) {
                                if ($_SESSION["PLAN"] == "S") {
                                    $sql1 = "SELECT * FROM CTCuentasEmpresa WHERE numero = ? AND rut_empresa = ?";
                                    $stmt1 = $this->mysqli->prepare($sql1);
                                    $stmt1->bind_param("ss", $cuenta, $rutEmpresa);
                                } else {
                                    $sql1 = "SELECT * FROM CTCuentas WHERE numero = ?";
                                    $stmt1 = $this->mysqli->prepare($sql1);
                                    $stmt1->bind_param("s", $cuenta);
                                }
                                
                                $stmt1->execute();
                                $resultados1 = $stmt1->get_result();
                                
                                while ($registro1 = $resultados1->fetch_assoc()) {
                                    $nCuenta = $registro1["detalle"];
                                }
                                $stmt1->close();
                            }
                        }else{

                            if($_SESSION["PLAN"] == "S") {
                                $sql1 = "SELECT * FROM CTCuentasEmpresa WHERE numero = ? AND rut_empresa = ?";
                                $stmt1 = $this->mysqli->prepare($sql1);
                                $stmt1->bind_param("ss", $cuenta, $rutEmpresa);
                            } else {
                                $sql1 = "SELECT * FROM CTCuentas WHERE numero = ?";
                                $stmt1 = $this->mysqli->prepare($sql1);
                                $stmt1->bind_param("s", $cuenta);
                            }

                            $stmt1->execute();
                            $resultados1 = $stmt1->get_result();

                            while ($registro1 = $resultados1->fetch_assoc()) {
                                $nCuenta = $registro1["detalle"];
                            }
                            $stmt1->close();
                        }


                        $clientesProveedores[] = [
                            "id" => $registro["id"],
                            "rut" => $registro["rut"],
                            "razonsocial" => $registro["razonsocial"],
                            "direccion" => $registro["direccion"],
                            "giro" => $registro["giro"],
                            "ciudad" => $ciudad,
                            "correo" => $registro["correo"],
                            "estado" => $registro["estado"],
                            "nuCuenta" => $nuCuenta,
                            "nCuenta" => $nCuenta
                        ];
                    }
                    $stmt->close();
                }

                $response = [
                    "success" => true,
                    "clientesProveedores" => $clientesProveedores
                ];

                echo json_encode($response);
            }catch(Exception $e) {
                echo json_encode([
                    "error" => true,
                    "mensaje" => "Error al cargar los clientes/proveedores: " . $e->getMessage()
                ]);
            }

        }

        public function ingresarClientePro() {

            try {

                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $rut = self::sanitizar($data["rut"]);
                $rsocial = self::sanitizar($data["rsocial"]);
                $direccion = self::sanitizar($data["direccion"]);
                $giro = self::sanitizar($data["giro"]);
                $ciudad = self::sanitizar($data["ciudad"]);
                $correo = self::sanitizar($data["correo"]);
                $cuenta = self::sanitizar($data["cuenta"]);
                $nomfrm = self::sanitizar($data["nomfrm"]);

                $estado = "A";

                $sqlValidar = "SELECT * FROM CTCliPro WHERE rut = ? AND tipo = ?";
                $stmtValidar = $this->mysqli->prepare($sqlValidar);
                $stmtValidar->bind_param("ss", $rut, $nomfrm);
                if (!$stmtValidar->execute()) {
                    throw new Exception("Error ejecutando consulta de validación: " . $stmtValidar->error);
                }
                $resultados = $stmtValidar->get_result();
                
                $row_cnt = $resultados->num_rows;
                $stmtValidar->close();
                
                if($nomfrm === "C") {
                    $titulo = "cliente";
                }else{
                    $titulo = "proveedor";
                }

                if ($row_cnt > 0) {
                    throw new Exception("El $titulo ya existe");
                }

                $sql = "INSERT INTO CTCliPro (rut, razonsocial, direccion, ciudad, giro, correo, cuenta, tipo, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("sssssssss", $rut, $rsocial, $direccion, $giro, $ciudad, $correo, $cuenta, $nomfrm, $estado);
                
                if($stmt->execute()) {
                    $response = [
                        "success" => true,
                        "mensaje" => "Se ingreso un $titulo"
                    ];
                }else{
                    throw new Exception("Error al ingresar el $titulo: " . $stmt->error);
                }

                $stmt->close();
                echo json_encode($response);


            }catch(Exception $e) {
                if($nomfrm === "C") {
                    $titulo = "cliente";
                }else{
                    $titulo = "proveedor";
                }
                echo json_encode([
                    "error" => true,
                    "mensaje" => "Error al ingresar $titulo: " . $e->getMessage()
                ]);
            }
        }

        public function modificarClientePro() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $id = self::sanitizar($data["idemp"]);
                $rut = self::sanitizar($data["rut"]);
                $rsocial = self::sanitizar($data["rsocial"]);
                $direccion = self::sanitizar($data["direccion"]);
                $giro = self::sanitizar($data["giro"]);
                $ciudad = self::sanitizar($data["ciudad"]);
                $correo = self::sanitizar($data["correo"]);
                $cuenta = self::sanitizar($data["cuenta"]);
                $nomfrm = self::sanitizar($data["nomfrm"]);

                $titulo = $nomfrm === "C" ? "cliente" : "proveedor";

                if($_SESSION["PLAN"] === "S") {
                    $SQL= "SELECT * FROM CTCliProCuenta  WHERE rut = ? AND rutempresa = ? AND tipo = ?";
                    $stmt = $this->mysqli->prepare($SQL);
                    $stmt->bind_param("sss", $rut, $_SESSION["RUTEMPRESA"], $nomfrm);
                    if (!$stmt->execute()) {
                        throw new Exception("Error ejecutando consulta: " . $stmt->error);
                    }
                    $resultados = $stmt->get_result();
                    $row_cnt = $resultados->num_rows;
                    $stmt->close();

                    if($row_cnt === 0) {
                        $ccosto = "";
                        $estado = "A";
                        $stmt = $this->mysqli->prepare("INSERT INTO CTCliProCuenta (rutempresa, rut, cuenta, ccosto, tipo, estado) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssss", $_SESSION["RUTEMPRESA"], $rut, $cuenta, $ccosto, $nomfrm, $estado);
                        if (!$stmt->execute()) {
                            throw new Exception("Error ejecutando INSERT: " . $stmt->error);
                        }
                        $stmt->close();

                    } else {
                        $stmt = $this->mysqli->prepare("UPDATE CTCliProCuenta SET cuenta = ? WHERE rut = ? AND rutempresa = ? AND tipo = ?");
                        $stmt->bind_param("ssss", $cuenta, $rut, $_SESSION["RUTEMPRESA"], $nomfrm);
                        if (!$stmt->execute()) {
                            throw new Exception("Error ejecutando UPDATE: " . $stmt->error);
                        }
                        $stmt->close();
                    }

                    $stmt = $this->mysqli->prepare("UPDATE CTCliPro SET razonsocial = ?, direccion = ?, ciudad = ?, correo = ?, giro = ? WHERE id = ?");
                    $stmt->bind_param("ssssss", $rsocial, $direccion, $ciudad, $correo, $giro, $id);
                    if (!$stmt->execute()) {
                        throw new Exception("Error ejecutando UPDATE principal: " . $stmt->error);
                    }
                    $stmt->close();

                } else {
                    $stmt = $this->mysqli->prepare("UPDATE CTCliPro SET razonsocial = ?, direccion = ?, ciudad = ?, correo = ?, giro = ?, cuenta = ? WHERE id = ?");
                    $stmt->bind_param("sssssss", $rsocial, $direccion, $ciudad, $correo, $giro, $cuenta, $id);
                    if (!$stmt->execute()) {
                        throw new Exception("Error ejecutando UPDATE: " . $stmt->error);
                    }
                    $stmt->close();

                }

                $response = [
                    "success" => true,
                    "mensaje" => "Se modifico el $titulo"
                ];

                echo json_encode($response);

            }catch(Exception $e) {
                echo json_encode([
                    "error" => true,
                    "mensaje" => "Error al modificar el cliente/proveedor: " . $e->getMessage()
                ]);
            }
        }

        public function eliminarClientePro() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $id = self::sanitizar($data["id"]);
                $nombre = self::sanitizar($data["nombre"]);

                $stmt = $this->mysqli->prepare("DELETE FROM CTCliPro WHERE id = ?");
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    throw new Exception("Error ejecutando DELETE: " . $stmt->error);
                }else{
                    $response = [
                        "success" => true,
                        "mensaje" => "Se eliminó el $nombre"
                    ];
                }
                $stmt->close();

                echo json_encode($response);
                
            }catch(Exception $e) {
                echo json_encode([
                    "error" => true,
                    "mensaje" => "Error al eliminar el $nombre: " . $e->getMessage()
                ]);
            }
        }

        public function estadoClientePro() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $id = self::sanitizar($data["id"]);

                $sql = "SELECT estado FROM CTCliPro WHERE id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                $estado = "";
                $stmt->bind_result($estado);
                
                if ($stmt->fetch()) {
                    $nuevoEstado = ($estado == "A") ? "B" : "A";
                } else {
                    throw new Exception("No se encontró el cliente/proveedor con ID: " . $id);
                }
                $stmt->close();

                $sqlUpdate = "UPDATE CTCliPro SET estado = ? WHERE id = ?";
                $stmtUpdate = $this->mysqli->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $nuevoEstado, $id);
            
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
                
            }catch(Exception $e) {
                echo json_encode([
                    "error" => true,
                    "mensaje" => "Error al actualizar el estado: " . $e->getMessage()
                ]);
            }
        }
        
    }
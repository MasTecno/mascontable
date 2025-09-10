<?php

    include '../../conexion/conexionmysqli.php';
    include '../../js/funciones.php';
    include '../../conexion/secciones.php';

    class CCostosController {

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
        
        public function ingresarCCosto() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $codigo = self::sanitizar($data["codigo"]);
                $nombre = self::sanitizar($data["nombre"]);

                $sql = "SELECT * FROM CTCCosto WHERE codigo = ? AND rutempresa = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("ss", $codigo, $_SESSION["RUTEMPRESA"]);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0) {
                    throw new Exception("El centro de costo ya existe");
                }
                
                $sql = "INSERT INTO CTCCosto (rutempresa, codigo, nombre, estado) VALUES (?, ?, ?, ?)";
                $estado = "A";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("ssss", $_SESSION["RUTEMPRESA"], $codigo, $nombre, $estado);
                
                if($stmt->execute()){
                    $response = [
                        "success" => true,
                        "message" => "CCosto ingresado correctamente"
                    ];
                    echo json_encode($response);
                    return;
                }else{
                    throw new Exception("Error al ingresar CCosto: " . $stmt->error);
                }
                
            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al ingresar CCosto: " . $e->getMessage()
                ];
                
                echo json_encode($response);
            }
        }

        public function cargarCCostos() {

            try {

                if(isset($_GET["buscar"])) {
                    $buscar = self::sanitizar($_GET["buscar"]);
                    $buscar = "%".$buscar."%";
                    $sql = "SELECT * FROM CTCCosto WHERE estado<>'X' AND rutempresa = ? AND (nombre LIKE ? OR codigo LIKE ?) ORDER BY nombre";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("sss", $_SESSION["RUTEMPRESA"], $buscar, $buscar);
                } else {
                    $sql = "SELECT * FROM CTCCosto WHERE estado<>'X' AND rutempresa = ? ORDER BY nombre";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("s", $_SESSION["RUTEMPRESA"]);
                }

                $stmt->execute();
                $resultados = $stmt->get_result();
                $response = [
                    "success" => true,
                    "ccostos" => $resultados->fetch_all(MYSQLI_ASSOC)
                ];
                
                echo json_encode($response);
            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al cargar CCostos: " . $e->getMessage()
                ];
                
                echo json_encode($response);
            }

        }

        public function modificarCCosto() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $id = $this->sanitizar($data["idccosto"]);
                $nombre = $this->sanitizar($data["nombre"]);

                $sql = "UPDATE CTCCosto SET nombre = ? WHERE id = ? AND rutempresa = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("sss", $nombre, $id, $_SESSION["RUTEMPRESA"]);
                $stmt->execute();
                $stmt->close();
                $response = [
                    "success" => true,
                    "message" => "Se modifico el CCosto"
                ];
                echo json_encode($response);
                return;
            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al modificar CCosto: " . $e->getMessage()
                ];
                echo json_encode($response);
                return;
            }
            
        }

        public function eliminarCCosto() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $id = $this->sanitizar($data["id"]);

                $sql = "SELECT * FROM CTRegLibroDiario WHERE ccosto = ? AND rutempresa = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("ss", $id, $_SESSION["RUTEMPRESA"]);
                $stmt->execute();
                $resultados = $stmt->get_result();
                $row_cnt = $resultados->num_rows;

                if ($row_cnt === 0) {
                    $sql = "DELETE FROM CTCCosto WHERE id = ? AND rutempresa = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("is", $id, $_SESSION["RUTEMPRESA"]);
                    
                    if($stmt->execute()){
                        $response = [
                            "success" => true,
                            "message" => "Se eliminó el CCosto"
                        ];
                        echo json_encode($response);
                        return;
                    }else{
                        throw new Exception("Error al eliminar CCosto: " . $stmt->error);
                    }

                }else{
                    $response = [
                        "error" => true,
                        "message" => "Este CCosto tiene movimientos, no se puede eliminar."
                    ];
                    echo json_encode($response);
                    return;
                }
                
            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al eliminar CCosto: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
        }

        public function estadoCCosto() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $id = $this->sanitizar($data["id"]);
                $estado = "";

                $sql = "SELECT estado FROM CTCCosto WHERE id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($estado);
                $stmt->fetch();
                $stmt->close();

                $nuevoEstado = ($estado == "A") ? "B" : "A";
                $sql = "UPDATE CTCCosto SET estado = ? WHERE id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("si", $nuevoEstado, $id);
                $stmt->execute();
                $stmt->close();

                $response = [
                    "success" => true,
                    "message" => "Se actualizo el estado",
                ];
                
                echo json_encode($response);
                return;

            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al cambiar estado CCosto: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
        }
    
    }
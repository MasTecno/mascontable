<?php

    include '../../conexion/conexionmysqli.php';
    include '../../js/funciones.php';
    include '../../conexion/secciones.php';

    class ContadoresController {

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


        public function ingresarContador() {

            try {

                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $rut = self::sanitizar($data["rut"]);
                $nombre = self::sanitizar($data["nombre"]);
                $cargo = self::sanitizar($data["cargo"]);
                $estado = "A";

                $sql = "SELECT * FROM CTContadoresFirma WHERE Rut = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0) {
                    $response = [
                        "error" => true,
                        "message" => "El contador ya fue ingresado"
                    ];
                    echo json_encode($response);
                }else{
                    $sql = "INSERT INTO CTContadoresFirma (Rut, Nombre, Cargo, Estado) VALUES (?, ?, ?, ?)";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("ssss", $rut, $nombre, $cargo, $estado);

                    if($stmt->execute()) {
                        $response = [
                            "success" => true,
                            "message" => "Se ingreso un contador"
                        ];
                        echo json_encode($response);
                    }else{
                        throw new Exception("Error al ingresar un contador: " . $stmt->error);
                    }    
                }

                

            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al ingresar un contador: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
            
        }

        public function cargarContadores() {
            try {

                if(isset($_GET["buscar"])) {
                    $buscar = self::sanitizar($_GET["buscar"]);
                    $buscar = "%".$buscar."%";
                    $sql = "SELECT * FROM CTContadoresFirma WHERE Estado<>'X' AND (Nombre LIKE ? OR Rut LIKE ?) ORDER BY Nombre ASC";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("ss", $buscar, $buscar);
                }else{
                    $sql = "SELECT * FROM CTContadoresFirma WHERE Estado<>'X' ORDER BY Nombre ASC";
                    $stmt = $this->mysqli->prepare($sql);
                }

                $stmt->execute();
                $resultados = $stmt->get_result();
                $contadores = $resultados->fetch_all(MYSQLI_ASSOC);
                $response = [
                    "success" => true,
                    "contadores" => $contadores
                ];
                echo json_encode($response);

            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al cargar contadores: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
        }

        
        public function modificarContador() {

            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                if(isset($data["idmod"])) {   
                    $id = self::sanitizar($data["idmod"]);
                }

                $rut = self::sanitizar($data["rut"]);
                $nombre = self::sanitizar($data["nombre"]);
                $cargo = self::sanitizar($data["cargo"]);

                $sql = "UPDATE CTContadoresFirma SET Rut = ?, Nombre = ?, Cargo = ? WHERE Id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("sssi", $rut, $nombre, $cargo, $id);
                $stmt->execute();
                $stmt->close();

                $response = [
                    "success" => true,
                    "message" => "Se modifico el contador"
                ];
                echo json_encode($response);

            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al modificar contador: " . $e->getMessage()
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
    

        public function eliminarContador() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $id = self::sanitizar($data["id"]);

                $sql = "DELETE FROM CTContadoresFirma WHERE Id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if($stmt->execute()) {
                    $response = [
                        "success" => true,
                        "message" => "Se eliminó el contador"
                    ];
                    echo json_encode($response);
                }else{
                    throw new Exception("Error al eliminar contador: " . $stmt->error);
                }

            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al eliminar contador: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
        }

        public function estadoContador() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                $id = self::sanitizar($data["id"]);

                $estado = "";
                $sql = "SELECT Estado FROM CTContadoresFirma WHERE Id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($estado);
                $stmt->fetch();
                $stmt->close();

                $nuevoEstado = ($estado == "A") ? "B" : "A";
                $sql = "UPDATE CTContadoresFirma SET Estado = ? WHERE Id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("si", $nuevoEstado, $id);
                $stmt->execute();
                $stmt->close();

                $response = [
                    "success" => true,
                    "message" => "Se actualizo el estado",
                ];
                echo json_encode($response);

            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al estado contador: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
        }

    }
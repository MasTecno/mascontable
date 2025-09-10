<?php

    include "../../conexion/conexionmysqli.php";
    include "../../js/funciones.php";
    include "../../conexion/secciones.php";

    class DocumentosController {

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

        public function ingresarDocumento() {

            try {

                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $nombre = self::sanitizar($data["nombre"]);
                $csii = self::sanitizar($data["csii"]);
                $sigla = "";
                $operador = self::sanitizar($data["operador"]);
                $estado = "A";

                $sql = "INSERT INTO CTTipoDocumento (tiposii, sigla, nombre, operador, estado) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->mysqli->prepare($sql);  
                $stmt->bind_param("sssss", $csii, $sigla, $nombre, $operador, $estado);
                
                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "mensaje" => "Documento ingresado"]);
                } else {
                    throw new Exception("Error al ingresar el documento");
                }

                $stmt->close();

            } catch (Exception $e) {
                throw new Exception("Error al ingresar el documento: " . $e->getMessage());
            }


        }

        public function cargarDocumentos() {
            try {
                $sql = "SELECT * FROM CTTipoDocumento WHERE estado<>'X'";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->execute();
                $resultados = $stmt->get_result();
            

                $documentos = [];
                while ($registro = $resultados->fetch_assoc()) {
                    $documentos[] = $registro;
                }

                echo json_encode(["success" => true, "documentos" => $documentos]);

            } catch (Exception $e) {
                echo json_encode(["error" => true, "mensaje" => "Error al cargar los documentos: " . $e->getMessage()]);
            }
        }

        public function modificarDocumento() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $id = $this->sanitizar($data["idmod"]);
                $nombre = $this->sanitizar($data["nombre"]);
                $csii = $this->sanitizar($data["csii"]);
                $operador = $this->sanitizar($data["operador"]);

                $sql = "UPDATE CTTipoDocumento SET tiposii = ?, nombre = ?, operador = ? WHERE id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("sssi", $csii, $nombre, $operador, $id);
                
                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "mensaje" => "Se modifico el documento"]);
                } else {
                    throw new Exception("Error al modificar el documento");
                }
                $stmt->close();

            } catch (Exception $e) {
                echo json_encode(["error" => true, "mensaje" => "Error al modificar el documento: " . $e->getMessage()]);
            }
        }

        public function estadoDocumento() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                $id = $this->sanitizar($data["id"]);
                $estado = "";

                $sql = "SELECT estado From CTTipoDocumento WHERE id = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($estado);
                $stmt->fetch();
                $stmt->close();

                $nuevoEstado = ($estado == "A") ? "B" : "A";

                $sqlUpdate = "UPDATE CTTipoDocumento SET estado = ? WHERE id = ?";
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
                
            } catch (Exception $e) {
                echo json_encode(["error" => true, "mensaje" => "Error al cambiar el estado del documento: " . $e->getMessage()]);
            }
        }
    }

    
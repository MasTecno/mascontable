<?php

    include '../../conexion/conexionmysqli.php';
    include '../../js/funciones.php';
    include '../../conexion/secciones.php';

    class CuentasController {
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

        public function cargarCategorias() {

            try {

                $sql = "SELECT * FROM CTCategoria WHERE estado <> 'X'";
                $resultados = $this->mysqli->query($sql);
                $categorias = $resultados->fetch_all(MYSQLI_ASSOC);
                
                $response = [
                    "success" => true,
                    "categorias" => $categorias
                ];

                echo json_encode($response);
                
            }catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al cargar categorías: " . $e->getMessage()
                ];
                
                echo json_encode($response);
            }
        

            
        }

        public function obtenerCodigo() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $id = $this->sanitizar($data["id"]);
                $estado = "A";
                
                $sql = "SELECT * FROM CTCategoria WHERE id = ? AND estado = ?";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param("ss", $id, $estado);
                $stmt->execute();
                $resultados = $stmt->get_result();
                
                $D1 = "";
                $D2 = "";
                while ($registro = $resultados->fetch_assoc()) {
                    $D1 = $registro["N1"];
                    $D2 = $registro["N2"];
                }

                $like = $D1.$D2."%";
                if ($_SESSION["PLAN"] === "S"){
                    $sql = "SELECT * FROM CTCuentasEmpresa WHERE numero LIKE ? AND rut_empresa = ? ORDER BY numero DESC LIMIT 1";
                    $rut = $_SESSION["RUTEMPRESA"];
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("ss", $like, $rut);
                }else{
                    $sql = "SELECT * FROM CTCuentas WHERE numero LIKE ? ORDER BY numero DESC LIMIT 1";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("s", $like);
                }
                
                $stmt->execute();
                $resultados = $stmt->get_result();
                
                $UltCta = 0;
                while ($registro = $resultados->fetch_assoc()) {
                    $UltCta = $registro["numero"];
                }
            
                echo json_encode(["success" => true, "cta" => $UltCta + 1]);
            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al obtener código: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
        }
        
        public function ingresarCuenta() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $selCat = self::sanitizar($data["selCat"]);
                $numero = self::sanitizar($data["numero"]);
                $nombre = self::sanitizar($data["nombre"]);
                $opt1 = self::sanitizar($data["opt1"]);
                $t1 = self::sanitizar($data["t1"]);

                if($_SESSION["PLAN"] === "S"){
                    $sql = "SELECT * FROM CTCuentasEmpresa WHERE numero = ? AND rut_empresa = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("ss", $numero, $_SESSION["RUTEMPRESA"]);
                }else{
                    $sql = "SELECT * FROM CTCuentas WHERE numero = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("s", $numero);
                }

                $stmt->execute();
                $resultados = $stmt->get_result();
                $row_cnt = $resultados->num_rows;
                
                if ($row_cnt === 0) {
                    $estado = "A";
                    if ($_SESSION["PLAN"] === "S"){
                        $sql = "INSERT INTO CTCuentasEmpresa (rut_empresa, numero, detalle, id_categoria, auxiliar, ingreso, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $this->mysqli->prepare($sql);
                        $stmt->bind_param("sssssss", $_SESSION["RUTEMPRESA"], $numero, $nombre, $selCat, $opt1, $t1, $estado);
                        
                        if($stmt->execute()){
                            $response = [
                                "success" => true,
                                "message" => "Cuenta ingresada correctamente"
                            ];
                            echo json_encode($response);
                            return;
                        }else{
                            $response = [
                                "error" => true,
                                "message" => "Error al ingresar cuenta: " . $stmt->error
                            ];
                            echo json_encode($response);
                            return;
                        }
                    }else{
                        $sql = "INSERT INTO CTCuentas (numero, detalle, id_categoria, auxiliar, ingreso, estado) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $this->mysqli->prepare($sql);
                        $stmt->bind_param("ssssss", $numero, $nombre, $selCat, $opt1, $t1, $estado);
                       
                        if($stmt->execute()){
                            $response = [
                                "success" => true,
                                "message" => "Cuenta ingresada correctamente"
                            ];
                            echo json_encode($response);
                            return;
                        }else{
                            $response = [
                                "error" => true,
                                "message" => "Error al ingresar cuenta: " . $stmt->error
                            ];
                            echo json_encode($response);
                            return;
                        }
                    }
                }else{
                    $response = [
                        "error" => true,
                        "message" => "Cuenta ya existe"
                    ];
                    echo json_encode($response);
                    return;
                }
                
            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al ingresar cuenta: " . $e->getMessage()
                ];
                echo json_encode($response);
                return;
            }
        }

        public function cargarCuentas() {
            try {
                $cuentas = [];
                if($_SESSION["PLAN"] === "S"){
                    $sql = "SELECT * FROM CTCuentasEmpresa WHERE estado <> 'X' AND rut_empresa = ? ORDER BY numero ASC";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("s", $_SESSION["RUTEMPRESA"]);
                }else{
                    $sql = "SELECT * FROM CTCuentas WHERE estado <> 'X' ORDER BY numero ASC";
                    $stmt = $this->mysqli->prepare($sql);
                }

                $stmt->execute();
                $resultados = $stmt->get_result();
                while($registro = $resultados->fetch_assoc()) {
                    $tipCat = "";
                    $sql1 = "SELECT * FROM CTCategoria WHERE id = ?";
                    $stmt1 = $this->mysqli->prepare($sql1);
                    $stmt1->bind_param("i", $registro["id_categoria"]);
                    $stmt1->execute();
                    $resultados1 = $stmt1->get_result();
                    while($registro1 = $resultados1->fetch_assoc()) {
                        $tipCat = $registro1["nombre"];
                        $tipTipo = $registro1["tipo"];
                    }

                    $mens = "";
                    if($registro["ingreso"] === "S"){
                        $mens = "SI";
                    }

                    if($registro["auxiliar"] === "E"){
                        $auxiliar = "EFECTIVO";
                    }else{
                        if($registro["auxiliar"] === "B"){
                            $auxiliar = "BANCO";
                        }else{
                            if($registro["auxiliar"] === "X"){
                                $auxiliar = "AUXILIAR";
                            }else{
                                $auxiliar = "";
                            }
                        }
                    }

                    $cuentas[] = [
                        "id" => $registro["id"],
                        "id_categoria" => $registro["id_categoria"],
                        "opt1" => $registro["auxiliar"],
                        "t1" => $registro["ingreso"],
                        "numero" => $registro["numero"],
                        "detalle" => $registro["detalle"],
                        "tipTipo" => mb_strtoupper($tipTipo, "UTF-8"),
                        "tipCat" => mb_strtoupper($tipCat, "UTF-8"),
                        "mens" => $mens,
                        "auxiliar" => $auxiliar,
                        "estado" => $registro["estado"]
                    ];

                } 

                $response = [
                    "success" => true,
                    "cuentas" => $cuentas
                ];

                echo json_encode($response);
                
            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al cargar cuentas: " . $e->getMessage()
                ];
                echo json_encode($response);
            }
        }

        public function obtenerCuenta() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                
                $id = $this->sanitizar($data["id"]);

                if ($_SESSION["PLAN"]=="S") {
                    $sql = "SELECT * FROM CTCuentasEmpresa WHERE id= ? AND rut_empresa =  ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("is", $id, $_SESSION["RUTEMPRESA"]);
                }else{
                    $sql = "SELECT * FROM CTCuentas WHERE id = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("i", $id);
                }

                $stmt->execute();
                $resultados = $stmt->get_result();
                while($registro = $resultados->fetch_assoc()) {
                    $xnumero = $registro["numero"];
                    $xdetalle = $registro["detalle"];
                    $xidcategoria = $registro["id_categoria"];
                    $xauxiliar = $registro["auxiliar"];
                    $xingreso = $registro["ingreso"];
                }

                $cuenta = [
                    "numero" => $xnumero,
                    "detalle" => $xdetalle,
                    "id_categoria" => $xidcategoria,
                    "auxiliar" => $xauxiliar,
                    "ingreso" => $xingreso
                ];
        
                $response = [
                    "success" => true,
                    "cuenta" => $cuenta
                ];
                echo json_encode($response);
                return;
            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al obtener cuenta: " . $e->getMessage()
                ];
                echo json_encode($response);
                return;
            }
        }

        public function modificarCuenta() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $id = $this->sanitizar($data["idmod"]);
                $selCat = $this->sanitizar($data["selCat"]);
                $numero = $this->sanitizar($data["numero"]);
                $nombre = $this->sanitizar($data["nombre"]);
                $opt1 = $this->sanitizar($data["opt1"]);
                $t1 = $this->sanitizar($data["t1"]);

                if($_SESSION["PLAN"] === "S") {
                    $sql = "UPDATE CTCuentasEmpresa SET detalle = ?, id_categoria = ?, auxiliar = ?, ingreso = ? WHERE id = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("sssss", $nombre, $selCat, $opt1, $t1, $id);
                }else{
                    $sql = "UPDATE CTCuentas SET detalle = ?, id_categoria = ?, auxiliar = ?, ingreso = ? WHERE id = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("sssss", $nombre, $selCat, $opt1, $t1, $id);
                }
                $stmt->execute();
                $stmt->close();
                $response = [
                    "success" => true,
                    "message" => "Se modifico la cuenta"
                ];
                echo json_encode($response);
                return;

            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al modificar cuenta: " . $e->getMessage()
                ];
                echo json_encode($response);
                return;
            }
        }

        public function eliminarCuenta() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $id = $this->sanitizar($data["id"]);

                if ($_SESSION["PLAN"] === "S") {
                    $sql = "SELECT * FROM CTCuentasEmpresa WHERE id = ? AND rut_empresa = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("is", $id, $_SESSION["RUTEMPRESA"]);
                }else{
                    $sql = "SELECT * FROM CTCuentas WHERE id = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("i", $id);
                }
        
                $stmt->execute();
                $resultados = $stmt->get_result();
                while ($registro = $resultados->fetch_assoc()) {
                    $Lxnumero = $registro["numero"];
                }

                if ($_SESSION["PLAN"] === "S"){
                    $sql = "SELECT * FROM CTRegLibroDiario WHERE cuenta = ? AND rutempresa = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("ss", $Lxnumero, $_SESSION["RUTEMPRESA"]);
                    $stmt->execute();

                    $resultados = $stmt->get_result();

                    $row_cnt = $resultados->num_rows;
                    if ($row_cnt === 0) {
                        $sql = "DELETE FROM CTCuentasEmpresa WHERE id = ? AND rut_empresa = ?";
                        $stmt = $this->mysqli->prepare($sql);
                        $stmt->bind_param("is", $id, $_SESSION["RUTEMPRESA"]);
                        $stmt->execute();
                        $stmt->close();
                    }else{
                        $NoElimina = "N";
                        $response = [
                            "error" => true,
                            "message" => "Esta cuenta tiene movimientos, no se puede eliminar."
                        ];
                        echo json_encode($response);
                        return;
                    }
                }else{
                    $sql = "SELECT * FROM CTRegLibroDiario WHERE cuenta = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("s", $Lxnumero);
                    $stmt->execute();

                    $resultados = $stmt->get_result();
                    $row_cnt = $resultados->num_rows;
                    if ($row_cnt === 0) {
                        $sql = "DELETE FROM CTCuentas WHERE id = ?";
                        $stmt = $this->mysqli->prepare($sql);
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $stmt->close();
                    }else{
                        $NoEliminaCom = "N";
                        $response = [
                            "error" => true,
                            "message" => "Esta cuenta tiene movimientos y puede estar utilizada en alguna empresa, ya que es plan de cuenta comun, no se puede eliminar."
                        ];
                        echo json_encode($response);
                        return;
                    }
                }
                
                $response = [
                    "success" => true,
                    "message" => "Se eliminó la cuenta"
                ];
                echo json_encode($response);
                return;

            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al eliminar cuenta: " . $e->getMessage()
                ];
                echo json_encode($response);
                return;
            }
        }

        public function estadoCuenta() {
            try {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);

                $id = $this->sanitizar($data["id"]);

                if ($_SESSION["PLAN"] === "S") {
                    $sql = "SELECT estado FROM CTCuentasEmpresa WHERE id = ? AND rut_empresa = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("is", $id, $_SESSION["RUTEMPRESA"]);
                }else{
                    $sql = "SELECT estado FROM CTCuentas WHERE id = ?";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("i", $id);
                }

                $stmt->execute();

                $resultado = $stmt->get_result();

                while ($registro = $resultado->fetch_assoc()) {
                    $estado = $registro["estado"];
                }

                $nuevoEstado = ($estado == "A") ? "B" : "A";

                $sqlUpdate = "UPDATE CTCuentasEmpresa SET estado = ? WHERE id = ? AND rut_empresa = ?";
                $stmtUpdate = $this->mysqli->prepare($sqlUpdate);
                $stmtUpdate->bind_param("sis", $nuevoEstado, $id, $_SESSION["RUTEMPRESA"]);
                $stmtUpdate->execute();
                $stmtUpdate->close();

                $sqlUpdate = "UPDATE CTCuentas SET estado = ? WHERE id = ?";
                $stmtUpdate = $this->mysqli->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $nuevoEstado, $id);
                $stmtUpdate->execute();
                $stmtUpdate->close();

                $response = [
                    "success" => true,
                    "message" => "Se actualizo el estado",
                    "estado" => $nuevoEstado
                ];
                echo json_encode($response);
                return;

            } catch (Exception $e) {
                $response = [
                    "error" => true,
                    "message" => "Error al cambiar estado de cuenta: " . $e->getMessage()
                ];
                echo json_encode($response);
                return;
            }
        }

    }
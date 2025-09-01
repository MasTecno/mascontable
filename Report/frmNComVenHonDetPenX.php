<?php
    include '../conexion/conexionmysqli.php';
    include '../js/funciones.php';
    include '../conexion/secciones.php';

    $mysqli = xconectar($_SESSION['UsuariaSV'], descriptSV($_SESSION['PassSV']), $_SESSION['BaseSV']);

    $NomCont=$_SESSION['NOMBRE'];
    $Periodo=$_SESSION['PERIODO'];
    $RazonSocial=$_SESSION['RAZONSOCIAL'];
    $RutEmpresa=$_SESSION['RUTEMPRESA'];

    $tidoc = ($_POST['id_tipodocumento'] == "C") ? "V" : (($_POST['id_tipodocumento'] == "P") ? "C" : "H");
    $tCliPro = ($_POST['id_tipodocumento'] == "C") ? "C" : "P";

    if($_POST['fdesde']!="" && $_POST['fhasta']!=""){
        $dia = substr($_POST['fdesde'],0,2);
        $mes = substr($_POST['fdesde'],3,2);
        $ano = substr($_POST['fdesde'],6,4);

        $Lfdesde=$ano."/".$mes."/".$dia;

        $dia = substr($_POST['fhasta'],0,2);
        $mes = substr($_POST['fhasta'],3,2);
        $ano = substr($_POST['fhasta'],6,4);

        $Lfhasta=$ano."/".$mes."/".$dia;
    }

    // Construir la consulta base
    $query = "
        SELECT 
            cd.rut, 
            cd.numero, 
            cd.fecha AS fecha_documento,
            cd.total, 
            cd.tipo AS tipo_documento,
            cd.id_tipodocumento,
            cp.razonsocial,
            ld.fecha AS fecha_movimiento,
            ld.glosa AS observacion,
            ld.tipo AS tipo_comprobante,
            ld.ncomprobante
        FROM 
            CTRegDocumentos cd
        JOIN 
            CTCliPro cp ON cd.rut = cp.rut
        LEFT JOIN
            CTRegLibroDiario ld ON cd.keyas = ld.keyas
        WHERE 
            ld.glosa <>''
            AND cd.rutempresa = ? 
            AND cd.tipo = ?
            AND cp.tipo = ?
    ";

    $params = array($RutEmpresa, $tidoc, $tCliPro);
    $types = "sss";

    // Agregar condición de rut si está presente
    // echo $_POST['trazon'];
    if (!empty($_POST['trazon'])) {
        $query .= " AND cp.rut LIKE ?";
        $params[] = '%' . $_POST['trazon'] . '%';
        $types .= "s";
    }

    // Agregar condición de número de documento si está presente
    if (!empty($_POST['ndocu'])) {
        $query .= " AND cd.numero LIKE ?";
        $params[] = '%' . $_POST['ndocu'] . '%';
        $types .= "s";
    }

    // Agregar condiciones de fecha si están presentes
    if (!empty($Lfdesde) && !empty($Lfhasta)) {
        $query .= " AND ld.fecha BETWEEN ? AND ?";
        $params[] = $Lfdesde;
        $params[] = $Lfhasta;
        $types .= "ss";

        $FiltroDiario = " AND fecha BETWEEN '$Lfdesde' AND '$Lfhasta'"; 
    }

    $query .= " ORDER BY cp.razonsocial, ld.fecha";
    // Preparar la consulta
    $stmt = $mysqli->prepare($query);
    // Bind parameters dinámicamente
    $stmt->bind_param($types, ...$params);
    // Ejecutar la consulta
    $stmt->execute();
    // Obtener resultados
    $result = $stmt->get_result();

// $debugSQL = debugSQL($query, $params, $types);
// echo "SQL completo: " . $debugSQL;

    $currentRut = '';
    $totalAdeudado = 0;
    $totalPagado = 0;
    $totalSaldo = 0;

    while ($row = $result->fetch_assoc()) {
        if ($row['rut'] !== $currentRut) {
            if ($currentRut !== '') {
                // Imprimir subtotal para el rut anterior
                echo "<tr style='font-weight: bold;'>
                        <td colspan='6'>Subtotal</td>
                        <td style='text-align: right;'>" . number_format($subtotalAdeudado, 0, ',', '.') . "</td>
                        <td style='text-align: right;'>" . number_format($subtotalPagado, 0, ',', '.') . "</td>
                        <td style='text-align: right;'>" . number_format($subtotalSaldo, 0, ',', '.') . "</td>
                    </tr>";
                echo "</tbody></table>";
                
                // Reiniciar subtotales
                $subtotalAdeudado = 0;
                $subtotalPagado = 0;
                $subtotalSaldo = 0;
            }
            $currentRut = $row['rut'];
            echo "<h3>{$row['rut']} / {$row['razonsocial']}</h3>";
            echo "<table class=\"table table-hover table-striped\">
                <thead>
                    <tr>
                    <th width=\"10%\">Fecha Doc</th>
                    <th width=\"5%\">N&deg; Doc</th>
                    <th width=\"10%\">T. Doc</th>
                    <th width=\"10%\">T. Comprobante</th>
                    <th width=\"10%\">F. Movimiento</th>
                    <th>Observaci&oacute;n</th>
                    <th width=\"10%\" style=\"text-align: right;\">Adeudado</th>
                    <th width=\"10%\" style=\"text-align: right;\">Pagado</th>
                    <th width=\"10%\" style=\"text-align: right;\">Saldo</th>
                    </tr>
                </thead>
                <tbody>";
        }

        $tipoDoc = getTipoDocumento($mysqli, $row['id_tipodocumento']);
        $tipoDoc = $tipoDoc['sigla'];

        $operaDoc = getTipoDocumentoOpera($mysqli, $row['id_tipodocumento']);
        $operaDoc = $operaDoc['operador'];

        $tipoComprobante = '';
        if ($row['tipo_comprobante']) {
            $tipoComprobante = str_replace(array('T', 'I', 'E'), array('Traspaso', 'Ingreso', 'Egreso'), $row['tipo_comprobante']) . '/' . $row['ncomprobante'];
        }
        $fechaMovimiento = $row['fecha_movimiento'] ? date('d-m-Y', strtotime($row['fecha_movimiento'])) : '';
        $observacion = $row['observacion'];

        if ($operaDoc == "R") { 
            $adeudado = 0;
            $pagado = $row['total'];
            $saldo = $adeudado - $pagado;
        }else{
            $adeudado = $row['total'];
            $pagado = 0;
            $saldo = $adeudado - $pagado;
        }

        // Actualizar subtotales y totales
        $subtotalAdeudado += $adeudado;
        $subtotalPagado += $pagado;
        $subtotalSaldo += $saldo;
        $totalAdeudado += $adeudado;
        $totalPagado += $pagado;
        $totalSaldo += $saldo;

        echo "<tr>
                <td>" . date('d-m-Y', strtotime($row['fecha_documento'])) . "</td>
                <td>{$row['numero']}</td>
                <td>{$tipoDoc}</td>
                <td>{$tipoComprobante}</td>
                <td>{$fechaMovimiento}</td>
                <td>{$observacion}</td>
                <td style=\"text-align: right;\">" . number_format($adeudado, 0, ',', '.') . "</td>
                <td style=\"text-align: right;\">" . number_format($pagado, 0, ',', '.') . "</td>
                <td style=\"text-align: right;\">" . number_format($saldo, 0, ',', '.') . "</td>
            </tr>";

            $SqlPagos="SELECT * FROM CTControRegDocPago WHERE rutempresa='$RutEmpresa' AND rut='".$row['rut']."' AND ndoc='".$row['numero']."' AND id_tipodocumento='".$row['id_tipodocumento']."' AND tipo='$tidoc'";
            $ResulPagos = $mysqli->query($SqlPagos);
            while ($RegPagos = $ResulPagos->fetch_assoc()) {

                $SqlDiarios="SELECT * FROM CTRegLibroDiario WHERE rutempresa='$RutEmpresa' AND glosa<>'' AND keyas='".$RegPagos['keyas']."'";

                if (!empty($Lfdesde) && !empty($Lfhasta)) {
                    $SqlDiarios=$SqlDiarios.$FiltroDiario;
                }

                $ResulDiarios = $mysqli->query($SqlDiarios);
                while ($RegDiarios = $ResulDiarios->fetch_assoc()) {

                        $TMovi = $RegDiarios['tipo'] == 'E' ? 'Egreso' : ($RegDiarios['tipo'] == 'I' ? 'Ingreso' : 'Traspaso');
                        $TMovi = $TMovi. '/' . $RegDiarios['ncomprobante'];
    
                        if ($operaDoc == "R") { 
                            $adeudado = $RegPagos['monto'];
                            $pagado = 0;
                            $saldo = $RegPagos['monto'];
                        }else{
                            $adeudado = 0;
                            $pagado = $RegPagos['monto'];
                            $saldo = -$RegPagos['monto'];
                        }
    
                        echo "<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>$TMovi</td>
                                <td>".date('d-m-Y', strtotime($RegDiarios['fecha']))."</td>
                                <td>".$RegDiarios['glosa']."</td>
                                <td style=\"text-align: right;\">" . number_format($adeudado, 0, ',', '.') . "</td>
                                <td style=\"text-align: right;\">" . number_format($pagado, 0, ',', '.') . "</td>
                                <td style=\"text-align: right;\">" . number_format($saldo, 0, ',', '.') . "</td>
                            </tr>";
    
                        $subtotalAdeudado += $adeudado;
                        $subtotalPagado += $pagado;
                        $subtotalSaldo += $saldo;
                        $totalAdeudado += $adeudado;
                        $totalPagado += $pagado;
                        $totalSaldo += $saldo;
                }
            }
    }

    // Imprimir subtotal para el último rut
    if ($currentRut !== '') {
        echo "<tr style='font-weight: bold;'>
                <td colspan='6'>Subtotal</td>
                <td style='text-align: right;'>" . number_format($subtotalAdeudado, 0, ',', '.') . "</td>
                <td style='text-align: right;'>" . number_format($subtotalPagado, 0, ',', '.') . "</td>
                <td style='text-align: right;'>" . number_format($subtotalSaldo, 0, ',', '.') . "</td>
        </tr>";
        echo "</tbody></table>";
    }

    // Imprimir el total acumulado
    echo "<table class=\"table table-hover table-striped\">
            <thead>
                <tr>
                    <th colspan='6'>Total Acumulado</th>
                    <th style='text-align: right;'>Adeudado</th>
                    <th style='text-align: right;'>Pagado</th>
                    <th style='text-align: right;'>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr style='font-weight: bold;'>
                    <td colspan='6'></td>
                    <td style='text-align: right;'>" . number_format($totalAdeudado, 0, ',', '.') . "</td>
                    <td style='text-align: right;'>" . number_format($totalPagado, 0, ',', '.') . "</td>
                    <td style='text-align: right;'>" . number_format($totalSaldo, 0, ',', '.') . "</td>
                </tr>
            </tbody>
    </table>";

    function debugSQL($query, $params, $types) {
        $indexed = $params; // Crea una copia de los parámetros
        $query = preg_replace_callback('/\?/', function() use (&$indexed, &$types) {
            $type = $types[0];
            $types = substr($types, 1);
            $value = array_shift($indexed);
            if ($type == 's') return "'" . addslashes($value) . "'";
            return $value;
        }, $query);
        return $query;
    }

    // // Después de construir tu query y params, pero antes de preparar la declaración:
    // $debugSQL = debugSQL($query, $params, $types);
    // echo "SQL completo: " . $debugSQL;

    function getTipoDocumento($mysqli, $id_tipodocumento) {
        $stmt = $mysqli->prepare("SELECT sigla FROM CTTipoDocumento WHERE id = ?");
        $stmt->bind_param("s", $id_tipodocumento);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function getTipoDocumentoOpera($mysqli, $id_tipodocumento) {
        $stmt = $mysqli->prepare("SELECT operador FROM CTTipoDocumento WHERE id = ?");
        $stmt->bind_param("s", $id_tipodocumento);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    $stmt->close();
    $mysqli->close();

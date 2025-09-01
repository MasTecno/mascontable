<?php
include '../js/funciones.php';
session_start();
$NomCont=$_SESSION['NOMBRE'];
$Periodo=$_SESSION['PERIODO'];
$RazonSocial=$_SESSION['RAZONSOCIAL'];
$RutEmpresa=$_SESSION['RUTEMPRESA'];

$host = 'localhost';
$dbname = $_SESSION['BaseSV'];
$username = $_SESSION['UsuariaSV'];
$password = descriptSV($_SESSION['PassSV']);


$dia = substr($_POST['fdesde'],0,2);
$mes = substr($_POST['fdesde'],3,2);
$ano = substr($_POST['fdesde'],6,4);

$LFdesde=$ano."-".$mes."-".$dia;

$dia = substr($_POST['fhasta'],0,2);
$mes = substr($_POST['fhasta'],3,2);
$ano = substr($_POST['fhasta'],6,4);

$LFhasta=$ano."-".$mes."-".$dia;



try {
    // Crear una conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para seleccionar los datos
    $sql_select = "SELECT * FROM CTRegLibroDiario WHERE rutempresa LIKE '$RutEmpresa' AND fecha BETWEEN '$LFdesde' AND '$LFhasta' ORDER BY id ASC";
    $stmt = $pdo->prepare($sql_select);
    $stmt->execute();

    //print_r($stmt);

    $host = 'localhost';
    $dbname = 'mastecno_server155';
    $username = 'mastecno_sv155';
    $password = '7wM9szh84TMa';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recorrer los resultados y hacer la inserción en la nueva tabla
    $sql_insert = "INSERT INTO CTRegLibroDiario (periodo,rutempresa,fecha,glosa,cuenta,debe,haber,fechareg,estado,keyas,nfactura,rut,ncomprobante,tipo,iddocref,tipodocref,ccosto,rutreferencia,tiporeferencia,docreferencia)";
    $sql_insert =  $sql_insert." VALUES (:Nperiodo,:Nrutempresa,:Nfecha,:Nglosa,:Ncuenta,:Ndebe,:Nhaber,:Nfechareg,:Nestado,:Nkeyas,:Nnfactura,:Nrut,:Nncomprobante,:Ntipo,:Niddocref,:Ntipodocref,:Nccosto,:Nrutreferencia,:Ntiporeferencia,:Ndocreferencia)";

    $stmt_insert = $pdo->prepare($sql_insert);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Asignar los valores de la fila a los parámetros de la consulta de inserción
        // $stmt_insert->bindParam(':Nid', '');
        $stmt_insert->bindParam(':Nperiodo', $row['periodo']);
        $stmt_insert->bindParam(':Nrutempresa', $row['rutempresa']);
        $stmt_insert->bindParam(':Nfecha', $row['fecha']);
        $stmt_insert->bindParam(':Nglosa', $row['glosa']);
        $stmt_insert->bindParam(':Ncuenta', $row['cuenta']);
        $stmt_insert->bindParam(':Ndebe', $row['debe']);
        $stmt_insert->bindParam(':Nhaber', $row['haber']);
        $stmt_insert->bindParam(':Nfechareg', $row['fechareg']);
        $stmt_insert->bindParam(':Nestado', $row['estado']);
        $stmt_insert->bindParam(':Nkeyas', $row['keyas']);
        $stmt_insert->bindParam(':Nnfactura', $row['nfactura']);
        $stmt_insert->bindParam(':Nrut', $row['rut']);
        $stmt_insert->bindParam(':Nncomprobante', $row['ncomprobante']);
        $stmt_insert->bindParam(':Ntipo', $row['tipo']);
        $stmt_insert->bindParam(':Niddocref', $row['iddocref']);
        $stmt_insert->bindParam(':Ntipodocref', $row['tipodocref']);
        $stmt_insert->bindParam(':Nccosto', $row['ccosto']);
        $stmt_insert->bindParam(':Nrutreferencia', $row['rutreferencia']);
        $stmt_insert->bindParam(':Ntiporeferencia', $row['tiporeferencia']);
        $stmt_insert->bindParam(':Ndocreferencia', $row['docreferencia']);

        // // Ejecutar la inserción
        $stmt_insert->execute();
    }

    echo "Datos transferidos con éxito.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
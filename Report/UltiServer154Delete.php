<?php
include '../js/funciones.php';
session_start();
$NomCont=$_SESSION['NOMBRE'];
$Periodo=$_SESSION['PERIODO'];
$RazonSocial=$_SESSION['RAZONSOCIAL'];
$RutEmpresa=$_SESSION['RUTEMPRESA'];

$dia = substr($_POST['fdesde'],0,2);
$mes = substr($_POST['fdesde'],3,2);
$ano = substr($_POST['fdesde'],6,4);

$LFdesde=$ano."-".$mes."-".$dia;

$dia = substr($_POST['fhasta'],0,2);
$mes = substr($_POST['fhasta'],3,2);
$ano = substr($_POST['fhasta'],6,4);

$LFhasta=$ano."-".$mes."-".$dia;

try{
    $host = 'localhost';
    $dbname = 'mastecno_server155';
    $username = 'mastecno_sv155';
    $password = '7wM9szh84TMa';

    // Crear una conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para seleccionar los datos
    $sql_select = "DELETE FROM CTRegLibroDiario WHERE rutempresa LIKE '$RutEmpresa' AND fecha BETWEEN '$LFdesde' AND '$LFhasta'";
    $stmt = $pdo->prepare($sql_select);
    $stmt->execute();


    echo "Datos eliminados con éxito.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
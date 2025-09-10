<?php
    session_start();
    
    include '../conexion/conexionmysqli.php';
    include '../conexion/secciones.php';
    include '../controllers/CuentasController.php';

    $action = $_GET["action"];

    $controller = new CuentasController();

    switch ($action) {

        case "cargarCategorias":
            $controller->cargarCategorias();
        break;

        case "ctaCont":
            $controller->obtenerCodigo();
        break;

        case "ingresarCuenta":
            $controller->ingresarCuenta();
        break;

        case "cargarCuentas":
            $controller->cargarCuentas();
        break;

        case "obtenerCuenta":
            $controller->obtenerCuenta();
        break;

        case "modificarCuenta":
            $controller->modificarCuenta();
        break;

        case "eliminarCuenta":
            $controller->eliminarCuenta();
        break;

        case "estadoCuenta":
            $controller->estadoCuenta();
        break;

    }
<?php
    session_start();
    
    require_once '../controllers/EmpresaController.php';

    $action = $_GET["action"];

    $controller = new EmpresaController();

    switch ($action) {
        case "ingresarEmpresa":
            $controller->ingresarEmpresa();
        break;

        case "modificarEmpresa":
            $controller->modificarEmpresa();
        break;

        case "cargarEmpresas":
            $controller->cargarEmpresas();
        break;

        case "eliminarEmpresa":
            $controller->eliminarEmpresa();
        break;

        case "estadoEmpresa":
            $controller->estadoEmpresa();
        break;

        case "verificarPermisos":
            $controller->verificarPermisos();
        break;

        case "ingresarRepresentante":
            $controller->ingresarRepresentante();
        break;

        case "cargarRepresentantes":
            $controller->cargarRepresentantes();
        break;

        case "modificarRepresentante":
            $controller->modificarRepresentante();
        break;

        case "eliminarRepresentante":
            $controller->eliminarRepresentante();
        break;
    }
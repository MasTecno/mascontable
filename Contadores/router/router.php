<?php

    include '../controllers/ContadoresController.php';

    $action = $_GET["action"];

    $controller = new ContadoresController();

    switch ($action) {

        case "ingresarContador":
            $controller->ingresarContador();
        break;

        case "modificarContador":
            $controller->modificarContador();
        break;

        case "cargarContadores":
            $controller->cargarContadores();
        break;

        case "verificarPermisos":
            $controller->verificarPermisos();
        break;

        case "eliminarContador":
            $controller->eliminarContador();
        break;

        case "estadoContador":
            $controller->estadoContador();
        break;
    }

?>
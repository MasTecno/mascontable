<?php

    include '../controllers/CCostosController.php';

    $action = $_GET["action"];

    $controller = new CCostosController();

    switch ($action) {

        case "ingresarCCosto":
            $controller->ingresarCCosto();
        break;

        case "cargarCCostos":
            $controller->cargarCCostos();
        break;

        case "modificarCCosto":
            $controller->modificarCCosto();
        break;

        case "eliminarCCosto":
            $controller->eliminarCCosto();
        break;

        case "estadoCCosto":
            $controller->estadoCCosto();
        break;
    }
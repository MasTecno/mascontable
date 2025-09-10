<?php
    session_start();
    
    require_once "../controllers/ClienteProController.php";

    $action = $_GET["action"];

    $controller = new ClienteProController();

    switch ($action) {
        
        case "ingresarClientePro":
            $controller->ingresarClientePro();
        break;

        case "cargarClientePro":
            $controller->cargarClientePro();
        break;

        case "modificarClientePro":
            $controller->modificarClientePro();
        break;

        case "eliminarClientePro":
            $controller->eliminarClientePro();
        break;

        case "estadoClientePro":
            $controller->estadoClientePro();
        break;

    }
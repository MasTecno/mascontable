<?php

    include "../controllers/DocumentosController.php";

    $controller = new DocumentosController();

    $action = $_GET["action"];
    
    switch ($action) {

        case "ingresarDocumento":
            $controller->ingresarDocumento();
        break;

        case "cargarDocumentos":
            $controller->cargarDocumentos();
        break;

        case "modificarDocumento":
            $controller->modificarDocumento();
        break;

        case "estadoDocumento":
            $controller->estadoDocumento();
        break;

    }

?>
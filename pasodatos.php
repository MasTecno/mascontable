<?php

    include 'conexion/conexion.php';
    conectar();
    $SQL="SELECT * FROM  CTRegLibroDiario WHERE  rutempresa='16310843-7'";

    $consulta=mysql_query("$SQL");
    while($registro=mysql_fetch_assoc($consulta))
    {
    	$Periodo=$registro["periodo"];
    	$RutEmpresa="13906244-2";
    	$xfecha=$registro["fecha"];
    	$xglosa=$registro["glosa"];
    	$xcuenta=$registro["cuenta"];
    	$xdebe=$registro["debe"];
    	$xhaber=$registro["haber"];
    	$FECHA=$registro["fechareg"];

		mysql_query("INSERT INTO CTRegLibroDiario VALUE('','$Periodo','$RutEmpresa','$xfecha','$xglosa','$xcuenta','$xdebe','$xhaber','$FECHA','A','')");
    }

    desconectar();
?>
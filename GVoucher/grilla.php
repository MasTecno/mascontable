<?php
include '../conexion/conexionmysqli.php';
include '../js/funciones.php';
include '../conexion/secciones.php';

$NomCont = $_SESSION['NOMBRE'];
$Periodo = $_SESSION['PERIODO'];
$RazonSocial = $_SESSION['RAZONSOCIAL'];
$RutEmpresa = $_SESSION['RUTEMPRESA'];
$frm = $_POST['tdocumentos'];

$mysqli = xconectar($_SESSION['UsuariaSV'], descriptSV($_SESSION['PassSV']), $_SESSION['BaseSV']);

$SQL="SELECT tipo, valor FROM CTParametros WHERE estado='A'";
$resultados = $mysqli->query($SQL);
while ($registro = $resultados->fetch_assoc()) {

    if($registro['tipo']=="IVA"){
        $DIVA=$registro['valor']; 
    }

    if($registro['tipo']=="SEPA_MILE"){
        $DMILE=$registro['valor'];  
    }

    if($registro['tipo']=="SEPA_DECI"){
        $DDECI=$registro['valor'];  
    }

    if($registro['tipo']=="SEPA_LIST"){
        $DLIST=$registro['valor'];  
    }

    if($registro['tipo']=="TIPO_MONE"){
        $DMONE=$registro['valor'];  
    }

    if($registro['tipo']=="NUME_DECI"){
        $NDECI=$registro['valor'];  
    } 
}


echo '
<br>
<div class="col-md-12 centrar-item">
    <button type="button" onclick="seleccionar_todo()" class="btn btn-xs btn-grabar">Marcar Todos</button>
    <button type="button" onclick="deseleccionar_todo()" class="btn btn-cancelar btn-xs">DesMarcar</button><br>
    <samp style="font-size: 11px;">* Grupo maximo de pago 500 Documentos</samp>
</div>
<br>
<table class="table table-hover">
    <thead>
        <tr>
            <th width="1%"></th>
            <th width="1%"></th>
            <th width="10%">Fecha</th>
            <th width="" style="text-align: center;">Periodo</th>
            <th width="5%">N&deg; Doc</th>
            <th width="10%">Rut</th>
            <th>Razon Social</th>
            <th>T. Documento</th>
            <th width="5%" style="text-align: right;"">A/C</th>
            <th width="5%" style="text-align: right;"">Total</th>
            <th width="5%" style="text-align: right;"">Dif</th>
        </tr>
    </thead>
<tbody id="myTable">';

$CodCliPro = ($frm == "V") ? "C" : "P";
if ($frm == "H") {
    $CodCliPro = "P";
}

$baseSQL = ($frm == "H") ?
    "SELECT H.id, H.fecha, H.rutempresa, H.numero, H.periodo, H.rut, C.razonsocial, H.liquido, H.estado, H.movimiento 
    FROM CTHonorarios H 
    INNER JOIN CTCliPro C ON H.rut = C.rut 
    WHERE H.estado='A' AND H.rutempresa='$RutEmpresa' AND H.movimiento<>'' AND C.tipo='$CodCliPro'" :
    "SELECT D.id, D.periodo, D.rutempresa, D.rut, C.razonsocial, D.id_tipodocumento, D.numero, D.fecha, D.total, D.tipo, D.estado, D.lote, D.keyas 
    FROM CTRegDocumentos D 
    LEFT JOIN CTCliPro C ON D.rut = C.rut 
    WHERE D.tipo='$frm' AND D.estado='A' AND D.rutempresa='$RutEmpresa' AND D.keyas<>'' AND C.tipo='$CodCliPro'";

if (!empty($_POST['cadena'])) {
    $search = $mysqli->real_escape_string($_POST['cadena']);
    $baseSQL .= " AND (D.numero LIKE '%$search%' OR D.rut LIKE '%$search%' OR C.razonsocial LIKE '%$search%')";
}

if (isset($_POST['LSelPeriodoDoc']) && $_POST['LSelPeriodoDoc'] != "" && $_POST['LSelPeriodoDoc'] != "T" && $frm != "H") {
    $period = $mysqli->real_escape_string($_POST['LSelPeriodoDoc']);
    $baseSQL .= " AND D.periodo='$period'";
}

if($frm == "H" && isset($_POST['LSelPeriodoDoc']) && $_POST['LSelPeriodoDoc'] != ""){
    $period = $mysqli->real_escape_string($_POST['LSelPeriodoDoc']);
    $baseSQL .= " AND H.periodo='$period'";
}

if($frm == "H"){
    $baseSQL .= " GROUP BY H.id ORDER BY H.fecha";
}else{
    $baseSQL .= " GROUP BY D.id ORDER BY D.fecha";
}

// echo $baseSQL;

$resultados = $mysqli->query($baseSQL);

$con = 1;
while ($registro = $resultados->fetch_assoc()) {
    $NC = substr($registro["keyas"], 0, 2);
    $xsuma = 0;

    if ($frm == "H") {
        $nomdoc = "HONORARIOS";
        $operador = 1;
        $Totalreg = $registro["liquido"];
    } else {
        $docSQL = $mysqli->prepare("SELECT nombre, operador FROM CTTipoDocumento WHERE id=?");
        $docSQL->bind_param("i", $registro["id_tipodocumento"]);
        $docSQL->execute();
        $docSQL->bind_result($nomdoc, $operador);
        $docSQL->fetch();
        $docSQL->close();
        $operador = ($operador == "R") ? -1 : 1;
        $Totalreg = $registro["total"];
    }

    if ($frm == "H") {
        $tipodocT=0;
    }else{
        $tipodocT=$registro["id_tipodocumento"];
    }

    $xsumaSQL = $mysqli->prepare("SELECT SUM(monto) as xsuma FROM CTControRegDocPago WHERE rutempresa=? AND id_tipodocumento=? AND rut=? AND ndoc=? AND tipo=?");
    $xsumaSQL->bind_param("sisss", $RutEmpresa, $tipodocT, $registro["rut"], $registro["numero"], $frm);
    $xsumaSQL->execute();
    $xsumaSQL->bind_result($xsuma);
    $xsumaSQL->fetch();
    $xsumaSQL->close();

    if (is_null($xsuma)) {
        $xsuma = 0;
    }

    $nomcuentaSQL = ($_SESSION["PLAN"] == "S") ?
        $mysqli->prepare("SELECT detalle FROM CTCuentasEmpresa WHERE numero=? AND rut_empresa=?") :
        $mysqli->prepare("SELECT detalle FROM CTCuentas WHERE numero=?");
    
    if ($_SESSION["PLAN"] == "S") {
        $nomcuentaSQL->bind_param("is", $registro["cuenta"], $_SESSION['RUTEMPRESA']);
    } else {
        $nomcuentaSQL->bind_param("i", $registro["cuenta"]);
    }
    $nomcuentaSQL->execute();
    $nomcuentaSQL->bind_result($nomcuenta);
    $nomcuentaSQL->fetch();
    $nomcuentaSQL->close();

    if ($xsuma < $Totalreg && $NC != "NC") {
        echo '
        <tr>
        <td>'.$con++.'</td>
        <td><input type="checkbox" name="check_list[]" value="'.$registro["id"].'" onclick="Calculo()"></td>
        <td>'.date('d-m-Y', strtotime($registro["fecha"])).'</td>
        <td align="center">'.$registro["periodo"].'</td>
        <td align="right">'.$registro["numero"].'</td>
        <td>'.$registro["rut"].'</td>
        <td>'.htmlspecialchars($registro["razonsocial"]).'</td>
        <td>'.htmlspecialchars($nomdoc).'</td>
        <td align="right">$'.number_format(($xsuma * $operador), $NDECI, $DDECI, $DMILE).'</td>
        <td align="right">$'.number_format(($Totalreg * $operador), $NDECI, $DDECI, $DMILE).'</td>
        <td align="right">$'.number_format((($Totalreg * $operador) - ($xsuma * $operador)), $NDECI, $DDECI, $DMILE).'</td>
        <td align="center" ></td>
        </tr>';
    }
}
$mysqli->close();
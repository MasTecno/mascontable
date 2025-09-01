<?php
    include 'conexion/conexionmysqli.php';
    include 'js/funciones.php';
    include 'conexion/secciones.php';

    if (isset($_POST['idempb']) && $_POST['idempb']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTTipoDocumento SET estado='B' WHERE id='".$_POST['idempb']."'");
        $mysqli->close();
    }

    if (isset($_POST['idempa']) && $_POST['idempa']!="") {
        $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
        $mysqli->query("UPDATE CTTipoDocumento SET estado='A' WHERE id='".$_POST['idempa']."'");
        $mysqli->close();
    }

	if(isset($_POST['idmod']) && $_POST['idmod']!=""){
		$sw=1;
		$mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
		$SQL="SELECT * FROM CTTipoDocumento WHERE id='".$_POST['idmod']."'";
		$resultados = $mysqli->query($SQL);
		while ($registro = $resultados->fetch_assoc()) {
			$tiposii=$registro["tiposii"];
			$xdetalle=strtoupper($registro["nombre"]);
			$operador=$registro["operador"];
		} 
		$mysqli->close();
	}


?>
<!DOCTYPE html>
<html>
    <head>
    <title>MasContable</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/MC.ico" type="favicon/ico" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="css/StConta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script type="text/javascript">
        function Baja(valor){
            form1.idempb.value=valor;
            form1.action="#";
            form1.submit();
        }
        function Alta(valor){
            form1.idempa.value=valor;
            form1.action="#";
            form1.submit();
        }
        function Volver(){
            form1.action="frmMain.php";
            form1.submit();
        }

        function Modifi(valor){
            form1.idmod.value=valor;
            form1.action="#";
            form1.submit();
        }

    </script>  
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            color: #fff;
            z-index: 9999;
        }
        .overlay-content {
            text-align: center;
        }
        .overlay img {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }
    </style>

    </head>
    <body onload="<?php if(isset($_GET['Exito']) && $_POST['idmod']=="") { echo "showMessage()";}?>">

        <?php 
            include 'nav.php';
        ?>

        <div class="container-fluid text-left">
        <div class="row content">

            <div id="overlay" class="overlay">
                <div class="overlay-content">
                    <img src="https://img.icons8.com/ios-filled/100/ffffff/checkmark.png" alt="Check">
                    <h1>Enviado Satisfactoriamente</h1>
                </div>
            </div>

            <div class="col-sm-2">
            </div>

            <div class="col-sm-8 text-left">
                <br>
                <div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
                    <div class="panel-heading">Registros de Documentos</div>
                    <div class="panel-body">

                        <form action="xfrmTipoDocumento.php" method="POST" name="form1" id="form1">

                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-addon">Nombre</span>
                                    <input type="text" class="form-control" id="nombre" name="nombre" onChange="javascript:this.value=this.value.toUpperCase();" value="<?php echo $xdetalle; ?>" required>
                                </div>

                                <input type="hidden" name="idempb" id="idempb">
                                <input type="hidden" name="idempa" id="idempa">
                                <input type="hidden" name="idmod" id="idmod" value="<?php echo $_POST['idmod'];?>">
                                
                            </div> 
                            <div class="clearfix"> </div>
                            <br>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">C&oacute;digo SII</span>
                                    <input type="text" class="form-control" id="csii" name="csii" value="<?php echo $tiposii; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">Operador</span>
                                    <select class="form-control" id="operador" name="operador" required>
                                        <option value="">Selecciones</option>
                                        <option value="S" <?php if ($operador=="S") { echo "selected"; } ?>>Suma</option>
								        <option value="R" <?php if ($operador=="R") { echo "selected"; } ?>>Resta</option>
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix"> </div>

                            <div class="col-md-12 text-right">
                                <br>
                                <p>
                                <?php 
                                    if ($sw==1) {
                                ?>
                                    <button type="submit" class="btn btn-modificar">
                                        <span class="glyphicon glyphicon-edit"></span> Modificar
                                    </button>
                                <?php 
                                }else{
                                ?>
                                    <button type="submit" class="btn btn-grabar">
                                        <span class="glyphicon glyphicon-floppy-saved"></span> Grabar
                                    </button>
                                <?php 
                                }
                                ?>
                                <button type="button" class="btn btn-cancelar" onclick="Volver()">
                                    <span class="glyphicon glyphicon-remove"></span> Cancelar
                                </button>            
                                </p>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="clearfix"> </div>
                <hr>

                <div class="panel panel-default" style="background-color: hsl(0, 0%, 0%, 0);">
                    <div class="panel-heading">Documentos Disponibles</div>
                    <div class="panel-body">
                        <form name="form2" action="#" method="POST">
                            <input type="hidden" name="CTEmpre">
                            <br>

                            <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Codigo SII</th>
                                    <th>Nombre</th>
                                    <th>Sigla</th>
                                    <th width="10%">Operador</th>
                                    <th width="10%"> </th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php 
                                $mysqli=xconectar($_SESSION['UsuariaSV'],descriptSV($_SESSION['PassSV']),$_SESSION['BaseSV']);
                                $SQL="SELECT * FROM CTTipoDocumento WHERE estado<>'X'";
                                $resultados = $mysqli->query($SQL);
                                while ($registro = $resultados->fetch_assoc()) {

                                echo '
                                <tr>
                                    <td>'.$registro["tiposii"].'</td>
                                    <td>'.$registro["nombre"].'</td>
                                    <td>'.$registro["sigla"].'</td>
                                ';
                                if ($registro["operador"]=="S") {
                                    echo '
                                    <td>Suma</td>
                                    ';
                                }else{
                                    echo '
                                    <td>Resta</td>
                                    ';
                                }

                                echo '<td><button type="button" class="btn btn-modificar btn-xs" onclick="Modifi('.$registro["id"].')">Modificar</button></td>';


                                if($registro["estado"]=="B"){
                                    echo ' <td><button type="button" class="btn btn-warning btn-xs" onclick="Alta('.$registro["id"].')">Alta</button></td>';
                                }else{
                                    echo ' <td><button type="button" class="btn btn-cancelar btn-xs" onclick="Baja('.$registro["id"].')">Baja</button></td>';
                                }

                                echo '
                                </tr>
                                ';
                                }       
                                $mysqli->close();
                                ?>



                            </tbody>

                            </table>      
                        </form>
                    </div>
                </div>

            </div>
            <div class="col-sm-2">
            </div>
        </div>
        </div>

        <script>
            function showMessage() {
                $("#overlay").fadeIn();
                setTimeout(function(){
                    $("#overlay").fadeOut();
                }, 3000); // Ocultar después de 3 segundos
            }
        </script>

        <?php include 'footer.php'; ?>

    </body>
</html>
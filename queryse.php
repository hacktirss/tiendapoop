<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

include_once ("service/QrysService.php");

$request = utils\HTTPUtils::getRequest();

$Return = "querys.php";
$Titulo = "Detalle de querys";

$busca = $request->hasAttribute("id") ? $request->getAttribute("id") : $request->getAttribute("busca");

$objectVO = new QrysVO();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca);
    $Titulo = "Detalle de Querys: " . $objectVO->getId();
}

?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <link type="text/css" rel="stylesheet" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>
        <script>
            $(document).ready(function () {
                var busca = "<?= $busca ?>";
                $("#busca").val("<?= $busca ?>");

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#Id").html("<?= $busca ?>");
                $("#Nombre").val("<?= $objectVO->getNombre() ?>").prop("required", true);
                $("#Froms").val("<?= $objectVO->getFroms() ?>").prop("required", true);
                $("#Tampag").val("<?= $objectVO->getTampag() ?>").prop("required", true);
            });
        </script>
    </head>


    <body>

        <?php BordeSuperior() ?>

        <form name="form1" method="post" action="">
            <div id="Formularios">
                <table>
                    <tbody>
                        <tr>
                            <td>        
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Configuracion de los Grids</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Nombre:</div>
                                                        <div class="col-9"><input type="text" name="Nombre" id="Nombre" onkeyup="mayus(this);" required=""></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Tablas:</div>
                                                        <div class="col-9"><input type="text" name="Froms" id="Froms" required=""></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">LEFT JOIN:</div>
                                                        <div class="col-md-9"><textarea name="Joins" id="Joins"><?= $objectVO->getJoins() ?></textarea></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Registros por página:</div>
                                                        <div class="col-3"><input type="number" name="Tampag" id="Tampag"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Campos a mapear:</div>
                                                        <div class="col-md-9"><textarea name="Campos" id="Campos"><?= $objectVO->getCampos() ?></textarea></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Campos y Etiquetas para el gris de datos:</div>
                                                        <div class="col-md-9"><textarea name="Edi" id="Edi"><?= $objectVO->getEdi() ?></textarea></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-4"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <?php regresar($Return) ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos de auxiliares</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">

                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
        </form>

        <?php BordeSuperiorCerrar(); ?>
    </body>
</html> 

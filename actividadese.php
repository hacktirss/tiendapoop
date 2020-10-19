<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

$Titulo = "Detalle de actividad";

require_once "./service/ActividadService.php";

$objectVO = new ActividadVO();
$objectVO->setFecha(date("Y-m-d"));
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $usuarioSesion->getCia());
} 

?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
    </head>
    <body>

        <?php BordeSuperior(); ?> 

        <form name="form1" id="form1" method="post" action="" autocomplete="off">
            <div id="Formularios">
                <table>
                    <tbody>
                        <tr>
                            <td>        
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos de la tarea</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha:</div>
                                                        <div class="col-9"><span id="FechaSpan"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Descripcion:</div>
                                                        <div class="col-9"><input type="text" name="Descripcion" id="Descripcion" onkeyup="mayus(this);"></div>
                                                    </div>                                                                                                    
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Revision:</div>
                                                        <div class="col-3">
                                                            <select name="Periodo" id="Periodo">
                                                                <option value="1">DIARIO</option>
                                                                <option value="2">SEMANAL</option>
                                                                <option value="3">QUINCENAL</option>
                                                                <option value="4">MENSUAL</option>
                                                                <option value="5">ANUAL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Cantidad:</div>
                                                        <div class="col-3"><input type="number" name="Lapso" id="Lapso" min="0"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Tipo de evento:</div>
                                                        <div class="col-3">
                                                            <select name="Tipo" id="Tipo">
                                                                <option value="Actividad">Actividad</option>
                                                                <option value="Servicio">Servicio</option>
                                                                <option value="Matriz">Matriz</option>
                                                            </select>
                                                        </div>
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

                                <?php regresar("actividades.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos auxiliares</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Observaciones:</div>
                                                        <div class="col-9">
                                                            <textarea name="Observaciones" id="Observaciones"><?= $objectVO->getObservaciones() ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
        </form>

        <?php BordeSuperiorCerrar(); ?>

        <script>
            $(document).ready(function () {
                let busca = "<?= $busca ?>";
                $("#busca").val("<?= $busca ?>");

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#Id").html("<?= $busca ?>");
                $("#FechaSpan").html("<?= $objectVO->getFecha() ?>");
                $("#Descripcion").val("<?= $objectVO->getDescripcion() ?>").prop("required", "required");       
                $("#Tipo").val("<?= $objectVO->getTipo() ?>").prop("required", "required");     
                $("#Periodo").val("<?= $objectVO->getPeriodo() ?>").prop("required", "required");                    
                $("#Lapso").val("<?= $objectVO->getLapso() ?>").prop("required", "required");

                $("#Descripcion").focus();
            });
        </script>
    </body>
</html>


